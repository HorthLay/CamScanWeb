<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\Casts;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'gender', 'password', 'active', 'role_id', 'photo', 'face_verified'])]
#[Hidden(['password', 'remember_token'])]
#[Casts(['active' => 'boolean', 'face_verified' => 'boolean', 'password' => 'hashed'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
     use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function canAccess(string $tabSlug): bool
    {
        if (! $this->role) return false;

        $this->role->loadMissing('tabs');

        return $this->role->hasAccessTo($tabSlug);
    }

    public function accessibleTabs()
    {
        if (! $this->role) return collect();

        $this->role->loadMissing('tabs');

        return $this->role->tabs->sortBy('order');
    }
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }
}
