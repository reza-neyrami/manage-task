<?php

namespace App\Model;

use App\Core\Interfaces\Model\Model;
use App\Core\Services\Response;
use Exception;
use PDO;

class Task extends Model
{
    public $timestamps = false;
    protected $table = 'tasks';
    protected $fillable = ['id', 'name', 'description', 'startDate', 'endDate', 'status', 'userId'];
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

    public function changeStatus(string $newStatus)
    {
        if ($newStatus == self::STATUS_IN_PROGRESS && $this->status != self::STATUS_TODO) {
            Response::json(['message' => 'وظیفه باید در حالت "برای انجام" باشد تا بتوان آن را به حالت "در حال انجام" تغییر داد.']);
        } elseif ($newStatus == self::STATUS_DONE && $this->status != self::STATUS_IN_PROGRESS) {
            Response::json(['message' => 'وظیفه باید در حالت "در حال انجام" باشد تا بتوان آن را به حالت "انجام شده" تغییر داد.']);
        } elseif ($newStatus == self::STATUS_TODO) {
            Response::json(['message' => 'وظیفه نمی‌تواند به حالت "برای انجام" برگردد.']);
        }

        $this->status = $newStatus;
    }

// get all task  by date
    public function getTasksByDateRange($startDate, $endDate)
    {
        $sql = "SELECT tasks.*, users.username as user_username
                FROM {$this->table}
                INNER JOIN user_tasks ON {$this->table}.id = user_tasks.taskId
                INNER JOIN users ON user_tasks.userId = users.id
                WHERE tasks.startDate >= ? AND tasks.endDate <= ?";

        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $startDate);
        $stmt->bindValue(2, $endDate);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

}
