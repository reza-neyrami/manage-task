<?php

namespace App\Model;

use App\Core\Interfaces\Model\Model;
use App\Core\Services\Auth;
use App\Core\Services\JWTApi;
use PDO;

class Report extends Model
{
    public $timestamps = false;
    protected $table = 'reports';
    protected $fillable = ['taskId', 'filename'];
    protected $toArray = ['id', 'taskId', 'filename', 'useId', 'created_at', 'updated_at'];

    public function task()
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->taskId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    public function user()
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $this->userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }


    public  function checkAccess($fileId, $jwt_token)
    {
        // Decode the JWT token to get the current user ID
        $decoded_token = JWTApi::decode_jwt_token($jwt_token);
        $currentUserId = $decoded_token->sub;

        // Get the user ID of the uploader from the database
        $sql = "SELECT userId FROM files WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $fileId);
        $stmt->execute();
        $uploaderUserId = $stmt->fetchColumn();

        // Check if the current user is the uploader or an admin
        if ($currentUserId == $uploaderUserId || $decoded_token->role == 'admin') {
            return true;
        } else {
            return false;
        }
    }
}
