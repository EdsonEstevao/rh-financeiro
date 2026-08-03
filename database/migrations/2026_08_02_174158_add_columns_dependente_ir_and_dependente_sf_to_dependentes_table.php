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
        Schema::table('dependentes', function (Blueprint $table) {
            // Adiciona as colunas para dependentes IR e dependentes SF do tipo boolean
            $table->boolean('dependente_ir')->default(false)->after('invalido')->comment("Dependente para Imposto de Renda");
            $table->boolean('dependente_sf')->default(false)->after('dependente_ir')->comment("Dependente para Salário Família");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dependentes', function (Blueprint $table) {
            // Remove as colunas dependentes IR e dependentes SF
            $table->dropColumn(['dependente_ir', 'dependente_sf']);
        });
    }
};