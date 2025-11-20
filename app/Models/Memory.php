<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'desc'
    ];

    public function memoryMedia()
    {
        return $this->hasMany(MemoryMedia::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
