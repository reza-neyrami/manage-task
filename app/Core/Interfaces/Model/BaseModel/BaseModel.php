<?php
namespace App\Core\Interfaces\Model\BaseModel;

use App\Core\TraitS\Arrayable;
use App\Core\TraitS\DatabaseConnectionTrait;

abstract class BaseModel{
    use DatabaseConnectionTrait,Arrayable;
    protected $table;
    protected $sql;
    protected $bindings = [];
    protected $whereUsed = false;
    protected $fillable = [];
    protected $toArray = [];
    public $id;
    protected $dirty = [];
  
}