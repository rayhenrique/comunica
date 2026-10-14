<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetPublicReleasesAction;
use App\Support\AppVersion;
use Illuminate\Contracts\View\View;

class SupportController extends Controller
{
    public function help(): View
    {
        return view('pages.help');
    }

    public function news(): View
    {
        return view('pages.news', ['releases' => app(GetPublicReleasesAction::class)->execute(), 'current' => AppVersion::current()]);
    }
}
