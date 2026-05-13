<?php

namespace Tests\Feature\Livewire;

use App\Models\Category;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Livewire\CustomTestCreator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomTestCreatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_test_creation_sets_user_id(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Question::factory()->count(5)->create()->each(fn($q) => $q->categories()->attach($category));

        Livewire::actingAs($user)
            ->test(CustomTestCreator::class)
            ->set('sourceType', 'categories')
            ->set('selectedCategories', [$category->id])
            ->set('totalQuestions', 3)
            ->call('submit');

        $quiz = Quiz::where('user_id', $user->id)->first();
        
        $this->assertNotNull($quiz);
        $this->assertEquals($user->id, $quiz->user_id);
        $this->assertFalse($quiz->public);
        $this->assertTrue($quiz->published);
        $this->assertCount(3, $quiz->questions);
    }
}
