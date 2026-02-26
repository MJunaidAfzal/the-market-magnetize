<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Mail\LeadAssigned;
use App\Models\LeadAssignToUser;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove assigned_users from the main data array (we'll handle it separately)
        $assignedUsers = $data['assigned_users'] ?? [];
        unset($data['assigned_users']);

        // Store for after creation
        $this->assignedUsers = $assignedUsers;

        return $data;
    }

    protected function afterCreate(): void
    {
        $currentUser = Auth::user();
        $lead = $this->record;
        $lead->load('leadSource');

        // Handle user assignments and send notifications
        if (!empty($this->assignedUsers)) {
            foreach ($this->assignedUsers as $userId) {
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
}
