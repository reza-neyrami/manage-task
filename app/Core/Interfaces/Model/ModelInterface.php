<?php

namespace App\Core\Interfaces\Model;


interface ModelInterface 
{
    public function getTableName(): string;
    public function save(): void;
    public function delete(): void;
    public  function getAll(): array;
    // ... سایر متدهای کاربردی

    public static function find(int $id);
    public static function first(): ?self;
    public static function findAll(): array;
    public static function create(array $data): self;
    public static function update(int $id, array $data): void;
    public static function deleteId(int $id): void;
    public static function paginate(int $limit = 15, int $page = 1): array;
    // public static function with(array $relations): self;
}
