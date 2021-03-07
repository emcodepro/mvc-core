<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 06-Mar-21
 * Time: 23:13
 */

namespace app\core\exceptions;


use Throwable;

class ForbiddenException extends \Exception
{
    public function __construct(string $message = "You don't have permission to access this page", int $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}