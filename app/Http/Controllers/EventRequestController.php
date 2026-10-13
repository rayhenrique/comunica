<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ListVisibleEventRequestsAction;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EventRequestController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, ListVisibleEventRequestsAction $list): JsonResponse|View|RedirectResponse
    {
        $this->authorize('viewAny', EventRequest::class);
        if (!$request->expectsJson()) {
            return $this->authenticatedUser($request)->isCommunicationAdmin()
                ? redirect('/comunicacao/triagem') : view('pages.my-requests');
        }

        return response()->json($list->execute($this->authenticatedUser($request)));
    }

    public function show(EventRequest $eventRequest): JsonResponse
    {
        $this->authorize('view', $eventRequest);

        return response()->json($eventRequest);
    }

    public function triage(Request $request, ListVisibleEventRequestsAction $list): JsonResponse|View
    {
        $this->authorize('moderate', EventRequest::class);
        if (!$request->expectsJson()) {
            return view('pages.triage');
        }

        return response()->json($list->execute($this->authenticatedUser($request)));
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return $user;
    }
}
