<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_is_admin_is_cast_to_boolean()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $this->assertTrue($user->is_admin);

        $user = User::factory()->create(['is_admin' => 0]);
        $this->assertFalse($user->is_admin);
    }
}
