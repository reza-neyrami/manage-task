<?php

namespace App\Core\Repository;

use App\Core\Interfaces\User\UserRepositoryInterface;
use App\Model\User;


class UserRepository implements UserRepositoryInterface
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function model()
    {
        return $this->model;
    }

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {

            throw new \Exception('There was an error creating the user.');
        }
    }


    public function delete(int $id): void
    {
        $user = $this->findById($id);
        if ($user) {
            $user->delete();
        }
    }

    public function all(): array
    {
        return $this->model->findAll();
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->model->paginate($page, $perPage);
    }

    public function findBy(string $field, string $value): ?User
    {
        return $this->model->where($field, $value)->first();
    }


    public function update(int $id, array $data): void
    {
        try {

            $this->model::update($id, $data);
        } catch (\PDOException $e) {
            throw new \Exception('There was an error updating the user.'. $e->getMessage());
        }
    }
}
