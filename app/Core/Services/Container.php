<?php

namespace App\Core\Services;

use Exception;
use ReflectionClass;

class Container {
    protected $instances = [];

    public function make($className) {
        if (!isset($this->instances[$className])) {
            $reflector = new ReflectionClass($className);
            if (!$reflector->isInstantiable()) {
                throw new Exception("Class [$className] is not instantiable.");
            }
            $constructor = $reflector->getConstructor();
            if (is_null($constructor)) {
                $this->instances[$className] = $reflector->newInstance();
            } else {
                $parameters = $constructor->getParameters();
                $dependencies = $this->getDependencies($parameters);
                $this->instances[$className] = $reflector->newInstanceArgs($dependencies);
            }
        }
        return $this->instances[$className];
    }

    public function getDependencies($parameters) {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if ($dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve class dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->make($dependency->name);
            }
        }
        return $dependencies;
    }
}

