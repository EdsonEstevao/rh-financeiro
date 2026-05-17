<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionario_beneficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')
                  ->unique() // 1:1
                  ->constrained('funcionarios')
                  ->cascadeOnDelete();

            // Vale Transporte
            $table->boolean('vale_transporte')->default(false);
            $table->decimal('valor_vale_transporte', 8, 2)->nullable();

            // Vale Alimentação
            $table->boolean('vale_alimentacao')->default(false);
            $table->decimal('valor_vale_alimentacao', 8, 2)->nullable();

            // Planos
            $table->boolean('plano_saude')->default(false);
            $table->boolean('plano_odontologico')->default(false);

            // Outros
            $table->boolean('sexto_dia_util_mes')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_beneficios');
    }
};
