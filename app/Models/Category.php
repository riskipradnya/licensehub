<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'color'])]
class Category extends Model
{
    // ─── Relationships ───────────────────────────────────────

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
