<?php

declare(strict_types=1);

use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ReleaseHistoryController;
use App\Livewire\Communication\Releases;
use App\Livewire\Communication\ReleaseEditor;
use App\Models\Release;
use App\Livewire\Auth\Login;
use App\Livewire\Coordinator\CreateRequest;
use App\Livewire\Public\CalendarView;
use App\Http\Middleware\ValidateCalendarParameters;
use App\Models\EventRequest;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/calendario');
Route::view('/sobre', 'pages.about')->name('about');
Route::get('/ajuda', [SupportController::class, 'help'])->name('help');
Route::get('/novidades', [SupportController::class, 'news'])->name('news');

Route::get('/login', Login::class)->middleware('guest')->name('login');
Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::get('/calendario', CalendarView::class)->middleware(ValidateCalendarParameters::class)->name('calendar');

Route::middleware('auth')->group(function (): void {
    Route::get('/comunicacao/novidades', Releases::class)->can('viewAny', Release::class)->name('releases.index');
    Route::get('/comunicacao/novidades/nova', ReleaseEditor::class)->can('create', Release::class)->name('releases.create');
    Route::get('/comunicacao/novidades/{id}/editar', ReleaseEditor::class)->whereNumber('id')->can('create', Release::class)->name('releases.edit');
    Route::get('/comunicacao/novidades/{release}/historico', ReleaseHistoryController::class)->whereNumber('release')->can('view', 'release')->name('releases.history');
    Route::get('/solicitacoes/nova', CreateRequest::class)
        ->can('create', EventRequest::class)->name('requests.create');
    Route::get('/solicitacoes', [EventRequestController::class, 'index'])
        ->can('viewAny', EventRequest::class)->name('requests.index');
    Route::get('/solicitacoes/{eventRequest}', [EventRequestController::class, 'show'])
        ->whereNumber('eventRequest')->can('view', 'eventRequest')->name('requests.show');
    Route::get('/comunicacao/triagem', [EventRequestController::class, 'triage'])
        ->can('moderate', EventRequest::class)->name('triage.index');
});
