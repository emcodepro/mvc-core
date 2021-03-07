<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 07-Mar-21
 * Time: 20:50
 */

namespace app\core;


class View
{
    public $title = '';
    public function render($callback, $params = [])
    {
        $getView = $this->renderView($callback, $params);
        $getLayout = $this->renderLayout();
        return str_replace("{{content}}", $getView, $getLayout);
    }

    public function renderView($callback, $params)
    {
        foreach ($params as $key => $param){
            $$key = $param;
        }

        ob_start();
        include_once Application::$ROOT_PATH . "/views/$callback.php";
        return ob_get_clean();
    }

    public function renderLayout()
    {
        ob_start();
        include_once Application::$ROOT_PATH . "/views/layouts/main.php";
        return ob_get_clean();
    }
}