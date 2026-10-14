<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutAndLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_is_public_and_identifies_developer_without_private_data(): void
    {
        $this->get('/sobre')->assertOk()->assertSee('lang="pt-BR"', false)
            ->assertSee('Sobre o Comunica')->assertSee('KL Tecnologia')
            ->assertSee('https://kltecnologia.com', false)->assertSee('48 horas corridas');
        $this->get('/calendario')->assertOk()->assertSee('href="http://localhost/sobre"', false)
            ->assertSee('Desenvolvido por')->assertSee('KL Tecnologia');
    }

    public function test_internal_layout_keeps_credit_and_about_for_both_profiles(): void
    {
        foreach (['coordinator' => '/solicitacoes', 'communication_admin' => '/comunicacao/triagem'] as $role => $route) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get($route)
                ->assertOk()->assertSee('Sobre')->assertSee('KL Tecnologia')->assertSee('lang="pt-BR"', false);
        }
    }

    public function test_common_translation_messages_and_pluralization_are_portuguese(): void
    {
        $this->assertSame('pt_BR', config('app.locale'));
        $this->assertSame('Anterior', __('pagination.previous'));
        $this->assertSame('E-mail ou senha inválidos.', __('auth.failed'));
        $this->assertSame('1 pauta confirmada neste mês', trans_choice('messages.confirmed', 1, ['count' => 1]));
        $this->assertSame('2 pautas confirmadas neste mês', trans_choice('messages.confirmed', 2, ['count' => 2]));
        foreach ([419 => 'Sua sessão expirou', 429 => 'Muitas tentativas', 500 => 'Não foi possível', 503 => 'Sistema temporariamente'] as $code => $text) {
            $this->assertStringContainsString($text, view('errors.'.$code)->render());
        }
    }
}
