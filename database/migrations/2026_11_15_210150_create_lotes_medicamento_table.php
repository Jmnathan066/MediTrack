<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_medicamento', function (Blueprint $table) {
            $table->id('idLote');

            $table->foreignId('idInventario')
                ->constrained('inventario_sede', 'idInventario')
                ->cascadeOnDelete();

            $table->string('codigoLote')->unique();

            $table->date('fechaIngreso');
            $table->date('fechaVencimiento');

            $table->integer('cantidadInicial')->default(0);
            $table->integer('stockDisponible')->default(0);

            $table->timestamps();

            $table->index(
                ['idInventario', 'fechaVencimiento'],
                'lotes_inventario_vencimiento_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes_medicamento');
    }
};
