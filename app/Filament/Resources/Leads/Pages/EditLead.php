<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Mail\LeadAssigned;
use App\Models\LeadAssignToUser;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get currently active assigned users
        $assignedUsers = $this->record->assignedUsers()
            ->wherePivot('is_active', true)
            ->pluck('users.id')
            ->toArray();

        $data['assigned_users'] = $assignedUsers;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove assigned_users from the main data array (we'll handle it separately)
        $assignedUsers = $data['assigned_users'] ?? [];
        unset($data['assigned_users']);

        // Store for after save
        $this->assignedUsers = $assignedUsers;

        return $data;
    }

    protected function afterSave(): void
    {
        $currentUser = Auth::user();
        $lead = $this->record;
        $lead->load('leadSource');

        // Get current active assignments
        $currentAssignments = LeadAssignToUser::where('lead_id', $lead->id)
            ->where('is_active', true)
            ->get();

        // Delete assignments that are no longer selected (delete from database)
        foreach ($currentAssignments as $assignment) {
            if (!in_array($assignment->user_id, $this->assignedUsers)) {
                $assignment->delete();
            }
        }

        // Create new assignments for newly selected users and send notifications
        $currentUserIds = $currentAssignments->pluck('user_id')->toArray();

        foreach ($this->assignedUsers as $userId) {
            if (!in_array($userId, $currentUserIds)) {
                // Create assignment
                LeadAssignToUser::create([
                    'lead_id' => $lead->id,
                    'user_id' => $userId,
                    'assigned_by' => $currentUser->id,
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);

                // Get the assigned user
                $assignedUser = User::find($userId);

                if ($assignedUser) {
                    // Send Filament notification to the user
                    Notification::make()
                        ->title('New Lead Assigned')
                        ->icon('heroicon-o-user-group')
                        ->body("You have been assigned to lead: ** {$lead->full_name} **")
                        ->info()
                        ->sendToDatabase($assignedUser);

                    // Send email notification if user has email
                    if ($assignedUser->email) {
                        try {
                            Mail::to($assignedUser->email)->send(
                                new LeadAssigned($lead, $assignedUser, $currentUser)
                            );
                        } catch (\Exception $e) {
                            // Log error but don't break the flow
                            \Log::error('Failed to send lead assignment email: ' . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash'),
        ];
    }
}
