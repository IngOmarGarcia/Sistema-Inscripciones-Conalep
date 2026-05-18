<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
{
    $data = $this->form->getState();

    $user = User::where('email', $data['email'])->first();

    //  Correo no existe
    if (!$user) {
        throw ValidationException::withMessages([
            'data.email' => 'El correo no está registrado.',
        ]);
    }

    $credentials = [
        'email' => $data['email'],
        'password' => $data['password'],
    ];

    //  Contraseña incorrecta
    if (!Auth::attempt($credentials, $data['remember'] ?? false)) {
        throw ValidationException::withMessages([
            'data.password' => 'La contraseña es incorrecta.',
        ]);
    }

    //  Login correcto
    session()->regenerate();

    return app(LoginResponse::class);
}
}