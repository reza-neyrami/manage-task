<?php

namespace App\Core\Interfaces\Model;

use App\Core\TraitS\Arrayable;
use App\Core\TraitS\DatabaseConnectionTrait;
use PDO;


abstract class Model  implements ModelInterface
{
    use  Arrayable , DatabaseConnectionTrait;
    protected $fillable = [];
    protected $toArray = [];
    protected $table;


    public function getTableName(): string
    {
        return $this->table;
    }

    public function jsonSerialize(): array
    {
      return $this->toArray($this);
    }
 

    public static function find(int $id)
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} WHERE id = ?";
        $stmt = $model->pdo->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject(static::class) ?: null;
    }
}
