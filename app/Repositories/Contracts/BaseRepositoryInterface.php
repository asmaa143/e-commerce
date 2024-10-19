<?php


namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);

    public function find($id, array $columns = ['*'], array $relations = []);

    public function findByField(string $field, $value, array $columns = ['*'], array $relations = []);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function paginate(int $perPage = 15, array $filters = [], array $columns = ['*'], array $relations = [], string $sortBy = 'id', string $sortDirection = 'asc');

    public function customQuery(array $conditions, array $columns = ['*'], array $relations = [], string $sortBy = 'id', string $sortDirection = 'asc');
}
