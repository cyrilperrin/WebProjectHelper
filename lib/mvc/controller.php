<?php

/**
 * Controller class
 */
abstract class Controller
{
	/**
	 * Run a controller
	 * @param $request Request request
	 * @param $view View view
	 * @param $layoutName string default layout name
	 */
	public static function run(Request $request,$view=null,$layoutName=null)
	{
		// Get configuration
		$configuration = Configuration::getInstance(
			$request->getModuleName(),
			$request->getControllerName(),
			$request->getActionName()
		);

		// Get controller/action name
		$controllerName = $request->getControllerName();
		$actionName = $request->getActionName();

		// Construct view
		if($view == null) {
			$view = new View();
		}

		// Get controller path
		$controllerPath = $configuration->getControllerPath($controllerName);
		
		// Check if controller exists
		if(file_exists($controllerPath)) {
			// Require controller
			if(!class_exists(ucfirst($controllerName).'Controller')) require($controllerPath);
				
			// Construct controller
			$controller = eval('return new '.ucfirst($controllerName).'Controller();');

			// Send request, view and layout name to controller
			$controller->request = $request;
			$controller->view = $view;
			$controller->layoutName = $layoutName;
			
			// Call init method if necessary
			if(method_exists($controller,'init')) {
				$controller->init();
			}

			// Check if action exists
			if(method_exists($controller,$actionName.'Action')) {
				// Run action
				call_user_func(array($controller,$actionName.'Action'));
			} elseif($view != null) {
				// Get view path
				$viewPath = $configuration->getViewPath($controllerName, $actionName);
				
				// Get layout name
				$layoutName = $configuration->get(Configuration::LAYOUT_DEFAULT);
				
				// Check if a layout is defined
				if($layoutName == null) {
					// Render view
					$view->render($request,$viewPath);
				} else {
					// Get layout path
					$layoutPath = $configuration->getLayoutPath($layoutName);
				
					// Construct layout
					$layout = new Layout($view,$viewPath);
				
					// Render layout
					$layout->render($request,$layoutPath);
				}
			} else {
				// Error
				Application::error(Application::getMode() == Application::MODE_DEVELOPMENT ? 'Action "'.$actionName.'" does not exist in controller "'.$controllerName.'".' : 'Error 404',404);
			}
		} elseif($view != null) {
			// Get view path
			$viewPath = $configuration->getViewPath($controllerName, $actionName);
			
			// Get layout name
			$layoutName = $configuration->get(Configuration::LAYOUT_DEFAULT);
			
			// Check if a layout is defined
			if($layoutName == null) {
				// Render view
				$view->render($request,$viewPath);
			} else {
				// Get layout path
				$layoutPath = $configuration->getLayoutPath($layoutName);
			
				// Construct layout
				$layout = new Layout($view,$viewPath);
			
				// Render layout
				$layout->render($request,$layoutPath);
			}
		} else {
			// Error
			Application::error(Application::getMode() == Application::MODE_DEVELOPMENT ? 'Controller "'.$controllerName.'" does not exist.' : 'Error 404',404);
		}
	}

	/** @var $view View view */
	protected $view;

	/** @var $request Request request */
	protected $request;
	
	/** @var $layoutName string default layout name */
	protected $layoutName;

	/**
	 * Render view
	 * @param $actionName string action name
	 * @param $controllerName string controller name
	 * @param $moduleName string module name
	 * @param $layoutName string layout name
	 * @param $return bool return view rendering result ?
	 */
	protected function render($actionName=null,$controllerName=null,$moduleName=null,$layoutName=null,$return=false)
	{

		// Get module/controller/action name
		if($moduleName == null) {
			$moduleName = $this->request->getModuleName();
			if($controllerName == null) {
				$controllerName = $this->request->getControllerName();
				if($actionName == null) {
					$actionName = $this->request->getActionName();
				}
			}
		}

		// Get configuration
		$configuration = Configuration::getInstance($moduleName,$controllerName,$actionName);

		// Get view path
		$viewPath = $configuration->getViewPath($controllerName, $actionName);

		// Get layout name
		if($layoutName !== false && $layoutName === null) {
			$layoutName = $this->layoutName !== null ? $this->layoutName : $configuration->get(Configuration::LAYOUT_DEFAULT);
		}
		
		// Start buffering
		if($return) {
			ob_start();
		}

		// Check if a layout is defined
		if($layoutName === false || $layoutName === null) {
			// Render view
			$this->view->render($this->request,$viewPath);
		} else {
			// Get layout path
			$layoutPath = $configuration->getLayoutPath($layoutName);
				
			// Construct layout
			$layout = new Layout($this->view,$viewPath);
				
			// Render layout
			$layout->render($this->request,$layoutPath);
		}
		
		// Stop buffering
		if($return) {
			return ob_get_clean();
		}
	}

	/**
	 * Forward request
	 * @param $actionName string action name
	 * @param $controllerName string controller name
	 * @param $moduleName string module name
	 */
	protected function forward($actionName=null,$controllerName=null,$moduleName=null)
	{
		// Redefine request module/controller/action
		if($actionName != null) {
			$this->request->setActionName($actionName);
			if($controllerName != null) {
				$this->request->setControllerName($controllerName);
				if($moduleName != null) {
					$this->request->setModuleName($moduleName);
				}
			}
		}

		// Run controller
		Controller::run($this->request,$this->view);
	}
}