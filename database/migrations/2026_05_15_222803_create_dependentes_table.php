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
       Schema::create('dependentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios');
            $table->string('nome_completo');
            $table->date('data_nascimento');
            $table->string('parentesco'); // filho, enteado, tutelado
            $table->boolean('invalido')->default(false);
            $table->boolean('ativo')->default(true); // para salário família
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependentes');
    }
};