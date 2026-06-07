<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'desc',
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'immutable_datetime:H:i:s',
        'end_time' => 'immutable_datetime:H:i:s',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
