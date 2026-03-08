<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *s
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            DB::table('participants')
                ->where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        });
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'participants',
            'email',
            'event_id',
            'email',
            'id'
        )->withPivot('role');
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->events;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        $hasAccess = $this->events()
        ->whereKey($tenant)
        ->where(function ($query) {
            $query->where('role', 'organizer')
                  ->orWhereNotIn('status', ['new', 'rejected']);
        })
        ->exists();

    if (!$hasAccess) {
        abort(redirect('/dashboard'));
    }

    return true;
    }

    public function isOrganizer(): bool
    {
        $currentEvent = Filament::getTenant();

        if (!$currentEvent) {
            return false;
        }

        return $this->events()
            ->where('events.id', $currentEvent->id)
            ->wherePivot('role', ParticipantRole::Organizer)
            ->exists();
    }

    public function hasPermissions(): bool
    {
        $currentEvent = Filament::getTenant();

        if (!$currentEvent) {
            return false;
        }

        $confirmInvitation = $this->events()
            ->where('events.id', $currentEvent->id)
            ->wherePivot('status', ParticipantStatus::Confirmed)
            ->exists();
        if ($this->isOrganizer() || $confirmInvitation) {
            return true;
        }

        return false;
    }
}
