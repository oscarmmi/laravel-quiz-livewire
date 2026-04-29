<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created()
    {
        $category = Category::factory()->create(['name' => 'PHP']);

        $this->assertDatabaseHas('categories', [
            'name' => 'PHP',
        ]);
    }

    public function test_category_has_questions_relationship()
    {
        $category = Category::factory()->create();
        $question = Question::factory()->create();
        
        $category->questions()->attach($question);

        $this->assertTrue($category->questions->contains($question));
        $this->assertInstanceOf(Question::class, $category->questions->first());
    }
}
