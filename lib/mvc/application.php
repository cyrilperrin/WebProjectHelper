<?php

/**
 * Application class
 */
class Application
{
	// Modes
	const MODE_DEVELOPMENT = 1;
	const MODE_PRODUCTION = 2;

	/** @var $mode int mode */
	private static $mode;
	
	/**
	 * Run application
	 * @param $mode int mode
	 * @param $startSession boolean start session ?
	 */
	public static function run($mode=self::MODE_DEVELOPMENT,$startSession=true)
	{	
		// Save mode
		self::$mode = $mode;
		
		// Start session
		if ($startSession && !isset($_SESSION) && !session_start()) {
			self::error('Error while start session.');
		}
		
		// Import ressources
		if(isset($_SESSION)) {
			foreach($_SESSION as $ressourceName => $ressourceValue) {
				self::$ressources[$ressourceName] = $ressourceValue;
			}
		}
		
		// Parse URL
		$request = Request::parseURL();

		// Check if request is valid
		if(!$request) {
			self::error('Invalid request.');
		}
		
		// Get configuration
		$configuration = Configuration::getInstance(
			$request->getModuleName(),
			$request->getControllerName(),
			$request->getActionName()
		);
		
		// Get filters names
		$filtersNames = $configuration->get(Configuration::FILTERS_DEFAULT);
		
		// Start buffering
		ob_start();

		// Check if filters are defined
		if(!$filtersNames) {
			// Run controller
			Controller::run($request);
		} else {
			// Run filters
			Filter::run($request,$filtersNames);
		}

		// Stop services
		self::stopServices();
		
		// Send buffer content to output
		ob_end_flush();
	}

	/**
	 * Report an error
	 * @param $message string error message
	 * @param $code int HTTP response code
	 */
	public static function error($message=null,$code=500)
	{
		// Clean buffer content
		ob_end_clean();
		
		// Set HTTP response code
		switch ($code) {
			case 100: $text = 'Continue'; break;
			case 101: $text = 'Switching Protocols'; break;
			case 200: $text = 'OK'; break;
			case 201: $text = 'Created'; break;
			case 202: $text = 'Accepted'; break;
			case 203: $text = 'Non-Authoritative Information'; break;
			case 204: $text = 'No Content'; break;
			case 205: $text = 'Reset Content'; break;
			case 206: $text = 'Partial Content'; break;
			case 300: $text = 'Multiple Choices'; break;
			case 301: $text = 'Moved Permanently'; break;
			case 302: $text = 'Moved Temporarily'; break;
			case 303: $text = 'See Other'; break;
			case 304: $text = 'Not Modified'; break;
			case 305: $text = 'Use Proxy'; break;
			case 400: $text = 'Bad Request'; break;
			case 401: $text = 'Unauthorized'; break;
			case 402: $text = 'Payment Required'; break;
			case 403: $text = 'Forbidden'; break;
			case 404: $text = 'Not Found'; break;
			case 405: $text = 'Method Not Allowed'; break;
			case 406: $text = 'Not Acceptable'; break;
			case 407: $text = 'Proxy Authentication Required'; break;
			case 408: $text = 'Request Time-out'; break;
			case 409: $text = 'Conflict'; break;
			case 410: $text = 'Gone'; break;
			case 411: $text = 'Length Required'; break;
			case 412: $text = 'Precondition Failed'; break;
			case 413: $text = 'Request Entity Too Large'; break;
			case 414: $text = 'Request-URI Too Large'; break;
			case 415: $text = 'Unsupported Media Type'; break;
			case 500: $text = 'Internal Server Error'; break;
			case 501: $text = 'Not Implemented'; break;
			case 502: $text = 'Bad Gateway'; break;
			case 503: $text = 'Service Unavailable'; break;
			case 504: $text = 'Gateway Time-out'; break;
			case 505: $text = 'HTTP Version not supported'; break;
			default: throw new Exception('Unknown HTTP status code "'.$code.'"');
		}
		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
		header($protocol.' '.$code.' '.$text);
		
		// Run error controller
		Controller::run(new Request(null,'error','error',$message === null ? array() : array('message' => $message)));
		
		// Stop services
		self::stopServices();
		
		// Stop application
		exit;
	}
	
	/**
	 * Get mode
	 * @return int mode
	 */
	public static function getMode()
	{
		return self::$mode;
	}

	// Ressources

	/** @var $ressources array ressources */
	private static $ressources = array();

	/**
	 * Get a ressource
	 * @param $ressourceName string ressource name
	 * @return ? ressource
	 */
	public static function getRessource($ressourceName)
	{
		return isset(self::$ressources[$ressourceName]) ? self::$ressources[$ressourceName] : null;
	}

	/**
	 * Add a ressource
	 * @param $ressourceName string ressource name
	 * @param $ressourceValue ? ressource value
	 * @param $persistant bool put ressource into session ?
	 */
	public static function addRessource($ressourceName,$ressourceValue,$persistant=false)
	{
		// Save ressource
		self::$ressources[$ressourceName] = $ressourceValue;
		
		// Put ressource into session if necessary
		if($persistant) {
			$_SESSION[$ressourceName] = $ressourceValue;
		}
	}
	
	/**
	 * Get ressources session id
	 * @return string session id
	 */
	public static function getRessourcesSessionId()
	{
		return session_id();
	}
	
	/**
	 * Set ressources session id
	 * @param $id string session id
	 */
	public static function setRessourcesSessionId($id)
	{
		session_id($id);
	}

	// Services

	/** @var $services array services */
	private static $services = array();

	/**
	 * Get a service
	 * @param $serviceName string service name
	 * @return Service service
	 */
	public static function getService($serviceName)
	{
		// Check if service is started
		if(!isset(self::$services[$serviceName])) {
			// Start service
			self::startService($serviceName);
		}

		// Return service's ressource
		return self::$services[$serviceName]->getRessource();
	}

	/**
	 * Start a service
	 * @param $serviceName string service name
	 */
	public static function startService($serviceName)
	{
		// Get service
		if($service = Service::init($serviceName)) {
			// Start service
			$service->start();
			
			// Save service
			self::$services[$serviceName] = $service;
		} else {
			// Error
			Application::error(Application::getMode() == Application::MODE_DEVELOPMENT ? 'Service "'.$serviceName.'" is unknown.' : 'Error 500',500);
		}
	}

	/**
	 * Stop services
	 */
	private static function stopServices()
	{
		// Stop services
		$services = array_reverse(self::$services);
		foreach($services as $service) {
			$service->stop();
			array_pop(self::$services);
		}
	}
}