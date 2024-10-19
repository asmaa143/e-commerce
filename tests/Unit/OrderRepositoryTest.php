<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;

use App\Models\User;
use App\Repositories\Eloquent\Order\OrderRepository;
use App\Repositories\Eloquent\Product\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;


class OrderRepositoryTest extends TestCase
{  use RefreshDatabase;

    public function test_it_creates_an_order_with_products()
    {
        // Create a user who will place the order
        $user = User::factory()->create();


        $productRepositoryMock = Mockery::mock(ProductRepository::class);

        $product1 = Product::factory()->create(['price' => 50, 'stock_quantity' => 10]);
        $product2 = Product::factory()->create(['price' => 100, 'stock_quantity' => 5]);


        $products = [
            ['product_id' => $product1->id, 'quantity' => 2],
            ['product_id' => $product2->id, 'quantity' => 1],
        ];


        $productRepositoryMock->shouldReceive('find')
            ->with($product1->id)
            ->andReturn($product1);

        $productRepositoryMock->shouldReceive('find')
            ->with($product2->id)
            ->andReturn($product2);


        $productRepositoryMock->shouldReceive('reduceStock')
            ->with($product1->id, 2)
            ->once()
            ->andReturn(true);
        $productRepositoryMock->shouldReceive('reduceStock')
            ->with($product2->id, 1)
            ->once()
            ->andReturn(true);


        $orderRepository = new OrderRepository(new Order(), $productRepositoryMock);


        $order = $orderRepository->createOrder($user->id, $products);

        // Assertions
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(200, $order->total);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total' => 200,
            'user_id' => $user->id
        ]);
        $this->assertCount(2, $order->products);
    }
}
