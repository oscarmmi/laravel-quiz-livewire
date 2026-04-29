<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Enums\QuestionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_can_be_created()
    {
        $question = Question::factory()->create([
            'question_text' => 'What is Laravel?',
        ]);

        $this->assertDatabaseHas('questions', [
            'question_text' => 'What is Laravel?',
        ]);
    }

    public function test_question_has_categories_relationship()
    {
        $question = Question::factory()->create();
        $category = Category::factory()->create();
        
        $question->categories()->attach($category);

        $this->assertTrue($question->categories->contains($category));
    }

    public function test_question_has_options_relationship()
    {
        $question = Question::factory()->create();
        $option = Option::factory()->create(['question_id' => $question->id]);

        $this->assertTrue($question->options->contains($option));
    }

    public function test_question_has_quizzes_relationship()
    {
        $question = Question::factory()->create();
        $quiz = Quiz::factory()->create();
        
        $question->quizzes()->attach($quiz);

        $this->assertTrue($question->quizzes->contains($quiz));
    }

    public function test_question_type_is_cast_to_enum()
    {
        $question = Question::factory()->create(['type' => 'unique-answer']);
        
        $this->assertInstanceOf(QuestionType::class, $question->type);
        $this->assertEquals(QuestionType::UniqueAnswer, $question->type);
    }
}
