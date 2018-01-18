<?php 

/**
 * Route
 *
 * Class to represent a route definition
 */
class Route
{
	/**
     * Properties
     */

    /**
     * The callback method to execute when the route is matched
     *
     * Any valid "callable" type is allowed
     *
     * @link http://php.net/manual/en/language.types.callable.php
     * @type callable
     */
	protected $callback;

	/**
     * The URL path to match
     *
     * Allows for regular expression matching and/or basic string matching
     *
     * Examples:
     * - '/posts'
     * - '/posts/[:post_slug]'
     * - '/posts/[i:id]'
     *
     * @type string
     */
	protected $path;

	/**
     * Build a Route instance
     *
     * @param string $path          Route URI path to match
     * @param callable $callback    Callable callback method to execute on route match
    */
	public function __construct($path, $callback){
		$this->setPath($path);
		$this->setCallback($callback);
	}

	public function setPath($path){
		$this->path=$path;
	}

	public function getPath(){
		return $this->path;
	}

	public function setCallback($callback){
		if (!is_callable($callback)) {
		    throw new InvalidArgumentException('Expected a callable. Got an uncallable '. gettype($callback));
		}

		$this->callback = $callback;

		return $this;
    	}

	public function getCallback(){
		return $this->callback;
	}
}
?>
