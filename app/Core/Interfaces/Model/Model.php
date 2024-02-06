<?php
namespace App\Core\Interfaces\Model;

use ModelInterface;

abstract class Model implements ModelInterface{

    public function __construct(array $attributes = []){
        $this->bootIfNotBooted();
        $this->syncOriginal();
        $this->fill($attributes);
    }
    public function __get($key){
        return $this->getAttribute($key);
    }
    public function __set($key, $value){
        $this->setAttribute($key, $value);
    }
    public function __isset($key){
        return $this->hasGetMutator($key);
    }
    public function __unset($key){
        $this->offsetUnset($key);
    }
    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }
        return $this->callCustomMethod($method, $parameters);
    }

    public function newInstance($attributes = []){
        $model = new static((array) $attributes);
        $model->setExists(false);
        return $model;
    }
}