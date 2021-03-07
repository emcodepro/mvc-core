<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 05-Mar-21
 * Time: 10:18
 */

namespace app\core;


class Controller
{
    const ACCESS_BEHAVIOR = 'access';
    const RULE_AUTHORIZED = 'authorized';

    public $behaviors = [];

    public function __construct()
    {
        if(!empty($this->behaviors())){
            foreach ($this->behaviors() as $behavior => $value){
                if($behavior === self::ACCESS_BEHAVIOR && $value['rule'][0] === self::RULE_AUTHORIZED && Application::isGuest()){
                    $this->behaviors[self::ACCESS_BEHAVIOR][] = $value['actions'];
                }
            }
        }
    }

    public function behaviors(): array
    {
        return [];
    }

    public function render($view, $params = [])
    {
        return Application::$app->router->render($view, $params);
    }

    public function getRestrictedActions()
    {
        $actions = [];
        if($this->behaviors[self::ACCESS_BEHAVIOR]){
            foreach ($this->behaviors[self::ACCESS_BEHAVIOR] as $value){
                array_push($actions, $value);
            }
        }
        return $actions[0] ?? $actions;
    }

}