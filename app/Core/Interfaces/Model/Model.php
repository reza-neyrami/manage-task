<?php

namespace App\Core\Interfaces\Model;

use App\Core\TraitS\DatabaseConnectionTrait;
use PDO;

abstract class Model implements ModelInterface
{
    use DatabaseConnectionTrait;
    protected $fillable = [];
    protected $table;
    public function __construct()
    {
        $this->table = $this->getTableName();
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public static function find(int $id): ?self
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} WHERE id = ?";
        $stmt = $model->pdo->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject(static::class) ?: null;
    }
}
