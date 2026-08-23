<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_monthly_income_expense_and_balance(): void
    {
        $user = User::factory()->create();
        $income = Category::factory()->for($user)->income()->create();
        $expense = Category::factory()->for($user)->expense()->create();

        // Inside the target month (2026-08).
        Transaction::factory()->for($user)->for($income)->income()->create(['date' => '2026-08-05', 'amount' => 300000]);
        Transaction::factory()->for($user)->for($expense)->expense()->create(['date' => '2026-08-10', 'amount' => 50000]);
        Transaction::factory()->for($user)->for($expense)->expense()->create(['date' => '2026-08-20', 'amount' => 20000]);

        // Outside the target month — must not be counted.
        Transaction::factory()->for($user)->for($income)->income()->create(['date' => '2026-07-01', 'amount' => 999999]);

        $response = $this->actingAs($user)->get(route('dashboard', ['year' => 2026, 'month' => 8]));

        $response->assertOk();
        $response->assertViewHas('income', 300000.0);
        $response->assertViewHas('expense', 70000.0);
        $response->assertViewHas('balance', 230000.0);
    }

    public function test_dashboard_defaults_to_the_current_month(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('year', now()->year);
        $response->assertViewHas('month', now()->month);
    }

    public function test_dashboard_only_aggregates_the_authenticated_users_transactions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $category = Category::factory()->for($user)->expense()->create();
        $otherCategory = Category::factory()->for($other)->expense()->create();

        Transaction::factory()->for($user)->for($category)->expense()->create(['date' => '2026-08-01', 'amount' => 1000]);
        Transaction::factory()->for($other)->for($otherCategory)->expense()->create(['date' => '2026-08-01', 'amount' => 99999]);

        $response = $this->actingAs($user)->get(route('dashboard', ['year' => 2026, 'month' => 8]));

        $response->assertViewHas('expense', 1000.0);
    }

    public function test_guest_cannot_view_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
