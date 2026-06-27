<?php

// database/migrations/2026_06_26_000002_create_faixas_inss_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faixas_inss', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabela_inss_id')->constrained('tabelas_inss')->cascadeOnDelete();
            $table->unsignedTinyInteger('ordem')->comment('Ordem da faixa (1,2,3,4)');

            $table->decimal('teto', 10, 2)->comment('Valor máximo desta faixa');
            $table->decimal('aliquota', 5, 4)->comment('Ex: 0.0750 para 7,5%');
            $table->decimal('deducao', 10, 2)->default(0)->comment('Parcela a deduzir (cálculo simplificado)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faixas_inss');
    }
};