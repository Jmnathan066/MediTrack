<?php

namespace App\Services;

use App\Models\Medicamento;
use App\Models\Sede;
use App\Models\InventarioSede;
use App\Models\LoteMedicamento;
use Illuminate\Support\Facades\DB;

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

        if (! $sede) {
            $sede = Sede::where('estado', true)
                ->orderBy('nombre')
                ->first();
        }

        abort_if(! $sede, 403, 'No hay sedes activas disponibles.');

        session([
            'idSede' => $sede->idSede,
            'nombreSede' => $sede->nombre,
        ]);

        return (int) $sede->idSede;
    }

    /**
     * Reglas de validación para los datos generales del medicamento.
     */
    public static function reglasValidacion(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'valor' => 'required|numeric|min:0',
        ];
    }

    /**
     * Reglas de validación para registrar un medicamento con su lote.
     */
    public static function reglasValidacionLote(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'valor' => 'required|numeric|min:0',

            'codigoLote' => 'required|string|max:255|unique:lotes_medicamento,codigoLote',
            'fechaIngreso' => 'required|date',
            'fechaVencimiento' => 'required|date',
            'cantidad' => 'required|integer|min:0',
            'stockMinimo' => 'required|integer|min:0',
        ];
    }

    /**
     * Lista los medicamentos de la sede actual.
     *
     * Administrador: visualiza activos e inactivos.
     * Los demás roles: visualizan únicamente activos.
     */

/**
 * Lista los medicamentos que tienen inventario en la sede actual.
 *
 * Administrador: visualiza activos e inactivos.
 * Los demás roles: visualizan únicamente activos.
 */
public function listar(?string $busqueda = null)
{
    $idSede = $this->sedeActualId();
    $rol = session('tipoUsuario');

    $query = Medicamento::query()
        ->whereHas('inventarios', function ($subquery) use ($idSede) {
            $subquery->where('idSede', $idSede);
        })
        ->with([
            'inventarios' => function ($subquery) use ($idSede) {
                $subquery->where('idSede', $idSede)
                    ->with('lotes');
            }
        ]);

    if ($rol !== 'admin') {
        $query->where('estado', true);
    }

    if ($busqueda !== null && trim($busqueda) !== '') {
        $termino = trim($busqueda);

        $query->where(function ($subquery) use ($termino) {
            $subquery->where('nombre', 'ILIKE', "%{$termino}%")
                ->orWhere('categoria', 'ILIKE', "%{$termino}%");

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
        $idSede = $this->sedeActualId();

        return Medicamento::where('idMedicamento', $id)
            ->whereHas('inventarios', function ($query) use ($idSede) {
                $query->where('idSede', $idSede);
            })
            ->with([
                'inventarios' => function ($query) use ($idSede) {
                    $query->where('idSede', $idSede)
                        ->with('lotes');
                }
            ])
            ->firstOrFail();
    }

    /**
     * Método anterior de creación.
     * Se conserva durante la transición al sistema de lotes.
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
     * Registra un medicamento y su primer lote,
     * o agrega un nuevo lote a un medicamento existente.
     *
     * Todo se ejecuta en una transacción para evitar
     * registros incompletos si ocurre algún error.
     */
    public function guardarConLote(array $datos): Medicamento
    {
        return DB::transaction(function () use ($datos) {

            $idSede = $this->sedeActualId();

            $nombre = trim($datos['nombre']);

            // Buscar el medicamento en el catálogo general.
            $medicamento = Medicamento::whereRaw(
                'LOWER(TRIM(nombre)) = LOWER(?)',
                [$nombre]
            )
                ->orderBy('idMedicamento')
                ->lockForUpdate()
                ->first();

            // Caso 1: el medicamento no existe en el catálogo.
            if (! $medicamento) {
                $medicamento = Medicamento::create([
                    'nombre' => $nombre,
                    'categoria' => $datos['categoria'],
                    'descripcion' => $datos['descripcion'],
                    'fechaVencimiento' => $datos['fechaVencimiento'],
                    'stock' => $datos['cantidad'],
                    'stockMinimo' => $datos['stockMinimo'],
                    'valor' => $datos['valor'],
                    'idSede' => $idSede,
                    'estado' => true,
                ]);
            }

            // Buscar el inventario del medicamento en la sede actual.
            $inventario = InventarioSede::where('idSede', $idSede)
                ->where('idMedicamento', $medicamento->idMedicamento)
                ->lockForUpdate()
                ->first();

            // Caso 2: existe el medicamento, pero no tiene inventario
            // en esta sede. Se crea su inventario.
            if (! $inventario) {
                $inventario = InventarioSede::create([
                    'idSede' => $idSede,
                    'idMedicamento' => $medicamento->idMedicamento,
                    'stock' => 0,
                    'stockMinimo' => $datos['stockMinimo'],
                    'estado' => true,
                ]);
            } else {
                $inventario->stockMinimo = $datos['stockMinimo'];
                $inventario->save();
            }

            // Registrar el lote correspondiente.
            LoteMedicamento::create([
                'idInventario' => $inventario->idInventario,
                'codigoLote' => $datos['codigoLote'],
                'fechaIngreso' => $datos['fechaIngreso'],
                'fechaVencimiento' => $datos['fechaVencimiento'],
                'cantidadInicial' => $datos['cantidad'],
                'stockDisponible' => $datos['cantidad'],
            ]);

            // Calcular el stock total sumando todos los lotes.
            $stockTotal = $inventario->lotes()
                ->sum('stockDisponible');

            $inventario->stock = $stockTotal;
            $inventario->save();

            // Compatibilidad temporal con la estructura anterior:
            // actualizar los campos antiguos cuando el medicamento
            // pertenece originalmente a esta misma sede.
            if ((int) $medicamento->idSede === $idSede) {
                $fechaVencimiento = $inventario->lotes()
                    ->where('stockDisponible', '>', 0)
                    ->min('fechaVencimiento');

                $medicamento->stock = $stockTotal;
                $medicamento->stockMinimo = $inventario->stockMinimo;

                if ($fechaVencimiento) {
                    $medicamento->fechaVencimiento = $fechaVencimiento;
                }

                $medicamento->save();
            }

            return $medicamento;
        });
    }

    /**
     * Actualiza los datos generales de un medicamento.
     */
    public function actualizar(int $id, array $datos): Medicamento
    {
        $medicamento = $this->obtener($id);

        $medicamento->update([
            'nombre' => $datos['nombre'],
            'categoria' => $datos['categoria'],
            'descripcion' => $datos['descripcion'],
            'valor' => $datos['valor'],
        ]);

        return $medicamento;
    }

  /**
 * Inactiva o reactiva el inventario de la sede actual.
 */
public function eliminarConConfirmacion(
    int $id,
    string $confirmacion
): ?bool {
    $medicamento = $this->obtener($id);

    if ($confirmacion !== (string) $medicamento->idMedicamento) {
        return null;
    }

    $inventario = $medicamento->inventarios->first();

    if (!$inventario) {
        return null;
    }

    $inventario->estado = !$inventario->estado;
    $inventario->save();

    return $inventario->estado;
}

/**
 * Activa o inactiva un lote de la sede actual.
 */
/**
 * Activa o inactiva un lote de la sede actual.
 */
public function cambiarEstadoLote(int $idMedicamento, int $idLote): bool
{
    $medicamento = $this->obtener($idMedicamento);
    $inventario = $medicamento->inventarios->first();

    $lote = $inventario->lotes()
        ->where('idLote', $idLote)
        ->firstOrFail();

    $lote->estado = !$lote->estado;
    $lote->save();

    $inventario->stock = $inventario->lotes()
        ->where('estado', true)
        ->sum('stockDisponible');

    $inventario->save();

    return $lote->estado;
}

    /**
     * Indica si el stock está en el mínimo o por debajo de él.
     */
    public function tieneStockBajo(int $id): bool
    {
        $medicamento = $this->obtener($id);
        $inventario = $medicamento->inventarios->first();

        if (!$inventario) {
            return false;
        }

        return $inventario->stock <= $inventario->stockMinimo;
    }

    /**
     * Comprueba si un medicamento existe en el catálogo
     * y si ya tiene inventario en la sede seleccionada.
     */
    public function detectarMedicamento(string $nombre): array
    {
        $nombre = trim($nombre);
        $idSede = $this->sedeActualId();

        $medicamento = Medicamento::whereRaw(
            'LOWER(TRIM(nombre)) = LOWER(?)',
            [$nombre]
        )
            ->orderBy('idMedicamento')
            ->first();

        if (! $medicamento) {
            return [
                'tipo' => 'nuevo',
                'mensaje' => 'Este medicamento aún no está registrado. Se creará su primer registro y podrás ingresar los datos de su primer lote.',
                'medicamento' => null,
            ];
        }

        $inventario = InventarioSede::where('idSede', $idSede)
            ->where('idMedicamento', $medicamento->idMedicamento)
            ->first();

        $datosMedicamento = [
            'idMedicamento' => $medicamento->idMedicamento,
            'nombre' => $medicamento->nombre,
            'categoria' => $medicamento->categoria,
            'descripcion' => $medicamento->descripcion,
            'valor' => $medicamento->valor,
        ];

        if ($inventario) {
            return [
                'tipo' => 'existente',
                'mensaje' => 'Este medicamento ya está registrado en esta sede. Se agregará un nuevo lote a su inventario.',
                'medicamento' => $datosMedicamento,
            ];
        }

        return [
            'tipo' => 'otra_sede',
            'mensaje' => 'Este medicamento ya existe en el catálogo, pero aún no tiene inventario en esta sede. Se creará su inventario y su primer lote.',
            'medicamento' => $datosMedicamento,
        ];
    }

    /**
 * Consulta la disponibilidad de un medicamento
 * en todas las sedes activas.
 */
public function consultarDisponibilidadPorSede(int $idMedicamento): array
{
    $medicamento = Medicamento::where('idMedicamento', $idMedicamento)
        ->where('estado', true)
        ->firstOrFail();

    $sedes = Sede::where('estado', true)
        ->orderBy('nombre')
        ->get();

    $inventarios = InventarioSede::where('idMedicamento', $medicamento->idMedicamento)
        ->whereIn('idSede', $sedes->pluck('idSede'))
        ->with([
            'lotes' => function ($query) {
                $query->where('estado', true);
            }
        ])
        ->get()
        ->keyBy('idSede');

    return $sedes->map(function ($sede) use ($inventarios) {

        $inventario = $inventarios->get($sede->idSede);

        $stock = 0;
        $stockMinimo = 0;
        $estadoDisponibilidad = 'Sin inventario';

        if ($inventario) {

            $stockMinimo = (int) $inventario->stockMinimo;

            if (!$inventario->estado) {
                $estadoDisponibilidad = 'Inventario inactivo';
            } else {

                $stock = (int) $inventario->lotes->sum('stockDisponible');

                if ($stock <= 0) {
                    $estadoDisponibilidad = 'Agotado';
                } elseif ($stock <= $stockMinimo) {
                    $estadoDisponibilidad = 'Existencias bajas';
                } else {
                    $estadoDisponibilidad = 'Disponible';
                }
            }
        }

        return [
            'idSede' => $sede->idSede,
            'nombre' => $sede->nombre,
            'direccion' => $sede->direccion,
            'stock' => $stock,
            'stockMinimo' => $stockMinimo,
            'estado' => $estadoDisponibilidad,
        ];

    })->toArray();
}


/**
 * Descuenta medicamentos de una sede específica,
 * utilizando primero los lotes próximos a vencer (FEFO).
 */
public function descontarStockPorLotes(
    int $idSede,
    int $idMedicamento,
    int $cantidad
): array {
    return DB::transaction(function () use (
        $idSede,
        $idMedicamento,
        $cantidad
    ) {
        $inventario = InventarioSede::where('idSede', $idSede)
            ->where('idMedicamento', $idMedicamento)
            ->where('estado', true)
            ->lockForUpdate()
            ->first();

        if (!$inventario) {
            throw new \Exception(
                'El medicamento no tiene inventario activo en la sede seleccionada.'
            );
        }

        $lotes = LoteMedicamento::where('idInventario', $inventario->idInventario)
            ->where('estado', true)
            ->where('stockDisponible', '>', 0)
            ->whereDate('fechaVencimiento', '>=', now()->toDateString())
            ->orderBy('fechaVencimiento')
            ->lockForUpdate()
            ->get();

        $stockDisponible = (int) $lotes->sum('stockDisponible');

        if ($stockDisponible < $cantidad) {
            throw new \Exception(
                "Stock insuficiente. Disponible: {$stockDisponible}. Solicitado: {$cantidad}."
            );
        }

        $pendiente = $cantidad;
        $lotesUtilizados = [];

        foreach ($lotes as $lote) {
            if ($pendiente <= 0) {
                break;
            }

            $descontar = min(
                $pendiente,
                (int) $lote->stockDisponible
            );

            $lote->stockDisponible -= $descontar;
            $lote->save();

            $lotesUtilizados[] = [
                'idLote' => $lote->idLote,
                'cantidadEntregada' => $descontar,
            ];

            $pendiente -= $descontar;
        }

        $inventario->stock = LoteMedicamento::where(
            'idInventario',
            $inventario->idInventario
        )
            ->where('estado', true)
            ->sum('stockDisponible');

        $inventario->save();

        return $lotesUtilizados;
    });
}

}
