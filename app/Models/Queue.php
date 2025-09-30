<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    /** @use HasFactory<\Database\Factories\QueueFactory> */
    use HasFactory;

    protected $fillable = [
        'service_id',
        'patient_name',
        'number',
        'code',
        'counter',
        'status',
        'called_at',
        'finished_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
