<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_redirects_to_provider(): void
    {
        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturnSelf();

        Socialite::shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://github.com/login/oauth/authorize'));

        $response = $this->get(route('social.redirect', ['provider' => 'github']));

        $response->assertRedirect('https://github.com/login/oauth/authorize');
    }

    public function test_it_creates_and_authenticates_a_new_user_on_callback(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('12345');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getNickname')->andReturn('johndoe');
        $socialiteUser->shouldReceive('getEmail')->andReturn('john@example.com');

        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturnSelf();

        Socialite::shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        $response = $this->get(route('social.callback', ['provider' => 'github']));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'provider_id' => '12345',
            'provider_name' => 'github',
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }

    public function test_it_logs_in_existing_user_on_callback(): void
    {
        $existingUser = User::factory()->create([
            'provider_id' => '98765',
            'provider_name' => 'github',
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('98765');
        $socialiteUser->shouldReceive('getName')->andReturn('Jane Smith'); // Update name
        $socialiteUser->shouldReceive('getNickname')->andReturn('janesmith');
        $socialiteUser->shouldReceive('getEmail')->andReturn('jane@example.com');

        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturnSelf();

        Socialite::shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        $response = $this->get(route('social.callback', ['provider' => 'github']));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'provider_id' => '98765',
            'provider_name' => 'github',
            'email' => 'jane@example.com',
            'name' => 'Jane Smith', // Verify name was updated
        ]);

        $this->assertDatabaseCount('users', 1);

        $this->assertAuthenticatedAs($existingUser->fresh());
    }
}
