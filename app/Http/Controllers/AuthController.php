<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Eps;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * AuthController concentra todo el modulo de autenticacion y control de
 * acceso por rol: inicio de sesion, registro y recuperacion de contrasena,
 * tal como se documenta en la Figura 3 (Diagrama de componentes, Nivel 3)
 * del Documento de Arquitectura de Software. Cubre HU-001 a HU-006.
 */
class AuthController extends Controller
{
    // ------------------------------------------------------------------
    // Login (HU-001, HU-004)
    // ------------------------------------------------------------------

    public function login()
    {
        return view('login');
    }

    public function validar(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'pass' => 'required',
        ]);

        $usuarioLogin = $request->input('user');
        $credencial = $request->input('pass');

        // Permite iniciar sesión con documento, correo o código.
        $usuario = User::where('documento', $usuarioLogin)
            ->orWhere('email', $usuarioLogin)
            ->orWhere('codigo', $usuarioLogin)
            ->first();

        // Usuario inexistente.
        if (!$usuario) {
            return redirect('/')->with(
                'error',
                'El usuario no existe en el sistema.'
            );
        }

        // Impedir el acceso a cuentas desactivadas.
        if (!$usuario->estado) {
            return redirect('/')->with(
                'error',
                'Tu cuenta está desactivada. Contacta al administrador.'
            );
        }

        // Credenciales correctas.
        if (
            $credencial === $usuario->codigo ||
            Hash::check($credencial, $usuario->password)
        ) {
            session([
                'login' => true,
                'user_id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'tipoUsuario' => $usuario->tipoUsuario,
            ]);

            return redirect('/home');
        }

        return redirect('/')->with(
            'error',
            'La contraseña o el código no son correctos.'
        );
    }

    public function logout()
    {
        if (!session('login')) {
            return redirect('/')->with(
                'error',
                'No hay una sesión activa (puede haber expirado).'
            );
        }

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/')->with(
            'success',
            'Sesión cerrada con éxito.'
        );
    }

    // ------------------------------------------------------------------
    // Registro (HU-002, HU-003)
    // ------------------------------------------------------------------

    public function registrarvista()
    {
        // Solo se envían EPS activas al formulario.
        $eps = Eps::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('registrar', compact('eps'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'documento' => [
                'required',
                'unique:users,documento',
            ],

            'telefono' => [
                'required',
                'regex:/^[0-9]{7,15}$/',
            ],

            'direccion' => 'required|string|max:255',

            'tipoUsuario' => [
                'required',
                Rule::in(['paciente', 'entidad']),
            ],

            // La EPS es obligatoria únicamente para pacientes.
            'idEPS' => [
                'required_if:tipoUsuario,paciente',
                'nullable',
                Rule::exists('eps', 'idEPS')
                    ->where('estado', true),
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Este correo ya está registrado.',

            'documento.required' => 'El documento es obligatorio.',
            'documento.unique' => 'Este documento ya está registrado.',

            'telefono.regex' => 'El teléfono solo debe contener números (7 a 15 dígitos).',

            'direccion.required' => 'La dirección es obligatoria.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener mínimo 6 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra y un número.',

            'tipoUsuario.required' => 'Debes seleccionar un tipo de usuario.',
            'tipoUsuario.in' => 'Solo puedes registrarte como paciente o entidad.',

            'idEPS.required_if' => 'Debes seleccionar una EPS para registrarte como paciente.',
            'idEPS.exists' => 'La EPS seleccionada no existe o está inactiva.',
        ]);

        // Los pacientes reciben la EPS seleccionada.
        // Las entidades quedan sin EPS por ahora.
        $idEPS = $request->tipoUsuario === 'paciente'
            ? $request->idEPS
            : null;

        // Las entidades conservan el campo empresa.
        // Los pacientes no pertenecen a una empresa.
        $empresa = $request->tipoUsuario === 'entidad'
            ? $request->empresa
            : null;

        try {
            $usuario = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'documento' => $request->documento,

                'empresa' => $empresa,
                'idEPS' => $idEPS,

                'tipoUsuario' => $request->tipoUsuario,
                'estado' => true,
                'password' => Hash::make($request->password),
            ]);

            // Generar código único del usuario.
            $usuario->codigo = 'USR-' .
                str_pad($usuario->id, 6, '0', STR_PAD_LEFT);

            $usuario->save();

        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with(
                'error',
                'Ocurrió un error al guardar tu registro. Inténtalo nuevamente.'
            );
        }

        return redirect('/')->with(
            'success',
            'Usuario registrado correctamente. Código asignado: ' . $usuario->codigo
        );
    }

    // ------------------------------------------------------------------
    // Recuperación de contraseña (HU-005)
    // ------------------------------------------------------------------

    public function vista()
    {
        return view('recuperar');
    }

    public function enviarCodigo(Request $request)
    {
        $usuario = User::where('documento', $request->documento)
            ->where(function ($query) use ($request) {
                $query->where('email', $request->contacto)
                    ->orWhere('telefono', $request->contacto);
            })
            ->first();

        if (!$usuario) {
            return back()->with(
                'error',
                'No encontramos un usuario con esos datos.'
            );
        }

        $codigo = rand(100000, 999999);

        $usuario->codigo_recuperacion = $codigo;
        $usuario->expira_recuperacion = Carbon::now()->addMinutes(15);
        $usuario->save();

        try {
            Mail::raw(
                "Tu código de recuperación es: " . $codigo,
                function ($message) use ($usuario) {
                    $message->to($usuario->email)
                        ->subject("Recuperación de cuenta - MediTrack EPS");
                }
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'No pudimos enviar el código en este momento. Inténtalo nuevamente.'
            );
        }

        session([
            'usuario_recuperacion' => $usuario->id,
        ]);

        return redirect('/validar-codigo');
    }

    public function vistaCodigo()
    {
        return view('validarCodigo');
    }

    public function validarCodigo(Request $request)
    {
        $usuario = User::find(session('usuario_recuperacion'));

        if (!$usuario) {
            return redirect('/recuperar')->with(
                'error',
                'Debes iniciar nuevamente el proceso de recuperación.'
            );
        }

        if ($usuario->codigo_recuperacion != $request->codigo) {
            return back()->with(
                'error',
                'El código ingresado es incorrecto.'
            );
        }

        if (Carbon::now()->gt($usuario->expira_recuperacion)) {
            return back()->with(
                'error',
                'El código ha expirado. Solicita uno nuevo.'
            );
        }

        session([
            'recuperacion_verificada' => true,
        ]);

        return redirect('/nueva-password');
    }

    public function vistaNuevaPassword()
    {
        return view('nuevaPassword');
    }

    public function actualizarPassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
                'confirmed',
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener mínimo 6 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario = User::find(session('usuario_recuperacion'));

        if (!$usuario || !session('recuperacion_verificada')) {
            return redirect('/recuperar')->with(
                'error',
                'Proceso inválido o expirado.'
            );
        }

        try {
            $ultimo = User::max('id') + 1;

            $nuevoCodigo = 'usr-' .
                str_pad($ultimo, 6, '0', STR_PAD_LEFT);

            $usuario->password = Hash::make($request->password);
            $usuario->codigo_recuperacion = null;
            $usuario->expira_recuperacion = null;
            $usuario->codigo = $nuevoCodigo;

            $usuario->save();

        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Ocurrió un error al generar tu nueva credencial. Inténtalo nuevamente.'
            );
        }

        session()->forget('usuario_recuperacion');
        session()->forget('recuperacion_verificada');

        return redirect('/')
            ->with(
                'success',
                'Contraseña actualizada. Tu nuevo código es: ' . $nuevoCodigo
            );
    }
}
