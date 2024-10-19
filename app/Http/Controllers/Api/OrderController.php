<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\OrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Repositories\Contracts\Order\OrderRepositoryInterface;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Traits\Api\ApiResponseTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $orderRepository;
    protected $productRepository;

    public function __construct(OrderRepositoryInterface $orderRepository, ProductRepositoryInterface $productRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function store(OrderRequest $request)
    {

        $data = $request->validated();

        foreach ($data["products"] as $product) {
            $productModel = $this->productRepository->find($product['product_id']);

            if (!$productModel) return $this->responseError(msg: "product not found");

            if ($productModel->stock_quantity < $product['quantity']) return $this->responseError(msg: 'Insufficient stock for product: ' . $productModel->name);


        }

        return $this->responseData(new OrderResource($this->orderRepository->createOrder($request->user()->id, $data["products"])));

    }

    public function show(Order $order)
    {
        return $this->responseData(new OrderResource($order));
    }
}
