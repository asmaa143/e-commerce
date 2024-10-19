<?php

namespace App\Repositories\Eloquent\Product;

use App\Filters\ProductFilter;
use App\Models\Product;


use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\AdvancedRepository;
use Illuminate\Support\Facades\Cache;

class ProductRepository extends AdvancedRepository implements ProductRepositoryInterface
{
    protected $filter;


    public function __construct(Product $product,ProductFilter $filter)
    {
        parent::__construct($product);
        $this->filter = $filter;// Call parent constructor with the User model
    }
    public function paginate(int $perPage = 15, array $filters = [], array $columns = ['*'], array $relations = [], string $sortBy = 'id', string $sortDirection = 'asc')
    {
        // Create a new query for the Product model
        $query = $this->model->newQuery();


        // Apply the filters through the ProductFilter class
        $query = $this->filter->apply($query);

        // Generate cache key based on filters and pagination
        $cacheKey = $this->generateCacheKey('paginate', $filters, $perPage, $sortBy, $sortDirection);

        // Return paginated result using caching
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($query, $columns, $relations, $sortBy, $sortDirection, $perPage) {
            return $query->with($relations)->orderBy($sortBy, $sortDirection)->paginate($perPage, $columns);
        });
    }

    private function generateCacheKey(string $method, ...$args): string
    {
        return md5($method . serialize($args));
    }
    public function reduceStock($productId, $quantity)
    {

        $product = $this->find($productId);

        $product->stock_quantity -= $quantity;
        $product->save();

        return $product;
    }
}
