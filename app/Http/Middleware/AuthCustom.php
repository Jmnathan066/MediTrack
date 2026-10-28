<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCustom
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (!session('login')) {
            return redirect('/')
                ->with('error', 'Debes iniciar sesión para continuar.');
        }

        // HU-017: comprobar que la cuenta siga activa en la base de datos.
        $usuario = User::find(session('user_id'));

        if (!$usuario || !$usuario->estado) {
            session()->invalidate();
            session()->regenerateToken();

            return redirect('/')->with(
                'error',
                'Tu cuenta está desactivada o ya no existe. Contacta al administrador.'
            );
        }

        // Mantener la restricción de acceso por roles.
        if (
            !empty($roles) &&
            !in_array($usuario->tipoUsuario, $roles, true)
        ) {
            return redirect('/home')->with(
                'error',
                'No tienes permiso para acceder a ese módulo.'
            );
        }

        return $next($request);
    }
}
