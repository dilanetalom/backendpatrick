<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
    ];

    /**
     * Get the conversation that the message belongs to.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
