<?php

namespace App\Model;

use App\Core\Interfaces\Model\Model;
use App\Core\Services\Response;
use Exception;
use PDO;

class Task extends Model
{
    protected $timestamps = false;
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

// get all task  by date
    public function getTasksUserByDateRange($startDate, $endDate,$userId)
    {
        $sql = "SELECT tasks.*, users.username as user_username
        FROM user_tasks
        INNER JOIN tasks ON user_tasks.taskId = tasks.id
        INNER JOIN users ON user_tasks.userId = users.id
        WHERE tasks.startDate >= ? AND tasks.endDate <= ? AND users.id = ?";
        // dd($sql);
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $startDate);
        $stmt->bindValue(2, $endDate);
        $stmt->bindValue(3, $userId);
        $stmt->execute();
        // $stmt->debugDumpParams();
        // dd($sql);

        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    // گزارش رنج زمانی و تعداد بر اساس وضیعت بر اساس کوئری 
    public function getTasksByDateRange($startDate, $endDate)
    {
        $sql = "SELECT
        u.username,
        u.id,
        u.role,
        COUNT(t.id) AS total_tasks,
        COUNT(CASE WHEN t.status = 'todo' THEN 1 END) AS todo_tasks,
        COUNT(CASE WHEN t.status = 'doing' THEN 1 END) AS doing_tasks,
        COUNT(CASE WHEN t.status = 'done' THEN 1 END) AS done_tasks
        FROM users u
        LEFT JOIN user_tasks ut ON u.id = ut.userId
        LEFT JOIN tasks t ON ut.taskId = t.id
        WHERE t.startDate BETWEEN :start_date AND t.endDate <= :end_date
        GROUP BY u.id";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }



    public function reports()
    {
        $sql = "SELECT * FROM reports WHERE taskId = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Report::class);
    }

    //add workflow as  status update for programmer
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

}
