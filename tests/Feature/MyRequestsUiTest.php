<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\CancelEventRequestAction;
use App\Livewire\Coordinator\MyRequests;
use App\Models\EventRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyRequestsUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_filter_and_cancel_confirmation(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $this->actingAs($event->user)->get('/solicitacoes')->assertOk()->assertSee('Minhas solicitações');
        Livewire::test(MyRequests::class)->set('status', 'pending')->assertSee($event->title)
            ->call('askCancel', $event->id)->assertSet('cancelOpen', true)->call('cancel')
            ->assertSet('cancelOpen', false)->assertDontSee($event->title);
        $this->assertSame('cancelled', $event->fresh()->status);
        $this->assertNull($event->fresh()->deleted_at);
    }

    public function test_decision_taken_after_confirmation_is_not_overwritten_by_cancellation(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $this->actingAs($event->user);
        $component = Livewire::test(MyRequests::class)->call('askCancel', $event->id);
        $event->status = 'approved';
        $event->save();
        $component->call('cancel')->assertHasErrors('cancel')->assertSet('cancelOpen', true);
        $this->assertSame('approved', $event->fresh()->status);
    }

    public function test_forged_detail_id_is_denied(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::firstOrFail();
        $other = User::where('role', 'coordinator')->whereKeyNot($event->user_id)->firstOrFail();
        $this->actingAs($other);
        Livewire::test(MyRequests::class)->call('openDetails', $event->id)->assertForbidden();
    }

    public function test_direct_cancellation_of_other_users_request_is_denied(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $this->actingAs(User::where('role', 'coordinator')->whereKeyNot($event->user_id)->firstOrFail());
        $this->expectException(AuthorizationException::class);
        app(CancelEventRequestAction::class)->execute($event->id);
    }
}
