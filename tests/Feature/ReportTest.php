<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function createManager(): User
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        return $manager;
    }

    public function test_sales_report_returns_completed_sales_only(): void
    {
        $this->seed();

        $manager = $this->createManager();

        $customer = Customer::factory()->create();

        Order::create([
            'customer_id' => $customer->id,
            'created_by' => $manager->id,
            'status' => OrderStatus::COMPLETED->value,
            'total_amount' => 7200,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'created_by' => $manager->id,
            'status' => OrderStatus::PENDING->value,
            'total_amount' => 5000,
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/report/sales');

        $response->assertOk();

        $response->assertJson([
            'total_sales' => '7200.00',
            'completed_orders' => 1,
            'average_order_value' => 7200,
        ]);
    }

    public function test_orders_report_returns_status_statistics(): void
    {
        $this->seed();

        $manager = $this->createManager();
        $customer = Customer::factory()->create();

        foreach ([
            OrderStatus::PENDING,
            OrderStatus::PENDING,
            OrderStatus::CONFIRMED,
            OrderStatus::COMPLETED,
            OrderStatus::CANCELLED,
        ] as $status) {
            Order::create([
                'customer_id' => $customer->id,
                'created_by' => $manager->id,
                'status' => $status->value,
                'total_amount' => 100,
            ]);
        }

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/report/orders');

        $response->assertOk();

        $response->assertJson([
            'total_orders' => 5,
            'pending_orders' => 2,
            'confirmed_orders' => 1,
            'completed_orders' => 1,
            'cancelled_orders' => 1,
        ]);
    }

    public function test_inventory_report_returns_current_stock_and_movements(): void
    {
        $this->seed();

        $manager = $this->createManager();

        $product1 = Product::factory()->create([
            'quantity' => 50,
        ]);

        $product2 = Product::factory()->create([
            'quantity' => 5,
        ]);

        Product::factory()->create([
            'quantity' => 0,
        ]);

        $product1->inventoryHistories()->create([
            'order_id' => null,
            'created_by' => $manager->id,
            'quantity_before' => 30,
            'quantity_change' => 20,
            'quantity_after' => 50,
            'type' => 'manual_adjustment',
            'note' => 'New stock',
        ]);

        $product1->inventoryHistories()->create([
            'order_id' => null,
            'created_by' => $manager->id,
            'quantity_before' => 60,
            'quantity_change' => -10,
            'quantity_after' => 50,
            'type' => 'manual_adjustment',
            'note' => 'Damaged products',
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/report/inventory');

        $response->assertOk();

        $response->assertJson([
            'total_products' => 3,
            'total_stock' => 55,
            'low_stock_products' => 1,
            'out_of_stock_products' => 1,
            'stock_in' => 20,
            'stock_out' => 10,
        ]);
    }

    public function test_product_sales_report_returns_completed_product_sales(): void
    {
        $this->seed();

        $manager = $this->createManager();
        $customer = Customer::factory()->create();

        $product = Product::factory()->create([
            'quantity' => 75,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'created_by' => $manager->id,
            'status' => OrderStatus::COMPLETED->value,
            'total_amount' => 500,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 100,
            'subtotal' => 500,
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/report/products');

        $response->assertOk();

        $response->assertJsonFragment([
            'units_sold' => 5,
        ]);

        $response->assertJsonPath('data.0.current_stock', 75);

        $response->assertJsonPath(
            'data.0.product.id',
            $product->id
        );

        $response->assertJsonPath(
            'data.0.product.name',
            $product->name
        );
    }
}