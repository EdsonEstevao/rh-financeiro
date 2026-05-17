<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionario_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')
                  ->unique() // 1:1
                  ->constrained('funcionarios')
                  ->cascadeOnDelete();

            // Documentos pessoais
            $table->string('rg', 20)->nullable();
            $table->string('orgao_expedidor_rg', 10)->nullable();
            $table->string('cpf', 14)->unique();
            $table->string('titulo_eleitor', 15)->nullable();
            $table->string('zona_eleitoral', 10)->nullable();
            $table->string('secao_eleitoral', 10)->nullable();
            $table->string('certificado_reservista', 20)->nullable();

            // Documentos trabalhistas
            $table->string('ctps_numero', 20)->nullable();
            $table->string('ctps_serie', 10)->nullable();
            $table->string('ctps_uf', 2)->nullable();
            $table->date('ctps_data_emissao')->nullable();
            $table->string('pis_pasep', 15)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_documentos');
    }
};
