<?php
namespace App\Model;

use App\Core\Interfaces\Model\Model;
use PDO;

class Task extends Model {
    public $timestamps = false;
    protected $table = 'tasks';
    protected $fillable = ['name', 'description', 'startDate','endDate','status','userId','created_at','updated_at'];
    protected $toArray = ['id','name', 'description','startDate','endDate','status','userId','created_at','updated_at'];

    public function users()
    {
        $sql = "SELECT users.* FROM users
                INNER JOIN user_tasks ON users.id = user_tasks.userId
                WHERE user_tasks.taskId = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }
    

    public function reports()
    {
        $sql = "SELECT * FROM reports WHERE taskId = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Report::class);
    }
}