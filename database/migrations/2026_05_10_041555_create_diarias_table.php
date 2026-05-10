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
         Schema::create('diarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade');
            $table->date('data');
            $table->decimal('valor', 12, 2);
            $table->enum('status', ['registrada','aprovada','paga','rejeitada'])->default('registrada');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['funcionario_id', 'data']); // não pode ter 2 diárias no mesmo dia/fun
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diarias');
    }
};