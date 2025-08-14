<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Projets extends Model
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
        'specific_data',
        'final_link',
        'device',
    ];

    protected $casts = [
        'specific_data' => 'json',
        'deadline' => 'date',
    ];

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that the project belongs to.
     */
    // public function service()
    // {
    //     return $this->belongsTo(Service::class);
    // }

    /**
     * Get the contract associated with the project.
     */
    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    /**
     * Get the payments for the project.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the conversation associated with the project.
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class, 'projet_id');
    }
}
