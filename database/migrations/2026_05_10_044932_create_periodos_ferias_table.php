<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('periodos_ferias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')
                ->constrained('funcionarios')
                ->cascadeOnDelete();

            $table->date('data_inicio');
            $table->date('data_fim');

            // NOVO: tipo para diferenciar origem
            $table->enum('tipo', ['prevista', 'programada', 'efetiva'])
                ->default('programada')
                ->comment('prevista=gerada na admissão; programada=agendada pelo RH; efetiva=já gozada');

             $table->boolean('abono_pecuniario')->default(false); // venda de 1/3?

            // Ex.: férias marcadas, aprovadas, gozadas, canceladas
            $table->enum('status', ['planejada', 'aprovada', 'gozada', 'cancelada'])
                ->default('planejada');

            $table->text('observacao')->nullable();
            $table->unsignedTinyInteger('numero_periodo')->default(1); // caso empresa permita dividir férias em até 3 vezes

            $table->timestamps();

            // Evita duplicar exatamente o mesmo período para o mesmo funcionário
            $table->unique(['funcionario_id', 'data_inicio', 'data_fim'], 'uq_periodos_ferias_func_inicio_fim');

            // Index para consultas por tipo
            $table->index(['funcionario_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_ferias');
    }
};
