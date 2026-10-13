<?php

declare(strict_types=1);

namespace Tests\Feature;

use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_create_the_planned_fixture_with_consistent_dates(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('event_requests', 10);
        $this->assertSame(1, DB::table('users')->where('role', 'communication_admin')->count());
        $this->assertSame(2, DB::table('users')->where('role', 'coordinator')->count());

        foreach (['pending', 'approved', 'rescheduled', 'rejected', 'cancelled'] as $status) {
            $this->assertSame(2, DB::table('event_requests')->where('status', $status)->count());
        }

        foreach (DB::table('users')->get() as $user) {
            $this->assertNotEmpty($user->department);
            $this->assertFalse(Hash::needsRehash($user->password));
        }

        foreach (DB::table('event_requests')->get() as $event) {
            $expectedEnd = CarbonImmutable::parse($event->start_at)->addMinutes($event->duration_minutes);
            $this->assertSame($expectedEnd->toDateTimeString(), $event->end_at);
            $moderated = in_array($event->status, ['approved', 'rescheduled', 'rejected'], true);
            $this->assertSame($moderated, $event->moderated_by !== null);
            $this->assertSame($moderated, $event->moderated_at !== null);
            $this->assertSame($event->status === 'rescheduled', $event->original_start_at !== null);

            if (in_array($event->status, ['rescheduled', 'rejected'], true)) {
                $this->assertGreaterThanOrEqual(10, mb_strlen($event->moderation_notes));
            }
        }
    }

    public function test_reseeding_preserves_existing_decisions_and_soft_deleted_fixtures(): void
    {
        $this->seed(DatabaseSeeder::class);
        $id = DB::table('event_requests')->where('status', 'pending')->value('id');
        DB::table('event_requests')->where('id', $id)->update([
            'status' => 'cancelled',
            'description' => 'Alteração posterior preservada',
            'deleted_at' => now(),
        ]);
        DB::table('users')->where('email', 'educacao@example.test')->update(['department' => 'Setor atualizado']);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('event_requests', 10);
        $this->assertDatabaseHas('event_requests', [
            'id' => $id, 'status' => 'cancelled', 'description' => 'Alteração posterior preservada',
        ]);
        $this->assertNotNull(DB::table('event_requests')->where('id', $id)->value('deleted_at'));
        $this->assertDatabaseHas('users', ['email' => 'educacao@example.test', 'department' => 'Setor atualizado']);
    }

    public function test_deleting_a_moderator_preserves_requests_and_clears_the_reference(): void
    {
        $this->seed(DatabaseSeeder::class);
        DB::table('users')->where('role', 'communication_admin')->delete();

        $this->assertDatabaseCount('event_requests', 10);
        $this->assertSame(0, DB::table('event_requests')->whereNotNull('moderated_by')->count());
    }

    public function test_deleting_a_request_owner_is_restricted(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->expectException(QueryException::class);

        DB::table('users')->where('email', 'educacao@example.test')->delete();
    }
}
