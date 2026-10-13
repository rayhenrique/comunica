<?php

declare(strict_types=1);

namespace App\Livewire\Communication;

use App\Actions\DetectEventConflictAction;
use App\Actions\ListTriageEventRequestsAction;
use App\Actions\ModerateEventAction;
use App\Models\EventRequest;
use App\Models\User;
use App\Support\EventLabels;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Triage extends Component
{
    use WithPagination;

    #[Url]
    public string $status = 'pending';
    #[Url]
    public string $department = '';
    #[Url]
    public string $demand_type = '';
    #[Url]
    public string $from = '';
    #[Url]
    public string $to = '';
    #[Locked]
    public ?int $selectedId = null;
    public bool $reviewOpen = false;
    public string $decision = 'approve';
    public string $start_at = '';
    public string $moderation_notes = '';
    #[Locked]
    public string $feedback = '';

    public function boot(): void
    {
        $this->authorize('moderate', EventRequest::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['status', 'department', 'demand_type', 'from', 'to'], true)) {
            $this->resetErrorBag();
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset('status', 'department', 'demand_type', 'from', 'to');
        $this->resetErrorBag();
        $this->resetPage();
    }

    #[Computed]
    public function selectedEvent(): ?EventRequest
    {
        if ($this->selectedId === null) {
            return null;
        }
        $event = EventRequest::with('user')->findOrFail($this->selectedId);
        $this->authorize('view', $event);

        return $event;
    }

    /** @return Collection<int, EventRequest> */
    #[Computed]
    public function conflicts(): Collection
    {
        $selected = $this->selectedEvent;
        if ($selected === null) {
            return new Collection();
        }
        $candidate = clone $selected;
        if ($this->decision === 'reschedule' && Validator::make(['date' => $this->start_at], ['date' => 'required|date'])->passes()) {
            $candidate->start_at = CarbonImmutable::parse($this->start_at, config('app.timezone'))->setTimezone(config('app.timezone'));
        }

        return app(DetectEventConflictAction::class)->execute($candidate);
    }

    public function openReview(int $id): void
    {
        $event = EventRequest::findOrFail($id);
        $this->authorize('view', $event);
        $this->selectedId = $id;
        $this->decision = 'approve';
        $this->start_at = $event->start_at->format('Y-m-d\TH:i');
        $this->moderation_notes = '';
        $this->resetErrorBag();
        $this->reviewOpen = true;
    }

    public function moderate(ModerateEventAction $moderate): void
    {
        abort_if($this->selectedId === null, 404);
        $this->resetErrorBag();
        try {
            $result = $moderate->execute(EventRequest::findOrFail($this->selectedId), $this->decision, $this->start_at, $this->moderation_notes);
            $this->feedback = 'Solicitação '.mb_strtolower(EventLabels::status($result->status)).'. A decisão foi registrada.';
            $this->reviewOpen = false;
            $this->selectedId = null;
            unset($this->selectedEvent, $this->conflicts);
            $this->resetPage();
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->dispatch('validation-failed');
        } catch (QueryException) {
            $this->addError('decision', 'Não foi possível registrar a decisão. Tente novamente.');
        }
    }

    public function render(): View
    {
        try {
            $requests = app(ListTriageEventRequestsAction::class)->execute([
                'status' => $this->status, 'department' => $this->department, 'demand_type' => $this->demand_type,
                'from' => $this->from, 'to' => $this->to,
            ]);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->errors());
            $requests = new LengthAwarePaginator([], 0, 15);
        }
        $conflictCounts = [];
        foreach ($requests as $event) {
            $conflictCounts[$event->id] = $event->status === 'pending'
                ? app(DetectEventConflictAction::class)->execute($event)->count() : 0;
        }

        return view('livewire.communication.triage', [
            'requests' => $requests, 'conflictCounts' => $conflictCounts,
            'departments' => User::query()->distinct()->orderBy('department')->pluck('department'),
        ]);
    }
}
