
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamos_medicamentos', function (Blueprint $table) {
            $table->id('idReclamo');

            // Paciente que registra el reclamo
            $table->unsignedBigInteger('idPaciente');

            // Entrega específica sobre la que se presenta el reclamo
            $table->unsignedBigInteger('idDetalleOrden');

            $table->string('motivo', 150);
            $table->text('descripcion');

            // Estado inicial del reclamo
            $table->string('estado', 30)->default('pendiente');

            $table->timestamps();

            $table->foreign('idPaciente')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            $table->foreign('idDetalleOrden')
                ->references('idDetalleOrden')
                ->on('detalle_orden_entrega')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamos_medicamentos');
    }
};
