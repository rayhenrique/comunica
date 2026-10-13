<?php

declare(strict_types=1);

use App\Livewire\Coordinator\MyRequests;
use App\Models\User;
use Livewire\Livewire;
use Tests\Support\Phase6Fixtures;

it('isolates both coordinators in html json details and forged livewire calls', function (string $status): void {
    $a = User::factory()->create();
    $b = User::factory()->create();
    $one = Phase6Fixtures::event($a, ['title' => 'Privado do setor A', 'status' => $status]);
    $two = Phase6Fixtures::event($b, ['title' => 'Privado do setor B', 'status' => $status]);

    foreach ([[$a, $one, $two], [$b, $two, $one]] as [$viewer, $own, $other]) {
        $this->actingAs($viewer);
        $this->get('/solicitacoes?user_id='.$other->user_id)
            ->assertOk()->assertSee($own->title)->assertDontSee($other->title);
        $this->getJson('/solicitacoes?user_id='.$other->user_id)
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $own->id);
        $this->getJson('/solicitacoes/'.$own->id)->assertOk();
        $this->get('/solicitacoes/'.$other->id)->assertForbidden()->assertDontSee($other->title);
        $this->getJson('/solicitacoes/'.$other->id)->assertForbidden();
        Livewire::test(MyRequests::class)->set('status', $status)
            ->assertSee($own->title)->assertDontSee($other->title);
        Livewire::test(MyRequests::class)->call('openDetails', $other->id)->assertForbidden();
        Livewire::test(MyRequests::class)->call('askCancel', $other->id)->assertForbidden();
    }
    expect($one->fresh()->status)->toBe($status);
    expect($two->fresh()->status)->toBe($status);
})->with(['pending', 'approved', 'rescheduled', 'rejected', 'cancelled']);
