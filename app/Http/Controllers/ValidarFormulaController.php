<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FormulaMedica;
use Illuminate\Http\Request;

class ValidarFormulaController extends Controller
{
    /**
     * Obtener y validar el usuario de empresa actual.
     */
    private function entidadActual()
    {
        $entidad = User::find(session('user_id'));

        if (
            !$entidad ||
            $entidad->tipoUsuario !== 'entidad' ||
            !$entidad->idEPS
        ) {
            abort(403, 'No tienes permiso para consultar estas fórmulas médicas.');
        }

        return $entidad;
    }

    /**
     * Obtener las fórmulas médicas de un paciente.
     */
    private function obtenerFormulas($paciente)
    {
        return FormulaMedica::with([
            'doctor:id,nombre,apellido',
            'medicamentos.medicamento',
        ])
            ->where('idPaciente', $paciente->id)
            ->orderByDesc('fechaFormula')
            ->get();
    }

    /**
     * Mostrar el buscador y consultar las fórmulas de un paciente.
     */
    public function index(Request $request)
    {
        $entidad = $this->entidadActual();

        $datos = $request->validate([
            'busqueda' => ['nullable', 'string', 'max:100'],
            'paciente_id' => ['nullable', 'integer'],
        ]);

        $busqueda = trim($datos['busqueda'] ?? '');

        $paciente = null;
        $pacientes = collect();
        $formulas = collect();

        if ($busqueda !== '') {

            // Si se seleccionó un paciente de la lista,
            // verificar que pertenezca a la EPS de la entidad.
            if (!empty($datos['paciente_id'])) {

                $paciente = User::where('id', $datos['paciente_id'])
                    ->where('tipoUsuario', 'paciente')
                    ->where('idEPS', $entidad->idEPS)
                    ->first();

            } else {

                // Primero intentar una coincidencia exacta por documento.
                $paciente = User::where('tipoUsuario', 'paciente')
                    ->where('idEPS', $entidad->idEPS)
                    ->where('documento', $busqueda)
                    ->first();

                // Si no coincide el documento, buscar por nombre o apellido.
                if (!$paciente) {

                    $terminos = preg_split('/\s+/', $busqueda);

                    $consulta = User::where('tipoUsuario', 'paciente')
                        ->where('idEPS', $entidad->idEPS);

                    foreach ($terminos as $termino) {
                        $consulta->where(function ($query) use ($termino) {
                            $query->where('nombre', 'ILIKE', "%{$termino}%")
                                ->orWhere('apellido', 'ILIKE', "%{$termino}%");
                        });
                    }

                    $pacientes = $consulta
                        ->orderBy('nombre')
                        ->orderBy('apellido')
                        ->get();

                    // Si solo hay una coincidencia, seleccionarla directamente.
                    if ($pacientes->count() === 1) {
                        $paciente = $pacientes->first();
                        $pacientes = collect();
                    }
                }
            }

            // Consultar las fórmulas únicamente del paciente seleccionado.
            if ($paciente) {
                $formulas = $this->obtenerFormulas($paciente);
            }
        }

        return view('empresa.validar-formulas', compact(
            'entidad',
            'busqueda',
            'paciente',
            'pacientes',
            'formulas'
        ));
    }
}
