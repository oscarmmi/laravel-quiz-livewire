<?php

namespace Tests\Feature\Models;

use App\Models\Answer;
use App\Models\User;
use App\Models\Test;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use RefreshDatabase;

    public function test_answer_can_be_created()
    {
        $answer = Answer::factory()->create(['correct' => true]);

        $this->assertDatabaseHas('answers', [
            'correct' => true,
        ]);
    }

    public function test_answer_relationships()
    {
        $user = User::factory()->create();
        $test = Test::factory()->create();
        $question = Question::factory()->create();
        $option = Option::factory()->create();

        $answer = Answer::factory()->create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'question_id' => $question->id,
            'option_id' => $option->id,
        ]);

        $this->assertEquals($user->id, $answer->user->id);
        $this->assertEquals($test->id, $answer->test->id);
        $this->assertEquals($question->id, $answer->question->id);
        $this->assertEquals($option->id, $answer->option->id);
    }
}
