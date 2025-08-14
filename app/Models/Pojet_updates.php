<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pojet_updates extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id', 'progress_percentage', 'title', 'description', 'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
