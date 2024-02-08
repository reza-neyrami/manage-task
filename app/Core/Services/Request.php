<?php

namespace App\Core\Services;


class Request 
{
    private $params;

    public function __construct()
    {
        $this->params = $_REQUEST;
        header('Content-Type: application/json');
    }


    public function get($key, $default = null)
    {

        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    public function all()
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
        return $_SERVER["REQUEST_METHOD"];
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
}

