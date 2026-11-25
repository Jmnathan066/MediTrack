<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observaciones_medicas', function (Blueprint $table) {
            $table->id('idObservacion');

            // Paciente al que pertenece la observación.
            $table->foreignId('idPaciente')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            // Doctor que registra la observación.
            $table->foreignId('idDoctor')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->text('observacion');

            $table->timestamps();

            $table->index(['idPaciente', 'created_at']);
            $table->index(['idDoctor', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observaciones_medicas');
    }
};
