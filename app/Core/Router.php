<?php

namespace App\Core;

use App\Core\Services\Container;
use App\Core\Services\Request;
use Closure;
use ReflectionMethod;

class Router
{
    private $routes = [];
    private $middleware = [];
    private $groupOptions = [];
    private $parameters = [];
    protected $container;
    protected $customPatterns;
    protected $request;
    protected $prefix;

    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function addRoute($method, $uri, $callback, array $middleware = [])
    {
        $prefix = '';
        if ($this->prefix) {
            $uri = $this->prefix . rtrim($uri, '/');
            $prefix = explode('/', $this->prefix)[0];
        }
        $pattern = $this->convertUriToRegex($uri);
        $middleware = array_merge($this->middleware, $middleware);
        $this->routes[$method][$pattern] = [
            'action' => $callback,
            'middleware' => $middleware,
            'prefix' => $prefix, // اضافه کردن پیشوند
        ];
    
        // مرتب‌سازی مسیرها بر اساس پیشوند، به طور صعودی
        uasort($this->routes[$method], function ($a, $b) {
            return strcmp($a['prefix'], $b['prefix']);
        });
    }
    
   
    
    // این کمی بهتره
    public function convertUriToRegex($uri)
    {
        $pattern = preg_replace_callback('/\{(\w+):(\w+)\}/', function ($matches) {
            if ($matches[2] == 'int') {
                return "(?P<{$matches[1]}>\d+)";
            } else if ($matches[2] == 'string') {
                return "(?P<{$matches[1]}>[^\/]+)";
            }
        }, $uri);

        // اضافه کردن $ به انتهای الگو برای پایان خط
        $pattern .= '$';
      
        return "#^{$pattern}#";
    }

     public function run()
    {
        $requestMethod = $this->request->Method();
        $requestUri = $this->request->getPath();

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $requestUri = "/" . trim($requestUri, '/');
        $bestMatch = null;
        $bestMatchLength = 0;
       

        foreach ($this->routes[$requestMethod] ?? [] as $pattern => $route) {
            if (preg_match($pattern, $requestUri, $matches)) {
            
                $matchLength = strlen($matches[0]);
                if ($matchLength > $bestMatchLength) {
                    $bestMatch = $route;
                    $bestMatchLength = $matchLength;
                    if ($bestMatch) {
                        $parameterValues = array_slice($matches, 1);
                        $parameterNames = array_keys($matches);

                        $parameterValues = [];
                        foreach ($parameterNames as $name) {
                            $parameterValues[$name] = $matches[$name];
                        }
                        // filter string key parameters to action and match for action...
                        $parameterValues = array_filter($parameterValues, function($key) {
                            return is_string(trim($key));
                        }, ARRAY_FILTER_USE_KEY);

                        $parameters = array_replace($parameterValues, $this->parameters);
                        if ($this->runMiddleware($bestMatch['middleware'])) {
                            
                            $response = $this->invokeAction($bestMatch['action'], $parameters);
                            echo $response;

                            return;
                        }
                    } else {
                        throw new \RuntimeException('No route found for ' . $requestMethod . ' ' . $requestUri);
                    }
                }
            }
        }

    }

    private function runMiddleware(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->container->make($middleware);
            }

            if (!$middleware) {
                throw new \RuntimeException('Middleware not found: ' . $middleware);
            }

            $middleware->handle($this->request, fn() => true);
        }

        return true;
    }

    private function invokeAction($action, array $parameters): mixed
    {
        // dd($parameters);
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
    
        // Extract the first name from the prefix
        $firstName = explode('/', trim($prefix, '/'))[0];
    
        call_user_func_array($callback, [$this]);
    
        // Now you can use $firstName to sort your routes
        // ...
    
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
