<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UsuarioService;

class HomeController extends Controller
{
    public function __construct(private UsuarioService $usuarios)
    {
    }

    /**
     * HU-007: panel principal segun el rol de session('tipoUsuario').
     * El menu diferenciado por rol vive en partials/sidebar.blade.php;
     * aqui solo se arma el contenido de bienvenida de la vista home.
     */
    public function home()
    {
        if (!session('login')) {
            return redirect('/');
        }

        return view('home');
    }

    /**
     * HU-008, escenario 1: ver informacion personal.
     */
    public function perfil()
    {
        $usuario = $this->usuarios->obtener(session('user_id'));

        return view('perfil', compact('usuario'));
    }

    /**
     * HU-008, escenarios 2 y 3: editar informacion personal.
     */
    public function actualizarPerfil(Request $request)
    {
        $id = session('user_id');

        $request->validate(UsuarioService::reglasPerfil($id), [
            'email.email' => 'El correo no tiene un formato válido.',
            'telefono.regex' => 'El teléfono solo debe contener números (7 a 15 dígitos).',
        ]);

        $usuario = $this->usuarios->actualizarPerfil($id, $request->all());

        session([
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
        ]);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * HU-009: formulario para cambiar contrasena.
     */
    public function formularioPassword()
    {
        return view('cambiar-password');
    }

    /**
     * HU-009, escenarios 1 a 4: procesa el cambio de contrasena.
     */
    public function actualizarPassword(Request $request)
    {
        $request->validate(UsuarioService::reglasPassword(), [
            'password_nueva.confirmed' => 'Las contraseñas no coinciden.',
            'password_nueva.min' => 'La nueva contraseña debe tener mínimo 6 caracteres.',
            'password_nueva.regex' => 'La nueva contraseña debe incluir al menos una letra y un número.',
        ]);

        $this->usuarios->cambiarPassword(
            session('user_id'),
            $request->password_actual,
            $request->password_nueva
        );

        return redirect('/perfil')->with('success', 'Contraseña actualizada correctamente.');
    }
}
