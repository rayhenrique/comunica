<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Communication\Triage;
use App\Models\EventRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TriageUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_communication_approves_and_queue_refreshes(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('role', 'communication_admin')->firstOrFail());
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $this->get('/comunicacao/triagem')->assertOk()->assertSee('Triagem de pautas');
        Livewire::test(Triage::class)->call('openReview', $event->id)->assertSet('reviewOpen', true)
            ->call('moderate')->assertHasNoErrors()->assertSet('reviewOpen', false)->assertDontSee($event->title);
        $this->assertSame('approved', $event->fresh()->status);
    }

    public function test_rejection_validation_keeps_dialog_and_entered_reason(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('role', 'communication_admin')->firstOrFail());
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        Livewire::test(Triage::class)->call('openReview', $event->id)->set('decision', 'reject')
            ->set('moderation_notes', 'curta')->call('moderate')->assertHasErrors('moderation_notes')
            ->assertSet('reviewOpen', true)->assertSet('moderation_notes', 'curta')
            ->set('moderation_notes', 'Equipe indisponível neste período.')->call('moderate')->assertHasNoErrors();
        $this->assertSame('rejected', $event->fresh()->status);
    }

    public function test_coordinators_cannot_mount_admin_component_directly(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(Triage::class)->assertForbidden();
    }

    public function test_invalid_date_range_shows_recoverable_error_instead_of_a_server_failure(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('role', 'communication_admin')->firstOrFail());
        Livewire::test(Triage::class)->set('from', '2027-01-20')->set('to', '2027-01-01')
            ->assertHasErrors('to')->call('clearFilters')->assertHasNoErrors();
    }

    public function test_department_type_and_date_filters_apply_on_server(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::where('role', 'communication_admin')->firstOrFail());
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        Livewire::test(Triage::class)->set('department', $event->user->department)
            ->set('demand_type', $event->demand_type)->set('from', $event->start_at->format('Y-m-d'))
            ->set('to', $event->start_at->format('Y-m-d'))->assertSee($event->title)
            ->set('department', 'Setor inexistente')->assertSee('Nenhuma pauta neste filtro');
    }
}
