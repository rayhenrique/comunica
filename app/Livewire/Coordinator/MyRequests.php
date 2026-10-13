<?php

declare(strict_types=1);

namespace App\Livewire\Coordinator;

use App\Actions\CancelEventRequestAction;
use App\Actions\ListVisibleEventRequestsAction;
use App\Models\EventRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';
    #[Locked]
    public ?int $selectedId = null;
    public bool $detailOpen = false;
    public bool $cancelOpen = false;
    #[Locked]
    public string $feedback = '';

    public function mount(): void
    {
        $this->feedback = (string) session()->pull('status', '');
    }

    public function boot(): void
    {
        $this->authorize('create', EventRequest::class);
    }

    public function updatedStatus(): void
    {
        $this->resetErrorBag();
        $this->resetPage();
    }

    #[Computed]
    public function selectedEvent(): ?EventRequest
    {
        if ($this->selectedId === null) {
            return null;
        }
        $event = EventRequest::findOrFail($this->selectedId);
        $this->authorize('view', $event);

        return $event;
    }

    public function openDetails(int $id): void
    {
        $event = EventRequest::findOrFail($id);
        $this->authorize('view', $event);
        $this->selectedId = $id;
        $this->resetErrorBag();
        $this->detailOpen = true;
    }

    public function askCancel(int $id): void
    {
        $event = EventRequest::findOrFail($id);
        $this->authorize('cancel', $event);
        $this->selectedId = $id;
        $this->resetErrorBag();
        $this->detailOpen = false;
        $this->cancelOpen = true;
    }

    public function cancel(CancelEventRequestAction $cancel): void
    {
        abort_if($this->selectedId === null, 404);
        try {
            $cancel->execute($this->selectedId);
            $this->cancelOpen = false;
            $this->selectedId = null;
            unset($this->selectedEvent);
            $this->resetPage();
            $this->feedback = 'Solicitação cancelada. Ela permanece no seu histórico.';
        } catch (AuthorizationException $exception) {
            $event = EventRequest::findOrFail($this->selectedId);
            $this->authorize('view', $event);
            $this->addError('cancel', 'Esta solicitação já foi decidida ou cancelada. Atualize a lista.');
        } catch (QueryException) {
            $this->addError('cancel', 'Não foi possível cancelar. Tente novamente.');
        }
    }

    public function render(): View
    {
        try {
            $requests = app(ListVisibleEventRequestsAction::class)->execute(auth()->user(), $this->status);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->errors());
            $requests = new LengthAwarePaginator([], 0, 15);
        }
        return view('livewire.coordinator.my-requests', [
            'requests' => $requests,
        ]);
    }
}
