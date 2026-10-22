<?php

namespace App\Console\Commands;

use App\Actions\Admin\BootstrapSuperAdmin as BootstrapAction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;

class BootstrapSuperAdmin extends Command
{
    protected $signature = 'app:bootstrap-super-admin';

    protected $description = 'Crea explícitamente la primera cuenta administrativa mediante entrada privada interactiva';

    public function handle(BootstrapAction $action): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Este comando requiere una terminal interactiva y una contraseña oculta.');

            return self::FAILURE;
        }

        if (User::query()->where('is_super_admin', true)->exists()) {
            $this->error('La primera cuenta administrativa ya existe. No se modificó ninguna cuenta.');

            return self::FAILURE;
        }

        $name = $this->ask('Nombre de la primera cuenta administrativa');
        $email = $this->ask('Correo administrativo');
        $password = $this->secret('Contraseña (12 caracteres, mayúsculas, minúsculas y números)', fallback: false);
        $confirmation = $this->secret('Repite la contraseña', fallback: false);

        if ((! is_string($password)) || (! is_string($confirmation)) || (! hash_equals($password, $confirmation))) {
            $this->error('Las contraseñas no coinciden. No se creó ninguna cuenta.');

            return self::FAILURE;
        }

        try {
            $action->handle((string) $name, (string) $email, $password);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            return self::FAILURE;
        } catch (LockTimeoutException) {
            $this->error('Hay otra creación administrativa en curso. Inténtalo de nuevo.');

            return self::FAILURE;
        }

        $this->info('Cuenta creada. Entra en /dashboard y configura el segundo factor antes de operar. Guarda los códigos de recuperación fuera del sitio.');

        return self::SUCCESS;
    }
}
