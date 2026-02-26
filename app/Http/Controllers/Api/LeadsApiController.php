<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class LeadsApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'status' => 'nullable|in:new,contacted,qualified,won,lost',
            'value' => 'nullable|numeric|min:0',
            'score' => 'nullable|integer|min:0|max:100',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'last_contacted_at' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lead = Lead::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'job_title' => $request->job_title,
            'lead_source_id' => $request->lead_source_id ?? 1,
            'status' => $request->status ?? 'new',
            'value' => $request->value,
            'score' => $request->score ?? 0,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'notes' => $request->notes,
            'last_contacted_at' => $request->last_contacted_at,
            'follow_up_date' => $request->follow_up_date,
        ]);

        // Load relationships
        $lead->load(['leadSource']);

        // Assign users if provided
        if ($request->has('assigned_users') && is_array($request->assigned_users)) {
            foreach ($request->assigned_users as $userId) {
                \App\Models\LeadAssignToUser::create([
                    'lead_id' => $lead->id,
                    'user_id' => $userId,
                    'assigned_by' => 1,
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);
            }
        }

        // Send notifications to Admin role users
        $this->sendAdminNotifications($lead);

        // Send email to specific admin email
        $this->sendAdminEmail($lead);

        $lead->load(['leadSource', 'assignedUsers']);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    /**
     * Send Filament notifications to all Admin role users
     */
    protected function sendAdminNotifications(Lead $lead): void
    {
        // Get all users with Admin role
        $adminUsers = User::role('Admin')->get();

        $email = $lead->email ?? 'No email';
        $phone = $lead->phone ?? 'No phone';

        foreach ($adminUsers as $admin) {
            Notification::make()
                ->title('New Lead Added')
                ->icon('heroicon-o-user-plus')
                ->body($lead->full_name . " has been added from API.\n\n📧 " . $email . "\n📱 " . $phone)
                ->danger()
                ->sendToDatabase($admin);
        }
    }

    /**
     * Send email notification to admin email
     */
    protected function sendAdminEmail(Lead $lead): void
    {
        try {
            Mail::to('mjunaidafzal395@gmail.com')->send(
                new NewLeadNotification($lead)
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error('Failed to send admin email notification: ' . $e->getMessage());
        }
    }

}
