<?php

/**
 * Request class
 */
class Request
{
	/** @var $instance Request instance */
	private static $instance;
	
	/** @var $base string base */
	private static $base;
	
	/** @var $url string base */
	private static $url;

	/** @var $routes array routes */
	private static $routes;
	
	/**
	 * Get base
	 * @return string base 
	 */
	public static function getBase()
	{
		return self::$base;
	}
	
	/**
	 * Set base
	 * @param $base string base
	 */
	public static function setBase($base)
	{
		self::$base = $base;
	}
	
	/**
	 * Get URL
	 * @return string URL 
	 */
	public static function getURL()
	{
		return self::$url;
	}
	
	/**
	 * Set URL
	 * @param $url string URL
	 */
	public static function setURL($url)
	{
		self::$url = $url;
	}

	/**
	 * Add a route
	 * @param $route Route route
	 */
	public static function addRoute(Route $route)
	{
		self::$routes[] = $route;
	}
	
	/**
	 * Parse URL
	 * @return Request request
	 */
	public static function parseURL()
	{
		// Check if instance is already set
		if(isset(self::$instance)) {
			return self::$instance;
		}
		
		// Resize base and url
		$nbCharacters = min(strlen(self::$base),strlen(self::$url));
		for($i=0;$i < $nbCharacters && self::$base[$i] == self::$url[$i];$i++) {}
		self::$base = substr(self::$base,0,$i);
		self::$url = substr(self::$url,$i);
		
		// Parse URL
		foreach(self::$routes as $route) {
			if($route->parseURL(self::$url)) {
				// Get configuration
				$moduleName = $route->getModuleName();
				$controllerName = $route->getControllerName();
				$actionName = $route->getActionName();
				
				// Check if controller name or action name are null
				if($controllerName == null || $actionName == null) {
					// Get configuration
					$configuration = Configuration::getInstance($moduleName,$controllerName,$actionName);
					
					// Default controller and action
					if($controllerName == null) {
						$controllerName = $configuration->get(Configuration::CONTROLLER_DEFAULT);
					}
					if($actionName == null) {
						$actionName = $configuration->get(Configuration::ACTION_DEFAULT);
					}
				}
				
				// Construct request
				$request = new Request($moduleName,$controllerName,$actionName,$route->getParameters()); 
				
				// Save request instance
				self::$instance = $request;
				
				// Return request
				return $request;
			}
		}
	}

	/**
	 * Build an URL
	 * @param $moduleName string module name
	 * @param $controllerName string controller name
	 * @param $actionName string action name
	 * @param $parameters array parameters
	 * @return string URL
	 */
	public static function buildURL($moduleName=null,$controllerName=null,$actionName=null,$parameters=array(),$reset=true)
	{
		// Keep module name, controller name, action name and parameters if necessary
		if(!$reset) {
			$parameters = array_merge(self::$instance->getParameters(),$parameters);
			if($moduleName == null) {
				$moduleName = self::$instance->getModuleName();
			}
			if($controllerName == null) {
				$controllerName = self::$instance->getControllerName();
			}
			if($actionName == null) {
				$actionName = self::$instance->getActionName();
			}
		}
		
		// Build URL
		foreach(self::$routes as $route) {
			if($url = $route->buildURL($moduleName,$controllerName,$actionName,$parameters)) {
				return Request::$base.$url;
			}
		}
		return null;
	}

	/**
	 * Redirect user
	 * @param $url string URL
	 */ 
	public static function redirect($url)
	{
		header('Location: '.$url);
		exit;
	}

	/** @var $moduleName string  */
	private $moduleName;

	/** @var $controllerName string controller name */
	private $controllerName;

	/** @var $actionName string action name */
	private $actionName;

	/** @var $parameters array parameters */
	private $parameters;
	
	/**
	 * Constructor
	 * @param $moduleName string module name
	 * @param $controllerName string controller name
	 * @param $actionName string action name
	 * @param $parameters array parameters
	 */
	public function __construct($moduleName,$controllerName,$actionName,$parameters)
	{
		$this->moduleName = $moduleName;
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->parameters = $parameters;
	}

	/**
	 * Get module name
	 * @return string module name
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
	/**
	 * Get controller name
	 * @return string module name
	 */
	public function getControllerName()
	{
		return $this->controllerName;
	}
	
	/**
	 * Get action name
	 * @return string action name
	 */
	public function getActionName()
	{
		return $this->actionName;
	}
	
	/**
	 * Get parameter value
	 * @param $name parameter name
	 * @param $defaut default value
	 * @return string parameter value
	 */
	public function getParameter($name,$defaut=null)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaut;
	}
	
	/**
	 * Get parameters
	 * @return array parameters
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Set module name
	 * @param $moduleName string module name
	 */
	public function setModuleName($moduleName)
	{
		$this->moduleName = $moduleName;
	}
	
	/**
	 * Set controller name
	 * @param $controllerName string controller name
	 */
	public function setControllerName($controllerName)
	{
		$this->controllerName = $controllerName;
	}
	
	/**
	 * Set action name
	 * @param $actionName string action name
	 */
	public function setActionName($actionName)
	{
		$this->actionName = $actionName;
	}
	
	/**
	 * Set parameter
	 * @param $name string name
	 * @param $value string value
	 */
	public function setParameter($name,$value)
	{
		$this->parameters[$name] = $value;
	}
	
	/**
	 * Set parameters
	 * @param $parameters array parameters
	 */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
	}
	
	/**
	 * Clear parameters
	 */
	public function clearParameters()
	{
		$this->parameters = array();
	}
}

// Get base
Request::setBase(dirname($_SERVER['SCRIPT_NAME']).'/');

// Get URL
Request::setURL($_SERVER['REQUEST_URI']);