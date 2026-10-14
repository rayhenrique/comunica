<?php

declare(strict_types=1);

use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Coordinator\CreateRequest;
use App\Livewire\Public\CalendarView;
use App\Http\Middleware\ValidateCalendarParameters;
use App\Models\EventRequest;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/calendario');
Route::view('/sobre', 'pages.about')->name('about');

Route::get('/login', Login::class)->middleware('guest')->name('login');
Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::get('/calendario', CalendarView::class)->middleware(ValidateCalendarParameters::class)->name('calendar');

Route::middleware('auth')->group(function (): void {
    Route::get('/solicitacoes/nova', CreateRequest::class)
        ->can('create', EventRequest::class)->name('requests.create');
    Route::get('/solicitacoes', [EventRequestController::class, 'index'])
        ->can('viewAny', EventRequest::class)->name('requests.index');
    Route::get('/solicitacoes/{eventRequest}', [EventRequestController::class, 'show'])
        ->whereNumber('eventRequest')->can('view', 'eventRequest')->name('requests.show');
    Route::get('/comunicacao/triagem', [EventRequestController::class, 'triage'])
        ->can('moderate', EventRequest::class)->name('triage.index');
});
