<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_sede', function (Blueprint $table) {
            $table->id('idInventario');

            $table->foreignId('idSede')
                ->constrained('sedes', 'idSede')
                ->cascadeOnDelete();

            $table->foreignId('idMedicamento')
                ->constrained('medicamentos', 'idMedicamento')
                ->cascadeOnDelete();

            $table->integer('stock')->default(0);
            $table->integer('stockMinimo')->default(0);

            $table->timestamps();

            $table->unique(
                ['idSede', 'idMedicamento'],
                'inventario_sede_medicamento_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_sede');
    }
};