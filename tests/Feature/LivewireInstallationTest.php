<?php

declare(strict_types=1);

namespace Tests\Feature;

use Livewire\Component;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireInstallationTest extends TestCase
{
    public function test_livewire_renders_and_updates_a_component(): void
    {
        Livewire::test(InstallationProbe::class)
            ->assertSee('0')
            ->call('increment')
            ->assertSet('count', 1)
            ->assertSee('1');
    }
}

class InstallationProbe extends Component
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function render(): string
    {
        return '<div>{{ $count }}</div>';
    }
}
