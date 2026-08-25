<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_only_their_own_categories(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $mine = Category::factory()->for($user)->create(['name' => '自分の食費']);
        Category::factory()->for($other)->create(['name' => '他人の食費']);

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertOk();
        $response->assertSee('自分の食費');
        $response->assertDontSee('他人の食費');
    }

    public function test_user_can_create_a_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '食費',
            'type' => 'expense',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => '食費',
            'type' => 'expense',
        ]);
    }

    public function test_category_requires_name_and_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '',
            'type' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'type']);
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_category_type_must_be_income_or_expense(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '食費',
            'type' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['type']);
    }

    public function test_user_can_update_their_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['name' => '旧名称']);

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => '新名称',
            'type' => $category->type,
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => '新名称',
        ]);
    }

    public function test_user_can_delete_an_unused_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_a_category_that_is_in_use(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($category)->expense()->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_access_another_users_category(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::factory()->for($other)->create();

        $this->actingAs($user)->get(route('categories.edit', $category))->assertNotFound();
        $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => '乗っ取り',
            'type' => 'expense',
        ])->assertNotFound();
        $this->actingAs($user)->delete(route('categories.destroy', $category))->assertNotFound();
    }

    public function test_guest_cannot_access_categories(): void
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
    }
}
