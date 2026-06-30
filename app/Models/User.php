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

#[Fillable(['name', 'gender', 'password', 'active', 'role_id', 'photo', 'face_verified', 'date_of_birth', 'age', 'note'])]
#[Hidden(['password', 'remember_token'])]
#[Casts(['active' => 'boolean', 'face_verified' => 'boolean', 'password' => 'hashed', 'date_of_birth' => 'date'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
     use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function calculateAge(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        // Ensure date_of_birth is a Carbon instance
        $dob = $this->date_of_birth instanceof \Carbon\Carbon 
            ? $this->date_of_birth 
            : \Carbon\Carbon::parse($this->date_of_birth);
        
        $today = now();
        $age = $today->year - $dob->year;
        
        // Subtract one if birthday hasn't occurred yet this year
        if ($today->month < $dob->month || 
            ($today->month == $dob->month && $today->day < $dob->day)) {
            $age--;
        }
        
        return $age;
    }

    public function updateAgeFromDob(): void
    {
        $this->age = $this->calculateAge();
        $this->save();
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
