<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRevision extends Model
{
    protected $table = 'order_revisions';

    protected $fillable = [
        'revision_files',
        'revision_note',
        'user_id',
        'order_id'
    ];

    protected function casts(): array
    {
        return [
            'revision_files' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
