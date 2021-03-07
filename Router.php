<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 04-Mar-21
 * Time: 21:30
 */

namespace emcodepro\mvc;

use emcodepro\mvc\exceptions\ForbiddenException;
use emcodepro\mvc\exceptions\NotFoundException;

class Router
{
    public $routes = [];
    public $response;
    public $request;

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Adding GET routes to array
     * @param $path
     * @param $callback
     */
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Adding POST routes to array
     * @param $path
     * @param $callback
     */
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * Call function which called for routing
     * @return mixed|string
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();


        $callback = $this->routes[$method][$path];

        if(!$callback){
            $this->response->setStatusCode(404);
            throw new NotFoundException();
        }

        $class = new $callback[0]();
        Application::$app->controller = $class;

        if(in_array($callback[1], Application::$app->controller->getRestrictedActions())){
            $this->response->setStatusCode(403);
            throw new ForbiddenException();
        }

        $callback[0] = $class;
        return call_user_func($callback, $this->request, $this->response);
    }

    public function render($callback, $params = [])
    {
        return Application::$app->view->render($callback, $params);
    }
}