<?php

namespace App\Model;

use App\Core\Interfaces\Model\Arrayable;
use App\Core\Interfaces\Model\Model;
use JsonSerializable;
use PDO;

class User extends Model implements Arrayable, JsonSerializable
{

    protected $table = 'users';
    protected $fillable = ['username', 'password', 'role', 'email'];

    protected $toArray = ['id', 'username', 'role', 'email','created_at'];
    public function task()
    {
        $sql = "SELECT * FROM tasks WHERE userId = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function __debugInfo()
    {
        $data = [];
        foreach ($this->toArray as $property) {
            $data[$property] = $this->{$property};
        }
        return $data;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->toArray as $property) {
            $data[$property] = $this->{$property};
        }
        return $data;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'user_tasks', 'userId', 'taskId', 'id', 'id');
    }

    public function assignedTasks()
    {
        $sql = "SELECT tasks.* FROM tasks
                INNER JOIN user_tasks ON tasks.id = user_tasks.taskId
                WHERE user_tasks.userId = ?";

        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function report()
    {
        $sql = "SELECT * FROM files WHERE user_id = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Report::class);
    }

}
