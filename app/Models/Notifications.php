<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'read', 'data'
    ];

    protected $casts = [
        'read' => 'boolean',
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['read' => true]);
    }
}
