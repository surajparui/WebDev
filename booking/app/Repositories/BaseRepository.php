<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return Array
     */
    public function create($attributes): array
    {
        return $this->model->create($attributes)->toArray();
    }

    /**
     * @param $id
     * @return Array
     */
    public function find($id): array
    {
        return $this->model->find($id)->toArray();
    }

    /**
     * @return Array
     */
    public function all(): array
    {
        return $this->model->all()->toArray();
    }
}