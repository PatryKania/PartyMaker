<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $fillable = ['event_id', 'title', 'description', 'due_date', 'is_completed'];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class);
    }
}
