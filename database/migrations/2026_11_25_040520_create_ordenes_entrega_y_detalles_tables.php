
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_entrega', function (Blueprint $table) {
            $table->id('idOrden');

            $table->foreignId('idPaciente')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedBigInteger('idFormula');
            $table->unsignedBigInteger('idUsuario');
            $table->unsignedBigInteger('idSede');

            $table->timestamp('fechaEntrega')->useCurrent();
            $table->string('estado', 20)->default('parcial');

            $table->timestamps();

            $table->foreign('idFormula')
                ->references('idFormula')
                ->on('formulas_medicas')
                ->restrictOnDelete();

            $table->foreign('idUsuario')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            $table->foreign('idSede')
                ->references('idSede')
                ->on('sedes')
                ->restrictOnDelete();
        });

        Schema::create('detalle_orden_entrega', function (Blueprint $table) {
            $table->id('idDetalleOrden');

            $table->unsignedBigInteger('idOrden');
            $table->unsignedBigInteger('idDetalleFormula');
            $table->unsignedBigInteger('idLote');

            $table->unsignedInteger('cantidadEntregada');

            $table->timestamps();

            $table->foreign('idOrden')
                ->references('idOrden')
                ->on('ordenes_entrega')
                ->cascadeOnDelete();

            $table->foreign('idDetalleFormula')
                ->references('idDetalle')
                ->on('formula_medicamentos')
                ->restrictOnDelete();

            $table->foreign('idLote')
                ->references('idLote')
                ->on('lotes_medicamento')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_orden_entrega');
        Schema::dropIfExists('ordenes_entrega');
    }
};
