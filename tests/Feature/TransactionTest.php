<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_only_their_own_transactions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $category = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($category)->expense()->create(['memo' => '自分のランチ']);

        $otherCategory = Category::factory()->for($other)->expense()->create();
        Transaction::factory()->for($other)->for($otherCategory)->expense()->create(['memo' => '他人のランチ']);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk();
        $response->assertSee('自分のランチ');
        $response->assertDontSee('他人のランチ');
    }

    public function test_user_can_create_a_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => '2026-08-15',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 1500,
            'memo' => 'スーパーで買い物',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 1500.00,
            'memo' => 'スーパーで買い物',
        ]);
    }

    public function test_amount_must_be_a_positive_number(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => '2026-08-15',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 0,
        ]);

        $response->assertSessionHasErrors(['amount']);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_amount_must_be_numeric(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => '2026-08-15',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 'abc',
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_date_and_category_are_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => '',
            'type' => 'expense',
            'category_id' => '',
            'amount' => 1000,
        ]);

        $response->assertSessionHasErrors(['date', 'category_id']);
    }

    public function test_category_type_must_match_transaction_type(): void
    {
        $user = User::factory()->create();
        $incomeCategory = Category::factory()->for($user)->income()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => '2026-08-15',
            'type' => 'expense',
            'category_id' => $incomeCategory->id,
            'amount' => 1000,
        ]);

        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_user_cannot_use_another_users_category(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherCategory = Category::factory()->for($other)->expense()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => '2026-08-15',
            'type' => 'expense',
            'category_id' => $otherCategory->id,
            'amount' => 1000,
        ]);

        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_user_can_update_a_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();
        $transaction = Transaction::factory()->for($user)->for($category)->expense()->create(['amount' => 1000]);

        $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'date' => $transaction->date->format('Y-m-d'),
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 2000,
            'memo' => '更新後メモ',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 2000.00,
            'memo' => '更新後メモ',
        ]);
    }

    public function test_user_can_delete_a_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();
        $transaction = Transaction::factory()->for($user)->for($category)->expense()->create();

        $response = $this->actingAs($user)->delete(route('transactions.destroy', $transaction));

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_user_cannot_access_another_users_transaction(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherCategory = Category::factory()->for($other)->expense()->create();
        $transaction = Transaction::factory()->for($other)->for($otherCategory)->expense()->create();

        $this->actingAs($user)->get(route('transactions.edit', $transaction))->assertNotFound();
        $this->actingAs($user)->delete(route('transactions.destroy', $transaction))->assertNotFound();
    }

    public function test_transactions_can_be_filtered_by_type(): void
    {
        $user = User::factory()->create();
        $income = Category::factory()->for($user)->income()->create();
        $expense = Category::factory()->for($user)->expense()->create();

        Transaction::factory()->for($user)->for($income)->income()->create(['memo' => '給与収入']);
        Transaction::factory()->for($user)->for($expense)->expense()->create(['memo' => '食費支出']);

        $response = $this->actingAs($user)->get(route('transactions.index', ['type' => 'income']));

        $response->assertOk();
        $response->assertSee('給与収入');
        $response->assertDontSee('食費支出');
    }

    public function test_transactions_are_ordered_by_date_descending(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();

        $older = Transaction::factory()->for($user)->for($category)->expense()->create(['date' => '2026-01-01']);
        $newer = Transaction::factory()->for($user)->for($category)->expense()->create(['date' => '2026-06-01']);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $transactions = $response->viewData('transactions');

        $this->assertTrue($transactions->first()->is($newer));
        $this->assertTrue($transactions->last()->is($older));
    }
}
