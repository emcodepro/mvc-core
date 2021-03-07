<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 05-Mar-21
 * Time: 10:13
 */

namespace app\core;

class Response
{
    public function setStatusCode($code)
    {
        http_response_code($code);
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }
}