<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\User;

use Tests\TestCase;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_place_order()
    {
        // Create a user
        $user = User::factory()->create();

        // Create some products
        $product1 = Product::factory()->create(['price' => 50, 'stock_quantity' => 10]);
        $product2 = Product::factory()->create(['price' => 100, 'stock_quantity' => 5]);

        // Act as  user
        $this->actingAs($user, 'sanctum');


        $payload = [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
        ];


        $response = $this->postJson('/api/order', $payload);


        $response->assertStatus(200);


        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 200,  // (2 * 50) + (1 * 100)
        ]);


        $this->assertDatabaseHas('order_product', [
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);


        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'stock_quantity' => 8,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'stock_quantity' => 4,
        ]);
    }

    public function test_unauthenticated_user_cannot_place_order()
    {
        // Create some products
        $product1 = Product::factory()->create(['price' => 50, 'stock_quantity' => 10]);

        // Define the product payload for the order
        $payload = [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
            ],
        ];

        // Send a POST request without authentication
        $response = $this->postJson('/api/order', $payload);

        // Assert the response status is 401 Unauthorized
        $response->assertStatus(401);
    }
}
