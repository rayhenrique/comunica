<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcknowledgeReleaseAction
{
    use AuthorizesRequests;

    public function execute(string $version): void
    {
        $this->authorize('acknowledgeRelease', User::class);
        if (version_compare($version, AppVersion::current(), '>') || !Release::where('version', $version)->where('state', 'published')->whereNotNull('published_at')->exists()) {
            throw ValidationException::withMessages(['notice' => 'Esta versão não está disponível. Atualize a página.']);
        }
        DB::table('user_release_views')->insertOrIgnore([
            'user_id' => auth()->id(), 'version' => $version, 'seen_at' => now(),
        ]);
    }
}
