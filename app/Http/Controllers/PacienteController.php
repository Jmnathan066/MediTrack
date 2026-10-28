<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = trim($request->query('busqueda', ''));

        $estado = $request->query('estado', 'todos');

        // Aceptar únicamente los filtros permitidos.
        if (!in_array($estado, ['todos', 'activos', 'inactivos'], true)) {
            $estado = 'todos';
        }

        // Consultar los usuarios que tienen rol de paciente.
        $consulta = User::query()
            ->where('tipoUsuario', 'paciente');

        // Buscar por nombre, apellido, nombre completo o documento.
        if ($busqueda !== '') {
            $consulta->where(function ($query) use ($busqueda) {
                $query->where('nombre', 'ILIKE', "%{$busqueda}%")
                    ->orWhere('apellido', 'ILIKE', "%{$busqueda}%")
                    ->orWhereRaw(
                        "nombre || ' ' || apellido ILIKE ?",
                        ["%{$busqueda}%"]
                    )
                    ->orWhere('documento', 'ILIKE', "%{$busqueda}%");
            });
        }

        // Filtrar según el estado de la cuenta.
        if ($estado === 'activos') {
            $consulta->where('estado', true);
        } elseif ($estado === 'inactivos') {
            $consulta->where('estado', false);
        }

        // Ordenar y mostrar 10 pacientes por página.
        $pacientes = $consulta
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(10)
            ->withQueryString();

        return view('pacientes.Pacientes', [
            'pacientes' => $pacientes,
            'busqueda' => $busqueda,
            'estado' => $estado,
        ]);
    }
}
