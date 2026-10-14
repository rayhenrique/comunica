<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Database\Eloquent\Collection;

class GetPublicReleasesAction
{
    /** @return Collection<int, Release> */
    public function execute(): Collection
    {
        return Release::where('state', 'published')->whereNotNull('published_at')
            ->get(['id', 'version', 'title', 'summary', 'content', 'published_at'])
            ->filter(fn (Release $release): bool => version_compare($release->version, AppVersion::current(), '<='))
            ->sort(fn (Release $a, Release $b): int => version_compare($b->version, $a->version))->values();
    }
}
