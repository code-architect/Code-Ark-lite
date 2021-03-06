<?php


namespace codearchitect\codearklite;

/**
 * Class Request
 * @author: Indranil Samanta (code-architec)
 * @package codearchitect\codearklite
 */
class Request
{
    /**
     * Get the current requested url [e.g: if the url is https://xys.com/user?id=12, this will return /user]
     * @return false|mixed|string
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if($position === false)
        {
            return $path;
        }
        return $path = substr($path, 0, $position);
    }

    /**
     * Get the requested method in $_SERVER super global
     * @return string
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get';
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function getBody()
    {
        $body = [];
        if($this->method() === 'get'){
            foreach ($_GET as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if($this->method() === 'post'){
            foreach ($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}