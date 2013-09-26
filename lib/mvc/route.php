<?php

/**
 * Route class
 */
class Route
{	
	/** @var $moduleName string default module name */
	private $defaultModuleName;
	
	/** @var $controllerName string default controller name */
	private $defaultControllerName;
	
	/** @var $actionName string default action name */
	private $defaultActionName;
	
	/** @var $moduleName string module name */
	private $moduleName;
	
	/** @var $controllerName string controller name */
	private $controllerName;
	
	/** @var $actionName string action name */
	private $actionName;
	
	/** @var $parameters array parameters */
	private $parameters;
	
	/** @var $buildPattern array build pattern */
	private $buildPattern;

	/** @var $parsePattern array parse pattern */
	private $parsePattern;
	
	/**
	 * Constructor
	 * @param $buildPattern array build pattern
	 * @param $parsePattern array parse pattern
	 * @param $moduleName string default module name
	 * @param $controllerName string default controller name
	 * @param $actionName string default action name
	 * @param $parameters array default parameters
	 */
	public function __construct($buildPattern,$parsePattern,$moduleName=null,$controllerName=null,$actionName=null,$parameters=array())
	{
		$this->parsePattern = $parsePattern;
		$this->buildPattern = $buildPattern;
		$this->moduleName = $this->defaultModuleName = $moduleName;
		$this->controllerName = $this->defaultControllerName = $controllerName;
		$this->actionName = $this->defaultActionName = $actionName;
		$this->parameters = $parameters;
	}

	/**
	 * Parse an URL
	 * @param $url string URL
	 * @return bool URL successfully parsed ?
	 */
	public function parseURL($url)
	{
		// Extract data from URL
		$data = $this->extractData($url, $this->parsePattern);
		
		// Check if URL is successfully parsed
		if($data === null) {
			// URL unsuccessfully parsed
			return false;
		}
		
		// Get module name
		if(isset($data['module'])) {
			if(isset($this->moduleName) && $data['module'] != $this->moduleName) {
				return false;
			}
			$this->moduleName = $data['module'];
			unset($data['module']);
		}
		
		// Get controller name
		if(isset($data['controller'])) {
			if(isset($this->controllerName) && $data['controller'] != $this->controllerName) {
				return false;
			}
			$this->controllerName = $data['controller'];
			unset($data['controller']);
		}
		
		// Get action name
		if(isset($data['action'])) {
			if(isset($this->actionName) && $data['action'] != $this->actionName) {
				return false;
			}
			$this->actionName = $data['action'];
			unset($data['action']);
		}
		
		// Get parameters
		if(isset($data['parameters'])) {
			foreach($data['parameters'] as $parameter) {
				$this->parameters[$parameter['name']] = $parameter['value'];
			}
			unset($data['parameters']);
		}
		foreach($data as $name => $value) {
			$this->parameters[$name] = $value;
		}
		
		// URL successfully parsed
		return true;
	}
	
	/**
	* Extract data from an URL
	* @param $url string URL
	* @param $pattern array parse pattern
	* @param $multiple bool multiple capture ?
	* @return array extracted data
	*/
	private function extractData($url,$pattern,$multiple=false)
	{
		// Build regular expression
		$regexp = $this->buildRegexp($pattern);
		
		// Check if it is a multiple capture
		if($multiple) {
			// Parse URL
			if(preg_match_all('|'.$regexp.'|', $url, $matches)) {
				// Init data
				$data = array();
				
				// Count matches
				$nbMatches = count($matches[0]);
				
				// Get data
				for($i=0;$i<$nbMatches;$i++) {
					// Init data
					$data[$i] = array();
					
					// Get data
					foreach($pattern[1] as $name => $subpattern) {
						// Remove :
						$name = substr($name,1);
						
						// Check if subpattern has a match
						if(isset($matches[$name][$i])) {
							// Check if it is a complexe subpattern
							if(is_array($subpattern)) {
								// Extract data
								$data[$i][$name] = $this->extractData($matches[$name][$i], $subpattern, true);
							} else {
								// Put data
								$data[$i][$name] = urldecode($matches[$name][$i]);
							}
						}
					}
				}
				
				// Return data
				return $data;
			}
			
			// Return empty array if no matches
			return array();
		} else {
			// Parse URL
			if(preg_match('|^'.$regexp.'$|', $url, $matches)) {
				// Init data
				$data = array();
				
				// Get data
				foreach($pattern[1] as $name => $subpattern) {
					// Remove :
					$name = substr($name,1);
					
					// Check if subpattern has a match
					if(isset($matches[$name])) {
						// Check if it is a complexe subpattern
						if(is_array($subpattern)) {
							// Extract data
							$data[$name] = $this->extractData($matches[$name], $subpattern, true);
						} else {
							// Put data
							$data[$name] = urldecode($matches[$name]);
						}
					}
				}
				
				// Return data
				return $data;
			}
			
			// Return null if no match
			return null;
		}
	}
	
	/**
	 * Build regular expression from a parse pattern
	 * @param $pattern array parse pattern
	 * @param $capture bool capturing subpatterns ?
	 * @return string regular expression
	 */
	private function buildRegexp($pattern,$capture=true)
	{
		// Get regular expression
		$regexp = $pattern[0];
		
		// Put subpatterns into regular expression
		foreach($pattern[1] as $name => $subpattern) {
			// Check if it is a complexe subpattern
			if(is_array($subpattern)) {
				$subpattern = '(?:'.$this->buildRegexp($subpattern,false).')*';
			}
			
			// Capturing subpattern
			if($capture) {
				$subpattern = '(?P<'.substr($name,1).'>'.$subpattern.')';
			}
			
			// Put subpattern into regular expression
			$regexp = str_replace($name,$subpattern,$regexp);
		}
		
		// Return regular expression
		return $regexp;
	}

	/**
	 * Build an URL
	 * @param $moduleName string module name
	 * @param $controllerName string controller name
	 * @param $actionName string action name
	 * @param $parameters array parameters
	 * @return string URL
	 */
	public function buildURL($moduleName=null,$controllerName=null,$actionName=null,$parameters=array())
	{
		// Check if URL can be build
		if(isset($this->defaultModuleName) && $this->defaultModuleName != $moduleName) {
			return null;
		}
		if(isset($this->defaultControllerName) && $this->defaultControllerName != $controllerName) {
			return null;
		}
		if(isset($this->defaultActionName) && $this->defaultActionName != $actionName) {
			return null;
		}
		
		// Get default controller and action
		if($actionName == null) {
			$actionName = Configuration::getInstance($moduleName,$controllerName)->get(Configuration::ACTION_DEFAULT);
			if($controllerName == null) {
				$controllerName = Configuration::getInstance($moduleName)->get(Configuration::CONTROLLER_DEFAULT);
			}
		}
		
		// Prepare parameters
		foreach(array_merge($parameters) as $name => $value) {
			$parameters[] = array('name' => $name,'value' => $value);
			unset($parameters[$name]);
		}
		
		// Build URL
		return $this->insertData($this->buildPattern,array(
			'controller' => $controllerName,
			'action' => $actionName,
			'parameters' => $parameters
		));
	}
	
	/**
	 * insert data into a build pattern
	 * @param $pattern array build pattern
	 * @param $data array data
	 * @return string result
	 */
	private function insertData($pattern,$data)
	{
		// Init result
		$result = $pattern[0];
		
		// Insert data
		if(preg_match_all('/:([a-z][a-z0-9]*)/i', $result, $names)) {
			// Count names
			$nbNames = count($names[0]);
			
			// Insert data
			for($i=0;$i<$nbNames;$i++) {
				// Check if value is defined
				if(!isset($data[$names[1][$i]])) {
					// Remove subpattern from result
					$result = str_replace($names[0][$i],'',$result);
				} else {
					// Check if it is a complexe subpattern
					if(is_array($data[$names[1][$i]])) {
						// Build value
						$value = '';
						foreach($data[$names[1][$i]] as $subdata) {
							$value .= $this->insertData($pattern[1][$names[0][$i]], $subdata);
						}
						
						// Insert value
						$result = str_replace($names[0][$i],$value,$result);
					} else {
						// Insert value
						$result = str_replace($names[0][$i],urlencode($data[$names[1][$i]]),$result);
					}
				}
			}
		}
		
		// Return result
		return $result;
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
	 * @return string controller name
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
	 * Get parameters
	 * @return array parameters
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}