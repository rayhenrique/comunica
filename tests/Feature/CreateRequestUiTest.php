<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\CreateEventRequestAction;
use App\Livewire\Coordinator\CreateRequest;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CreateRequestUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_creates_request_with_current_owner_and_calculated_end(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/solicitacoes/nova')->assertOk()->assertSee('Nova solicitação');
        $start = now()->addDays(3)->startOfMinute();
        Livewire::test(CreateRequest::class)->set('form.title', 'Pauta criada na interface')
            ->set('form.start_at', $start->format('Y-m-d\TH:i'))->set('form.duration_minutes', 90)
            ->set('form.location', 'Auditório')->set('form.target_audience', 'Comunidade')
            ->call('save')->assertHasNoErrors()->assertRedirect('/solicitacoes');
        $event = EventRequest::firstOrFail();
        $this->assertSame($user->id, $event->user_id);
        $this->assertSame('pending', $event->status);
        $this->assertTrue($event->end_at->equalTo($start->addMinutes(90)));
    }

    public function test_briefing_that_exceeds_mysql_text_capacity_is_rejected_without_saving(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(CreateRequest::class)->set('form.description', str_repeat('ç', 32768))
            ->call('save')->assertHasErrors('form.description');
        $this->assertDatabaseCount('event_requests', 0);
    }

    public function test_invalid_form_preserves_fields_and_does_not_save(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(CreateRequest::class)->set('form.title', 'Dados preservados')->call('save')
            ->assertHasErrors('form.start_at')->assertSet('form.title', 'Dados preservados');
        $this->assertDatabaseCount('event_requests', 0);
    }

    public function test_repeated_submission_token_does_not_duplicate_and_ignores_owner_payload(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user);
        $data = ['title' => 'Pauta única', 'demand_type' => 'art', 'start_at' => now()->addDays(3)->toDateTimeString(),
            'duration_minutes' => 60, 'location' => 'Auditório', 'target_audience' => 'Comunidade',
            'user_id' => $other->id, 'status' => 'approved', 'end_at' => '2027-12-01'];
        $token = (string) Str::uuid();
        $one = app(CreateEventRequestAction::class)->execute($data, $token);
        $two = app(CreateEventRequestAction::class)->execute($data, $token);
        $this->assertSame($one->id, $two->id);
        $this->assertSame($user->id, $one->user_id);
        $this->assertSame('pending', $one->status);
        $this->assertDatabaseCount('event_requests', 1);
    }
}
