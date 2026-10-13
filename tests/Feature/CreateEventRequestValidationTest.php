<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\CalculateEventEndAction;
use App\Http\Requests\CreateEventRequestRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateEventRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(CarbonImmutable::parse('2027-01-08 09:00:00', config('app.timezone')));
        // Endpoint restrito ao teste: resolve o FormRequest sem criar funcionalidade de escrita.
        Route::middleware(['web', 'auth'])->post(
            '/_test/event-validation',
            fn (CreateEventRequestRequest $request): JsonResponse => response()->json($request->validated())
        );
    }

    public function test_exact_48_hours_are_accepted_across_a_weekend_and_earlier_time_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/_test/event-validation', $this->payload())->assertOk();
        $this->postJson('/_test/event-validation', $this->payload(['start_at' => '2027-01-10T08:59:59']))
            ->assertUnprocessable()->assertJsonValidationErrors('start_at')
            ->assertJsonPath('errors.start_at.0', 'A solicitação deve ter antecedência mínima de 48 horas corridas.');
    }

    public function test_only_coordinators_can_submit_and_protected_payload_fields_are_ignored(): void
    {
        $this->postJson('/_test/event-validation', $this->payload())->assertUnauthorized();
        $this->actingAs(User::factory()->create(['role' => 'communication_admin']))
            ->postJson('/_test/event-validation', $this->payload())->assertForbidden();
        $response = $this->actingAs(User::factory()->create())->postJson('/_test/event-validation', $this->payload([
            'user_id' => 999, 'status' => 'approved', 'end_at' => '2027-12-01', 'moderated_by' => 999,
        ]))->assertOk();
        foreach (['user_id', 'status', 'end_at', 'moderated_by'] as $field) {
            $this->assertArrayNotHasKey($field, $response->json());
        }
    }

    public function test_invalid_dates_types_lengths_and_duration_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/_test/event-validation', $this->payload([
            'title' => str_repeat('a', 151), 'demand_type' => 'invalid', 'start_at' => '2027-02-30',
            'duration_minutes' => 0, 'location' => str_repeat('a', 256), 'target_audience' => '',
        ]))->assertUnprocessable()->assertJsonValidationErrors([
            'title', 'demand_type', 'start_at', 'duration_minutes', 'location', 'target_audience',
        ]);
        $this->postJson('/_test/event-validation', $this->payload(['duration_minutes' => 65536]))
            ->assertUnprocessable()->assertJsonValidationErrors('duration_minutes');
    }

    public function test_lead_time_compares_absolute_instants_with_timezone_offsets(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('/_test/event-validation', $this->payload(['start_at' => '2027-01-10T12:00:00+00:00']))
            ->assertOk();
        $this->postJson('/_test/event-validation', $this->payload(['start_at' => '2027-01-10T11:59:59+00:00']))
            ->assertUnprocessable()->assertJsonValidationErrors('start_at');
    }

    public function test_end_calculation_crosses_midnight_without_mutating_start(): void
    {
        $start = CarbonImmutable::parse('2027-01-10 23:30:00', config('app.timezone'));
        $end = app(CalculateEventEndAction::class)->execute($start, 90);
        $this->assertSame('2027-01-11 01:00:00', $end->toDateTimeString());
        $this->assertSame('2027-01-10 23:30:00', $start->toDateTimeString());
        $this->expectException(ValidationException::class);
        app(CalculateEventEndAction::class)->execute($start, -1);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Evento institucional', 'demand_type' => 'coverage',
            'start_at' => '2027-01-10T09:00:00', 'duration_minutes' => 60,
            'location' => 'Auditório', 'target_audience' => 'Comunidade', 'description' => null,
            ...$overrides,
        ];
    }
}
