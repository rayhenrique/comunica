<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Release extends Model
{
    protected $guarded = ['*'];

    /** @return HasMany<ReleaseRevision, $this> */
    public function revisions(): HasMany
    {
        return $this->hasMany(ReleaseRevision::class)->orderByDesc('id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
