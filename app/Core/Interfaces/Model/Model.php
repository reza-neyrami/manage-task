<?php

namespace App\Core\Interfaces\Model;

use App\Core\Interfaces\Model\BaseModel\BaseModel;
use App\Core\Interfaces\Model\QueryBuilder\Conditions;
use App\Core\Interfaces\Model\QueryBuilder\Relations;
use PDO;

abstract class Model extends BaseModel implements ModelInterface
{
    use Relations, Conditions;
    protected $relations;

    public function __construct()
    {
        $this->getPDO();
    }

    public function __set($name, $value)
    {
        $this->bindings[$name] = $value;
        if (isset($this->{$name}) && $this->{$name} !== $value) {
            $this->dirty[$name] = $value;
        }

        $this->{$name} = $value;
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

    public function getAll(): array
    {
        $model = new static();
        $sql = "SELECT * FROM {$model->table}";
        if (isset($this->sql)) {
            $sql = $this->sql;
        }

        $stmt = $model::$pdo->prepare($sql);
        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }

        $stmt->execute();
        unset($this->sql, $this->bindings);

        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public function save(): void
    {
        $this->executeTransaction(function () {
            $properties = $this->getUpdateProperties();
            $placeholders = implode(', ', array_fill(0, count($properties), '?'));
            if (isset($this->id)) {
                $set = [];
                foreach ($properties as $property) {
                    $set[] = "$property = ?";
                }
                $values = array_merge(array_map(fn($p) => $this->{$p}, $properties), [$this->id]);

                $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE id = ?";
            } else {
                $sql = "INSERT INTO {$this->table} (" . implode(', ', $properties) . ") VALUES ($placeholders)";
                $values = array_map(fn($p) => $this->{$p}, $properties);
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
            $properties = array_flip($model->getUpdateProperties());

            $set = [];
            foreach ($data as $key => $value) {
                if (isset($properties[$key])) {
                    $set[] = "$key = ?";
                }
            }

            // dd(array_intersect_key($data, $properties));
            $values = array_values(array_intersect_key($data, $properties));
            $values[] = $id;

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
        return array_filter($this->fillable, fn($p) => $p !== 'id' && $p !== 'created_at' && $p !== 'updated_at');
    }

    protected function getInsertProperties(): array
    {
        return array_filter($this->fillable, fn($p) => $p !== 'id');
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
