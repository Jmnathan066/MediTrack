<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            $medicamentos = DB::table('medicamentos')
                ->orderBy('idMedicamento')
                ->get();

            foreach ($medicamentos as $medicamento) {

                if ($medicamento->idSede === null) {
                    throw new RuntimeException(
                        "El medicamento {$medicamento->idMedicamento} no tiene una sede asignada."
                    );
                }

                // Buscar si ya existe el inventario de esa sede.
                $inventario = DB::table('inventario_sede')
                    ->where('idSede', $medicamento->idSede)
                    ->where('idMedicamento', $medicamento->idMedicamento)
                    ->first();

                if ($inventario) {

                    $idInventario = $inventario->idInventario;

                    DB::table('inventario_sede')
                        ->where('idInventario', $idInventario)
                        ->update([
                            'stock' => $medicamento->stock,
                            'stockMinimo' => $medicamento->stockMinimo,
                            'estado' => $medicamento->estado ?? true,
                            'updated_at' => now(),
                        ]);

                } else {

                    $idInventario = DB::table('inventario_sede')
                        ->insertGetId([
                            'idSede' => $medicamento->idSede,
                            'idMedicamento' => $medicamento->idMedicamento,
                            'stock' => $medicamento->stock,
                            'stockMinimo' => $medicamento->stockMinimo,
                            'estado' => $medicamento->estado ?? true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ], 'idInventario');
                }

                // Crear el primer lote con las existencias actuales.
                DB::table('lotes_medicamento')->insert([
                    'idInventario' => $idInventario,
                    'codigoLote' => 'MIG-'
                        . str_pad(
                            $medicamento->idMedicamento,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),
                    'fechaIngreso' => now()->toDateString(),
                    'fechaVencimiento' => $medicamento->fechaVencimiento,
                    'cantidadInicial' => $medicamento->stock,
                    'stockDisponible' => $medicamento->stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // No se eliminan los datos migrados automáticamente.
        // Se conservan para evitar pérdidas accidentales.
    }
};
