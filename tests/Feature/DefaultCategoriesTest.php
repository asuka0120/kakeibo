<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_categories_are_created_when_a_user_registers(): void
    {
        $user = User::factory()->create();

        event(new Registered($user));

        $this->assertTrue($user->categories()->where('type', 'income')->exists());
        $this->assertTrue($user->categories()->where('type', 'expense')->exists());
        $this->assertDatabaseHas('categories', ['user_id' => $user->id, 'name' => '食費', 'type' => 'expense']);
        $this->assertDatabaseHas('categories', ['user_id' => $user->id, 'name' => '給与', 'type' => 'income']);
    }
}
