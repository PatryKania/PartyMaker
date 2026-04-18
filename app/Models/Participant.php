<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'related_id'
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

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function parents()
    {
        return $this->belongsToMany(
            Participant::class,
            'participant_parent',
            'participant_id',
            'parent_id'
        );
    }

    public function children()
    {
        return $this->belongsToMany(
            Participant::class,
            'participant_parent',
            'parent_id',
            'participant_id'
        );
    }

    public function isReletedParticipant(){
        $user = auth()->user();

        if ($this->email === $user->email)
            return true;
    

        if ($this->parents()->where('email', $user->email)->exists())
            return true;
        

        if($this->related_id == $user->id)
            return true;

        return false;
    }

    public function scopeWhereReletedParticipant($query, $userEmail,$userId)
    {
        return $query->where(function ($subQuery) use ($userEmail,$userId) {
            $subQuery->where('email', $userEmail)
                ->orWhereHas('parents', fn($q) => $q->where('email', $userEmail))->orWhere('related_id',$userId);
        });
    }

    public function relatedParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'related_id');
    }
}
