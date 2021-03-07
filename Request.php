<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 04-Mar-21
 * Time: 21:43
 */

namespace app\core;

class Request
{
    /**
     * Returns path from url
     * @return bool|string
     */
    public function getPath()
    {
       $path = $_SERVER['REQUEST_URI'];
       $position = strpos($path, '?') ?? false;

       if($position === false){
           return $path;
       }

       return substr($path, 0, $position);
    }

    /**
     * Returns request method
     * @return string
     */
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isPost()
    {
        return $this->getMethod() == 'post' ?? false;
    }

    public function isGet()
    {
        return $this->getMethod() == 'get' ?? false;
    }

    public function requestBody()
    {
        $body = [];

        if($this->isPost()){
            foreach ($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        if($this->isGet()){
            foreach ($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}