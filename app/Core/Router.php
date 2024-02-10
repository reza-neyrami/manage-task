<?php

namespace App\Core;


use App\Core\Services\Container;
use App\Core\Services\Request;
use App\Core\Services\Response;
use Closure;
use ReflectionMethod;


class Router
{
    private $routes = [];
    private $middleware = [];
    private $groupOptions = [];
    private $parameters = [];
    protected $container;
    protected $prefix;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function addRoute($method, $uri, $callback, array $middleware = [])
    {
        // If a prefix is set, add it to the URI
        if ($this->prefix) {
            $uri = $this->prefix . $uri;
        }
    
        $pattern = $this->convertUriToRegex($uri);
    
        $this->routes[$method][$pattern] = [
            'action' => $callback,
            'middleware' => $middleware,
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

            if (preg_match("~^$pattern~", $requestUri, $matches)) {

                $parameterValues = array_slice($matches, 1);
                $parameterNames = array_keys($matches);

                $parameterValues = [];
                foreach ($parameterNames as $name) {
                    $parameterValues[$name] = $matches[$name];
                }
                $parameters = array_merge($parameterNames, $parameterValues, $this->parameters);
                if ($this->runMiddleware($route['middleware'])) {
                    $response = $this->invokeAction($route['action'], $parameters);
                    Response::json($response);
                    return;
                }
            }
        }

        throw new \RuntimeException('No route found for ' . $requestMethod . ' ' . $requestUri);
    }

    private function runMiddleware(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->container->make($middleware);
            }

            if (!$middleware) {
                return false;
            }

            // Pass Closure instance to middleware
            $middleware->handle($_REQUEST, fn () => true);
        }

        return true;
    }

    private function invokeAction($action, array $parameters): mixed
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $parameters);
        }

        list($controller, $method) = explode('@', $action);
        $controller = '\\App\\Http\\Controllers\\' . $controller;
        $controller = $this->container->make($controller);

        $reflectionMethod = new ReflectionMethod($controller, $method);

        if (!$reflectionMethod->isPublic()) {
            throw new \RuntimeException('Controller method must be public');
        }

        $mappedParams = array_map(function ($param) use ($parameters) {
            return $parameters[$param->getName()];
        }, $reflectionMethod->getParameters());

        return $reflectionMethod->invokeArgs($controller, $mappedParams);
    }

    public function group($prefix, array $middleware = [], array $options = [], Closure $callback)
    {
        $this->groupOptions = array_merge($this->groupOptions, $options);
        $this->middleware = array_merge($this->middleware, $middleware);
    
        // Add the prefix to each route in the group
        $this->prefix = $prefix;
    
        call_user_func_array($callback, [$this]);
    
        $this->groupOptions = [];
        $this->middleware = [];
        $this->parameters = [];
        $this->prefix = '';
    }

    public function get($uri, $callback, array $middleware = [])
    {
        $this->addRoute('GET', $uri, $callback, $middleware);
    }

    public function post($uri, $callback, array $middleware = [])
    {
        $this->addRoute('POST', $uri, $callback, $middleware);
    }

    public function put($uri, $callback, array $middleware = [])
    {
        $this->addRoute('PUT', $uri, $callback, $middleware);
    }

    public function delete($uri, $callback, array $middleware = [])
    {
        $this->addRoute('DELETE', $uri, $callback, $middleware);
    }

    public function patch($uri, $callback, array $middleware = [])
    {
        $this->addRoute('PATCH', $uri, $callback, $middleware);
    }
}
