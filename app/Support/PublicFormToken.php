<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class PublicFormToken
{
    public function make(string $scope): string
    {
        return Crypt::encryptString($scope.'|'.now()->timestamp.'|'.session()->getId());
    }

    public function validate(string $token, string $scope): void
    {
        try {
            $parts = explode('|', Crypt::decryptString($token));
        } catch (DecryptException) {
            $parts = [];
        }
        if (count($parts) !== 3 || $parts[0] !== $scope || ! ctype_digit($parts[1]) || ! hash_equals(session()->getId(), $parts[2]) || now()->timestamp - (int) $parts[1] < 3 || now()->timestamp - (int) $parts[1] > 7200) {
            throw ValidationException::withMessages(['form_token' => 'Espera unos segundos o abre de nuevo el formulario para enviar.']);
        }
    }
}
