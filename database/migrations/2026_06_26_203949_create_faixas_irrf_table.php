<?php

// database/migrations/2026_06_26_000004_create_faixas_irrf_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faixas_irrf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabela_irrf_id')->constrained('tabelas_irrf')->cascadeOnDelete();
            $table->unsignedTinyInteger('ordem')->comment('Ordem da faixa (1,2,3,4)');

            $table->decimal('teto', 10, 2)->comment('Valor máximo desta faixa');
            $table->decimal('aliquota', 5, 4)->comment('Ex: 0.0750 para 7,5%');
            $table->decimal('deducao', 10, 2)->default(0)->comment('Parcela a deduzir');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faixas_irrf');
    }
};