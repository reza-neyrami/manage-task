<?php

namespace App\Model;

use App\Core\Interfaces\Model\Model;
use Exception;
use PDO;

class Task extends Model
{
    public $timestamps = false;
    protected $table = 'tasks';
    protected $fillable = ['name', 'description', 'startDate', 'endDate', 'status', 'userId'];
    protected $toArray = ['id', 'name', 'description', 'startDate', 'endDate', 'status', 'userId'];

    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'doing';
    const STATUS_DONE = 'done';

    public $status;

    public function start()
    {
        if ($this->status != self::STATUS_TODO) {
            throw new Exception('وظیفه باید در حالت "برای انجام" باشد تا بتوان آن را شروع کرد.');
        }
        $this->status = self::STATUS_IN_PROGRESS;
    }

    public function finish()
    {
        if ($this->status != self::STATUS_IN_PROGRESS) {
            throw new Exception('وظیفه باید در حالت "در حال انجام" باشد تا بتوان آن را به پایان رساند.');
        }
        $this->status = self::STATUS_DONE;
    }
    public function users()
    {
        $sql = "SELECT users.* FROM users
                INNER JOIN user_tasks ON users.id = user_tasks.userId
                WHERE user_tasks.taskId = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function reports()
    {
        $sql = "SELECT * FROM reports WHERE taskId = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Report::class);
    }
}
