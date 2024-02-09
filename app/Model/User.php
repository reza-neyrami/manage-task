<?php

namespace App\Model;

use App\Core\Interfaces\Model\Model;
use PDO;

class User extends Model

{
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['username', 'password', 'role', 'email'];
    protected $toArray = ['id', 'username', 'role', 'email'];


    public function task()
    {
        $sql = "SELECT * FROM tasks WHERE userId = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function assignedTasks()
    {
        $sql = "SELECT tasks.* FROM tasks
                INNER JOIN user_tasks ON tasks.id = user_tasks.taskId
                WHERE user_tasks.userId = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }


    public function report()
    {
        $sql = "SELECT * FROM files WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Report::class);
    }
}
