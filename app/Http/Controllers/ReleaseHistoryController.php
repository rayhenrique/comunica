<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReleaseHistoryController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Release $release): View
    {
        $this->authorize('view', $release);

        return view('pages.release-history', ['release' => $release, 'revisions' => $release->revisions()->with('user')->paginate(10)]);
    }
}
