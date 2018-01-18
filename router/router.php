<?php
require_once 'route.php';


/**
* 
*/
class Router
{
	/**
     * Class properties
     */
	/**
     * The types to detect in a defined match "block"
     *
     * Examples of these blocks are as follows:
     *
     * - integer:       '[i:id]'
     * - alphanumeric:  '[a:username]'
     * - hexadecimal:   '[h:color]'
     * - slug:          '[s:article]'
     *
     * @type array
     */

	/**
	 * @var array Array of default match types (regex helpers)
	 * *                    // Match all request URIs
	 * [i]                  // Match an integer 
	 * [i:id]               // Match an integer as 'id' - '[i:id]'
	 * [a:action]           // Match alphanumeric characters as 'action'
	 * [h:key]              // Match hexadecimal characters as 'key'
	 * [s:slug]             // Match alphanumeric characters as 'slug'
	 * [:action]            // Match anything up to the next / or end of the URI as 'action'
	 * [create|edit:action] // Match either 'create' or 'edit' as 'action'
	 * [*]                  // Catch all (lazy, stops at the next trailing slash)
	 * [*:trailing]         // Catch all as 'trailing' (lazy)
	 * [**:trailing]        // Catch all (possessive - will match the rest of the URI)
	 * .[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional
	*/
	protected $match_types = array(
		'i'  => '[0-9]++',
		'a'  => '[0-9A-Za-z]++',
		'h'  => '[0-9A-Fa-f]++',
		's'  => '[0-9A-Za-z-_]++',
		'*'  => '.+?',
		'**' => '.++',
		''   => '[^/\.]++'
	);


    /**
     * Collection of routes
     *
     * @type array
     */
	protected $routes = array();

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
			if (preg_match('~' . $this->parseUriParameters($route->getPath()) . '~', $uri, $matches)) {
				call_user_func_array($route->getCallback(), $matches);
				die();
			}
		}
	}

	/**
	 * Reverse regex
	 *
	 * Replace the supplied URI patern parameters with their respective regex
	 * @param string $path_patern The URI of the route with named parameters in place.
	 */
	public function parseUriParameters($path_patern){
		//extract text between square brackets '[]' and separated by ':' [type:parameter]
		if (preg_match_all('`\[([^:\]]*+)(?::([^:\]]*+))?\]`', $path_patern, $matches, PREG_SET_ORDER)) {
			$new_uri_path=$path_patern;
			foreach($matches as $index) {
				list($block, $type, $parameter) = $index;
				$new_uri_path = str_replace($block, "(" . $this->match_types[$type] . ")", $new_uri_path);
			}
			return $new_uri_path;
		}
	}
}
?>
