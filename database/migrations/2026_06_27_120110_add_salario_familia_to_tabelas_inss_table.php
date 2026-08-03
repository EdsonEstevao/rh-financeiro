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
        Schema::table('tabelas_inss', function (Blueprint $table) {
            //
            $table->decimal('limite_salario_familia', 10, 2)->nullable()->after('vigencia_fim')->comment('Limite de salário para concessão do benefício de salário-família');
            $table->decimal('valor_salario_familia', 10, 2)->nullable()->after('limite_salario_familia')->comment('Valor do benefício de salário-família');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabelas_inss', function (Blueprint $table) {
            //
            $table->dropColumn(['limite_salario_familia', 'valor_salario_familia']);
        });
    }
};