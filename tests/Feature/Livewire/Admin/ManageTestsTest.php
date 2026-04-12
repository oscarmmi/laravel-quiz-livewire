<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\ManageTests;
use App\Models\Quiz;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageTestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_creates_a_new_test()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();

        Livewire::actingAs($admin)
            ->test(ManageTests::class)
            ->set('user_id', $user->id)
            ->set('quiz_id', $quiz->id)
            ->set('result', 85)
            ->set('ip_address', '127.0.0.1')
            ->set('time_spent', 120)
            ->call('save')
            ->assertDispatched('close-modal', 'test-form')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tests', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'result' => 85,
            'ip_address' => '127.0.0.1',
            'time_spent' => 120,
        ]);
    }

    public function test_save_updates_an_existing_test()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $test = Test::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'result' => 50,
            'ip_address' => '192.168.1.1',
            'time_spent' => 60,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageTests::class)
            ->set('testId', $test->id)
            ->set('user_id', $user->id)
            ->set('quiz_id', $quiz->id)
            ->set('result', 95)
            ->set('ip_address', '127.0.0.2')
            ->set('time_spent', 150)
            ->call('save')
            ->assertDispatched('close-modal', 'test-form')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tests', [
            'id' => $test->id,
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'result' => 95,
            'ip_address' => '127.0.0.2',
            'time_spent' => 150,
        ]);
    }

    public function test_save_validates_required_fields()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(ManageTests::class)
            ->call('save')
            ->assertHasErrors([
                'user_id' => 'required',
                'quiz_id' => 'required',
                'result' => 'required',
            ]);
    }

    public function test_save_validates_field_formats()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(ManageTests::class)
            ->set('user_id', 9999) // does not exist
            ->set('quiz_id', 9999) // does not exist
            ->set('result', -10)
            ->set('ip_address', 'not-an-ip')
            ->set('time_spent', -5)
            ->call('save')
            ->assertHasErrors([
                'user_id' => 'exists',
                'quiz_id' => 'exists',
                'result' => 'min',
                'ip_address' => 'ip',
                'time_spent' => 'min',
            ]);
    }
}
