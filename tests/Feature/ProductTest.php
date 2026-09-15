<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // public function test_user_can_view_product(): void
    // {
    //     $user = User::factory()->create();
    //     Sanctum::actingAs($user);
    //     $response = $this->getJson(
    //         'api/products'
    //     );

    //     $response->assertStatus(200);
    // }

    public function test_admin_can_create_product(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        Sanctum::actingAs($user);

        $category = Category::factory()->create();

        $response = $this->postJson('/api/product', [
            'category_id' => $category->id,
            'name' => 'iPhone 17 Pro Max',
            'price' => 1400,
            'description' => 'Apple phone',
            'quantity' => 10
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 17 Pro Max',
            'price' => 1400
        ]);
    }

    public function test_product_name_is_required(): void
    {
        $this->seed();


        $user = User::factory()->create();
        $user->assignRole('super-admin');
        Sanctum::actingAs($user);

        $category = Category::factory()->create();

        $response = $this->postJson('api/product', [
            'category_id' => $category->id,
            'price' => 1300,
            'description' => 'Apple Phone',
            'quantity' => 10
            
            ]);


        $response->assertStatus(422);

        $response->assertJsonValidationErrors(
            'name'
        );
    }


    public function test_user_without_permission_cannot_create_product(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $response = $this->postJson('api/product', [
            'category_id' => $category->id,
            'name' => 'iPhone 17 Pro Max Orange',
            'description' => 'Apple product',
            'price' => 1500,
            'quantity' => 10
        ]);

        $response->assertStatus(403);

    }


    public function test_admin_can_update_product(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $response = $this->putJson("api/product/{$product->id}",
        [
            'category_id' => $product->category_id,
            'name' => 'Updated Product',
            'price' => 1000
        ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 1000
        ]);




    }


    public function test_admin_can_delete_product(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('super-admin');

        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $response = $this->deleteJson("api/product/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}
