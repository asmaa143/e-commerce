<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Traits\Api\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function index(ProductRequest $request)
    {

        $data=$request->validated();

        $products = $this->productRepository->paginate(perPage: $data["per_page"]??15);

        return $this->responsePaginated([ProductResource::collection($products)]);
    }
}
