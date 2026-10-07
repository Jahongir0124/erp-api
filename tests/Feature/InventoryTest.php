<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InventoryHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_inventory_stock(): void
    {
        $this->seed();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $product = Product::factory()->create([
            'quantity' => 50,
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/inventory');

        $response->assertOk();

        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => $product->name,
            'stock' => 50,
            'stock_status' => 'in_stock',
        ]);
    }

    public function test_inventory_stock_status_filter_works(): void
    {
        $this->seed();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $low = Product::factory()->create([
            'quantity' => 5,
        ]);

        Product::factory()->create([
            'quantity' => 50,
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/inventory?stock_status=low_stock');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment([
            'id' => $low->id,
            'stock_status' => 'low_stock',
        ]);
    }

    public function test_manager_can_adjust_inventory(): void
    {
        $this->seed();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $product = Product::factory()->create([
            'quantity' => 100,
        ]);

        $reason = 'Damaged products';

        $response = $this
            ->actingAs($manager)
            ->patchJson("/api/inventory/{$product->id}/adjust", [
                'quantity' => -5,
                'reason' => $reason,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 95,
        ]);

        $this->assertDatabaseHas('inventory_histories', [
            'product_id' => $product->id,
            'order_id' => null,
            'created_by' => $manager->id,
            'quantity_before' => 100,
            'quantity_change' => -5,
            'quantity_after' => 95,
            'type' => 'manual_adjustment',
            'note' => $reason,
        ]);
    }

    public function test_seller_cannot_adjust_inventory(): void
    {
        $this->seed();

        $seller = User::factory()->create();
        $seller->assignRole('seller');

        $product = Product::factory()->create([
            'quantity' => 100,
        ]);

        $response = $this
            ->actingAs($seller)
            ->patchJson("/api/inventory/{$product->id}/adjust", [
                'quantity' => -5,
                'reason' => 'Not allowed',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 100,
        ]);
    }

    public function test_inventory_cannot_become_negative(): void
    {
        $this->seed();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $product = Product::factory()->create([
            'quantity' => 5,
        ]);

        $response = $this
            ->actingAs($manager)
            ->patchJson("/api/inventory/{$product->id}/adjust", [
                'quantity' => -10,
                'reason' => 'Invalid adjustment',
            ]);

        $response->assertUnprocessable();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseMissing('inventory_histories', [
            'product_id' => $product->id,
            'type' => 'manual_adjustment',
        ]);
    }

    public function test_inventory_history_can_be_viewed(): void
    {
        $this->seed();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $product = Product::factory()->create([
            'quantity' => 95,
        ]);

        $history = $product->inventoryHistories()->create([
            'order_id' => null,
            'created_by' => $manager->id,
            'quantity_before' => 100,
            'quantity_change' => -5,
            'quantity_after' => 95,
            'type' => 'manual_adjustment',
            'note' => 'Damaged products',
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/inventory-histories');

        $response->assertOk();

        $response->assertJsonFragment([
            'id' => $history->id,
            'quantity_before' => 100,
            'quantity_change' => -5,
            'quantity_after' => 95,
            'type' => 'manual_adjustment',
            'note' => 'Damaged products',
        ]);
    }
}