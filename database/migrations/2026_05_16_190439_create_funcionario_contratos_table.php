<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionario_contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')
                  ->unique() // 1:1
                  ->constrained('funcionarios')
                  ->cascadeOnDelete();

            // Dados contratuais
            $table->date('data_admissao');
            $table->date('data_demissao')->nullable();
            $table->enum('tipo_contratacao', ['clt', 'pj', 'autonomo', 'avulso', 'estatutario'])->default('clt')->comment('Regime/Vínculo');
            $table->enum('tipo_contrato', ['indeterminado', 'determinado', 'experiencia','intermitente', 'temporario', 'aprendiz', 'estagio'])->default('indeterminado')->comment('Prazo/Termo');
            $table->enum('tipo_remuneracao', ['mensal', 'diaria', 'horaria'])->default('mensal');

            // Remuneração
            $table->decimal('salario_base', 12, 2);
            $table->decimal('valor_diaria', 12, 2)->nullable();
            $table->decimal('valor_hora', 12, 2)->nullable();
            $table->boolean('eh_diarista')->default(false);
            $table->boolean('aplica_inss')->default(true);

            // Jornada
            $table->integer('carga_horaria_semanal')->default(44);
            $table->time('horario_entrada')->default('08:00:00');
            $table->time('horario_saida')->default('17:00:00');
            $table->time('horario_almoco_inicio')->default('12:00:00');
            $table->time('horario_almoco_fim')->default('13:00:00');

            // Local
            $table->string('local_trabalho', 255)->nullable();

            // Dependentes (para IR e Salário Família)
            $table->integer('qtd_dependentes_ir')->default(0);
            $table->integer('qtd_dependentes_salario_familia')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_contratos');
    }
};
