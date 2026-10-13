<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\AuthenticateUserAction;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Acesso interno')]
class Login extends Component
{
    #[Validate('required|email', as: 'e-mail')]
    public string $email = '';

    #[Validate('required|string', as: 'senha')]
    public string $password = '';

    public function login(AuthenticateUserAction $authenticate): void
    {
        try {
            $this->validate();
            $user = $authenticate->execute($this->email, $this->password);
            $this->reset('password');
            $this->redirectIntended($user->isCommunicationAdmin() ? '/comunicacao/triagem' : '/solicitacoes');
        } catch (ValidationException $exception) {
            $this->reset('password');
            throw $exception;
        }
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
