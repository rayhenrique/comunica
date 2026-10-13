<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EventRequest;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class EventRequestModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_dates_relationships_and_time_formatting_are_available(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'rescheduled')->firstOrFail();

        foreach (['start_at', 'end_at', 'original_start_at', 'moderated_at'] as $field) {
            $this->assertInstanceOf(Carbon::class, $event->{$field});
        }

        $this->assertTrue($event->user->isCoordinator());
        $this->assertTrue($event->moderator->isCommunicationAdmin());
        $this->assertSame('09:00', $event->formattedStartTime());
        $this->assertSame('10:30', $event->formattedEndTime());
        $this->assertIsInt($event->duration_minutes);

        $pending = EventRequest::where('status', 'pending')->firstOrFail();
        $this->assertNull($pending->original_start_at);
        $this->assertNull($pending->moderated_at);
        $this->assertNull($pending->moderator);
        $this->assertNull((new EventRequest())->formattedStartTime());
        $this->assertNull((new EventRequest())->formattedEndTime());
    }

    public function test_mass_assignment_does_not_change_owner_decisions_or_calculated_fields(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        $before = $event->getAttributes();
        $event->fill([
            'title' => 'Título atualizado', 'user_id' => 999, 'status' => 'approved',
            'moderated_by' => 999, 'moderation_notes' => 'Parecer indevido',
            'moderated_at' => now(), 'original_start_at' => now(), 'end_at' => now(),
            'deleted_at' => now(), 'id' => 999,
        ]);

        $this->assertSame('Título atualizado', $event->title);
        foreach (['id', 'user_id', 'status', 'moderated_by', 'moderation_notes', 'moderated_at',
            'original_start_at', 'end_at', 'deleted_at'] as $field) {
            $this->assertSame($before[$field], $event->getAttributes()[$field]);
        }
    }

    public function test_confirmed_scope_and_relationship_exclude_soft_deleted_events(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(4, EventRequest::confirmed()->count());
        $this->assertSame(['approved', 'rescheduled'], EventRequest::confirmed()
            ->distinct()->orderBy('status')->pluck('status')->all());
        $event = EventRequest::where('status', 'approved')->firstOrFail();
        $owner = $event->user;
        $id = $event->id;
        $event->delete();

        $this->assertSame(3, EventRequest::confirmed()->count());
        $this->assertNull(EventRequest::find($id));
        $this->assertNotNull(EventRequest::withTrashed()->find($id));
        $this->assertSame(4, $owner->eventRequests()->count());
        $this->assertDatabaseCount('event_requests', 10);

        EventRequest::withTrashed()->findOrFail($id)->restore();
        $this->assertSame(4, EventRequest::confirmed()->count());
    }

    public function test_month_scope_handles_intersections_and_year_boundaries(): void
    {
        $this->seed(DatabaseSeeder::class);
        $userId = DB::table('users')->where('role', 'coordinator')->value('id');
        $intervals = [
            'ending_at_month_start' => ['2026-12-31 23:00:00', '2027-01-01 00:00:00'],
            'crossing_month_start' => ['2026-12-31 23:30:00', '2027-01-01 00:30:00'],
            'starting_at_month_start' => ['2027-01-01 00:00:00', '2027-01-01 01:00:00'],
            'inside_month' => ['2027-01-15 09:00:00', '2027-01-15 10:00:00'],
            'crossing_month_end' => ['2027-01-31 23:30:00', '2027-02-01 00:30:00'],
            'starting_next_month' => ['2027-02-01 00:00:00', '2027-02-01 01:00:00'],
        ];

        foreach ($intervals as $title => [$start, $end]) {
            DB::table('event_requests')->insert([
                'user_id' => $userId, 'title' => $title, 'demand_type' => 'coverage',
                'location' => 'Auditório', 'target_audience' => 'Comunidade',
                'start_at' => $start, 'end_at' => $end, 'duration_minutes' => 60,
                'status' => $title === 'inside_month' ? 'pending' : 'approved',
            ]);
        }

        $fixtures = array_keys($intervals);
        $query = EventRequest::whereIn('title', $fixtures)->inMonth(2027, 1);
        $this->assertSame([
            'crossing_month_end', 'crossing_month_start', 'inside_month', 'starting_at_month_start',
        ], (clone $query)->orderBy('title')->pluck('title')->all());
        $this->assertSame(3, (clone $query)->confirmed()->count());
        $this->assertSame(2, EventRequest::whereIn('title', $fixtures)->inMonth(2026, 12)->count());
    }

    public function test_month_scope_rejects_invalid_months(): void
    {
        $this->expectException(InvalidArgumentException::class);
        EventRequest::inMonth(2027, 13)->get();
    }
}
