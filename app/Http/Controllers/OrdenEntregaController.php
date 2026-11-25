<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sede;
use App\Models\FormulaMedica;
use App\Models\FormulaMedicamento;
use App\Models\OrdenEntrega;
use App\Models\DetalleOrdenEntrega;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrdenEntregaController extends Controller
{
    private function entidadActual(): User
    {
        $entidad = User::find(session('user_id'));

        if (
            !$entidad ||
            $entidad->tipoUsuario !== 'entidad' ||
            !$entidad->idEPS
        ) {
            abort(403, 'No tienes permiso para registrar entregas.');
        }

        return $entidad;
    }

    public function index(Request $request)
    {
        $entidad = $this->entidadActual();

        $datos = $request->validate([
            'busqueda' => ['nullable', 'string', 'max:100'],
            'paciente_id' => ['nullable', 'integer'],
            'idFormula' => ['nullable', 'integer'],
        ]);

        $busqueda = trim($datos['busqueda'] ?? '');

        $sedes = Sede::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $paciente = null;
        $pacientes = collect();
        $formulas = collect();
        $formulaSeleccionada = null;

        if (!empty($datos['paciente_id'])) {
            $paciente = User::where('id', $datos['paciente_id'])
                ->where('tipoUsuario', 'paciente')
                ->where('idEPS', $entidad->idEPS)
                ->where('estado', true)
                ->first();
        } elseif ($busqueda !== '') {
            $paciente = User::where('tipoUsuario', 'paciente')
                ->where('idEPS', $entidad->idEPS)
                ->where('estado', true)
                ->where('documento', $busqueda)
                ->first();

            if (!$paciente) {
                $terminos = preg_split('/\s+/', $busqueda);

                $consulta = User::where('tipoUsuario', 'paciente')
                    ->where('idEPS', $entidad->idEPS)
                    ->where('estado', true);

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

                if ($pacientes->count() === 1) {
                    $paciente = $pacientes->first();
                    $pacientes = collect();
                }
            }
        }

        if ($paciente) {
            $formulas = FormulaMedica::with([
                'doctor:id,nombre,apellido',
                'medicamentos.medicamento',
            ])
                ->where('idPaciente', $paciente->id)
                ->where('estado', 'activa')
                ->orderByDesc('fechaFormula')
                ->get();

            if (!empty($datos['idFormula'])) {
                $formulaSeleccionada = $formulas->firstWhere(
                    'idFormula',
                    (int) $datos['idFormula']
                );

                if ($formulaSeleccionada) {
                    foreach ($formulaSeleccionada->medicamentos as $detalle) {
                        $detalle->cantidadEntregada = (int) DetalleOrdenEntrega::where(
                            'idDetalleFormula',
                            $detalle->idDetalle
                        )->sum('cantidadEntregada');

                        $detalle->cantidadPendiente = max(
                            0,
                            (int) $detalle->cantidad - $detalle->cantidadEntregada
                        );
                    }
                }
            }
        }

        return view('empresa.ordenes-entrega', compact(
            'entidad',
            'busqueda',
            'sedes',
            'paciente',
            'pacientes',
            'formulas',
            'formulaSeleccionada'
        ));
    }

    public function guardar(
        Request $request,
        InventarioService $inventarioService
    ) {
        $entidad = $this->entidadActual();

        $datos = $request->validate([
            'idSede' => [
                'required',
                'integer',
                Rule::exists('sedes', 'idSede')->where('estado', true),
            ],
            'idFormula' => ['required', 'integer'],
            'cantidades' => ['required', 'array', 'min:1'],
            'cantidades.*' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use (
            $datos,
            $entidad,
            $inventarioService
        ) {
            $formula = FormulaMedica::where('idFormula', $datos['idFormula'])
                ->where('estado', 'activa')
                ->whereHas('paciente', function ($query) use ($entidad) {
                    $query->where('tipoUsuario', 'paciente')
                        ->where('idEPS', $entidad->idEPS)
                        ->where('estado', true);
                })
                ->lockForUpdate()
                ->firstOrFail();

            $detalles = FormulaMedicamento::where('idFormula', $formula->idFormula)
                ->whereIn('idDetalle', array_keys($datos['cantidades']))
                ->lockForUpdate()
                ->get();

            if ($detalles->count() !== count($datos['cantidades'])) {
                throw new \Exception(
                    'Uno o más medicamentos no pertenecen a esta fórmula.'
                );
            }

            $orden = OrdenEntrega::create([
                'idPaciente' => $formula->idPaciente,
                'idFormula' => $formula->idFormula,
                'idUsuario' => $entidad->id,
                'idSede' => $datos['idSede'],
                'fechaEntrega' => now(),
                'estado' => 'parcial',
            ]);

            $entregaCompleta = true;

            foreach ($detalles as $detalle) {
                $cantidadEntregar = (int) $datos['cantidades'][$detalle->idDetalle];

                $cantidadAnterior = (int) DetalleOrdenEntrega::where(
                    'idDetalleFormula',
                    $detalle->idDetalle
                )->sum('cantidadEntregada');

                $pendiente = (int) $detalle->cantidad - $cantidadAnterior;

                if ($cantidadEntregar > $pendiente) {
                    throw new \Exception(
                        "La cantidad solicitada de {$detalle->medicamento->nombre} supera lo pendiente por entregar ({$pendiente})."
                    );
                }

                $lotesUtilizados = $inventarioService->descontarStockPorLotes(
                    (int) $datos['idSede'],
                    (int) $detalle->idMedicamento,
                    $cantidadEntregar
                );

                foreach ($lotesUtilizados as $lote) {
                    DetalleOrdenEntrega::create([
                        'idOrden' => $orden->idOrden,
                        'idDetalleFormula' => $detalle->idDetalle,
                        'idLote' => $lote['idLote'],
                        'cantidadEntregada' => $lote['cantidadEntregada'],
                    ]);
                }

                if ($cantidadEntregar < $pendiente) {
                    $entregaCompleta = false;
                }
            }

            // Comprobar si todos los medicamentos de la fórmula
            // quedaron completamente entregados.
            foreach ($formula->medicamentos as $detalleFormula) {
                $totalEntregado = (int) DetalleOrdenEntrega::where(
                    'idDetalleFormula',
                    $detalleFormula->idDetalle
                )->sum('cantidadEntregada');

                if ($totalEntregado < (int) $detalleFormula->cantidad) {
                    $entregaCompleta = false;
                    break;
                }
            }

            $orden->estado = $entregaCompleta ? 'completa' : 'parcial';
            $orden->save();
        });

        return redirect()
            ->route('empresa.ordenes.index')
            ->with('success', 'La entrega se registró correctamente y el inventario fue actualizado.');
    }
}
