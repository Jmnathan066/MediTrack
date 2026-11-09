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
     * Lista los medicamentos de la sede actual.
     *
     * Administrador: visualiza activos e inactivos.
     * Empresa, doctor y paciente: visualizan únicamente activos.
     */
    public function listar(?string $busqueda = null)
    {
        $idSede = $this->sedeActualId();
        $rol = session('tipoUsuario');

        $query = Medicamento::where('idSede', $idSede);

        // Los demás roles no pueden visualizar medicamentos inactivos.
        if ($rol !== 'admin') {
            $query->where('estado', true);
        }

        if ($busqueda !== null && trim($busqueda) !== '') {
            $termino = trim($busqueda);

            $query->where(function ($subquery) use ($termino) {
                $subquery->where('nombre', 'ILIKE', "%{$termino}%")
                    ->orWhere('categoria', 'ILIKE', "%{$termino}%");

                // El ID se busca como número exacto.
                if (ctype_digit($termino)) {
                    $subquery->orWhere(
                        'idMedicamento',
                        (int) $termino
                    );
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
     * Todo medicamento nuevo se registra como activo.
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
            'estado' => true,
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
     * Inactiva o reactiva un medicamento después de confirmar su ID.
     *
     * true: el medicamento queda activo.
     * false: el medicamento queda inactivo.
     * null: el ID de confirmación no coincide.
     *
     * No elimina físicamente el registro de la base de datos.
     */
    public function eliminarConConfirmacion(
        int $id,
        string $confirmacion
    ): ?bool {
        $medicamento = $this->obtener($id);

        if ($confirmacion !== (string) $medicamento->idMedicamento) {
            return null;
        }

        // Invierte el estado actual.
        $medicamento->estado = ! $medicamento->estado;

        $medicamento->save();

        return $medicamento->estado;
    }

    /**
     * Indica si el stock está en el mínimo o por debajo de él.
     */
    public function tieneStockBajo(Medicamento $medicamento): bool
    {
        return $medicamento->stock <= $medicamento->stockMinimo;
    }
}
