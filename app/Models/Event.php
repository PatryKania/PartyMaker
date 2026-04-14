<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;
use App\Enums\EventType;


class Event extends Model
{
    protected $fillable = ['name', 'date', 'type', 'color'];

    protected $casts = [
        'type' => EventType::class,
    ];
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function memories()
    {
        return $this->hasMany(Memory::class);
    }
      public function eventPage()
    {
        return $this->hasOne(EventPage::class);
    }
}
