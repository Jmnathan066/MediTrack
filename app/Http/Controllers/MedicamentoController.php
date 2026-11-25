<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InventarioService;

class MedicamentoController extends Controller
{
    public function __construct(
        private InventarioService $inventario
    ) {}

    public function listarMedicamentos(Request $request)
    {
        $busqueda = $request->input('busqueda');

        $medicamentos = $this->inventario->listar($busqueda);

        return view(
            'medicamentos.lista',
            compact('medicamentos', 'busqueda')
        );
    }

    public function mostrarFormulario()
    {
        return view('medicamentos.crear');
    }

    /**
     * Guarda un medicamento junto con su lote.
     */
    public function store(Request $request)
    {
        $datos = $request->validate(
            InventarioService::reglasValidacionLote()
        );

        $this->inventario->guardarConLote($datos);

        return redirect('/medicamentos')
            ->with('success', 'Medicamento y lote registrados correctamente.');
    }

    /**
     * Detecta si el medicamento ya existe en el catálogo
     * y si tiene inventario en la sede actual.
     */
    public function detectarMedicamento(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $resultado = $this->inventario->detectarMedicamento(
            $datos['nombre']
        );

        return response()->json($resultado);
    }

    public function mostrarEditar($id)
    {
        $medicamento = $this->inventario->obtener((int) $id);

        return view('medicamentos.editar', compact('medicamento'));
    }

    public function actualizarMedicamento(Request $request, $id)
    {
        $datos = $request->validate(
            InventarioService::reglasValidacion()
        );

        $this->inventario->actualizar((int) $id, $datos);

        return redirect('/medicamentos')
            ->with('success', 'Medicamento actualizado correctamente.');
    }

    public function mostrarEliminar($id)
    {
        $medicamento = $this->inventario->obtener((int) $id);

        return view('medicamentos.eliminar', compact('medicamento'));
    }

    public function eliminarMedicamento(Request $request, $id)
    {
        $datos = $request->validate([
            'confirmacion' => 'required|string',
        ]);

        $estado = $this->inventario->eliminarConConfirmacion(
            (int) $id,
            $datos['confirmacion']
        );

        if ($estado === null) {
            return back()->withErrors([
                'confirmacion' => 'El ID ingresado no coincide.',
            ]);
        }

        $mensaje = $estado
            ? 'Medicamento reactivado correctamente.'
            : 'Medicamento inactivado correctamente.';

        return redirect('/medicamentos')->with('success', $mensaje);
    }

    public function cambiarEstadoLote(int $idMedicamento, int $idLote)
{
    $estado = $this->inventario->cambiarEstadoLote(
        $idMedicamento,
        $idLote
    );

    $mensaje = $estado
        ? 'Lote reactivado correctamente.'
        : 'Lote inactivado correctamente.';

    return redirect('/medicamentos')->with('success', $mensaje);
}

/**
 * Consulta la disponibilidad de un medicamento
 * en todas las sedes activas.
 */
public function consultarDisponibilidad(int $id)
{
    $disponibilidad = $this->inventario
        ->consultarDisponibilidadPorSede($id);

    return response()->json($disponibilidad);
}

}
