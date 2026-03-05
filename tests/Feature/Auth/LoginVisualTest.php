<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginVisualTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_screen_can_be_rendered_with_new_design()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('movete');
        $response->assertSee('¡Bienvenido nuevamente!');
        $response->assertSee('Inicio Sesión');
        $response->assertSee('Registro');
        $response->assertSee('¿Olvidaste tu contraseña?');
    }
}
