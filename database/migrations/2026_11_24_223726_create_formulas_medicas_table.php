
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas_medicas', function (Blueprint $table) {

            $table->id('idFormula');

            // Paciente que recibe la fórmula.
            $table->foreignId('idPaciente')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            // Doctor que emite la fórmula.
            $table->foreignId('idDoctor')
                ->constrained('users', 'id')
                ->restrictOnDelete();

            $table->date('fechaFormula');

            $table->text('observaciones')->nullable();

            $table->string('estado', 20)->default('activa');

            $table->timestamps();

            $table->index(['idPaciente', 'fechaFormula']);
            $table->index(['idDoctor', 'fechaFormula']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas_medicas');
    }
};
