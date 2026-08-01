<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('funcionario_contratos', function (Blueprint $table) {
            //
            $table->json('dias_trabalho')->nullable()->after('carga_horaria_semanal')->comment('Dias da Semana trabalhados: 1=Seg, 2=Ter, 3=Qua, 4=Qui, 5=Sex, 6=Sab');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcionario_contratos', function (Blueprint $table) {
            //
            $table->dropColumn('dias_trabalho');
        });
    }
};
