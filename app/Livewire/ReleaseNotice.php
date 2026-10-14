<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\AcknowledgeReleaseAction;
use App\Actions\ShouldShowReleaseNoticeAction;
use App\Models\User;
use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReleaseNotice extends Component
{
    public bool $noticeOpen = false;

    #[Locked]
    public string $version = '';

    public function boot(): void
    {
        $this->authorize('acknowledgeRelease', User::class);
    }

    public function mount(ShouldShowReleaseNoticeAction $shouldShow): void
    {
        $this->version = AppVersion::current();
        $this->noticeOpen = $shouldShow->execute();
    }

    public function updatedNoticeOpen(bool $value): void
    {
        if (!$value) {
            $this->dismiss();
        }
    }

    public function dismiss(): void
    {
        try {
            app(AcknowledgeReleaseAction::class)->execute($this->version);
            session()->forget('release-notice-login');
            $this->noticeOpen = false;
        } catch (QueryException) {
            $this->noticeOpen = true;
            $this->addError('notice', 'Não foi possível registrar a leitura. Tente novamente.');
        }
    }

    public function render(): View
    {
        return view('livewire.release-notice', [
            'release' => Release::where('version', $this->version)->where('state', 'published')->whereNotNull('published_at')->first(),
        ]);
    }
}
