<?php

namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
class ProductFilter
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query)
    {
        foreach ($this->filters() as $filter => $value) {
            if (method_exists($this, $filter) && $value != null) {
                $this->$filter($query, $value);
            }
        }

        return $query;
    }

    protected function filters()
    {
        return $this->request->all();
    }


    protected function name(Builder $query, $value)
    {
        $query->where('name', 'like', '%' . $value . '%');
    }

    protected function category_id(Builder $query, $value)
    {
        $query->where('category_id',  $value );
    }


    protected function min_price(Builder $query, $value)
    {
        $query->where('price', '>=', $value);
    }

    protected function max_price(Builder $query, $value)
    {
        $query->where('price', '<=', $value);
    }

}
