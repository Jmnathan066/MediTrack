<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EpsController extends Controller
{
    /**
     * Mostrar el listado y formulario de EPS.
     */
    public function index()
    {
        $eps = Eps::orderBy('nombre')->get();

        $epsEditar = null;

        return view('eps.eps', compact('eps', 'epsEditar'));
    }

    /**
     * Registrar una nueva EPS.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                'unique:eps,nombre',
            ],
            'nit' => [
                'required',
                'string',
                'max:30',
                'unique:eps,nit',
            ],
        ], [
            'nombre.required' => 'El nombre de la EPS es obligatorio.',
            'nombre.unique' => 'Ya existe una EPS registrada con ese nombre.',
            'nit.required' => 'El NIT es obligatorio.',
            'nit.unique' => 'Ya existe una EPS registrada con ese NIT.',
        ]);

        $datos['estado'] = true;

        Eps::create($datos);

        return redirect()
            ->route('eps.index')
            ->with('success', 'La EPS se registró correctamente.');
    }

    /**
     * Cargar una EPS en el formulario de edición.
     */
    public function edit(int $id)
    {
        $epsEditar = Eps::findOrFail($id);

        $eps = Eps::orderBy('nombre')->get();

        return view('eps.eps', compact('eps', 'epsEditar'));
    }

    /**
     * Actualizar los datos de una EPS.
     */
    public function update(Request $request, int $id)
    {
        $eps = Eps::findOrFail($id);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('eps', 'nombre')->ignore($eps->idEPS, 'idEPS'),
            ],
            'nit' => [
                'required',
                'string',
                'max:30',
                Rule::unique('eps', 'nit')->ignore($eps->idEPS, 'idEPS'),
            ],
        ], [
            'nombre.required' => 'El nombre de la EPS es obligatorio.',
            'nombre.unique' => 'Ya existe otra EPS registrada con ese nombre.',
            'nit.required' => 'El NIT es obligatorio.',
            'nit.unique' => 'Ya existe otra EPS registrada con ese NIT.',
        ]);

        $eps->update($datos);

        return redirect()
            ->route('eps.index')
            ->with('success', 'Los datos de la EPS se actualizaron correctamente.');
    }

    /**
     * Activar o inactivar una EPS.
     */
    public function cambiarEstado(int $id)
    {
        $eps = Eps::findOrFail($id);

        $eps->estado = !$eps->estado;
        $eps->save();

        $mensaje = $eps->estado
            ? 'La EPS fue activada correctamente.'
            : 'La EPS fue inactivada correctamente.';

        return redirect()
            ->route('eps.index')
            ->with('success', $mensaje);
    }
}
