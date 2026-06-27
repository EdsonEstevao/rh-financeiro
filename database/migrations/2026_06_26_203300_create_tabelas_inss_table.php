<?php

// database/migrations/2026_06_26_000001_create_tabelas_inss_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabelas_inss', function (Blueprint $table) {
            $table->id();
            $table->year('ano_vigencia');
            $table->string('descricao')->nullable()->comment('Ex: Tabela 2025, Reajuste Jan/2025');
            $table->boolean('ativo')->default(true);

            // Datas de vigência
            $table->date('vigencia_inicio');
            $table->date('vigencia_fim')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabelas_inss');
    }
};
