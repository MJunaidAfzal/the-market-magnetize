<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'order_category_id',
        'created_by',
        'order_number',
        'title',
        'description',
        'status',
        'priority',
        'amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'start_date',
        'due_date',
        'completed_at',
        'attachments',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'attachments' => 'array',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
            if (empty($order->created_by) && auth()->check()) {
                $order->created_by = auth()->id();
            }
        });

        static::created(function ($order) {
            // Sync assignees if they were provided
            if (request()->has('assignees') && is_array(request('assignees'))) {
                $order->assignees()->sync(request('assignees'));
            }
        });

        static::updated(function ($order) {
            // Sync assignees if they were provided
            if (request()->has('assignees') && is_array(request('assignees'))) {
                $order->assignees()->sync(request('assignees'));
            }
        });
    }

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'Pending';
    public const STATUS_CONFIRMED = 'Confirmed';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_ON_HOLD = 'On Hold';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';

    /**
     * Priority constants
     */
    public const PRIORITY_LOW = 'Low';
    public const PRIORITY_MEDIUM = 'Medium';
    public const PRIORITY_HIGH = 'High';
    public const PRIORITY_URGENT = 'Urgent';

    /**
     * Payment status constants
     */
    public const PAYMENT_UNPAID = 'Unpaid';
    public const PAYMENT_PARTIAL = 'Partial';
    public const PAYMENT_PAID = 'Paid';
    public const PAYMENT_OVERDUE = 'Overdue';

    /**
     * Get all status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get all priority options.
     */
    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * Get all payment status options.
     */
    public static function getPaymentStatusOptions(): array
    {
        return [
            self::PAYMENT_UNPAID => 'Unpaid',
            self::PAYMENT_PARTIAL => 'Partial',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_OVERDUE => 'Overdue',
        ];
    }

    /**
     * Get the lead.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Get the order category.
     */
    public function orderCategory(): BelongsTo
    {
        return $this->belongsTo(OrderCategory::class, 'order_category_id');
    }

    /**
     * Get the creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assigned users.
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'order_assignees')
                    ->withTimestamps();
    }

    /**
     * Calculate and update due amount.
     */
    public function calculateDueAmount(): void
    {
        $this->due_amount = $this->amount - $this->paid_amount;
        
        if ($this->due_amount <= 0) {
            $this->payment_status = self::PAYMENT_PAID;
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = self::PAYMENT_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_UNPAID;
        }
    }

    /**
     * Scope for pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
