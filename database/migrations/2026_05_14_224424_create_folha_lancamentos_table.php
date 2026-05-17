<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folha_lancamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folha_pagamento_id')
                  ->constrained('folha_pagamentos')
                  ->cascadeOnDelete();

            // Tipo do lançamento
            $table->enum('categoria', ['provento', 'desconto']);
            $table->string('tipo'); // hora_extra_normal, hora_extra_sabado, hora_extra_feriado, falta, gratificacao, vale, etc.
            $table->string('descricao'); // Descrição amigável

            // Valores
            $table->decimal('quantidade', 10, 2)->default(0); // horas ou dias
            $table->decimal('valor_unitario', 10, 2)->default(0);
            $table->decimal('percentual_acrescimo', 5, 2)->default(0); // 50 = 50%
            $table->decimal('valor_total', 10, 2)->default(0);

            // Informações adicionais
            $table->date('data_referencia')->nullable(); // Data do lançamento
            $table->text('observacao')->nullable();

            // Controle
            $table->boolean('automatico')->default(true); // Se foi calculado automaticamente
            $table->foreignId('criado_por')->nullable()->constrained('users');

            $table->timestamps();

            // Índices
            $table->index(['folha_pagamento_id', 'tipo']);
            $table->index(['folha_pagamento_id', 'categoria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folha_lancamentos');
    }
};
