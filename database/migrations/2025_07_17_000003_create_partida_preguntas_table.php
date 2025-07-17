<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partida_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sesion');
            $table->foreignId('id_pregunta');
            $table->boolean('esCorrecto')->nullable();
            $table->dateTime('fecha_creacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partida_preguntas');
    }
};
