<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_helpers_and_request_relationship_match_seeded_users(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'comunicacao@example.test')->firstOrFail();
        $coordinator = User::where('email', 'educacao@example.test')->firstOrFail();

        $this->assertTrue($admin->isCommunicationAdmin());
        $this->assertFalse($admin->isCoordinator());
        $this->assertTrue($coordinator->isCoordinator());
        $this->assertFalse($coordinator->isCommunicationAdmin());
        $this->assertSame(0, $admin->eventRequests()->count());
        $this->assertSame(5, $coordinator->eventRequests()->count());
        $this->assertTrue($coordinator->eventRequests->every(fn ($event): bool => $event->user_id === $coordinator->id));
    }

    public function test_user_assignment_protects_role_and_hides_credentials(): void
    {
        $user = User::factory()->create();
        $user->fill(['name' => 'Nome atualizado', 'role' => 'communication_admin', 'id' => 999]);

        $this->assertSame('Nome atualizado', $user->name);
        $this->assertTrue($user->isCoordinator());
        $this->assertNotSame(999, $user->id);
        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->assertArrayNotHasKey('remember_token', $user->toArray());
    }

    public function test_password_cast_hashes_new_passwords_without_hashing_existing_hashes_again(): void
    {
        $user = User::factory()->create();
        $password = Str::random(32);
        $user->password = $password;
        $user->save();

        $this->assertTrue(Hash::check($password, $user->fresh()->password));
        $hash = $user->password;
        $user->password = $hash;
        $this->assertTrue($hash === $user->password);
    }
}
