<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * UsuarioService concentra la logica de negocio del propio usuario
 * autenticado: consultar/editar su perfil (HU-008) y cambiar su
 * contrasena (HU-009). El controlador no habla directo con el modelo
 * Eloquent, igual que InventarioService para medicamentos.
 */
class UsuarioService
{
    public static function reglasPerfil(int $usuarioId): array
    {
        return [
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:users,email,' . $usuarioId,
            'telefono' => 'required|regex:/^[0-9]{7,15}$/',
            'direccion' => 'required',
        ];
    }

    public static function reglasPassword(): array
    {
        return [
            'password_actual' => 'required',
            'password_nueva' => ['required', 'min:6', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
        ];
    }

    public function obtener(int $id): User
    {
        return User::findOrFail($id);
    }

    public function actualizarPerfil(int $id, array $datos): User
    {
        $usuario = $this->obtener($id);

        $usuario->update([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'direccion' => $datos['direccion'],
        ]);

        return $usuario;
    }

    /**
     * Cambia la contrasena del usuario autenticado, verificando primero
     * la contrasena actual (HU-009, escenarios 1 y 2). Lanza
     * ValidationException si no coincide, para que el controlador la
     * traduzca en un error de formulario tal como $request->validate().
     */
    public function cambiarPassword(int $id, string $passwordActual, string $passwordNueva): void
    {
        $usuario = $this->obtener($id);

        if (! Hash::check($passwordActual, $usuario->password)) {
            throw ValidationException::withMessages([
                'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        $usuario->password = Hash::make($passwordNueva);
        $usuario->save();
    }
}
