<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funcionario_contatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')
                  ->unique() // 1:1
                  ->constrained('funcionarios')
                  ->cascadeOnDelete();

            $table->string('telefone', 15)->nullable();
            $table->string('celular', 15)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('email_pessoal', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario_contatos');
    }
};
