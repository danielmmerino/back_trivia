<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_question_requires_four_options_and_one_correct(): void
    {
        $payload = [
            'pregunta' => '¿Cuál es la capital de Ecuador?',
            'id_categoria' => 1,
            'id_dificultad' => 1,
            'opciones' => [
                [ 'opcion' => 'Quito', 'esCorrecta' => 'true' ],
                [ 'opcion' => 'Ambato', 'esCorrecta' => 'false' ],
                [ 'opcion' => 'Guayaquil', 'esCorrecta' => 'false' ],
                [ 'opcion' => 'Riobamba', 'esCorrecta' => 'false' ],
            ],
        ];

        $response = $this->postJson('/api/crear_pregunta', $payload);
        $response->assertStatus(201);

        $this->assertDatabaseHas('juego_preguntas', [
            'descripcion' => '¿Cuál es la capital de Ecuador?',
            'categoria' => 1,
            'id_dificultad' => 1,
            'estado' => 1,
        ]);
    }
}
