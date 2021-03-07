<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 05-Mar-21
 * Time: 10:00
 */

namespace emcodepro\mvc\exceptions;


use Throwable;

class NotFoundException extends \Exception
{

    public function __construct(string $message = "Page not found", int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}