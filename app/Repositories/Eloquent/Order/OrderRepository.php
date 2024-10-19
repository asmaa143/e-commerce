<?php

namespace App\Repositories\Eloquent\Order;


use App\Enum\OrderStatusEnum;
use App\Events\OrderPlaced;
use App\Models\Order;



use App\Repositories\Contracts\Order\OrderRepositoryInterface;

use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\AdvancedRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderRepository extends AdvancedRepository implements OrderRepositoryInterface
{

    protected $productRepository;


    public function __construct(Order $order,ProductRepositoryInterface $productRepository)
    {
        parent::__construct($order);
        $this->productRepository = $productRepository;

    }
    public function createOrder($userId, array $products)
    {
        // Start a transaction to ensure atomicity
        return DB::transaction(function () use ($userId, $products) {
            // Calculate the total order amount
            $total = 0;

            foreach ($products as $product) {
                $productModel = $this->productRepository->find($product['product_id']);
                if ($productModel) {
                    $total += $productModel->price * $product['quantity'];
                }
            }

            // Create the order record
            $orderData = [
                'user_id' => $userId,
                'reference' => $this->generateReference(),  // Generate a unique reference
                'status' => OrderStatusEnum::PENDING,
                'total' => $total
            ];


            $order = $this->create($orderData);


            foreach ($products as $product) {

                $this->productRepository->reduceStock($product['product_id'], $product['quantity']);


                $order->products()->attach($product['product_id'], ['quantity' => $product['quantity']]);
            }
            event(new OrderPlaced($order));

            return $order;
        });
    }


    protected function generateReference(): string
    {
        return strtoupper(uniqid('ORDER-'));
    }

}
