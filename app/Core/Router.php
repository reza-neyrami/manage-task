<?php

namespace App\Core;

use App\Controllers\Controller;
use ReflectionMethod;

class Router {

    private $routes = [];

    public function __construct() {
        $this->routes = [
            'GET' => [],
            'POST' => [],
            'PUT' => [],
            'DELETE' => [],
            'PATCH' => [],
        ];
    }

    public function add($method, $route, $callback) {
        $this->routes[strtoupper($method)][$route] = $callback;
    }

    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];

        foreach ($this->routes[$requestMethod] as $route => $callback) {
            if (preg_match("~^$route$~", $requestUri, $matches)) {
                array_shift($matches); // remove the first match which is the full text

                $response = $this->invokeCallback($callback, $matches);

                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
        }

        throw new \RuntimeException('No route found for ' . $requestMethod . ' ' . $requestUri);
    }

    private function invokeCallback($callback, $matches) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $matches);
        } else {
            list($controller, $method) = explode('@', $callback);

            if (!is_subclass_of($controller, Controller::class)) {
                throw new \RuntimeException('Controller must be a subclass of ' . Controller::class);
            }

            $controller = new $controller;
            $reflectionMethod = new ReflectionMethod($controller, $method);

            if (!$reflectionMethod->isPublic()) {
                throw new \RuntimeException('Controller method must be public');
            }

            return $reflectionMethod->invokeArgs($controller, $this->resolveMethodParameters($reflectionMethod, $matches));
        }
    }

    private function resolveMethodParameters(ReflectionMethod $reflectionMethod, array $matches) {
        $parameters = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            if ($parameter->hasType()) {
                $parameterType = $parameter->getType();

                if ($parameterType->isBuiltin()) {
                    $parameterValue = filter_input(INPUT_GET, $parameterName);
                } else {
                    // ... (تزریق از طریق Dependency Injection)
                }
            } else {
                // ... (مدیریت پارامترهای بدون نوع)
            }

            $parameters[] = $parameterValue;
        }

        return $parameters;
    }
}
