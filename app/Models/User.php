<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    /** @var list<string> */
    protected $fillable = ['name', 'email', 'password', 'department'];

    /** @return HasMany<EventRequest, $this> */
    public function eventRequests(): HasMany
    {
        return $this->hasMany(EventRequest::class);
    }

    public function isCommunicationAdmin(): bool
    {
        return $this->role === 'communication_admin';
    }

    public function isCoordinator(): bool
    {
        return $this->role === 'coordinator';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
