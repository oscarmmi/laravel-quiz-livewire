<?php

namespace Tests\Feature\Models;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_can_be_created()
    {
        $quiz = Quiz::factory()->create(['title' => 'Sample Quiz']);

        $this->assertDatabaseHas('quizzes', [
            'title' => 'Sample Quiz',
        ]);
    }

    public function test_quiz_has_questions_relationship()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create();
        
        $quiz->questions()->attach($question);

        $this->assertTrue($quiz->questions->contains($question));
    }

    public function test_quiz_published_cast_to_boolean()
    {
        $quiz = Quiz::factory()->create(['published' => 1]);
        $this->assertTrue($quiz->published);
    }

    public function test_quiz_public_cast_to_boolean()
    {
        $quiz = Quiz::factory()->create(['public' => 1]);
        $this->assertTrue($quiz->public);
    }

    public function test_quiz_scopes()
    {
        $publicQuiz = Quiz::factory()->create(['public' => true]);
        $privateQuiz = Quiz::factory()->create(['public' => false]);
        $publishedQuiz = Quiz::factory()->create(['published' => true]);
        $unpublishedQuiz = Quiz::factory()->create(['published' => false]);

        $this->assertTrue(Quiz::public()->get()->contains($publicQuiz));
        $this->assertFalse(Quiz::public()->get()->contains($privateQuiz));
        
        $this->assertTrue(Quiz::published()->get()->contains($publishedQuiz));
        $this->assertFalse(Quiz::published()->get()->contains($unpublishedQuiz));
    }

    public function test_quiz_scope_not_by_user()
    {
        $adminQuiz = Quiz::factory()->create(['created_by' => 'admin']);
        $userQuiz = Quiz::factory()->create(['created_by' => 'user']);

        $results = Quiz::notByUser()->get();

        $this->assertTrue($results->contains($adminQuiz));
        $this->assertFalse($results->contains($userQuiz));
    }
}
