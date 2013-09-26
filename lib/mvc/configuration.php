<?php

/**
 * Configuration class
 */
class Configuration
{
	/** @var $instances array configurations instances */
	private static $instances = array();

	/**
	 * Create configuration instance
	 * @param $moduleName string module name
	 * @param $controllerName string controller name
	 * @param $actionName string action name
	 * @param parameters array parameters
	 */
	public static function createInstance($moduleName=null,$controllerName=null,$actionName=null,$parameters=array())
	{
		self::$instances[$moduleName][$controllerName][$actionName] = new Configuration($moduleName,$controllerName,$actionName,$parameters);
	}

	/**
	 * Get configuration instance
	 * @param $moduleName string module name
	 * @return Configuration configuration instance
	 */
	public static function getInstance($moduleName=null,$controllerName=null,$actionName=null)
	{
		if(isset(self::$instances[$moduleName][$controllerName][$actionName])) {
			return self::$instances[$moduleName][$controllerName][$actionName];
		}
		if(isset(self::$instances[$moduleName][$controllerName][null])) {
			return self::$instances[$moduleName][$controllerName][null];
		}
		if(isset(self::$instances[$moduleName][null][null])) {
			return self::$instances[$moduleName][null][null];
		}
		if(isset(self::$instances[null][null][null])) {
			return self::$instances[null][null][null];
		}
		return null;
	}

	// Parameters names
	const PATH_MODULE			= 'path.module';		// string, module path
	const PATH_CONTROLLERS		= 'path.controllers';	// string, controllers path
	const PATH_VIEWS			= 'path.views';			// string, views path
	const PATH_LAYOUTS			= 'path.layouts';		// string, layouts path
	const PATH_PARTIALS			= 'path.partials';		// string, partials path
	const PATH_FILTERS			= 'path.filters';		// string, filters path
	const PATH_SERVICES			= 'path.services';		// string, services path
	const LAYOUT_DEFAULT		= 'layout.default';		// string, default layout
	const CONTROLLER_DEFAULT	= 'controller.default';	// string, default controller
	const ACTION_DEFAULT		= 'action.default';		// string, default action
	const FILTERS_DEFAULT		= 'filters.default';	// array, default filters
	const SERVICES_DEFAULT		= 'services.default';	// array, default services

	/** @var $parameters array parameters */
	private $parameters;
	
	/** @var $moduleName string module name */
	private $moduleName;
	
	/** @var $controllerName string controller name */
	private $controllerName;
	
	/** @var $actionName string action name */
	private $actionName;

	/**
	 * Constructor
	 * @param $moduleName string module name
	 * @param $controllerName string controller name
	 * @param $viewName string view name
	 * @param $parameters array parameters
	 */
	private function __construct($moduleName=null,$controllerName=null,$actionName=null,$parameters=array())
	{
		$this->moduleName = $moduleName;
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->parameters = $parameters;
	}

	/**
	 * Get parameter value
	 * @param $name string parameter name
	 * @return ? parameter value
	 */
	public function get($name)
	{
		if(isset($this->parameters[$name])) {
			return $this->parameters[$name];
		}
		if($this->actionName != null) {
			return $this->getInstance($this->moduleName,$this->controllerName)->get($name);
		}
		if($this->controllerName != null) {
			return $this->getInstance($this->moduleName)->get($name);
		}
		if($this->moduleName != null) {
			return $this->getInstance()->get($name);
		}
		return null;
	}

	/**
	 * Set parameter value
	 * @param $name string parameter name
	 * @param $value ? parameter value
	 */
	public function set($name,$value)
	{
		$this->parameters[$name] = $value;
	}

	// Paths

	/**
	 * Get controller path
	 * @param $name string controller name
	 * @return string controller path
	 */
	public function getControllerPath($controllerName)
	{
		return $this->get(self::PATH_MODULE).$this->get(self::PATH_CONTROLLERS).$controllerName.'.php';
	}

	/**
	 * Get view path
	 * @param $controllerName string controller name
	 * @param $actionName string action name
	 * @return string view path
	 */
	public function getViewPath($controllerName,$actionName)
	{
		return $this->get(self::PATH_MODULE).$this->get(self::PATH_VIEWS).$controllerName.'/'.$actionName.'.phtml';
	}

	/**
	 * Get layout path
	 * @param $name layout name
	 * @return layout path
	 */
	public function getLayoutPath($layoutName)
	{
		return $this->get(self::PATH_MODULE).$this->get(self::PATH_LAYOUTS).$layoutName.'.phtml';
	}

	/**
	 * Get partial path
	 * @param $name string partial name
	 * @return string partial path
	 */
	public function getPartialPath($partialName)
	{
		return $this->get(self::PATH_MODULE).$this->get(self::PATH_PARTIALS).$partialName.'.phtml';
	}

	/**
	 * Get filter path
	 * @param $name string filter name
	 * @return string filter path
	 */
	public function getFilterPath($filterName)
	{
		return $this->get(self::PATH_MODULE).$this->get(self::PATH_FILTERS).$filterName.'.php';
	}

	/**
	 * Get service path
	 * @param $name service path
	 * @return string service path
	 */
	public function getServicePath($serviceName)
	{
		return $this->get(self::PATH_MODULE).$this->get(self::PATH_SERVICES).$serviceName.'.php';
	}
}

// Create default configuration
Configuration::createInstance(null,null,null,array(
	Configuration::PATH_CONTROLLERS		=> 'controllers/',
	Configuration::PATH_VIEWS			=> 'renders/views/',
	Configuration::PATH_LAYOUTS			=> 'renders/layouts/',
	Configuration::PATH_PARTIALS 		=> 'renders/partials/',
	Configuration::PATH_FILTERS 		=> 'filters/',
	Configuration::PATH_SERVICES 		=> 'services/',
	Configuration::CONTROLLER_DEFAULT 	=> 'index',
	Configuration::ACTION_DEFAULT 		=> 'index'
));