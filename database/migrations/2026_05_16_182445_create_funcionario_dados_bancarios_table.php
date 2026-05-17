<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionario_dados_bancarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')
                  ->unique() // 1:1
                  ->constrained('funcionarios')
                  ->cascadeOnDelete();

            $table->string('banco_codigo', 5);
            $table->string('banco_nome', 255);
            $table->string('agencia', 10);
            $table->string('conta', 15);
            $table->enum('tipo_conta', ['corrente', 'poupanca']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_dados_bancarios');
    }
};
