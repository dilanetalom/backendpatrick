<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service',
        'name',
        'description',
        'objectives',
        'deadline',
        'client_price',
        'final_price',
        'status',
        'progress',
        'specific_fields',
        'final_link',
    ];
    
    // Le champ specific_fields sera automatiquement converti en tableau/objet
    protected $casts = [
        'specific_fields' => 'array',
    ];

    // Un projet appartient à un utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un projet appartient à un service
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Un projet peut avoir plusieurs propositions de prix
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    // Un projet a un seul contrat (logique métier)
    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }
    
    // Un projet peut avoir plusieurs fichiers joints
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
