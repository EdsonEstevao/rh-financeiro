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
       Schema::create('folha_pagamentos', function (Blueprint $table) {
            $table->id();
            // Relacionamentos
            $table->foreignId('funcionario_id')
                  ->constrained('funcionarios')
                  ->onDelete('cascade');

            $table->date('competencia'); // ex: 2026-05-01 (sempre dia 1)

            // ─── PROVENTOS ───────────────────────────────────────
            $table->decimal('salario_base', 10, 2)->default(0);
            $table->decimal('gratificacao_feriado', 10, 2)->default(0);
            // $table->decimal('dsr_hora_extra', 10, 2)->default(0);
            $table->decimal('salario_familia_hr_extra', 10, 2)->default(0);
            $table->decimal('arredondamento_provento', 10, 2)->default(0);

            // ---- Horas Extras
            $table->decimal('horas_extras_totais', 10, 2)->default(0); // Horas Extras
            $table->decimal('valor_hora_extra', 10, 2)->default(0); // Valor por hora extra



            // ─── DESCONTOS ───────────────────────────────────────
            $table->decimal('desconto_inss', 10, 2)->default(0);
            $table->decimal('vale_dia_20', 10, 2)->default(0);
            $table->decimal('vale_extra', 10, 2)->default(0);
            $table->decimal('faltas_valor', 10, 2)->default(0);
            $table->decimal('dsr_faltas', 10, 2)->default(0);
            $table->decimal('arredondamento_desconto', 10, 2)->default(0);

              // ─── CONTROLE ────────────────────────────────────────
            $table->date('quinto_dia_util')->nullable();
            $table->text('observacao')->nullable();


            $table->enum('status', ['aberta', 'fechada', 'cancelada'])->default('aberta');
            $table->timestamps();

            // Cria a constraint composta (funcionario_id + competencia)
            $table->unique(['funcionario_id', 'competencia'], 'folha_pagamentos_funcionario_competencia_unique');

            // index para pesquisa
            $table->index('competencia');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folha_pagamentos');
    }
};