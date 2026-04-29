<?php

namespace Tests\Feature\Models;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_option_can_be_created()
    {
        $option = Option::factory()->create(['text' => 'Option 1']);

        $this->assertDatabaseHas('options', [
            'text' => 'Option 1',
        ]);
    }

    public function test_option_has_question_relationship()
    {
        $question = Question::factory()->create();
        $option = Option::factory()->create(['question_id' => $question->id]);

        $this->assertEquals($question->id, $option->question->id);
        $this->assertInstanceOf(Question::class, $option->question);
    }

    public function test_option_correct_is_cast_to_boolean()
    {
        $option = Option::factory()->create(['correct' => 1]);
        $this->assertTrue($option->correct);

        $option = Option::factory()->create(['correct' => 0]);
        $this->assertFalse($option->correct);
    }
}
