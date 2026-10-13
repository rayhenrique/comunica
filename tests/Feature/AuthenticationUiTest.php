<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_layout_and_invalid_credentials(): void
    {
        $this->get('/login')->assertOk()->assertSee('Acesso interno')->assertSee('lang="pt-BR"', false);
        Livewire::test(Login::class)->set('email', 'inexistente@example.test')->set('password', Str::random(20))
            ->call('login')->assertHasErrors('email')->assertSet('password', '');
        $this->assertGuest();
    }

    public function test_successful_login_regenerates_session_and_logout_ends_it(): void
    {
        $password = Str::random(24);
        $user = User::factory()->create(['password' => Hash::make($password)]);
        Livewire::test(Login::class)->set('email', $user->email)->set('password', $password)
            ->call('login')->assertRedirect('/solicitacoes')->assertSet('password', '');
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/calendario');
        $this->assertGuest();
    }

    public function test_repeated_failed_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            Livewire::test(Login::class)->set('email', 'limit@example.test')->set('password', Str::random(20))
                ->call('login')->assertSee('E-mail ou senha inválidos.');
        }
        Livewire::test(Login::class)->set('email', 'limit@example.test')->set('password', Str::random(20))
            ->call('login')->assertSee('Muitas tentativas.');
    }
}
