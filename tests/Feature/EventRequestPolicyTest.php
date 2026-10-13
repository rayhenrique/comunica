<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EventRequest;
use App\Models\User;
use App\Policies\EventRequestPolicy;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class EventRequestPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_registration_and_profile_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('role', 'communication_admin')->firstOrFail();
        $coordinator = User::where('role', 'coordinator')->firstOrFail();

        $this->assertInstanceOf(EventRequestPolicy::class, Gate::getPolicyFor(EventRequest::class));
        foreach ([$admin, $coordinator] as $user) {
            $this->assertTrue(Gate::forUser($user)->allows('viewAny', EventRequest::class));
        }
        $this->assertTrue(Gate::forUser($coordinator)->allows('create', EventRequest::class));
        $this->assertFalse(Gate::forUser($admin)->allows('create', EventRequest::class));
        $this->assertTrue(Gate::forUser($admin)->allows('moderate', EventRequest::class));
        $this->assertFalse(Gate::forUser($coordinator)->allows('moderate', EventRequest::class));
        $this->assertFalse(Gate::forUser($coordinator)->allows('update', EventRequest::firstOrFail()));
        $this->assertFalse(Gate::forUser($admin)->allows('delete', EventRequest::firstOrFail()));
    }

    public function test_owners_and_communication_can_view_but_other_coordinators_cannot(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $owner = $event->user;
        $other = User::where('role', 'coordinator')->whereKeyNot($owner->id)->firstOrFail();
        $admin = User::where('role', 'communication_admin')->firstOrFail();

        $this->assertTrue(Gate::forUser($owner)->allows('view', $event));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $event));
        $this->assertFalse(Gate::forUser($other)->allows('view', $event));
        $this->assertFalse(Gate::forUser($other)->allows('cancel', $event));
        $this->assertFalse(Gate::forUser($admin)->allows('cancel', $event));
        $this->assertTrue(Gate::forUser($owner)->allows('cancel', $event));
    }

    public function test_cancellation_requires_pending_status_and_a_live_record(): void
    {
        $this->seed(DatabaseSeeder::class);
        foreach (EventRequest::all() as $event) {
            $this->assertSame($event->status === 'pending', Gate::forUser($event->user)->allows('cancel', $event));
        }
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $owner = $event->user;
        $event->delete();
        $this->assertFalse(Gate::forUser($owner)->allows('cancel', $event));
        $this->assertFalse(Gate::forUser($owner)->allows('view', $event));
    }

    public function test_guests_and_unknown_roles_receive_no_operational_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::firstOrFail();
        $unknown = $event->user;
        $unknown->role = 'unknown';

        foreach ([null, $unknown] as $user) {
            foreach (['viewAny', 'create', 'moderate'] as $ability) {
                $this->assertFalse(Gate::forUser($user)->allows($ability, EventRequest::class));
            }
            foreach (['view', 'cancel'] as $ability) {
                $this->assertFalse(Gate::forUser($user)->allows($ability, $event));
            }
        }
    }
}
