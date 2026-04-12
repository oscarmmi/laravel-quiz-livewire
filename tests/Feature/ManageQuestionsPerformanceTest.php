<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageQuestions;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ManageQuestionsPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_query_performance(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        // Create some categories
        for ($i = 0; $i < 5; $i++) {
            Category::create(['name' => 'Category '.$i]);
        }

        Cache::flush();
        DB::enableQueryLog();

        Livewire::actingAs($user)
            ->test(ManageQuestions::class)
            ->assertStatus(200);

        $queryLog = DB::getQueryLog();
        $this->assertNotEmpty($queryLog);

        $categoriesQueryFound = false;
        foreach ($queryLog as $log) {
            if (strpos($log['query'], 'select "id", "name" from "categories" order by "name" asc') !== false) {
                $categoriesQueryFound = true;
                break;
            }
            if (strpos($log['query'], 'select `id`, `name` from `categories` order by `name` asc') !== false) {
                $categoriesQueryFound = true;
                break;
            }
            // Add check for standard SQL to ensure our query is matched
            if (strpos($log['query'], 'select `id`, `name` from `categories`') !== false || strpos($log['query'], 'select "id", "name" from "categories"') !== false) {
                $categoriesQueryFound = true;
                break;
            }
        }
        $this->assertTrue($categoriesQueryFound, 'Optimized Categories query should be executed (fetching only id and name)');

        // Second request should hit cache
        DB::flushQueryLog();
        Livewire::actingAs($user)
            ->test(ManageQuestions::class)
            ->assertStatus(200);

        $queryLog2 = DB::getQueryLog();
        foreach ($queryLog2 as $log) {
            $this->assertStringNotContainsString('from "categories"', $log['query']);
            $this->assertStringNotContainsString('from `categories`', $log['query']);
        }
    }
}
