<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestionUsuariosController extends Controller
{
    /**
     * HU-017: listar, buscar y filtrar usuarios.
     */
    public function index(Request $request)
    {
        $busqueda = trim((string) $request->input('buscar', ''));
        $rol = $request->input('rol', '');

        $rolesPermitidos = [
            'admin',
            'paciente',
            'entidad',
            'doctor',
        ];

        if (!in_array($rol, $rolesPermitidos, true)) {
            $rol = '';
        }

        $consulta = User::query();

        if ($busqueda !== '') {
            $consulta->where(function ($query) use ($busqueda) {
                $query->where('codigo', 'ilike', "%{$busqueda}%")
                    ->orWhere('nombre', 'ilike', "%{$busqueda}%")
                    ->orWhere('apellido', 'ilike', "%{$busqueda}%")
                    ->orWhereRaw(
                        "CONCAT(nombre, ' ', apellido) ILIKE ?",
                        ["%{$busqueda}%"]
                    )
                    ->orWhere('email', 'ilike', "%{$busqueda}%");
            });
        }

        if ($rol !== '') {
            $consulta->where('tipoUsuario', $rol);
        }

        $usuarios = $consulta
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $totalUsuarios = User::count();
        $usuariosActivos = User::where('estado', true)->count();
        $usuariosInactivos = User::where('estado', false)->count();

        return view('administradores.usuarios', compact(
            'usuarios',
            'busqueda',
            'rol',
            'totalUsuarios',
            'usuariosActivos',
            'usuariosInactivos'
        ));
    }

    /**
     * HU-017: activar o desactivar una cuenta.
     */
    public function cambiarEstado(User $usuario)
    {
        // Evita que el administrador se bloquee a sí mismo.
        if ((int) $usuario->id === (int) session('user_id')) {
            return back()->with(
                'error',
                'No puedes desactivar tu propia cuenta.'
            );
        }

        // Evita dejar el sistema sin ningún administrador activo.
        if ($usuario->tipoUsuario === 'admin' && $usuario->estado) {
            $administradoresActivos = User::where('tipoUsuario', 'admin')
                ->where('estado', true)
                ->count();

            if ($administradoresActivos <= 1) {
                return back()->with(
                    'error',
                    'No puedes desactivar al último administrador activo.'
                );
            }
        }

        DB::transaction(function () use ($usuario) {
            $usuario->estado = !$usuario->estado;
            $usuario->save();
        });

        $mensaje = $usuario->estado
            ? 'La cuenta se activó correctamente.'
            : 'La cuenta se desactivó correctamente.';

        return back()->with('success', $mensaje);
    }
}
