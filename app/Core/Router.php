<?php

namespace App\Core;

use App\Http\Controllers\Controller;
use Closure;
use ReflectionMethod;

/**
 * Class Router
 *
 * @package App\Core
 */
class Router
{
    private $routes = [];
    private $middleware = [];
    private $groupOptions = [];
    private $parameters = [];

    public function addRoute($method, $uri, $callback)
    {
        $pattern = $this->convertUriToRegex($uri);

        $this->routes[$method][$pattern] = [
            'action' => $callback,
            'middleware' => $this->middleware,
        ];
    }

    public function convertUriToRegex($uri)
    {
        return str_replace(['{', '}'], ['(?P<', '>[^/]+)'], $uri);
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];

        // Remove query string from URI.
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Normalize route URIs with leading/trailing slashes.
        $requestUri = "/" . trim($requestUri, '/');
        foreach ($this->routes[$requestMethod] ?? [] as $pattern => $route) {

            if (preg_match("~^$pattern$~", $requestUri, $matches)) {

                $parameterValues = array_slice($matches, 1);
                $parameterNames = array_keys($matches);

                $parameterValues = [];
                foreach ($parameterNames as $name) {
                    $parameterValues[$name] = $matches[$name];
                }

                $parameters = array_merge($parameterNames, $parameterValues, $this->parameters);
                if ($this->runMiddleware($route['middleware'])) {
                    $response = $this->invokeAction($route['action'], $parameters);
                    echo $response;
                    return;
                }
            }
        }

        throw new \RuntimeException('No route found for ' . $requestMethod . ' ' . $requestUri);
    }

    private function runMiddleware(array $middleware): bool
    {
        foreach ($middleware as $middleware) {
            if (!$middleware($this)) {
                return false;
            }
        }

        return true;
    }

    private function invokeAction($action, array $parameters): mixed
    {

        if (is_callable($action)) {
            return call_user_func_array($action, $parameters);
        }

        list($controller, $method) = explode('@', $action);

        if (!is_subclass_of($controller, Controller::class)) {
            $controller = '\\App\\Http\\Controllers\\' . $controller;

            if (!class_exists($controller)) {
                throw new \RuntimeException('Controller not found: ' . $controller);
            }

            if (!is_subclass_of($controller, Controller::class)) {
                throw new \RuntimeException('Controller must be a subclass of ' . Controller::class);
            }
        }

        $controller = new $controller;
        $reflectionMethod = new ReflectionMethod($controller, $method);
        $methodParams = $reflectionMethod->getParameters();

        if (!$reflectionMethod->isPublic()) {
            throw new \RuntimeException('Controller method must be public');
        }

        foreach ($methodParams as $param) {
            $paramName = $param->getName();
            $mappedParams[] = $parameters[$paramName];
        }

        // Call method
        return $reflectionMethod->invokeArgs($controller, $mappedParams);
    }

    public function group($prefix, array $middleware = [], array $options = [], Closure $callback)
    {
        $this->groupOptions = array_merge($this->groupOptions, $options);
        $this->middleware = array_merge($this->middleware, $middleware);

        call_user_func_array([$this, 'addRoutes'], func_get_args());

        $this->groupOptions = [];
        $this->middleware = [];
        $this->parameters = [];
    }

    public function get($uri, $callback)
    {
        $this->addRoute('GET', $uri, $callback);
    }

    public function post($uri, $callback)
    {
        $this->addRoute('POST', $uri, $callback);
    }

    public function put($uri, $callback)
    {
        $this->addRoute('PUT', $uri, $callback);
    }

    public function delete($uri, $callback)
    {
        $this->addRoute('DELETE', $uri, $callback);
    }

    public function patch($uri, $callback)
    {
        $this->addRoute('PATCH', $uri, $callback);
    }
}
