<?php

namespace App\Core\Interfaces\Model;

use JsonSerializable;

interface ModelInterface extends Arrayable, JsonSerializable
{
    public function getTableName(): string;
    public function save(): void;
    public function update(): void;
    public function delete(): void;
    // ... سایر متدهای کاربردی

    public static function find(int $id);
    // public static function first(): ?self;
    public static function findAll(): array;
    public static function create(array $data): array;
    // public static function paginate(int $limit = 15, int $page = 1): array;
    // public static function with(array $relations): self;
}
