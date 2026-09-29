<?php

namespace Tests\Feature;

use app\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class OrderTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_guest_cannot_view_orders(): void
    {
        $response = $this->getJson('api/orders');

        $response->assertUnauthorized();
    }
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_seller_can_view_order(): void
    {
        $this->seed();
        $seller = User::factory()->create();
      
        $seller->assignRole('seller');
        
        $response = $this->actingAs($seller)->getJson('/api/orders');

        $response->assertOk();
    }

    public function test_seller_can_view_self_orders(): void
    {
        $this->seed();

        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();
        $customer = Customer::factory()->create();
        $seller1->assignRole('seller');
        $seller2->assignRole('seller');

        $order1 = Order::create([
            'created_by' => $seller1->id,
            'customer_id' => $customer->id
        ]);

        $order2 = Order::create([
            'created_by' => $seller2->id,
            'customer_id' => $customer->id
        ]);

        $response1 = $this->actingAs($seller2)->getJson('api/orders');
        $response2 = $this->actingAs($seller1)->getJson('api/orders');

        $response1->assertOk();
        $response2->assertOk();

        $response1->assertJsonCount(1, 'data');
        $response2->assertJsonCount(1, 'data');

        $this->assertSame(
            $order2->id,
            $response1->json('data.0.id')
        );

        $this->assertSame(
            $order1->id,
            $response2->json('data.0.id')
        );

    }


    public function test_seller_cannot_view_other_sellers_order(): void
    {
        $this->seed();

        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();


        $customer = Customer::factory()->create();

        $seller1->assignRole('seller');
        $seller2->assignRole('seller');

        $order = Order::create([
            'created_by' => $seller2->id,
            'customer_id' => $customer->id
        ]);


        $response = $this->actingAs($seller1)->getJson("/api/orders/{$order->id}");

        $response->assertForbidden();


    }

    public function test_seller_can_update_self_pending_order(): void
    {
        $this->seed();
        $seller = User::factory()->create();
        $customer = Customer::factory()->create();
        $seller->assignRole('seller');
      

        $product = Product::factory()->create([
            'quantity' => 100
        ]);

        $order = Order::create([
            'created_by' => $seller->id,
            'customer_id' => $customer->id
        ]);

        $order->items()->create([

            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
            'subtotal' => $product->price * 2
        ]);
        $response = $this->actingAs($seller)
            ->patchJson("/api/orders/{$order->id}", [
                'customer_id' => $customer->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5
                    ],
                ],

            ]);

        $response->assertOk();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);



    }



    public function test_seller_cannot_update_others_pending_orders(): void
    {
        $this->seed();


        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();
        $customer = Customer::factory()->create();

        $seller1->assignRole('seller');
        $seller2->assignRole('seller');

        $product = Product::factory()->create([
            'quantity' => 100
        ]);


        $order = Order::create([
            'created_by' => $seller1->id,
            'customer_id' => $customer->id
        ]);


        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => $product->price,
            'subtotal' => $product->price * 10
        ]);


        $response = $this->actingAs($seller2)
                    ->patchJson("/api/orders/{$order->id}", [
                        'customer_id' => $customer->id,
                        'items' => [
                            [
                                'product_id' => $product->id,
                                'quantity' => 15
                            ],
                        ],
                    ]);

        $response->assertForbidden();
    }


    public function test_seller_cannot_update_confirmed_order(): void
    {
        $this->seed();

        $seller = User::factory()->create();
        $manager = User::factory()->create();

        $seller->assignRole('seller');
        $manager->assignRole('manager');

        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'quantity' => 50
        ]);

        $order = Order::create([
            'created_by' => $seller->id,
            'customer_id' => $customer->id
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'price' => $product->price,
            'subtotal' => $product->price * 10
        ]);

        $order->update([
            'confirmed_by' => $manager->id,
            'status' => OrderStatus::CONFIRMED,
            'confirmed_at' => now()
        ]);

        $response = $this->actingAs($seller)
                ->patchJson("/api/orders/{$order->id}", [
                    'customer_id' => $customer->id,
                    'items' => [
                        [
                            'product_id' => $product->id,
                            'quantity' => 30
                        ],
                    ]
                ]);

        $response->assertForbidden();
    }


    public function test_crate_valid_order(): void
    {
        $this->seed();

        $seller = User::factory()->create();
        $seller->assignRole('seller');

        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'quantity' => 100
        ]);
        $product2 = Product::factory()->create([
            'quantity' => 200
        ]);

        $response = $this->actingAs($seller)
                ->postJson("/api/orders", [
                    
                    'customer_id' => $customer->id,
                    'items' => [
                        [
                            'product_id' => $product1->id,
                            'quantity' => 20
                        ],
                        [
                            'product_id' => $product2->id,
                            'quantity' => 30
                        ]
                    ]
                ]);

        $response->assertCreated();
        $order = Order::latest()->first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 20
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 30
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'quantity' => 100
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'quantity' => 200
        ]);
    }

    public function test_manager_can_confirm_pending_order(): void
    {
        $this->seed();

        $seller = User::factory()->create();
        $manager = User::factory()->create();

        $seller->assignRole('seller');
        $manager->assignRole('manager');

        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'quantity' => 200
        ]);
        $order = Order::create([
            'created_by' => $seller->id,
            'customer_id' => $customer->id
        ]);

        $order->items()->create([
            
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => 10,
            'subtotal' => $product->price * 10
        ]);

        



    }
}
