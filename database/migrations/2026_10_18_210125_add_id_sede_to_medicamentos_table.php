
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->foreignId('idSede')
                ->nullable()
                ->constrained('sedes', 'idSede')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->dropForeign(['idSede']);
            $table->dropColumn('idSede');
        });
    }
};

