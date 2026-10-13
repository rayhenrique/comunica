<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetLocalUserPassword extends Command
{
    protected $signature = 'comunica:password {email : E-mail de um usuário existente}';
    protected $description = 'Define uma senha local com entrada oculta, sem alterar perfil ou criar usuários';

    public function handle(): int
    {
        if (!app()->environment(['local', 'testing'])) {
            $this->error('Este comando é exclusivo do ambiente local.');
            return self::FAILURE;
        }

        $user = User::where('email', $this->argument('email'))->first();
        if ($user === null) {
            $this->error('Usuário não encontrado.');
            return self::FAILURE;
        }

        $password = $this->secret('Nova senha (mínimo 12 caracteres)');
        $confirmation = $this->secret('Confirme a nova senha');
        if (!is_string($password) || mb_strlen($password) < 12 || $password !== $confirmation) {
            $this->error('A senha deve ter pelo menos 12 caracteres e a confirmação deve corresponder.');
            return self::FAILURE;
        }

        $user->password = $password;
        $user->save();
        $this->info('Senha local atualizada.');

        return self::SUCCESS;
    }
}
