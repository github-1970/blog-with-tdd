<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable('categories');
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable('categories');
        parent::tearDown();
    }

    public function test_select_categories()
    {
        Category::factory()->create();
        $categories = Category::all();
        $this->assertTrue($categories->count() >= 1);
    }

    public function test_insert_category()
    {
        $category = Category::factory()->create();
        $this->assertDatabaseHas('categories', $category->toArray());
    }

    public function test_update_category()
    {
        $category = Category::factory()->create();
        $oldName = $category->name;
        $category->name = 'new name';
        $category->save();
        $newName = $category->name;

        $this->assertNotEquals($oldName, $newName);

        return $category->id;
    }

    public function test_update_category_with_another_method()
    {
        $category = Category::factory()->create();
        $category = Category::find($category->id);
        $oldName = $category->name;
        $category->update(['name' => 'new name']);
        $newName = $category->name;

        $this->assertNotEquals($oldName, $newName);
    }

    public function test_delete_category()
    {
        $category = Category::factory()->create();
        $category->forceDelete();
        $this->assertDatabaseMissing('categories', $category->toArray());
    }

    public function test_soft_delete_category()
    {
        $category = Category::factory()->create();
        $category->delete();
        $this->assertDatabaseHas('categories', $category->toArray())
        ->assertNotNull($category->deleted_at);
    }

    public function test_restore_category()
    {
        $category = Category::factory()->create();
        $category->delete();
        $category->restore();
        $this->assertDatabaseHas('categories', $category->toArray())
        ->assertNull($category->deleted_at);
    }

    public function test_category_has_many_posts()
    {
        $category = Category::factory()->hasPosts(5)->create();
        $posts = $category->posts;
        $this->assertInstanceOf(Post::class, $posts->first());
        $this->assertDatabaseHas('posts', $posts->first()->toArray());
    }

    public function test_category_have_a_parent()
    {
        $category = Category::factory()->forParent()->create();
        $parent = $category->parent;
        $this->assertEquals($parent->id, $category->parent_id);
        $this->assertInstanceOf(Category::class, $parent->first());
        $this->assertDatabaseHas('categories', $parent->toArray());
    }

    public function test_category_have_a_children()
    {
        $category = Category::factory()->hasChildren()->create();
        $children = $category->children;
        $this->assertEquals($children->first()->parent_id, $category->id);
        $this->assertInstanceOf(Category::class, $children->first());
        $this->assertDatabaseHas('categories', $children->first()->toArray());
    }
}
