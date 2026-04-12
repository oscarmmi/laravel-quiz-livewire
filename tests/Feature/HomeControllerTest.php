<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard_and_see_public_quizzes_with_questions(): void
    {
        $user = User::factory()->create();

        // 1. Public quiz with questions (Should be visible)
        $publicQuizWithQuestions = Quiz::factory()->create(['public' => true]);
        $question1 = Question::factory()->create();
        $publicQuizWithQuestions->questions()->attach($question1);

        // 2. Private quiz with questions (Should NOT be visible)
        $privateQuizWithQuestions = Quiz::factory()->create(['public' => false]);
        $question2 = Question::factory()->create();
        $privateQuizWithQuestions->questions()->attach($question2);

        // 3. Public quiz WITHOUT questions (Should NOT be visible)
        $publicQuizWithoutQuestions = Quiz::factory()->create(['public' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');

        $quizzes = $response->viewData('quizzes');
        $this->assertCount(1, $quizzes);
        $this->assertEquals($publicQuizWithQuestions->id, $quizzes->first()->id);
        $this->assertEquals(1, $quizzes->first()->questions_count);
    }
}
