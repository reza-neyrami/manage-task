<?php

namespace App\Core\Interfaces\Model;



abstract class Model implements ModelInterface
{
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
    
    public function save(): void
    {
    }
}
