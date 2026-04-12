<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/admin-route', function () {
            return 'Admin Content';
        })->middleware(AdminMiddleware::class);
    }

    public function test_unauthenticated_user_is_redirected()
    {
        $response = $this->get('/admin-route');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'No tienes permisos de administrador.');
    }

    public function test_non_admin_user_is_redirected()
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin-route');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'No tienes permisos de administrador.');
    }

    public function test_admin_user_can_access()
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin-route');

        $response->assertStatus(200);
        $response->assertSee('Admin Content');
    }
}
