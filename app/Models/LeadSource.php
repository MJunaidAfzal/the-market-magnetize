<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Generate slug from name automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leadSource) {
            if (empty($leadSource->slug)) {
                $leadSource->slug = \Str::slug($leadSource->name);
            }
        });

        static::updating(function ($leadSource) {
            if (empty($leadSource->slug)) {
                $leadSource->slug = \Str::slug($leadSource->name);
            }
        });
    }

    /**
     * Get all leads from this source.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'lead_source_id');
    }
}
