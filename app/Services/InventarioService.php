<?php

namespace App\Services;

use App\Models\Medicamento;
use App\Models\Sede;

class InventarioService
{
    /**
     * Obtiene el ID de la sede activa seleccionada en la sesión.
     */
    private function sedeActualId(): int
    {
        $idSede = session('idSede');

        $sede = null;

        if ($idSede) {
            $sede = Sede::where('estado', true)
                ->where('idSede', $idSede)
                ->first();
        }

        // Si todavía no se ha seleccionado una sede, usa la primera activa.
        if (! $sede) {
            $sede = Sede::where('estado', true)
                ->orderBy('nombre')
                ->first();
        }

        // No permite gestionar medicamentos si no hay sedes activas.
        abort_if(! $sede, 403, 'No hay sedes activas disponibles.');

        session([
            'idSede' => $sede->idSede,
            'nombreSede' => $sede->nombre,
        ]);

        return (int) $sede->idSede;
    }

    /**
     * Reglas de validación para crear y actualizar medicamentos.
     */
    public static function reglasValidacion(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fechaVencimiento' => 'required|date',
            'stock' => 'required|integer|min:0',
            'stockMinimo' => 'required|integer|min:0',
            'valor' => 'required|numeric|min:0',
        ];
    }

    /**
     * Lista exclusivamente los medicamentos de la sede actual.
     */
    public function listar(?string $busqueda = null)
    {
        $idSede = $this->sedeActualId();

        $query = Medicamento::where('idSede', $idSede);

        if ($busqueda !== null && trim($busqueda) !== '') {
            $termino = trim($busqueda);

            $query->where(function ($subquery) use ($termino) {
                $subquery->where('nombre', 'ILIKE', "%{$termino}%")
                    ->orWhere('categoria', 'ILIKE', "%{$termino}%");

                // El ID se busca como número exacto.
                if (ctype_digit($termino)) {
                    $subquery->orWhere('idMedicamento', (int) $termino);
                }
            });
        }

        return $query->orderBy('idMedicamento')->get();
    }

    /**
     * Busca un medicamento solo dentro de la sede actual.
     */
    public function obtener(int $id): Medicamento
    {
        return Medicamento::where('idSede', $this->sedeActualId())
            ->where('idMedicamento', $id)
            ->firstOrFail();
    }

    /**
     * Crea el medicamento asignándolo a la sede actual.
     * El ID de sede no se toma de los datos enviados por el formulario.
     */
    public function crear(array $datos): Medicamento
    {
        $datosPermitidos = [
            'nombre' => $datos['nombre'],
            'categoria' => $datos['categoria'],
            'descripcion' => $datos['descripcion'],
            'fechaVencimiento' => $datos['fechaVencimiento'],
            'stock' => $datos['stock'],
            'stockMinimo' => $datos['stockMinimo'],
            'valor' => $datos['valor'],
            'idSede' => $this->sedeActualId(),
        ];

        return Medicamento::create($datosPermitidos);
    }

    /**
     * Actualiza un medicamento perteneciente a la sede actual.
     */
    public function actualizar(int $id, array $datos): Medicamento
    {
        $medicamento = $this->obtener($id);

        $medicamento->update([
            'nombre' => $datos['nombre'],
            'categoria' => $datos['categoria'],
            'descripcion' => $datos['descripcion'],
            'fechaVencimiento' => $datos['fechaVencimiento'],
            'stock' => $datos['stock'],
            'stockMinimo' => $datos['stockMinimo'],
            'valor' => $datos['valor'],
        ]);

        return $medicamento;
    }

    /**
     * Elimina un medicamento de la sede actual tras confirmar su ID.
     */
    public function eliminarConConfirmacion(
        int $id,
        string $confirmacion
    ): bool {
        $medicamento = $this->obtener($id);

        if ($confirmacion !== (string) $medicamento->idMedicamento) {
            return false;
        }

        $medicamento->delete();

        return true;
    }

    /**
     * Indica si el stock está en el mínimo o por debajo de él.
     */
    public function tieneStockBajo(Medicamento $medicamento): bool
    {
        return $medicamento->stock <= $medicamento->stockMinimo;
    }
}
