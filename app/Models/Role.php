<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description'])]
class Role extends Model
{
    public function tabs(): BelongsToMany
    {
        return $this->belongsToMany(Tab::class, 'role_tab');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasAccessTo(string $slug): bool
    {
        return $this->tabs->contains('slug', $slug);
    }
}
