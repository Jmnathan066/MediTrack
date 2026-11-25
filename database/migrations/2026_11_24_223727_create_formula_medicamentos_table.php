
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_medicamentos', function (Blueprint $table) {

            $table->id('idDetalle');

            // Fórmula a la que pertenece el medicamento.
            $table->foreignId('idFormula')
                ->constrained('formulas_medicas', 'idFormula')
                ->cascadeOnDelete();

            // Medicamento prescrito.
            $table->foreignId('idMedicamento')
                ->constrained('medicamentos', 'idMedicamento')
                ->restrictOnDelete();

            $table->string('dosis', 100);

            $table->string('frecuencia', 150);

            $table->string('duracion', 100);

            $table->integer('cantidad');

            $table->text('indicaciones')->nullable();

            $table->timestamps();

            $table->index('idMedicamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_medicamentos');
    }
};
