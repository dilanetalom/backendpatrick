<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;


    protected $fillable = [
        'project_id',
        'amount',
        'proof_path',
        'is_verified',
    ];

    /**
     * Get the project that the payment belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
