<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_cache_is_cleared_on_create(): void
    {
        Cache::put('categories.id_name', 'some-data');
        Category::create(['name' => 'New Category']);
        $this->assertFalse(Cache::has('categories.id_name'));
    }

    public function test_category_cache_is_cleared_on_update(): void
    {
        $category = Category::create(['name' => 'Initial Category']);
        Cache::put('categories.id_name', 'some-data');
        $category->update(['name' => 'Updated Category']);
        $this->assertFalse(Cache::has('categories.id_name'));
    }

    public function test_category_cache_is_cleared_on_delete(): void
    {
        $category = Category::create(['name' => 'Category To Delete']);
        Cache::put('categories.id_name', 'some-data');
        $category->delete();
        $this->assertFalse(Cache::has('categories.id_name'));
    }
}
