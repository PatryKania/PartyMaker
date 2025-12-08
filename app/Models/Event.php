<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;
use App\Enums\EventType;

class Event extends Model
{
    protected $fillable = ['name', 'date', 'type', 'color', 'invitation', 'image', 'reminder_1', 'reminder_7', 'reminder_30'];

    protected $casts = [
        'type' => EventType::class,
    ];
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
