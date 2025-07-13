<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juego_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pregunta');
            $table->text('descripcion');
            $table->boolean('esCorrecto');
            $table->boolean('estado')->default(1);
            $table->dateTime('fecha_creacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juego_opciones');
    }
};
