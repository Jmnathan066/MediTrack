<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ObservacionMedica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    /**
     * Consultar pacientes de la EPS del doctor y sus observaciones.
     */
    public function pacientes(Request $request)
    {
        $doctor = User::find(session('user_id'));

        if (!$doctor || $doctor->tipoUsuario !== 'doctor' || !$doctor->idEPS) {
            abort(403, 'No tienes una EPS asignada para consultar pacientes.');
        }

        $busqueda = trim($request->query('busqueda', ''));
        $estado = $request->query('estado', 'todos');

        if (!in_array($estado, ['todos', 'activos', 'inactivos'], true)) {
            $estado = 'todos';
        }

        $consulta = User::query()
            ->where('tipoUsuario', 'paciente')
            ->where('idEPS', $doctor->idEPS);

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

        if ($estado === 'activos') {
            $consulta->where('estado', true);
        } elseif ($estado === 'inactivos') {
            $consulta->where('estado', false);
        }

        $pacientes = $consulta
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(10)
            ->withQueryString();

        // Consultar las observaciones de los pacientes de esta página.
        $idsPacientes = $pacientes->getCollection()->pluck('id');

        $observacionesPorPaciente = DB::table('observaciones_medicas as o')
            ->leftJoin('users as d', 'd.id', '=', 'o.idDoctor')
            ->whereIn('o.idPaciente', $idsPacientes)
            ->select(
                'o.idObservacion',
                'o.idPaciente',
                'o.observacion',
                'o.created_at',
                'd.nombre as doctor_nombre',
                'd.apellido as doctor_apellido'
            )
            ->orderByDesc('o.created_at')
            ->get()
            ->groupBy('idPaciente');

        return view('doctor.consultaPacientes', compact(
            'pacientes',
            'busqueda',
            'estado',
            'doctor',
            'observacionesPorPaciente'
        ));
    }

    /**
     * Registrar una observación médica.
     */
    public function guardarObservacion(Request $request)
    {
        $doctor = User::find(session('user_id'));

        if (!$doctor || $doctor->tipoUsuario !== 'doctor' || !$doctor->idEPS) {
            abort(403, 'No tienes autorización para realizar esta acción.');
        }

        $datos = $request->validate([
            'idPaciente' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($doctor) {
                    $query->where('tipoUsuario', 'paciente')
                        ->where('idEPS', $doctor->idEPS);
                }),
            ],
            'observacion' => [
                'required',
                'string',
                'min:5',
                'max:5000',
            ],
        ], [
            'idPaciente.required' => 'Debes seleccionar un paciente.',
            'idPaciente.exists' => 'El paciente no pertenece a tu EPS.',
            'observacion.required' => 'Debes escribir la observación médica.',
            'observacion.min' => 'La observación debe tener al menos 5 caracteres.',
            'observacion.max' => 'La observación no puede superar los 5000 caracteres.',
        ]);

        ObservacionMedica::create([
            'idPaciente' => $datos['idPaciente'],
            'idDoctor' => $doctor->id,
            'observacion' => $datos['observacion'],
        ]);

        return redirect()
            ->route('doctor.pacientes')
            ->with('success', 'La observación médica se registró correctamente.');
    }
}
