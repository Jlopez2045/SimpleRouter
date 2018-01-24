<?php 
/**
 * An exception derivation which represents that a route hasn't been found
 *
 * @package router
 */

namespace router\Exceptions;

class RouteNotFoundException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>