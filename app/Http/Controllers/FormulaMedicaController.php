<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medicamento;
use App\Models\FormulaMedica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FormulaMedicaController extends Controller
{
    private function doctorActual()
    {
        $doctor = User::find(session('user_id'));

        if (!$doctor || $doctor->tipoUsuario !== 'doctor' || !$doctor->idEPS) {
            abort(403, 'No tienes permiso para gestionar fórmulas médicas.');
        }

        return $doctor;
    }

    private function reglasValidacion($doctor): array
    {
        return [
            'idPaciente' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($doctor) {
                    $query->where('tipoUsuario', 'paciente')
                        ->where('idEPS', $doctor->idEPS)
                        ->where('estado', true);
                }),
            ],

            'fechaFormula' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:5000'],

            'medicamentos' => ['required', 'array', 'min:1'],

            'medicamentos.*.idDetalle' => [
                'nullable',
                'integer',
            ],

            'medicamentos.*.idMedicamento' => [
                'required',
                'integer',
                Rule::exists('medicamentos', 'idMedicamento')
                    ->where('estado', true),
            ],

            'medicamentos.*.dosis' => ['required', 'string', 'max:100'],
            'medicamentos.*.frecuencia' => ['required', 'string', 'max:150'],
            'medicamentos.*.duracion' => ['required', 'string', 'max:100'],
            'medicamentos.*.cantidad' => ['required', 'integer', 'min:1'],

            'medicamentos.*.unidad' => [
                'required',
                'string',
                Rule::in([
                    'tableta',
                    'capsula',
                    'frasco',
                    'ampolla',
                    'tubo',
                    'sobre',
                    'unidad',
                    'ml',
                    'g',
                ]),
            ],

            'medicamentos.*.indicaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function index()
    {
        $doctor = $this->doctorActual();

        $pacientes = User::where('tipoUsuario', 'paciente')
            ->where('idEPS', $doctor->idEPS)
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $medicamentos = Medicamento::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $formulas = FormulaMedica::with([
            'paciente',
            'medicamentos.medicamento'
        ])
            ->where('idDoctor', $doctor->id)
            ->orderByDesc('fechaFormula')
            ->paginate(10);

        return view('doctor.formulas', compact(
            'doctor',
            'pacientes',
            'medicamentos',
            'formulas'
        ));
    }

    public function guardar(Request $request)
    {
        $doctor = $this->doctorActual();

        $datos = $request->validate(
            $this->reglasValidacion($doctor),
            [
                'medicamentos.*.unidad.required' =>
                    'Debes seleccionar la unidad de dispensación de cada medicamento.',

                'medicamentos.*.unidad.in' =>
                    'La unidad seleccionada no es válida.',

                'medicamentos.*.cantidad.required' =>
                    'Debes indicar la cantidad del medicamento.',
            ]
        );

        DB::transaction(function () use ($datos, $doctor) {

            $formula = FormulaMedica::create([
                'idPaciente' => $datos['idPaciente'],
                'idDoctor' => $doctor->id,
                'fechaFormula' => $datos['fechaFormula'],
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => 'activa',
            ]);

            foreach ($datos['medicamentos'] as $medicamento) {
                unset($medicamento['idDetalle']);

                $formula->medicamentos()->create($medicamento);
            }
        });

        return redirect()
            ->route('doctor.formulas')
            ->with('success', 'Fórmula médica registrada correctamente.');
    }

    public function editar($id)
    {
        $doctor = $this->doctorActual();

        $formula = FormulaMedica::with('medicamentos.medicamento')
            ->where('idDoctor', $doctor->id)
            ->findOrFail($id);

        $pacientes = User::where('tipoUsuario', 'paciente')
            ->where('idEPS', $doctor->idEPS)
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $medicamentos = Medicamento::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $formulas = FormulaMedica::with([
            'paciente',
            'medicamentos.medicamento'
        ])
            ->where('idDoctor', $doctor->id)
            ->orderByDesc('fechaFormula')
            ->paginate(10);

        return view('doctor.formulas', compact(
            'doctor',
            'pacientes',
            'medicamentos',
            'formulas',
            'formula'
        ));
    }

    public function actualizar(Request $request, $id)
    {
        $doctor = $this->doctorActual();

        $formula = FormulaMedica::where('idDoctor', $doctor->id)
            ->findOrFail($id);

        $datos = $request->validate(
            $this->reglasValidacion($doctor),
            [
                'medicamentos.*.unidad.required' =>
                    'Debes seleccionar la unidad de dispensación de cada medicamento.',

                'medicamentos.*.unidad.in' =>
                    'La unidad seleccionada no es válida.',

                'medicamentos.*.cantidad.required' =>
                    'Debes indicar la cantidad del medicamento.',
            ]
        );

        DB::transaction(function () use ($datos, $formula) {

            $formula->update([
                'idPaciente' => $datos['idPaciente'],
                'fechaFormula' => $datos['fechaFormula'],
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $detallesActuales = $formula->medicamentos()
                ->lockForUpdate()
                ->get()
                ->keyBy('idDetalle');

            $detallesRecibidos = collect($datos['medicamentos']);

            $idsRecibidos = $detallesRecibidos
                ->pluck('idDetalle')
                ->filter()
                ->map(fn ($id) => (int) $id);

            /*
             * Revisar los detalles que se intentan eliminar.
             */
            foreach ($detallesActuales as $detalle) {

                $seConserva = $idsRecibidos->contains(
                    (int) $detalle->idDetalle
                );

                if (!$seConserva) {

                    $tieneEntrega = DB::table('detalle_orden_entrega')
                        ->where('idDetalleFormula', $detalle->idDetalle)
                        ->exists();

                    if ($tieneEntrega) {
                        throw ValidationException::withMessages([
                            'medicamentos' =>
                                'No puedes eliminar el medicamento "' .
                                ($detalle->medicamento->nombre ?? 'registrado') .
                                '" porque ya tiene una entrega registrada.',
                        ]);
                    }

                    $detalle->delete();
                }
            }

            /*
             * Crear o actualizar los medicamentos recibidos.
             */
            foreach ($datos['medicamentos'] as $medicamento) {

                $idDetalle = $medicamento['idDetalle'] ?? null;

                unset($medicamento['idDetalle']);

                if ($idDetalle) {

                    $idDetalle = (int) $idDetalle;

                    if (!$detallesActuales->has($idDetalle)) {
                        throw ValidationException::withMessages([
                            'medicamentos' =>
                                'Se recibió un detalle que no pertenece a esta fórmula médica.',
                        ]);
                    }

                    $detalle = $detallesActuales->get($idDetalle);

                    $tieneEntrega = DB::table('detalle_orden_entrega')
                        ->where('idDetalleFormula', $detalle->idDetalle)
                        ->exists();

                    if ($tieneEntrega) {
                        /*
                         * No modificar detalles que forman parte
                         * de una entrega histórica.
                         */
                        continue;
                    }

                    $detalle->update($medicamento);

                } else {

                    /*
                     * Es un medicamento nuevo.
                     */
                    $formula->medicamentos()->create($medicamento);
                }
            }
        });

        return redirect()
            ->route('doctor.formulas')
            ->with('success', 'Fórmula médica actualizada correctamente.');
    }
}

