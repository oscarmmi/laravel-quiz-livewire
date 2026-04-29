<?php

namespace Tests\Feature\Models;

use App\Models\Test;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Enums\TestCreatedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestTest extends TestCase
{
    use RefreshDatabase;

    public function test_test_can_be_created()
    {
        $test = Test::factory()->create(['result' => 80]);

        $this->assertDatabaseHas('tests', [
            'result' => 80,
        ]);
    }

    public function test_test_has_user_relationship()
    {
        $user = User::factory()->create();
        $test = Test::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $test->user->id);
    }

    public function test_test_has_quiz_relationship()
    {
        $quiz = Quiz::factory()->create();
        $test = Test::factory()->create(['quiz_id' => $quiz->id]);

        $this->assertEquals($quiz->id, $test->quiz->id);
    }

    public function test_test_has_questions_relationship_through_answers()
    {
        $test = Test::factory()->create();
        $question = Question::factory()->create();
        
        // Answers table is the pivot for questions relationship in Test model
        Answer::factory()->create([
            'test_id' => $test->id,
            'question_id' => $question->id,
        ]);

        $this->assertTrue($test->questions->contains($question));
    }
}
