<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_sesiones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_usuario');
            $table->text('token');
            $table->dateTime('fecha_creacion');
            $table->boolean('estado')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_sesiones');
    }
};
