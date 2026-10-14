<?php

declare(strict_types=1);

use App\Actions\SaveReleaseAction;
use App\Actions\PublishReleaseAction;
use App\Actions\AcknowledgeReleaseAction;
use App\Actions\ShouldShowReleaseNoticeAction;
use App\Livewire\ReleaseNotice;
use App\Models\User;
use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

function releaseData(string $version = '1.3.0'): array
{
    return ['version' => $version, 'title' => 'Novidades', 'summary' => 'Ajuda e versões', 'content' => '<script>alert(1)</script>'];
}

beforeEach(function (): void {
    $this->admin = User::factory()->create(['role' => 'communication_admin']);
    $this->coordinator = User::factory()->create();
});

it('centraliza a versão SemVer', function (): void {
    expect(AppVersion::current())->toBe(config('version.current'));
    config(['version.current' => '01.3.0']);
    expect(fn () => AppVersion::current())->toThrow(LogicException::class);
});

it('protege administração para visitantes e coordenadores', function (): void {
    $this->get(route('releases.index'))->assertRedirect(route('login'));
    $this->actingAs($this->coordinator)->get(route('releases.index'))->assertForbidden();
    expect(fn () => app(SaveReleaseAction::class)->execute(releaseData()))->toThrow(AuthorizationException::class);
    $this->actingAs($this->admin)->get(route('releases.create'))->assertOk();
});

it('salva rascunho e histórico sem alterar versão do código', function (): void {
    $this->actingAs($this->admin);
    $release = app(SaveReleaseAction::class)->execute(releaseData());
    expect($release->state)->toBe('draft')->and($release->published_at)->toBeNull();
    app(SaveReleaseAction::class)->execute(array_replace(releaseData(), ['title' => 'Revisada']), $release->id);
    expect($release->revisions()->count())->toBe(2);
    expect($release->revisions()->first()->snapshot['title'])->toBe('Revisada');
    expect($release->revisions()->reorder('id')->first()->snapshot['title'])->toBe('Novidades');
    expect(AppVersion::current())->toBe('1.3.0');
    $this->get(route('releases.history', $release))->assertOk()->assertSee('Revisada');
});

it('valida SemVer e unicidade', function (string $version): void {
    $this->actingAs($this->admin);
    expect(fn () => app(SaveReleaseAction::class)->execute(releaseData($version)))->toThrow(ValidationException::class);
})->with(['1.0', '01.3.0', '1.3.0-beta', 'texto']);

it('rejeita duplicidade e publicação de versão diferente da implantada', function (): void {
    $this->actingAs($this->admin);
    $release = app(SaveReleaseAction::class)->execute(releaseData('1.4.0'));
    expect(fn () => app(SaveReleaseAction::class)->execute(releaseData('1.4.0')))->toThrow(ValidationException::class);
    expect(fn () => app(PublishReleaseAction::class)->execute($release->id))->toThrow(ValidationException::class);
    expect($release->fresh()->state)->toBe('draft');
});

it('publica de forma idempotente e escapa conteúdo público', function (): void {
    $this->actingAs($this->admin);
    $release = app(SaveReleaseAction::class)->execute(releaseData());
    $this->get(route('news'))->assertDontSee('alert(1)');
    app(PublishReleaseAction::class)->execute($release->id);
    app(PublishReleaseAction::class)->execute($release->id);
    expect($release->revisions()->count())->toBe(2);
    $this->get(route('news'))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    expect(fn () => app(SaveReleaseAction::class)->execute(releaseData('1.2.0'), $release->id))->toThrow(ValidationException::class);
    $this->actingAs($this->coordinator);
    expect(fn () => app(PublishReleaseAction::class)->execute($release->id))->toThrow(AuthorizationException::class);
    $this->get(route('releases.history', $release))->assertForbidden();
});

it('mostra novidades uma vez por usuário e versão após login', function (): void {
    $this->actingAs($this->admin);
    $release = app(SaveReleaseAction::class)->execute(releaseData());
    app(PublishReleaseAction::class)->execute($release->id);
    $this->actingAs($this->coordinator);
    expect(app(ShouldShowReleaseNoticeAction::class)->execute())->toBeFalse();
    session()->put('release-notice-login', true);
    Livewire::test(ReleaseNotice::class)->assertSet('noticeOpen', true)->call('dismiss')->assertSet('noticeOpen', false);
    session()->put('release-notice-login', true);
    expect(app(ShouldShowReleaseNoticeAction::class)->execute())->toBeFalse();
    app(AcknowledgeReleaseAction::class)->execute('1.3.0');
    expect(DB::table('user_release_views')->count())->toBe(1);
    $this->actingAs($this->admin);
    expect(app(ShouldShowReleaseNoticeAction::class)->execute())->toBeTrue();
    Livewire::test(ReleaseNotice::class)->set('noticeOpen', false)->assertSet('noticeOpen', false);
    expect(DB::table('user_release_views')->count())->toBe(2);
    config(['version.current' => '1.4.0']);
    $next = app(SaveReleaseAction::class)->execute(releaseData('1.4.0'));
    app(PublishReleaseAction::class)->execute($next->id);
    session()->put('release-notice-login', true);
    expect(app(ShouldShowReleaseNoticeAction::class)->execute())->toBeTrue();
});

it('não exibe modal sem publicação e não registra rascunho', function (): void {
    $this->actingAs($this->admin);
    app(SaveReleaseAction::class)->execute(releaseData());
    session()->put('release-notice-login', true);
    Livewire::test(ReleaseNotice::class)->assertSet('noticeOpen', false);
    expect(fn () => app(AcknowledgeReleaseAction::class)->execute('1.3.0'))->toThrow(ValidationException::class);
    $this->get(route('help'))->assertOk()->assertSee('Ajuda');
});


it('mantém a transação íntegra se o histórico falhar', function (): void {
    $this->actingAs($this->admin);
    App\Models\ReleaseRevision::creating(fn () => throw new RuntimeException('Auditoria indisponível'));
    try {
        expect(fn () => app(SaveReleaseAction::class)->execute(releaseData()))->toThrow(RuntimeException::class);
        expect(Release::count())->toBe(0);
    } finally {
        App\Models\ReleaseRevision::flushEventListeners();
    }
});

it('executa criação edição e publicação pelos componentes', function (): void {
    $this->actingAs($this->admin);
    Livewire::test(App\Livewire\Communication\ReleaseEditor::class)
        ->set('title', 'Entrega')->set('summary', 'Resumo')->set('content', 'Conteúdo')
        ->call('save')->assertHasNoErrors()->assertRedirect(route('releases.index'));
    $release = Release::firstOrFail();
    Livewire::test(App\Livewire\Communication\ReleaseEditor::class, ['id' => $release->id])
        ->assertSet('title', 'Entrega')->set('title', 'Atualizada')->call('save')->assertHasNoErrors();
    Livewire::test(App\Livewire\Communication\Releases::class)
        ->call('askPublish', $release->id)->assertSet('publishOpen', true)
        ->call('publishSelected')->assertHasNoErrors()->assertSet('publishOpen', false);
    expect($release->fresh()->title)->toBe('Atualizada')->and($release->fresh()->state)->toBe('published');
});
