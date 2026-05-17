<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionario_enderecos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')
                  ->unique() // 1:1
                  ->constrained('funcionarios')
                  ->cascadeOnDelete();

            $table->string('cep', 9);
            $table->string('logradouro', 255);
            $table->string('numero', 10);
            $table->string('complemento', 255)->nullable();
            $table->string('bairro', 255);
            $table->string('cidade', 255);
            $table->string('estado', 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_enderecos');
    }
};
