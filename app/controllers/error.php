<?php

/**
 * Errors controller
 */
class ErrorController extends AbstractController
{
	/**
	 * Called action for an error
	 */
	public function errorAction()
	{
		// Get error message
		$this->view->message = $this->request->getParameter('message','An error occurred.');
		
		// Render view
		$this->render();
	}
}