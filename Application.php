<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 04-Mar-21
 * Time: 21:26
 */

namespace app\core;

use app\core\db\Database;
use app\models\User;

class Application
{
    public $request;
    public $response;
    public $router;
    public $controller;
    public $session;
    public $user = null;
    public $userClass;
    public $view;

    public $db;
    public static $app;
    public static $ROOT_PATH;

    /**
     * Application constructor.
     * @param $rootPath
     */
    public function __construct($rootPath, $config)
    {
        self::$app = $this;
        self::$ROOT_PATH = $rootPath;

        $this->userClass = $config['userClass'];
        $this->db = new Database($config['db']);
        $this->request = new Request();
        $this->session = new Session();
        $this->controller = new Controller();
        $this->view = new View();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);

        $primaryValue = $this->session->get('user');
        if(!empty($primaryValue)){
            $primaryKey = $this->userClass::primaryKey();
            $user = $this->userClass::findOne([$primaryKey => $primaryValue]);
            $this->user = $user;
        }
    }

    /**
     * Resolve routing and run application
     */
    public function run()
    {
        try{
            echo $this->router->resolve();
        }catch (\Exception $e){
            echo $this->router->render('_errors', ['exception' => $e]);
        }
    }

    public function login(User $user){
        $this->user = $user;
        $primaryKey = $user::primaryKey();
        $this->session->set('user', $user->{$primaryKey});
        return true;
    }

    public static function isGuest()
    {
        return empty(self::$app->user);
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
        return true;
    }
}