<?php
namespace App\Core\Repository;
use App\Core\Interfaces\User\UserRepositoryInterface;
use App\Model\User;

class UserRepository implements UserRepositoryInterface
{
    private $model;

    public function __construct(User $user) {

        $this->model = $user;
    }

    public function findById(int $id): ?User
    {
        return  $this->model->find($id);
    }
    
    public function create(array $data): mixed
    {
        return  $this->model->save($data);
    }

   
}
