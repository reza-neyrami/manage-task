<?php

namespace App\Model;

use App\Core\Interfaces\Model\Model;

class UserTask extends Model {
    public $timestamps = false;
    protected $table = 'user_tasks';
    
    protected $fillable = ['userId', 'taskId'];

    public function user()
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->userId);
        $stmt->execute();
        return $stmt->fetchObject(User::class);
    }

    public function task()
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->taskId);
        $stmt->execute();
        return $stmt->fetchObject(Task::class);
    }
}
