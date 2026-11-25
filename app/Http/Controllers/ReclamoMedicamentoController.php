<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrdenEntrega;
use App\Models\ReclamoMedicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReclamoMedicamentoController extends Controller
{
    /**
     * Mostrar medicamentos entregados al paciente y sus reclamos.
     */
    public function index()
    {
        $idPaciente = session('user_id');

        $entregas = DetalleOrdenEntrega::with([
            'orden',
            'detalleFormula.medicamento',
            'lote',
        ])
        ->whereHas('orden', function ($query) use ($idPaciente) {
            $query->where('idPaciente', $idPaciente);
        })
        ->orderByDesc('created_at')
        ->get();

        $reclamos = ReclamoMedicamento::with([
            'detalleOrden.detalleFormula.medicamento',
        ])
        ->where('idPaciente', $idPaciente)
        ->orderByDesc('created_at')
        ->get();

        return view('paciente.reclamos', compact(
            'entregas',
            'reclamos'
        ));
    }

    /**
     * Registrar un reclamo sobre un medicamento recibido.
     */
    public function guardar(Request $request)
    {
        $idPaciente = session('user_id');

        $request->validate([
            'idDetalleOrden' => 'required|integer',
            'motivo' => 'required|string|max:150',
            'descripcion' => 'required|string|max:2000',
        ], [
            'idDetalleOrden.required' => 'Selecciona el medicamento recibido.',
            'motivo.required' => 'Selecciona el motivo del reclamo.',
            'descripcion.required' => 'Describe el problema presentado.',
        ]);

        // Verificar que la entrega pertenezca al paciente autenticado.
        $entrega = DetalleOrdenEntrega::where(
            'idDetalleOrden',
            $request->idDetalleOrden
        )
        ->whereHas('orden', function ($query) use ($idPaciente) {
            $query->where('idPaciente', $idPaciente);
        })
        ->first();

        if (!$entrega) {
            return back()
                ->withInput()
                ->with('error', 'El medicamento seleccionado no corresponde a una entrega tuya.');
        }

        ReclamoMedicamento::create([
            'idPaciente' => $idPaciente,
            'idDetalleOrden' => $entrega->idDetalleOrden,
            'motivo' => $request->motivo,
            'descripcion' => $request->descripcion,
            'estado' => 'pendiente',
        ]);

        return redirect()
            ->route('paciente.reclamos.index')
            ->with('success', 'Tu reclamo fue registrado correctamente.');
    }
}
