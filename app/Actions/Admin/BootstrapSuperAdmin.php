<?php

namespace App\Actions\Admin;

use App\Enums\AuditAction;
use App\Enums\AuditResult;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class BootstrapSuperAdmin
{
    public function handle(string $name, string $email, #[SensitiveParameter] string $password): User
    {
        return Cache::store('database')->lock('admin:bootstrap', 30)->block(5, function () use ($name, $email, $password): User {
            return DB::transaction(function () use ($name, $email, $password): User {
                if (User::query()->where('is_super_admin', true)->exists()) {
                    throw ValidationException::withMessages(['email' => 'La primera cuenta administrativa ya existe.']);
                }

                $data = Validator::make([
                    'name' => trim($name), 'email' => mb_strtolower(trim($email)), 'password' => $password,
                ], [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                    'password' => ['required', Password::defaults()],
                ])->validate();

                $user = new User($data);
                $user->is_super_admin = true;
                $user->save();

                app(RecordAuditEvent::class)->handle(AuditAction::AdminBootstrapped, AuditResult::Success, $user, $user, ['is_super_admin']);

                return $user;
            });
        });
    }
}
