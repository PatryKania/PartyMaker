<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\User;
use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use App\Enums\ParticipantType;

class Participant extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'role',
        'type',
        'status',
    ];

    protected $casts = [
        'role' => ParticipantRole::class,
        'status' => ParticipantStatus::class,
        'type' => ParticipantType::class,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
