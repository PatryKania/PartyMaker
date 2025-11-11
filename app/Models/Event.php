<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;


class Event extends Model
{
    protected $fillable = ['name', 'date', 'type', 'color'];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
