<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ObservacionesTest extends TestCase
{
    /**
     * Test function create of class ObservacionesController.
     *
     * @test
     */
    public function test_function_create()
    {
        $response = $this->withoutExceptionHandling()->post('/observacion');
        
        $response
            ->assertStatus(201)
            ->assertSee('Observación creada exitosamente');
    }

    /**
     * Test function update of class ObservacionesController.
     *
     * @test
     */
    public function test_function_update()
    {
        $response = $this->withoutExceptionHandling()->put('/observacion');
        
        $response
            ->assertStatus(200)
            ->assertSee('Observacion actualizada exitosamente');
    }

    /**
     * Test function delete of class ObservacionesController.
     *
     * @test
     */
    public function test_function_delete()
    {
        $response = $this->withoutExceptionHandling()->delete('/observacion/{id}');
        
        $response
            ->assertStatus(200)
            ->assertSee('Observacion eliminada exitosamente');
    }

    /**
     * Test function X of class XController.
     *
     * @test
     */
    /*public function test_function_X()
    {
        $response = $this->withoutExceptionHandling()->X('/');
        
        $response
            ->assertStatus()
            ->assertSee('');
    }*/
}
