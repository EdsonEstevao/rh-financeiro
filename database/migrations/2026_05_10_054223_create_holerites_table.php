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
        Schema::create('holerites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('folha_pagamento_id')->constrained('folhas_pagamento')->cascadeOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->cascadeOnDelete();

            $table->decimal('salario_bruto', 10, 2)->default(0);
            $table->decimal('inss_base', 10, 2)->default(0);
            $table->decimal('inss_valor', 10, 2)->default(0);
            $table->decimal('inss_aliquota_aplicada', 5, 4)->nullable();
            $table->decimal('irrf_valor', 10, 2)->default(0);
            $table->decimal('vt_valor', 10, 2)->default(0);
            $table->decimal('outros_descontos', 10, 2)->default(0);
            $table->decimal('salario_liquido', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['folha_pagamento_id', 'funcionario_id'], 'uq_holerite_folha_func');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holerites');
    }
};