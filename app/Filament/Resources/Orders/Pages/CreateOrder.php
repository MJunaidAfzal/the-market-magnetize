<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Mail\OrderAssigned;
use App\Models\Lead;
use App\Models\OrderAssignee;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Check if lead_id is passed via query parameter
        $leadId = request()->query('lead_id');
        
        if ($leadId) {
            $lead = Lead::find($leadId);
            
            if ($lead) {
                $this->form->fill([
                    'lead_id' => $leadId,
                    'lead_first_name' => $lead->first_name,
                    'lead_last_name' => $lead->last_name ?? '',
                    'lead_email' => $lead->email ?? '',
                    'lead_phone' => $lead->phone ?? '',
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove assignees from the main data array (we'll handle it separately)
        $assignees = $data['assignees'] ?? [];
        unset($data['assignees']);

        // Store for after creation
        $this->assignees = $assignees;

        return $data;
    }

    protected function afterCreate(): void
    {
        $currentUser = Auth::user();
        $order = $this->record;
        $order->load(['lead', 'orderCategory', 'creator']);

        // Handle user assignments
        if (!empty($this->assignees)) {
            foreach ($this->assignees as $userId) {
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

        // Send notification to admin users
        $this->sendAdminNotification($order, $currentUser);
    }

    /**
     * Send notification to admin users
     */
    protected function sendAdminNotification($order, $currentUser)
    {
        // Get admin role users
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $adminUsers = User::role('admin')->get();
        } else {
            // Fallback: get all users with all-permissions trait or first user
            $adminUsers = User::where('id', '!=', $currentUser->id)->limit(5)->get();
        }

        foreach ($adminUsers as $admin) {
            Notification::make()
                ->title('New Order Created')
                ->icon('heroicon-o-shopping-cart')
                ->body("A new order has been created: ** {$order->order_number} - {$order->title} ** by {$currentUser->name}")
                ->info()
                ->sendToDatabase($admin);
        }
    }
}
