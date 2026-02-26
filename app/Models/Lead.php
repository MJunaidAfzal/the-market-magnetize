<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'job_title',
        'lead_source_id',
        'status',
        'value',
        'score',
        'country',
        'city',
        'address',
        'notes',
        'last_contacted_at',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'score' => 'integer',
            'last_contacted_at' => 'datetime',
            'follow_up_date' => 'datetime',
        ];
    }

    /**
     * Status constants
     */
    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_WON = 'won';
    public const STATUS_LOST = 'lost';

    /**
     * Get all status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_QUALIFIED => 'Qualified',
            self::STATUS_WON => 'Won',
            self::STATUS_LOST => 'Lost',
        ];
    }

    /**
     * Get the lead source.
     */
    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    /**
     * Get the assigned users.
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lead_assign_to_users')
                    ->withPivot('id', 'assigned_by', 'assigned_at', 'unassigned_at', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get active assignments.
     */
    public function activeAssignments()
    {
        return $this->hasMany(LeadAssignToUser::class, 'lead_id')->where('is_active', true);
    }

    /**
     * Get the full name of the lead.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Scope for active leads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for leads by status.
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
