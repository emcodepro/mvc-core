<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 06-Mar-21
 * Time: 18:14
 */

namespace emcodepro\mvc;


class Session
{
    const FLASH_KEY = 'type_flash';

    /**
     * Session constructor.
     */
    public function __construct()
    {
        session_start();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as &$flashMessage)
        {
            $flashMessage['removed'] =  true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     */
    public function get($key)
    {
        return $_SESSION[$key];
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setFlash($key, $value)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'value' => $value,
            'removed' => false
        ];
    }

    /**
     * @param $key
     * @return bool
     */
    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    /**
     * Remove all flash after application destruct
     */
    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage)
        {
            if($flashMessage['removed']){
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}