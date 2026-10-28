<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InventarioService;

class MedicamentoController extends Controller
{
    public function __construct(
        private InventarioService $inventario
    ) {
    }

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

    public function store(Request $request)
    {
        $datos = $request->validate(
            InventarioService::reglasValidacion()
        );

        $this->inventario->crear($datos);

        return redirect('/medicamentos')
            ->with('success', 'Medicamento registrado correctamente.');
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

        $eliminado = $this->inventario->eliminarConConfirmacion(
            (int) $id,
            $datos['confirmacion']
        );

        if (! $eliminado) {
            return back()->withErrors([
                'confirmacion' => 'El ID ingresado no coincide.',
            ]);
        }

        return redirect('/medicamentos')
            ->with('success', 'Medicamento eliminado correctamente.');
    }
}
