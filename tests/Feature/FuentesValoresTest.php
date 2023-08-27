<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FuentesValoresTest extends TestCase
{
    /**
     * Test function listSourcesValues of class FuentesValoresController.
     *
     * @test
     */
    public function test_function_listSourcesValues()
    {
        $response = $this->withoutExceptionHandling()->get('/fuentes');
        //$response = $this->get('/fuentes');

        $response
            ->assertStatus(404)
            ->assertSee('Lista de fuentes y valores obtenida exitosamente.');
    }

    /**
     * Test function listSourcesValues (error) of class FuentesValoresController.
     *
     * @test
     */
    public function test_function_listSourcesValues_error()
    {
        $response = $this->withoutExceptionHandling()->get('/fuentes');
        
        $response
            ->assertStatus(500)
            ->assertSee('Hubo un error al obtener la lista de fuentes y valores.');
    }
}
