<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProblemasOportunidadesTest extends TestCase
{
    /**
     * Test function create of class ProblemasOportunidadesController.
     *
     * @test
     */
    public function test_function_create()
    {
        $response = $this->withoutExceptionHandling()->post('/problema');
        
        $response
            ->assertStatus(201)
            ->assertSee('Problema opoortunidad creada exitosamente');
    }

    /**
     * Test function create(else) of class ProblemasOportunidadesController.
     *
     * @test
     */
    public function test_function_create_2()
    {
        $response = $this->withoutExceptionHandling()->post('/problema');
        
        $response
            ->assertStatus(404)
            ->assertSee('No se encontro el plan');
    }

    /**
     * Test function update of class ProblemasOportunidadesController.
     *
     * @test
     */
    public function test_function_update()
    {
        $response = $this->withoutExceptionHandling()->put('/problema');
        
        $response
            ->assertStatus(200)
            ->assertSee('Problema oportunidad actualizada exitosamente');
    }

    /**
     * Test function delete of class ProblemasOportunidadesController.
     *
     * @test
     */
    public function test_function_delete()
    {
        $response = $this->withoutExceptionHandling()->delete('/problema/{id}');
        
        $response
            ->assertStatus(200)
            ->assertSee('Problema oportunidad eliminada exitosamente');
    }
    
}
