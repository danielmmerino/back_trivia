<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juego_preguntas', function (Blueprint $table) {
            $table->id();
            $table->integer('categoria');
            $table->text('descripcion');
            $table->integer('id_dificultad');
            $table->boolean('estado')->default(1);
            $table->dateTime('fecha_creacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juego_preguntas');
    }
};
