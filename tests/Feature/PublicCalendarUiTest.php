<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\GetPublicCalendarEventsAction;
use App\Livewire\Public\CalendarView;
use App\Models\EventRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicCalendarUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_month_and_day_include_only_confirmed_public_fields(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'approved')->firstOrFail();
        $this->get('/calendario')->assertOk()->assertSee('Calendário institucional');
        Livewire::test(CalendarView::class)->set('year', $event->start_at->year)->set('month', $event->start_at->month)
            ->call('selectDay', $event->start_at->format('Y-m-d'))->assertSet('dayOpen', true)
            ->assertSee($event->title)->assertSee($event->location)->assertSee($event->target_audience);
        $events = app(GetPublicCalendarEventsAction::class)->execute($event->start_at->year, $event->start_at->month);
        $this->assertCount(4, $events);
        foreach ($events as $publicEvent) {
            foreach (['description','user_id','status','moderated_by','moderation_notes','moderated_at'] as $privateField) {
                $this->assertArrayNotHasKey($privateField, $publicEvent->getAttributes());
            }
        }
    }

    public function test_calendar_does_not_render_unconfirmed_titles_or_private_briefings(): void
    {
        $this->seed(DatabaseSeeder::class);
        $event = EventRequest::where('status', 'pending')->firstOrFail();
        Livewire::test(CalendarView::class)->set('year', $event->start_at->year)->set('month', $event->start_at->month)
            ->assertDontSee($event->title)->assertDontSee('Dados fictícios para validação do ambiente local.');
    }

    public function test_navigation_handles_year_boundary_and_empty_day(): void
    {
        Livewire::test(CalendarView::class)->set('year', 2027)->set('month', 1)->call('previousMonth')
            ->assertSet('year', 2026)->assertSet('month', 12)->call('nextMonth')
            ->assertSet('year', 2027)->assertSet('month', 1)->call('selectDay', '2027-01-10')
            ->assertSee('Nenhum evento confirmado neste dia');
    }

    public function test_invalid_month_url_returns_app_owned_not_found(): void
    {
        $this->get('/calendario?year=2027&month=13')->assertNotFound();
        $this->get('/calendario?year=2027&month=invalid')->assertNotFound();
        $this->get('/calendario?year[]=2027&month=1')->assertNotFound();
    }

    public function test_selected_day_can_be_cleared_and_out_of_month_values_are_rejected(): void
    {
        Livewire::test(CalendarView::class)->set('year', 2027)->set('month', 1)
            ->call('selectDay', '2027-02-01')->assertHasErrors('selectedDay')
            ->call('selectDay', '2027-01-10')->assertSet('dayOpen', true)
            ->call('selectDay', '')->assertSet('dayOpen', false)->assertSet('selectedDay', '')->assertHasNoErrors();
    }
}
