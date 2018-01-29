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
		's'  => '[\w-]+',
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
	
	/**
	 * @var string Can be used to ignore leading part of the Request URL (if main file lives in subdirectory of host)
	 */
	protected $base_path = "";

	protected $matches;

	protected $request_uri;

	public function __construct($base_path = null){
		//is used parse_url [path] to avoid getting GET parameters in the requested URI and thus not make mistakes with preg_match against any route
		$this->request_uri = filter_var(parse_url(strip_tags($_SERVER['REQUEST_URI']))['path'], FILTER_SANITIZE_URL);
		$this->setBasePath($base_path);
	}

	/**
	 * Set the base path.
	 * Useful if you are running your application from a subdirectory.
	 */
	public function setBasePath($basePath) {
		$this->base_path = $basePath;
	}

	/**
	 * Map a route
	 *
	 * @param string $uri_patern_path The route pre-regex patern, You can use multiple pre-set regex filters, like [i:id]
	 * @param callback $callback The callback method to execute when the route is matched
	 */
	public function map($uri_patern_path, $callback){
		$this->routes[] = new Route($this->base_path . $this->parseUriParameters($uri_patern_path), $callback);
	}

	public function respond(){
		foreach ($this->routes as $route) {
			if ($this->matches($route)) {
				return $route->executeCallback($this->matches);
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
	private function matches(Route $route){
	    if (preg_match('~/?' . $route->getPath() . '/?$~iu', $this->request_uri, $this->matches)) {
	    	unset($this->matches[0]); //unset the requested URI from the array
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
	private function parseUriParameters($path_patern){
		//extract text between square brackets '[]' and separated by ':' [type:parameter]
		preg_match_all('~\[([^:\]]*+)(?::([^:\]]*+))?\]~iu', $path_patern, $matches, PREG_SET_ORDER);
		//transform uri patern "article/[a:articleid]/post/[s:slug]"
		//to the new regex uri patern "article/([0-9A-Za-z]++)/post/([0-9A-Za-z-_]++)"
		$new_uri_path=$path_patern;
		foreach($matches as $index) {
			list($block, $type, $parameter) = $index;
			$new_uri_path = str_replace($block,"(" . $this->match_types[$type] . ")", $new_uri_path);
		}
		return $new_uri_path;
	}

	public function getRoutes($type = "path"){
        switch ($type) {
        	case 'path':
        		echo "Total Number of Routes: " . count($this->routes) . "<br/>";
        		foreach ($this->routes as $route) {
        			echo $route->getPath() . "<br/>";
        		}
        		break;
            case 'printr':
                echo "<pre>";
                print_r($this->routes);
                echo "</pre>";
                break;
            case 'vardump':
                echo "<pre>";
                var_dump($this->routes);
                echo "</pre>";
                break;
            default:
                throw new InvalidArgumentException("Expected 'printr' or 'vardump' or nothing. Got '$type'", 1);
                break;
        }
    }
}
?>