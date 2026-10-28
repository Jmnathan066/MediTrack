<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SedeController extends Controller
{
    public function index()
    {
        $sedes = Sede::orderBy('nombre')->get();
        $sedeEditar = null;

        return view('sedes.sedes', compact('sedes', 'sedeEditar'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150|unique:sedes,nombre',
            'direccion' => 'required|string|max:255',
        ]);

        Sede::create($datos + ['estado' => true]);

        return redirect()
            ->route('sedes.index')
            ->with('success', 'La sede se registró correctamente.');
    }

    public function edit(int $id)
    {
        $sedes = Sede::orderBy('nombre')->get();
        $sedeEditar = Sede::findOrFail($id);

        return view('sedes.sedes', compact('sedes', 'sedeEditar'));
    }

    public function update(Request $request, int $id)
    {
        $sede = Sede::findOrFail($id);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('sedes', 'nombre')->ignore($sede->idSede, 'idSede'),
            ],
            'direccion' => 'required|string|max:255',
        ]);

        $sede->update($datos);

        // Mantiene actualizado el nombre de la sede en la sesión actual.
        if ((int) session('idSede') === (int) $sede->idSede) {
            session(['nombreSede' => $sede->nombre]);
        }

        return redirect()
            ->route('sedes.index')
            ->with('success', 'Los datos de la sede se actualizaron correctamente.');
    }

    
    public function cambiarEstado(int $id)
    {
        $sede = Sede::findOrFail($id);

        if (
            $sede->estado &&
            (int) session('idSede') === (int) $sede->idSede
        ) {
            return redirect()
                ->route('sedes.index')
                ->with(
                    'error',
                    'No puedes inactivar la sede seleccionada. Cambia primero a otra sede.'
                );
        }

        $sede->estado = ! $sede->estado;
        $sede->save();

        return redirect()
            ->route('sedes.index')
            ->with(
                'success',
                $sede->estado
                    ? 'La sede se activó correctamente.'
                    : 'La sede se inactivó correctamente. Sus medicamentos se conservaron.'
            );
    }


    public function destroy(int $id)
    {
        $sede = Sede::findOrFail($id);

        if ((int) session('idSede') === (int) $sede->idSede) {
            return redirect()
                ->route('sedes.index')
                ->with('error', 'No puedes eliminar la sede que tienes seleccionada. Cambia primero a otra sede.');
        }

        if (Medicamento::where('idSede', $sede->idSede)->exists()) {
            return redirect()
                ->route('sedes.index')
                ->with('error', 'No puedes eliminar esta sede porque tiene medicamentos asociados. Puedes inactivarla para conservar sus registros.');
        }

        $sede->delete();

        return redirect()
            ->route('sedes.index')
            ->with('success', 'La sede se eliminó correctamente.');
    }
}
