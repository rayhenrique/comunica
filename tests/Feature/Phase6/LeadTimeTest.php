<?php

declare(strict_types=1);

use App\Actions\CreateEventRequestAction;
use App\Livewire\Coordinator\CreateRequest;
use App\Models\EventRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\Support\Phase6Fixtures;

beforeEach(function (): void {
    $this->travelTo(CarbonImmutable::parse('2027-01-08 09:00:00', config('app.timezone')));
    $this->actingAs(User::factory()->create());
});

it('rejects insufficient lead time in the persistence action without saving', function (string $start): void {
    try {
        app(CreateEventRequestAction::class)->execute(Phase6Fixtures::submission($start), (string) Str::uuid());
        $this->fail('Uma solicitação com menos de 48 horas não pode ser gravada.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('start_at');
        expect($exception->errors()['start_at'][0])->toBe('A solicitação deve ter antecedência mínima de 48 horas corridas.');
    }
    $this->assertDatabaseCount('event_requests', 0);
})->with([
    'past' => ['2027-01-08T08:00:00'],
    'one minute before' => ['2027-01-10T08:59:00'],
    'one second before' => ['2027-01-10T08:59:59'],
    'equivalent UTC offset' => ['2027-01-10T11:59:59+00:00'],
]);

it('accepts exactly 48 elapsed hours across a weekend and computes the end', function (): void {
    $event = app(CreateEventRequestAction::class)->execute(
        Phase6Fixtures::submission('2027-01-10T09:00:00'),
        (string) Str::uuid(),
    );
    expect($event->status)->toBe('pending');
    expect($event->start_at->toDateTimeString())->toBe('2027-01-10 09:00:00');
    expect($event->end_at->toDateTimeString())->toBe('2027-01-10 10:00:00');
    $this->assertDatabaseCount('event_requests', 1);
});

it('shows lead time errors in the real form and preserves entered data', function (): void {
    $component = Livewire::test(CreateRequest::class);
    foreach (Phase6Fixtures::submission('2027-01-10T08:59:00') as $field => $value) {
        $component->set('form.'.$field, $value);
    }
    $component->call('save')->assertHasErrors('form.start_at')
        ->assertSet('form.title', 'Pauta de aceite')->assertSet('form.start_at', '2027-01-10T08:59:00');
    expect(EventRequest::count())->toBe(0);
});
