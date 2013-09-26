<?php

/**
 * View class
 */
class View
{	
	/** @var $request Request request */
	protected $request;

	/**
	 * Render view
	 * @param $request Request request
	 * @param $viewPath string view path
	 */
	public function render(Request $request,$viewPath)
	{
		// Check is file exists before require it
		if(file_exists($viewPath)) {
			// Save request
			$this->request = $request;
			
			// Render view
			require($viewPath);
		} else {
			// Error
			Application::error(Application::getMode() == Application::MODE_DEVELOPMENT ? 'View "'.basename($viewPath).'" is unknown.' : 'Error 404',404);
		}
	}

	// Magic getters and setters
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}
	public function __set($name,$value)
	{
		$this->$name = $value;
	}

	/**
	 * Render a partial
	 * @param $partialName string partial name
	 * @param $parameters array parameters
	 */
	public function partial($partialName,$parameters=array())
	{
		// Call partial
		Partial::call($this->request, $partialName, $parameters);
	}
	
	/**
	 * Call an action
	 * @param $moduleName string module name
	 * @param $controllerName string controller name
	 * @param $actionName string action name
	 * @param $parameters array parameters
	 */
	public function action($moduleName,$controllerName,$actionName,$parameters=array())
	{
		// Build request
		$request = new Request($moduleName, $controllerName, $actionName, $parameters);
		
		// Run controller
		Controller::run($request,null,false);
	}
}