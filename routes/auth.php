<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('login', function () {
        $credentials = request()->only('email', 'password');
        
        if (Auth::attempt($credentials, request()->filled('remember'))) {
            request()->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirigir según el rol del usuario
            if ($user->esAdmin()) {
                return redirect()->intended('dashboard');
            } else {
                // Para líderes y veedores, ir a votantes
                return redirect()->intended('votantes');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
