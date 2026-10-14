<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class ShouldShowReleaseNoticeAction
{
    use AuthorizesRequests;

    public function execute(): bool
    {
        $this->authorize('acknowledgeRelease', User::class);

        return session()->get('release-notice-login', false)
            && Release::where('version', AppVersion::current())->where('state', 'published')->whereNotNull('published_at')->exists()
            && !DB::table('user_release_views')->where('user_id', auth()->id())->where('version', AppVersion::current())->exists();
    }
}
