<?php

declare(strict_types=1);

namespace App\Livewire\Coordinator;

use App\Actions\CreateEventRequestAction;
use App\Livewire\Forms\EventRequestForm;
use App\Models\EventRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nova solicitação')]
class CreateRequest extends Component
{
    public EventRequestForm $form;

    #[Locked]
    public string $submissionToken = '';

    public function boot(): void
    {
        $this->authorize('create', EventRequest::class);
    }

    public function mount(): void
    {
        $this->submissionToken = (string) Str::uuid();
    }

    public function save(CreateEventRequestAction $create): void
    {
        $this->resetErrorBag();
        try {
            $data = $this->form->validate();
            $create->execute($data, $this->submissionToken);
            session()->flash('status', 'Solicitação criada. Acompanhe a decisão nesta lista.');
            $this->dispatch('form-saved');
            $this->redirect('/solicitacoes');
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $key = str_starts_with($field, 'form.') ? $field : 'form.'.$field;
                $this->addError($key, $messages[0]);
            }
            $this->dispatch('validation-failed');
        } catch (QueryException) {
            $this->addError('submission', 'Não foi possível criar a solicitação. Seus dados foram mantidos; tente novamente.');
            $this->dispatch('validation-failed');
        }
    }

    public function render(): View
    {
        return view('livewire.coordinator.create-request');
    }
}
