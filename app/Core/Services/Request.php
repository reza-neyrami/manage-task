<?php

namespace App\Core\Services;

class Request
{
    protected $params;

    public function __construct()
    {
        $this->params = $this->body();
        // $this->params = $this->request;
        header('Content-Type: application/json');
        header("Access-Control-Allow-Headers: Authorization");
    }

    public function header($key, $default = null)
    {
        $headers = apache_request_headers();
        $jwt_token = $headers[$key] ?? null;
        return $jwt_token ?? $default;
    }
    public function __get($name)
    {
        return $this->params->{$name} ?? null;
    }

    public function __set($name, $value)
    {
        $this->params->{$name} = $value;
    }

    protected function content()
    {
        return file_get_contents('php://input');
    }

    public function all()
    {
        return json_decode($this->content(), true);
    }
    public function body()
    {
        return json_decode($this->content());

    }

    public function input(...$keys)
    {
        $data = json_decode($this->content(), true);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $data[$key] ?? null;
        }
        return $result;
    }

    public function get($key, $default = null)
    {
        return isset($this->params->{$key}) ? $this->params->{$key} : $default;
    }

    public function request()
    {
        return $this->params;
    }

    public function has($key)
    {
        return isset($this->params[$key]);
    }

    public function only($keys)
    {
        $filtered = [];
        foreach ($keys as $key) {
            if (isset($this->params[$key])) {
                $filtered[$key] = $this->params[$key];
            }
        }
        return json_encode($filtered);
    }

    public function except($keys)
    {
        $filtered = [];
        foreach ($this->params as $key => $value) {
            if (!in_array($key, $keys)) {
                $filtered[$key] = $value;
            }
        }
        return json_encode($filtered);
    }

    public function method()
    {
        return strtoupper($_SERVER["REQUEST_METHOD"]);
    }

    public function isGet()
    {
        return $this->method() === "GET";
    }

    public function isPost()
    {
        return $this->method() === "POST";
    }

    public function isPut()
    {
        return $this->method() === "PUT";
    }

    public function isDelete()
    {
        return $this->method() === "DELETE";
    }

    public function isAjax()
    {
        return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest";
    }

    public function ip()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function userAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public function referrer()
    {
        return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;
    }

    public function __toString()
    {
        return json_encode($this->params);
    }

    public function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getPath()
    {
        $uri = $this->getUri();
        $path = parse_url($uri, PHP_URL_PATH);
        return $path;
    }
}
