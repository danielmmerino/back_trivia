<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partida_resultados', function (Blueprint $table) {
            $table->id();
            $table->integer('idPartida')->nullable();
            $table->integer('respuestas_correctas')->nullable();
            $table->integer('respuestas_incorrectas')->nullable();
            $table->string('nickname', 200)->nullable();
            $table->dateTime('fecha_creacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partida_resultados');
    }
};
