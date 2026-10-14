<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Release;
use App\Models\ReleaseRevision;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecordReleaseRevisionAction
{
    use AuthorizesRequests;

    public function execute(Release $release, string $operation): void
    {
        $this->authorize('update', $release);
        $revision = new ReleaseRevision();
        $revision->release_id = $release->id;
        $revision->user_id = auth()->id();
        $revision->operation = $operation;
        $revision->snapshot = $release->only(['version', 'title', 'summary', 'content', 'state', 'published_at']);
        $revision->created_at = now();
        $revision->save();
    }
}
