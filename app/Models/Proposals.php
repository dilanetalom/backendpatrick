<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposals extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'price',
        'status',
    ];
    
    // Une proposition appartient à un projet
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    
    // Une proposition est faite par un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
