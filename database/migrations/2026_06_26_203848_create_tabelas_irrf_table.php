<?php

// database/migrations/2026_06_26_000003_create_tabelas_irrf_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabelas_irrf', function (Blueprint $table) {
            $table->id();
            $table->year('ano_vigencia')->comment('Ano de vigência da tabela');
            $table->string('descricao')->nullable()->comment('Descrição da tabela');
            $table->boolean('ativo')->default(true);

            $table->date('vigencia_inicio')->comment('Data de início da vigência');
            $table->date('vigencia_fim')->nullable()->comment('Data de término da vigência');

            // Deduções base
            $table->decimal('deducao_dependente', 10, 2)->default(189.59)
                  ->comment('Valor a deduzir por dependente');
            $table->decimal('deducao_pensao', 10, 2)->default(0)
                  ->comment('Dedução de pensão alimentícia (se aplicável)');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabelas_irrf');
    }
};