<?php

declare(strict_types=1);

namespace App\Livewire\Communication;

use App\Actions\SaveReleaseAction;
use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Editar nota de versão')]
class ReleaseEditor extends Component
{
    #[Locked]
    public ?int $releaseId = null;
    public string $version = '';
    public string $title = '';
    public string $summary = '';
    public string $content = '';

    public function boot(): void
    {
        $this->authorize('create', Release::class);
    }

    public function mount(?int $id = null): void
    {
        $this->version = AppVersion::current();
        if ($id !== null) {
            $release = Release::findOrFail($id);
            $this->authorize('update', $release);
            $this->releaseId = $id;
            foreach (['version', 'title', 'summary', 'content'] as $field) {
                $this->{$field} = $release->{$field};
            }
        }
    }

    public function save(SaveReleaseAction $save): void
    {
        $this->resetErrorBag();
        try {
            $save->execute($this->only(['version', 'title', 'summary', 'content']), $this->releaseId);
            session()->flash('status', 'Nota salva. A publicação é uma ação separada.');
            $this->redirect(route('releases.index'));
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->errors());
        } catch (QueryException) {
            $this->addError('save', 'Não foi possível salvar. Confira se a versão já existe e tente novamente.');
        }
    }

    public function render(): View
    {
        return view('livewire.communication.release-editor');
    }
}
