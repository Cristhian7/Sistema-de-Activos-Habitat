<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Login\Login;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar formulario de acceso
    public function show()
    {
        Auth::logout();
        return view('login.auth'); // Apunta a la subcarpeta de vistas
    }

    // Procesar el inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        // Buscar el usuario en la tabla login
        $user = Login::where('usuario', $request->usuario)->first();

        // Verificar credenciales
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'usuario' => 'El usuario o la contraseña son incorrectos.',
        ])->onlyInput('usuario');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}