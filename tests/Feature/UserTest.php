<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test function register of class UserController.
     *
     * @test
     */
    public function test_function_register()
    {
        $response = $this->withoutExceptionHandling()->post('/register');
        
        $response
            ->assertStatus(201)
            ->assertSee('Correo registrado exitosamente');
    }

    /**
     * Test function userProfile of class UserController.
     *
     * @test
     */
    public function test_function_userProfile()
    {
        $response = $this->withoutExceptionHandling()->get('/user-profile');
        
        $response
            ->assertStatus(200)
            ->assertSee('Perfil de usuario obtenido exitosamente');
    }

    /**
     * Test function listUser of class UserController.
     *
     * @test
     */
    public function test_function_listUser()
    {
        $response = $this->withoutExceptionHandling()->get('/user');
        
        $response
            ->assertStatus(200)
            ->assertSee('Lista de usuarios obtenida exitosamente');
    }

    /**
     * Test function listUserHabilitados of class UserController.
     *
     * @test
     */
    public function test_function_listUserHabilitados()
    {
        $response = $this->withoutExceptionHandling()->get('/enabled_users');
        
        $response
            ->assertStatus(200)
            ->assertSee('Lista de usuarios no nulos y habilitados obtenida exitosamente');
    }

    /**
     * Test function updateRoleEstado of class UserController.
     *
     * @test
     */
    public function test_function_updateRoleEstado()
    {
        $response = $this->withoutExceptionHandling()->put('/update');
        
        $response
            ->assertStatus(200)
            ->assertSee('Usuario actualizado exitosamente');
    }
    
}
