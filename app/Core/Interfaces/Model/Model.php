<?php

namespace App\Core\Interfaces\Model;

use App\Core\TraitS\Arrayable;
use App\Core\TraitS\DatabaseConnectionTrait;
use PDO;


abstract class Model implements ModelInterface
{
    use  DatabaseConnectionTrait, Arrayable;
    protected $fillable = [];
    protected $toArray = [];
    protected $bindings = [];
    protected $table;
    protected $sql;
    public $id;

    public function __construct()
    {
        $this->getPDO();
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public static function find(int $id)
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} WHERE id = ?";
        $stmt = $model::$pdo->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject(static::class) ?: null;
    }

    public static function findAll(): array
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table}";
        $stmt = $model::$pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public function save(): void
    {
        $this->executeTransaction(function () {
            $properties = $this->getUpdateProperties();
            $placeholders = implode(', ', array_fill(0, count($properties), '?'));
            $values = array_map(fn ($p) => $this->{$p}, $properties);

            if (isset($this->id)) {
                $set = [];
                foreach ($properties as $property) {
                    $set[] = "$property = ?";
                }
                $values = array_merge(array_values($properties), [$this->id]);
                $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE id = ?";
            } else {
                $sql = "INSERT INTO {$this->table} (" . implode(', ', $properties) . ") VALUES ($placeholders)";
            }

            $stmt = $this->getPDO()->prepare($sql);
            $stmt->execute($values);

            if (!isset($this->id)) {
                $this->id = $this->getPDO()->lastInsertId();
            }
        });
    }

    public function delete(): void
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function first(): ?self
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table} ORDER BY id ASC LIMIT 1";
        $stmt = $model::$pdo->query($sql);
        return $stmt->fetchObject(static::class) ?: null;
    }

    public static function update(int $id, array $data): void
    {
        $model = new static();
        $model->executeTransaction(function () use ($model, $id, $data) {
            $properties = $model->getUpdateProperties();
            $set = [];
            foreach ($properties as $property) {
                $set[] = "$property = ?";
            }
            $values = array_merge(array_values($data), [$id]);
            $sql = "UPDATE {$model->table} SET " . implode(', ', $set) . " WHERE id = ?";
            $stmt = $model::$pdo->prepare($sql);
            $stmt->execute($values);
        });
    }

    public static function create(array $data): self
    {
        $model = new static();
        foreach ($data as $property => $value) {
            if (in_array($property, $model->fillable)) {
                $model->{$property} = $value;
            }
        }
        $model->save();
        return $model;
    }

    public function where(string $column, string $value, string $operator = '='): self
    {
        // Initialize $sql if it's not yet defined (to avoid the error)
        if (!isset($this->sql)) {
            $this->sql = "SELECT * FROM {$this->table}";
        }

        $this->sql .= " WHERE $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }



    public static function deleteId(int $id): void
    {
        $model = static::find($id);
        if ($model) {
            $model->delete();
        }
    }

    public static function paginate(int $page = 1, int $perPage = 15): array
    {
        $model = new static();
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$model->table} LIMIT $perPage OFFSET $offset";
        $stmt = $model::$pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    protected function getUpdateProperties(): array
    {
        return array_filter($this->fillable, fn ($p) => $p !== 'id' && $p !== 'created_at' && $p !== 'updated_at');
    }

    protected function getInsertProperties(): array
    {
        return array_filter($this->fillable, fn ($p) => $p !== 'id');
    }

    public function getFilable()
    {
        return $this->fillable;
    }


    public function exists(int $id)
    {
        $user = $this->find($id);
        return $user !== null;
    }
}
