<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pregunta_and_opcion_length_validation(): void
    {
        $payload = [
            'pregunta' => str_repeat('a', 256),
            'id_categoria' => 1,
            'id_dificultad' => 1,
            'opciones' => [
                ['opcion' => 'valid', 'esCorrecta' => 'true'],
                ['opcion' => 'valid', 'esCorrecta' => 'false'],
                ['opcion' => 'valid', 'esCorrecta' => 'false'],
                ['opcion' => 'valid', 'esCorrecta' => 'false'],
            ],
        ];

        $response = $this->postJson('/api/crear_pregunta', $payload);

        $response->assertStatus(422);
    }
}
