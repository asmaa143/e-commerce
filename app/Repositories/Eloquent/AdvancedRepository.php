<?php

// app/Repositories/Eloquent/AdvancedRepository.php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdvancedRepository implements BaseRepositoryInterface
{
    protected $model;
    protected $cacheTime = 300; // Cache for 1 hour

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        return Cache::remember($this->generateCacheKey('all', $relations), $this->cacheTime, function () use ($columns, $relations) {
            return $this->model->with($relations)->get($columns);
        });
    }

    public function find($id, array $columns = ['*'], array $relations = [])
    {
        return Cache::remember($this->generateCacheKey('find', $relations, $id), $this->cacheTime, function () use ($id, $columns, $relations) {
            return $this->model->with($relations)->find($id, $columns);
        });
    }

    public function findByField(string $field, $value, array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->where($field, $value)->first($columns);
    }

    public function create(array $data)
    {
        Cache::flush();
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            Cache::flush();
            $record->update($data);
            return $record;
        }
        return null;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            Cache::flush();
            return $record->delete();
        }
        return false;
    }

    public function paginate(int $perPage = 15, array $filters = [], array $columns = ['*'], array $relations = [], string $sortBy = 'id', string $sortDirection = 'asc')
    {
        $query = $this->model->newQuery();

        // Apply filters
        foreach ($filters as $key => $value) {
            if (isset($value)) {
                $query->where($key, $value);
            }
        }

        // Apply eager loading and sorting
        return Cache::remember($this->generateCacheKey('paginate', $filters, $perPage, $sortBy, $sortDirection), $this->cacheTime, function () use ($query, $columns, $relations, $sortBy, $sortDirection, $perPage) {
            return $query->with($relations)->orderBy($sortBy, $sortDirection)->paginate($perPage, $columns);
        });
    }

    public function customQuery(array $conditions, array $columns = ['*'], array $relations = [], string $sortBy = 'id', string $sortDirection = 'asc')
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return Cache::remember($this->generateCacheKey('customQuery', $conditions, $sortBy, $sortDirection), $this->cacheTime, function () use ($query, $columns, $relations, $sortBy, $sortDirection) {
            return $query->with($relations)->orderBy($sortBy, $sortDirection)->get($columns);
        });
    }

    private function generateCacheKey(string $method, ...$args): string
    {
        return md5($method . serialize($args));
    }
}

