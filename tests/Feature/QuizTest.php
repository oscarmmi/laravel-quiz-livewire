<?php

namespace Tests\Feature;

use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_scope()
    {
        // Create a public quiz
        $publicQuiz = Quiz::create([
            'title' => 'Public Quiz',
            'slug' => 'public-quiz',
            'description' => 'This is a public quiz',
            'public' => true,
        ]);

        // Create a private quiz
        $privateQuiz = Quiz::create([
            'title' => 'Private Quiz',
            'slug' => 'private-quiz',
            'description' => 'This is a private quiz',
            'public' => false,
        ]);

        $publicQuizzes = Quiz::public()->get();

        $this->assertTrue($publicQuizzes->contains($publicQuiz));
        $this->assertFalse($publicQuizzes->contains($privateQuiz));
    }

    public function test_published_scope()
    {
        // Create a published quiz
        $publishedQuiz = Quiz::create([
            'title' => 'Published Quiz',
            'slug' => 'published-quiz',
            'description' => 'This is a published quiz',
            'published' => true,
        ]);

        // Create an unpublished quiz
        $unpublishedQuiz = Quiz::create([
            'title' => 'Unpublished Quiz',
            'slug' => 'unpublished-quiz',
            'description' => 'This is an unpublished quiz',
            'published' => false,
        ]);

        $publishedQuizzes = Quiz::published()->get();

        $this->assertTrue($publishedQuizzes->contains($publishedQuiz));
        $this->assertFalse($publishedQuizzes->contains($unpublishedQuiz));
    }
}
