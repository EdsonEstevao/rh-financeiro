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
       Schema::create('folhas_pagamento', function (Blueprint $table) {
            $table->id();
            $table->date('competencia'); // ex: 2026-05-01 (sempre dia 1)
            $table->enum('status', ['aberta', 'fechada', 'cancelada'])->default('aberta');
            $table->timestamps();

            $table->unique(['competencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folhas_pagamento');
    }
};
