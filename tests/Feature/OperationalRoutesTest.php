<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ListVisibleEventRequestsAction;
use App\Models\EventRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_blocked_on_operational_routes_and_calendar_is_public(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::firstOrFail();

        foreach (['/solicitacoes', '/solicitacoes/'.$event->id, '/comunicacao/triagem'] as $uri) {
            $this->getJson($uri)->assertUnauthorized();
            $this->get($uri)->assertRedirect('/login');
        }

        $this->get('/calendario')->assertOk()->assertSee('Calendário institucional');
    }

    public function test_coordinator_list_ignores_submitted_owner_ids_and_returns_only_owned_requests(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::where('email', 'educacao@example.test')->firstOrFail();
        $other = User::where('email', 'saude@example.test')->firstOrFail();

        $response = $this->actingAs($owner)->getJson('/solicitacoes?user_id='.$other->id);
        $response->assertOk()->assertJsonPath('total', 5)->assertJsonCount(5, 'data');
        $this->assertSame([$owner->id], array_values(array_unique(array_column($response->json('data'), 'user_id'))));
        $this->assertSame(15, $response->json('per_page'));
    }

    public function test_owner_can_view_details_but_other_coordinator_gets_forbidden(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::firstOrFail();
        $other = User::where('role', 'coordinator')->whereKeyNot($event->user_id)->firstOrFail();

        $this->actingAs($event->user)->getJson('/solicitacoes/'.$event->id)
            ->assertOk()->assertJsonPath('id', $event->id);
        $this->actingAs($other)->getJson('/solicitacoes/'.$event->id)->assertForbidden();
        $this->getJson('/comunicacao/triagem')->assertForbidden();
    }

    public function test_communication_can_list_all_and_access_other_owners_details_and_triage(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('role', 'communication_admin')->firstOrFail();
        $event = EventRequest::firstOrFail();

        $this->actingAs($admin)->getJson('/solicitacoes')->assertOk()->assertJsonPath('total', 10);
        $this->getJson('/comunicacao/triagem')->assertOk()->assertJsonPath('total', 10);
        $this->getJson('/solicitacoes/'.$event->id)->assertOk()->assertJsonPath('id', $event->id);
    }

    public function test_deleted_and_missing_records_are_not_exposed(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::firstOrFail();
        $owner = $event->user;
        $event->delete();

        $this->actingAs($owner)->getJson('/solicitacoes/'.$event->id)->assertNotFound();
        $this->getJson('/solicitacoes/999999')->assertNotFound();
        $this->getJson('/solicitacoes')->assertOk()->assertJsonPath('total', 4);
    }

    public function test_unknown_profile_cannot_list_even_if_authenticated(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('role', 'coordinator')->firstOrFail();
        $user->role = 'unknown';

        $this->actingAs($user)->getJson('/solicitacoes')->assertForbidden();
        $this->getJson('/comunicacao/triagem')->assertForbidden();
    }

    public function test_listing_action_checks_authorization_when_called_directly(): void
    {
        $user = new User();
        $user->role = 'unknown';
        $this->expectException(AuthorizationException::class);

        app(ListVisibleEventRequestsAction::class)->execute($user);
    }
}
