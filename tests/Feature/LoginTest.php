<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * Test function login(if Hash::check) of class LoginController.
     *
     * @test
     */
    public function test_function_login_1()
    {
        $response = $this->withoutExceptionHandling()->post('/login');
        
        $response
            ->assertStatus(200)
            ->assertSee('Usuario logueado');
    }

    /**
     * Test function login(else Hash::check) of class LoginController.
     *
     * @test
     */
    public function test_function_login_2()
    {
        $response = $this->withoutExceptionHandling()->post('/login');
        
        $response
            ->assertStatus(401)
            ->assertSee('Credenciales inválidas(password)');
    }

    /**
     * Test function login(else isset($user->id)) of class LoginController.
     *
     * @test
     */
    public function test_function_login_3()
    {
        $response = $this->withoutExceptionHandling()->post('/login');
        
        $response
            ->assertStatus(404)
            ->assertSee('Usuario no registrado o Usuario deshabilitado');
    }


    /**
     * Test function handleProviderCallback of class LoginController.
     *
     * @test
     */
    public function test_function_handleProviderCallback_1()
    {
        $response = $this->withoutExceptionHandling()->get('/login/{provider}');
        
        $response
            ->assertStatus(200)
            ->assertSee('Usuario ha iniciado sesion');
    }

    /**
     * Test function redirectToProvider(else) of class LoginController.
     *
     * @test
     */
    public function test_function_handleProviderCallback_2()
    {
        $response = $this->withoutExceptionHandling()->get('/login/{provider}');
        
        $response
            ->assertStatus(404)
            ->assertSee('Usuario no registrado o Usuario deshabilitado');
    }

    /**
     * Test function logout of class LoginController.
     *
     * @test
     */
    public function test_function_logout()
    {
        $response = $this->withoutExceptionHandling()->get('/logout');
        
        $response
            ->assertStatus(200)
            ->assertSee('Sesion cerrada');
    }
}
