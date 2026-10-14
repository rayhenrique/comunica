<?php

declare(strict_types=1);

namespace App\Livewire\Communication;

use App\Actions\PublishReleaseAction;
use App\Models\Release;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Administrar novidades')]
class Releases extends Component
{
    use WithPagination;
    public bool $publishOpen = false;
    #[Locked]
    public ?int $selectedId = null;
    #[Locked]
    public string $feedback = '';
    public function boot(): void
    {
        $this->authorize('viewAny', Release::class);
    }
    public function mount(): void
    {
        $this->feedback = (string) session()->pull('status', '');
    }

    public function askPublish(int $id): void
    {
        $release = Release::findOrFail($id);
        $this->authorize('publish', $release);
        $this->selectedId = $id;
        $this->resetErrorBag();
        $this->publishOpen = true;
    }

    public function publishSelected(PublishReleaseAction $publish): void
    {
        abort_if($this->selectedId === null, 404);
        try {
            $publish->execute($this->selectedId);
            $this->feedback = 'Nota publicada. Os usuários verão o resumo no próximo login.';
            $this->publishOpen = false;
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->errors());
        } catch (QueryException) {
            $this->addError('publish', 'Não foi possível publicar. Tente novamente.');
        }
    }

    public function render(): View
    {
        return view('livewire.communication.releases', [
            'releases' => Release::orderByDesc('id')->paginate(15),
            'selected' => $this->selectedId ? Release::find($this->selectedId) : null,
        ]);
    }
}
