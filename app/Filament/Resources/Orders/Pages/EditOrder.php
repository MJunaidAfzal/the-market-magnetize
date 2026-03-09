<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Mail\OrderAssigned;
use App\Models\Lead;
use App\Models\OrderAssignee;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get currently assigned users
        $assignees = $this->record->assignees()
            ->pluck('users.id')
            ->toArray();

        $data['assignees'] = $assignees;

        // Load lead information
        if ($this->record->lead) {
            $data['lead_first_name'] = $this->record->lead->first_name;
            $data['lead_last_name'] = $this->record->lead->last_name ?? '';
            $data['lead_email'] = $this->record->lead->email ?? '';
            $data['lead_phone'] = $this->record->lead->phone ?? '';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove assignees from the main data array (we'll handle it separately)
        $assignees = $data['assignees'] ?? [];
        unset($data['assignees']);

        // Store for after save
        $this->assignees = $assignees;

        return $data;
    }

    protected function afterSave(): void
    {
        $currentUser = Auth::user();
        $order = $this->record;
        $order->load(['lead', 'orderCategory', 'creator']);

        // Get current assignments
        $currentAssignments = OrderAssignee::where('order_id', $order->id)
            ->get();

        // Delete assignments that are no longer selected
        foreach ($currentAssignments as $assignment) {
            if (!in_array($assignment->user_id, $this->assignees)) {
                $assignment->delete();
            }
        }

        // Get current user IDs
        $currentUserIds = $currentAssignments->pluck('user_id')->toArray();

        // Create new assignments for newly selected users and send notifications
        foreach ($this->assignees as $userId) {
            if (!in_array($userId, $currentUserIds)) {
                // Create assignment
                OrderAssignee::create([
                    'order_id' => $order->id,
                    'user_id' => $userId,
                ]);

                // Get the assigned user
                $assignedUser = User::find($userId);

                if ($assignedUser) {
                    // Send Filament notification to the assigned user
                    Notification::make()
                        ->title('New Order Assigned')
                        ->icon('heroicon-o-shopping-cart')
                        ->body("You have been assigned to order: ** {$order->order_number} - {$order->title} **")
                        ->info()
                        ->sendToDatabase($assignedUser);

                    // Send email notification to assigned user
                    if ($assignedUser->email) {
                        try {
                            Mail::to($assignedUser->email)->send(
                                new OrderAssigned($order, $assignedUser, $currentUser)
                            );
                        } catch (\Exception $e) {
                            \Log::error('Failed to send order assignment email: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Send notification to admin users about the order update
        $this->sendAdminNotification($order, $currentUser);
    }

    /**
     * Send notification to admin users about order update
     */
    protected function sendAdminNotification($order, $currentUser)
    {
        // Get admin role users
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $adminUsers = User::role('admin')->get();
        } else {
            // Fallback: get users other than current user
            $adminUsers = User::where('id', '!=', $currentUser->id)->limit(5)->get();
        }

        foreach ($adminUsers as $admin) {
            Notification::make()
                ->title('Order Updated')
                ->icon('heroicon-o-shopping-cart')
                ->body("Order has been updated: ** {$order->order_number} - {$order->title} ** by {$currentUser->name}")
                ->info()
                ->sendToDatabase($admin);
        }
    }
}
