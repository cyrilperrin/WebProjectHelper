<?php

/**
 * Filter class
 */
abstract class Filter
{
	/**
	 * Run filters
	 * @param $request Request request
	 * @param $filtersNames array filters names
	 */
	public static function run(Request $request, $filtersNames)
	{
		// Reverse filters array
		$filtersNames = array_reverse($filtersNames);

		// Get configuration
		$configuration = Configuration::getInstance(
			$request->getModuleName(),
			$request->getControllerName(),
			$request->getActionName()
		);

		// Construct filters
		$following = null;
		foreach($filtersNames as $filterName) {
			// Get filter path
			$filterPath = $configuration->getFilterPath($filterName);
			
			// Check if filter exists
			if(file_exists($filterPath)) {
				// Require filter
				if(!class_exists(ucfirst($filterName).'Filter')) require($filterPath);
					
				// Construct filter
				$filter = eval('return new '.ucfirst($filterName).'Filter();');
					
				// Send request and following filter to filter
				$filter->request = $request;
				$filter->following = $following;

				// Save current filter as following filter
				$following = $filter;
			} else {
				// Error
				Application::error(Application::getMode() == Application::MODE_DEVELOPMENT ? 'Filter "'.$filterName.'" does not exist.' : 'Error 500',500);
			}
		}
		
		// Run first filter
		$following->process();
	}

	/** @var $request Request request */
	protected $request;

	/** @var $following Filter following filter */
	protected $following;

	/**
	 * Filter request
	 */
	public abstract function process();

	/**
	 * Forward request
	 */
	protected function forward()
	{
		// Check if following is defined
		if($this->following != null) {
			// Run filter
			$this->following->process();
		} else {
			// Run controller
			Controller::run($this->request);
		}
	}

}