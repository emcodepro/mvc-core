<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 05-Mar-21
 * Time: 15:18
 */

namespace app\core\form;


class Form
{
    /**
     * @param string $action
     * @param string $method
     * @return Form
     */
    public static function begin($action = '', $method = 'get')
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);

        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field($model, $attribute)
    {
        return new Field($model, $attribute);
    }
}