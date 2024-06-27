<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'user_id',
        'therapist_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
