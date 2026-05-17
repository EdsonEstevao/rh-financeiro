<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('departamento_id')->constrained('departamentos')->restrictOnDelete();
            $table->foreignId('cargo_id')->constrained('cargos')->restrictOnDelete();

            // Dados Pessoais
            $table->string('nome_completo');
            // $table->string('cpf', 14)->unique();
            // $table->string('rg', 20);
            // $table->string('orgao_expedidor_rg', 10)->nullable();

            $table->date('data_nascimento');
            $table->enum('estado_civil', ['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel']);
            $table->enum('genero', ['masculino', 'feminino', 'outro']);
            $table->string('nacionalidade')->default('brasileira');
            $table->string('naturalidade')->nullable(); // Cidade de nascimento

            // Contato
            // $table->string('telefone', 15);
            // $table->string('celular', 15);
            // $table->string('email')->unique();
            // $table->string('email_pessoal')->nullable();

            // Endereço
            // $table->string('cep', 9);
            // $table->string('logradouro');
            // $table->string('numero', 10);
            // $table->string('complemento')->nullable();
            // $table->string('bairro');
            // $table->string('cidade');
            // $table->string('estado', 2);

            // Dados Trabalhistas
            // $table->string('ctps_numero', 20)->nullable();
            // $table->string('ctps_serie', 10)->nullable();
            // $table->string('ctps_uf', 2)->nullable();
            // $table->date('ctps_data_emissao')->nullable();
            // $table->string('pis_pasep', 15)->nullable();
            // $table->string('titulo_eleitor', 15)->nullable();
            // $table->string('zona_eleitoral', 10)->nullable();
            // $table->string('secao_eleitoral', 10)->nullable();
            // $table->string('certificado_reservista', 20)->nullable();

            // Dados do Contrato
            // $table->date('data_admissao');
            // $table->date('data_demissao')->nullable();

            // $table->enum('tipo_contrato', ['indeterminado', 'determinado', 'experiencia', 'intermitente', 'temporario', 'aprendiz', 'estagio'])->nullable();  // indeterminado, determinado, experiencia, intermitente, temporario, aprendiz, estagio
            // $table->enum('tipo_contratacao', ['clt', 'pj', 'autonomo', 'avulso', 'estatutario'])->nullable(); // clt, pj, autonomo, avulso, estatutario

            // $table->enum('tipo_remuneracao', ['mensal', 'diaria', 'horaria'])->default('mensal');

            // 3) Valores-base (use apenas o que fizer sentido conforme tipo_remuneracao)
            // $table->decimal('salario_base_mensal', 12, 2)->nullable();
            // $table->decimal('valor_diaria', 12, 2)->nullable();
            // $table->unsignedSmallInteger('dias_trabalhados_mes')->default(0);
            // $table->decimal('valor_hora', 12, 2)->nullable();
            // $table->decimal('horas_trabalhadas_mes', 8, 2)->default(0);


            // 4) Valores-base (use apenas o que fizer sentido conforme tipo_remuneracao)
            // indicar se é diarista
            // $table->boolean('eh_diarista')->default(false); // ou avalie pelo tipo_contratacao



            // $table->decimal('salario_base', 12, 2);
            // $table->decimal('desconto_inss_8_porcento', 8, 2)->default(0);
            // $table->boolean('aplica_inss')->default(true);

            // $table->integer('carga_horaria_semanal')->default(44);
            // $table->time('horario_entrada')->default('08:00:00');
            // $table->time('horario_saida')->default('17:00:00');
            // $table->time('horario_almoco_inicio')->default('12:00:00');
            // $table->time('horario_almoco_fim')->default('13:00:00');

            // Benefícios
            // $table->boolean('vale_transporte')->default(false);
            // $table->decimal('valor_vale_transporte', 8, 2)->nullable();
            // $table->boolean('vale_alimentacao')->default(false);
            // $table->decimal('vale_extra', 8, 2)->default(0);
            // $table->decimal('valor_vale_alimentacao', 8, 2)->nullable();
            // $table->boolean('plano_saude')->default(false);
            // $table->boolean('plano_odontologico')->default(false);

            // Dados Bancários
            // $table->string('banco_codigo', 5);
            // $table->string('banco_nome');
            // $table->string('agencia', 10);
            // $table->string('conta', 15);
            // $table->enum('tipo_conta', ['corrente', 'poupanca']);

            // Dependentes e IR
            // $table->integer('qtd_dependentes_ir')->default(0);
            // $table->integer('qtd_dependentes_salario_familia')->default(0);
            // $table->integer('faltas')->default(0);
            // $table->decimal('dsr_faltas', 8, 2)->default(0);
            // $table->decimal('desconto_faltas', 8, 2)->default(0);
            // Proventos extras
            // $table->decimal('gratificacao_provento', 8, 2)->default(0);
            // $table->decimal('dsr_hora_extra', 8, 2)->default(0);
            // $table->decimal('salario_familia', 8, 2)->default(0);
            // $table->decimal('hora_extra', 8, 2)->default(0);
            // $table->boolean('sexto_dia_util_mes')->default(false);

            // Campos específicos para folha de pagamento
            // $table->string('local_trabalho')->nullable();

            // Valores e descontos







             // Férias (calculado automaticamente)
            $table->date('periodo_aquisitivo_inicio')->nullable();
            $table->date('periodo_aquisitivo_fim')->nullable();
            $table->date('ferias_vencimento')->nullable();
            $table->boolean('ferias_vencidas')->default(false);
            $table->boolean('ferias_em_dobro')->default(false);




            // Controle
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();

            // Auditoria
            $table->timestamps();

            // Índices
            $table->index(['departamento_id', 'cargo_id', 'ativo']);
            // $table->index(['data_admissao', 'ativo']);
            // $table->index(['ferias_vencimento', 'ativo']);
            // $table->index(['cpf', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
