<?php
/*namespace router;
use router\Exceptions\RouteNotFoundException;*/
require_once 'route.php';
require_once 'Exceptions/RouteNotFoundException.php';

/**
* 
*/
class Router
{
	/**
     * Class properties
     */
	protected $match_types = array(
		'i'  => '[0-9]+',
		'a'  => '[0-9A-Za-z]+',
		'h'  => '[0-9A-Fa-f]+',
		's'  => '[0-9A-Za-z-_]+',
		'*'  => '.+?',
		'**' => '.+',
		''   => '[^/\.]+'
	);


    /**
     * Collection of routes
     *
     * @type array
     */
	protected $routes = array();

	protected $matches;

	/**
	 * Map a route
	 *
	 * @param string $uri_patern_path The route pre-regex patern, You can use multiple pre-set regex filters, like [i:id]
	 * @param callback $callback The callback method to execute when the route is matched
	 */
	public function map($uri_patern_path, $callback){
		$this->routes[] = new Route($uri_patern_path, $callback);
	}

	public function respond(){
		$uri = strtolower(filter_var(strip_tags($_SERVER['REQUEST_URI']), FILTER_SANITIZE_URL));
		foreach ($this->routes as $route) {
			if ($this->matches($route)) {
				return $route->executeCallback($this->matches);
				//die();
			}
		}
		throw new RouteNotFoundException("No routes matching the requested URI ''{$_SERVER['REQUEST_URI']}''");
	}
	/**
     * See if the requested URI matches with a route
     *
     * @param Route $route
     * @return boolean
     */
	public function matches(Route $route){
		$uri = filter_var(strip_tags($_SERVER['REQUEST_URI']), FILTER_SANITIZE_URL);
		if (preg_match('#' . $this->parseUriParameters($route->getPath()) . '$#i', $uri, $this->matches)) {
	        	return true;
		}
		return false;
	}

	/**
	 * Reverse regex
	 *
	 * Replace the supplied URI patern parameters with their respective regex
	 * @param string $path_patern The URI of the route with named parameters in place.
	 */
	public function parseUriParameters($path_patern){
		//extract text between square brackets '[]' and separated by ':' [type:parameter]
		//if (preg_match_all('`\[([^:\]]*+)(?::([^:\]]*+))?\]`', $path_patern, $matches, PREG_SET_ORDER)) {
		preg_match_all('#\[([^:\]]*+)(?::([^:\]]*+))?\]#', $path_patern, $matches, PREG_SET_ORDER);
			//transform uri patern "article/[a:articleid]/post/[s:slug]"
			//to the new regex uri patern "article/([0-9A-Za-z]++)/post/([0-9A-Za-z-_]++)"
			$new_uri_path=$path_patern;
			foreach($matches as $index) {
				list($block, $type, $parameter) = $index;
				$new_uri_path = str_replace($block, "(" . $this->match_types[$type] . ")", $new_uri_path);
			}
			return $new_uri_path;
		//}
	}
}
?>
