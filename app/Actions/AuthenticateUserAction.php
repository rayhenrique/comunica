<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticateUserAction
{
    public function execute(string $email, string $password): User
    {
        Validator::make(compact('email', 'password'), [
            'email' => ['required', 'email'], 'password' => ['required', 'string'],
        ])->validate();
        $key = 'login:'.hash('sha256', Str::lower(trim($email)).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Muitas tentativas. Aguarde um minuto e tente novamente.']);
        }

        RateLimiter::hit($key, 60);
        if (!Auth::attempt(['email' => trim($email), 'password' => $password])) {
            throw ValidationException::withMessages(['email' => 'E-mail ou senha inválidos.']);
        }

        $user = Auth::user();
        if (!$user instanceof User || (!$user->isCoordinator() && !$user->isCommunicationAdmin())) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'Não foi possível entrar. Verifique seus dados de acesso.']);
        }

        session()->regenerate();
        session()->put('release-notice-login', true);
        RateLimiter::clear($key);

        return $user;
    }
}
