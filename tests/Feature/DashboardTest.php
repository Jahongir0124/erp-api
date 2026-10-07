<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_dashboard(): void
    {
        $this->seed();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $customer = \App\Models\Customer::factory()->create();

        Product::factory()->create([
            'quantity' => 50,
        ]);

        Product::factory()->create([
            'quantity' => 5,
        ]);

        Product::factory()->create([
            'quantity' => 0,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'created_by' => $manager->id,
            'status' => OrderStatus::PENDING->value,
            'total_amount' => 1000,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'created_by' => $manager->id,
            'status' => OrderStatus::COMPLETED->value,
            'total_amount' => 7200,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'created_by' => $manager->id,
            'status' => OrderStatus::CANCELLED->value,
            'total_amount' => 5000,
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/dashboard');

        $response->assertOk();

        $response->assertJsonPath('data.orders.total', 3);
        $response->assertJsonPath('data.orders.pending', 1);
        $response->assertJsonPath('data.orders.completed', 1);
        $response->assertJsonPath('data.orders.cancelled', 1);

        $response->assertJsonPath('data.products.total', 3);

        $response->assertJsonPath('data.inventory.stock_total', 55);
        $response->assertJsonPath('data.inventory.low_stock_count', 1);
        $response->assertJsonPath('data.inventory.out_of_stock_count', 1);

        $response->assertJsonPath('data.sales.total', '7200.00');
    }
}