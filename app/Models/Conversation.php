<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
    ];

    /**
     * Get the project that the conversation belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Projets::class);
    }

    /**
     * Get the messages for the conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
