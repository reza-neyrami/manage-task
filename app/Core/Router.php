<?php

namespace App\Core;

use App\Http\Controllers\Controller;
use ReflectionMethod;
use Closure;

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
    /**
     * Register a route for a specific HTTP method and URI.
     *
     * @param string $method   HTTP method (GET, POST, PUT, DELETE, PATCH)
     * @param string $uri      URI pattern
     * @param mixed  $callback Action to be executed for the route
     * @return void
     */
    public function addRoute($method, $uri, $callback)
    {
        $this->routes[$method][$uri] = [
            'action' => $callback,
            'middleware' => $this->middleware,
        ];
    }

    /**
     * Define a GET route.
     *
     * @param string $uri      URI pattern
     * @param mixed  $callback Action to be executed for the route
     * @return void
     */
    public function get($uri, $callback)
    {
        $this->addRoute('GET', $uri, $callback);
    }

    /**
     * Define a POST route.
     *
     * @param string $uri      URI pattern
     * @param mixed  $callback Action to be executed for the route
     * @return void
     */
    public function post($uri, $callback)
    {
        $this->addRoute('POST', $uri, $callback);
    }

    /**
     * Define a PUT route.
     *
     * @param string $uri      URI pattern
     * @param mixed  $callback Action to be executed for the route
     * @return void
     */
    public function put($uri, $callback)
    {
        $this->addRoute('PUT', $uri, $callback);
    }

    /**
     * Define a DELETE route.
     *
     * @param string $uri      URI pattern
     * @param mixed  $callback Action to be executed for the route
     * @return void
     */
    public function delete($uri, $callback)
    {
        $this->addRoute('DELETE', $uri, $callback);
    }

    /**
     * Define a PATCH route.
     *
     * @param string $uri      URI pattern
     * @param mixed  $callback Action to be executed for the route
     * @return void
     */
    public function patch($uri, $callback)
    {
        $this->addRoute('PATCH', $uri, $callback);
    }
    /**
     * Register a route group with shared middleware and options.
     *
     * @param string $prefix    Prefix to prepend to all URIs in the group
     * @param array  $middleware Optional middleware to apply to all routes in the group
     * @param array  $options   Additional options for route group (e.g., namespace)
     * @param Closure $callback Callback to define routes within the group
     *
     * @return void
     */
    public function group($prefix, array $middleware = [], array $options = [], Closure $callback)
    {
        $this->groupOptions = array_merge($this->groupOptions, $options);
        $this->middleware = array_merge($this->middleware, $middleware);

        call_user_func_array([$this, 'addRoutes'], func_get_args());

        $this->groupOptions = [];
        $this->middleware = [];
    }

    /**
     * Execute the router and dispatch the appropriate route.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];

        // Remove query string from URI.
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Normalize route URIs with leading/trailing slashes.
        $requestUri =  "/" . trim($requestUri, '/');
        foreach ($this->routes[$requestMethod] ?? [] as $pattern => $route) {

            if (preg_match("~^$pattern$~", $requestUri, $matches)) {
                array_shift($matches); // Remove the full text match.

                if ($this->runMiddleware($route['middleware'])) {
                    $response = $this->invokeAction($route['action'], $matches);
                    echo $response;
                    return;
                }
            }
        }

        throw new \RuntimeException('No route found for ' . $requestMethod . ' ' . $requestUri);
    }

    /**
     * Execute the given route middleware and check for halts.
     *
     * @param array $middleware Middlewares to execute
     *
     * @return bool True if middleware allows route execution, false otherwise
     */
    private function runMiddleware(array $middleware): bool
    {
        foreach ($middleware as $middleware) {
            if (!$middleware($this)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Invoke the route action and handle its return value.
     *
     * @param mixed  $action    Action to be executed
     * @param array  $parameters Route parameters to pass to the action
     *
     * @return mixed Response from the action execution
     *
     * @throws \ReflectionException
     */
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

        if (!$reflectionMethod->isPublic()) {
            throw new \RuntimeException('Controller method must be public');
        }

        return $reflectionMethod->invokeArgs($controller, $this->resolveMethodParameters($reflectionMethod, $parameters));
    }


    private function resolveMethodParameters(ReflectionMethod $reflectionMethod, array $matches): array
    {
        $parameters = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            if (!$parameter->hasType()) {
                $parameterValue = $matches[$parameterName] ?? null; // استفاده از مقادیر از URI
                $parameterType = $parameter->getType();

                if ($parameterType->isBuiltin()) {
                    $parameterValue = filter_input(INPUT_GET, $parameterName);
                } else {
                    // Dependency Injection)
                }
            } else {
                // ... (مدیریت پارامترهای بدون نوع)
            }

            $parameters[] = $parameterValue;
        }

        return $parameters;
    }
}
