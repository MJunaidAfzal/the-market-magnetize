<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'plain_password',
        'phone_number',
        'profile_photo',
        'date_of_birth',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Store plain password temporarily for export (not persisted to database).
     */
    protected $plainPassword = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Set the password attribute - hash it but store plain version permanently.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        // Only update if value is not empty
        if (!empty($value)) {
            // Store the plain password for export (persisted to database)
            $this->plainPassword = $value;
            $this->attributes['plain_password'] = $value;
            // Hash the password for database storage
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Get the plain password for export.
     *
     * @return string|null
     */
    public function getPlainPasswordAttribute(): ?string
    {
        return $this->attributes['plain_password'] ?? null;
    }

    /**
     * Set the plain password.
     *
     * @param string $value
     * @return void
     */
    public function setPlainPassword($value): void
    {
        $this->plainPassword = $value;
        $this->attributes['plain_password'] = $value;
    }

    /**
     * Get all leads assigned to this user.
     */
    public function assignedLeads()
    {
        return $this->belongsToMany(Lead::class, 'lead_assign_to_users')
                    ->withPivot('id', 'assigned_by', 'assigned_at', 'unassigned_at', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get active leads assigned to this user.
     */
    public function activeAssignedLeads()
    {
        return $this->belongsToMany(Lead::class, 'lead_assign_to_users')
                    ->wherePivot('is_active', true)
                    ->withPivot('id', 'assigned_by', 'assigned_at', 'unassigned_at', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get lead assignments for this user.
     */
    public function leadAssignments()
    {
        return $this->hasMany(LeadAssignToUser::class, 'user_id');
    }

    /**
     * Get all orders assigned to this user.
     */
    public function assignedOrders()
    {
        return $this->belongsToMany(Order::class, 'order_assignees')
                    ->withTimestamps();
    }

    /**
     * Get order assignments for this user.
     */
    public function orderAssignments()
    {
        return $this->hasMany(OrderAssignee::class, 'user_id');
    }
}
