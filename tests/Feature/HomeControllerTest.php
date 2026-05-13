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

    public function test_authenticated_user_can_access_dashboard_and_see_relevant_quizzes(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 1. Admin quiz (Should be visible)
        $adminQuiz = Quiz::factory()->create([
            'created_by' => \App\Enums\TestCreatedBy::Admin,
            'public' => true
        ]);
        $adminQuiz->questions()->attach(Question::factory()->create());

        // 2. User's own quiz (Should be visible, even if not public)
        $userQuiz = Quiz::factory()->create([
            'user_id' => $user->id,
            'created_by' => \App\Enums\TestCreatedBy::User,
            'public' => false
        ]);
        $userQuiz->questions()->attach(Question::factory()->create());

        // 3. Other user's quiz (Should NOT be visible)
        $otherUserQuiz = Quiz::factory()->create([
            'user_id' => $otherUser->id,
            'created_by' => \App\Enums\TestCreatedBy::User,
            'public' => false
        ]);
        $otherUserQuiz->questions()->attach(Question::factory()->create());

        // 4. Admin quiz WITHOUT questions (Should NOT be visible)
        $adminQuizNoQuestions = Quiz::factory()->create([
            'created_by' => \App\Enums\TestCreatedBy::Admin,
            'public' => true
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');

        $quizzes = $response->viewData('quizzes');
        
        $this->assertCount(2, $quizzes);
        $this->assertTrue($quizzes->contains($adminQuiz));
        $this->assertTrue($quizzes->contains($userQuiz));
        $this->assertFalse($quizzes->contains($otherUserQuiz));
    }
}
