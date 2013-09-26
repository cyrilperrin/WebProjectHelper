<?php

/**
 * Partial class
 */
class Partial extends View
{
	/**
	 * Call a partial
	 * @param $request Request $request
	 * @param $partialName string partial name
	 * @param $parameters array parameters
	 */
	public static function call(Request $request,$partialName,$parameters)
	{
		// Get configuration
		$configuration = Configuration::getInstance(
			$request->getModuleName(),
			$request->getControllerName(),
			$request->getActionName()
		);

		// Get partial path
		$partialPath = $configuration->getPartialPath($partialName);

		// Construct partial
		$partial = new Partial();

		// Send parameters to partial
		foreach($parameters as $name => $value) {
			$partial->$name = $value;
		}

		// Render partial
		$partial->render($request,$partialPath);
	}
}