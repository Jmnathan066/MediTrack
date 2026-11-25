<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Eps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdministradorController extends Controller
{
    public function crear()
    {
        // Solo se muestran EPS activas para asignarlas a los doctores.
        $eps = Eps::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('administradores.crear', compact('eps'));
    }

    public function guardar(Request $request)
    {
        $datos = $request->validate([
            'tipoUsuario' => [
                'required',
                Rule::in(['admin', 'doctor']),
            ],

            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'documento' => [
                'required',
                'string',
                'unique:users,documento',
            ],

            'telefono' => [
                'required',
                'regex:/^[0-9]{7,15}$/',
            ],

            'direccion' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',

            // La EPS es obligatoria únicamente para los doctores.
            'idEPS' => [
                'required_if:tipoUsuario,doctor',
                'nullable',
                Rule::exists('eps', 'idEPS')
                    ->where('estado', true),
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
                'confirmed',
            ],
        ], [
            'tipoUsuario.required' =>
                'Debes seleccionar el tipo de usuario.',

            'tipoUsuario.in' =>
                'El tipo de usuario seleccionado no es válido.',

            'nombre.required' =>
                'El nombre es obligatorio.',

            'apellido.required' =>
                'El apellido es obligatorio.',

            'email.required' =>
                'El correo electrónico es obligatorio.',

            'email.email' =>
                'El correo electrónico no tiene un formato válido.',

            'email.unique' =>
                'Este correo electrónico ya está registrado.',

            'documento.required' =>
                'El documento es obligatorio.',

            'documento.unique' =>
                'Este documento ya está registrado.',

            'telefono.regex' =>
                'El teléfono debe contener entre 7 y 15 números.',

            'direccion.required' =>
                'La dirección es obligatoria.',

            'idEPS.required_if' =>
                'Debes seleccionar una EPS para el doctor.',

            'idEPS.exists' =>
                'La EPS seleccionada no existe o está inactiva.',

            'password.min' =>
                'La contraseña debe tener mínimo 6 caracteres.',

            'password.regex' =>
                'La contraseña debe incluir al menos una letra y un número.',

            'password.confirmed' =>
                'Las contraseñas no coinciden.',
        ]);

        $usuario = DB::transaction(function () use ($datos) {

            $usuario = User::create([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'email' => $datos['email'],
                'documento' => $datos['documento'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion'],
                'empresa' => $datos['empresa'] ?? null,

                'tipoUsuario' => $datos['tipoUsuario'],

                // Solo los doctores reciben una EPS.
                'idEPS' => $datos['tipoUsuario'] === 'doctor'
                    ? $datos['idEPS']
                    : null,

                'estado' => true,
                'password' => $datos['password'],
            ]);

            $usuario->codigo = 'USR-' .
                str_pad($usuario->id, 6, '0', STR_PAD_LEFT);

            $usuario->save();

            return $usuario;
        });

        $rol = $usuario->tipoUsuario === 'doctor'
            ? 'doctor'
            : 'administrador';

        return redirect()
            ->route('administradores.crear')
            ->with(
                'success',
                "El usuario {$rol} se creó correctamente. Código asignado: {$usuario->codigo}"
            )
            ->with('rolCreado', $usuario->tipoUsuario);
    }
}
