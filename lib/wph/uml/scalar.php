<?php

/**
 * Scalar
 */
class Scalar extends Element {
	
	/**
	 * Get identifier name
	 * @param $element Element element
	 * @return string identifier name
	 */
	public static function name_id(Element $element) {
		return 'id'.ucfirst($element->getName());
	}

	/**
	 * Generate id from an element
	 * @param $element Element element
	 * @return Scalar generated id
	 */
	public static function id(Element $element) {
		return new Scalar(Scalar::name_id($element),Scalar::T_INT_AI);
	}
	
	/** @var $type ? type */
	private $type;
	
	/** @var $length int length */
	private $length;
	
	/** @ar $default ? default value */
	private $default;
	
	/** @var $owner Object owner */
	private $owner;
	
	/** @var options string options */
	private $options;
	
	// Types
	const T_BOOL = 1;
	const T_INT = 2;
	const T_INT_AI = 4;
	const T_FLOAT = 8;
	const T_TIME = 16;
	const T_DATE = 32;
	const T_DATETIME = 64;
	const T_TEXT = 128;
	
	// Defaults
	const DEF_NOW = 'now'; // Now
	
	// Options
	const OPT_DATABASE = 'd'; // Don't rename scalar's name in database
	
	/**
	 * Constructor
	 * @param $name string name
	 * @param $type ? name
	 * @param $description string description
	 * @param $default ? default value
	 * @param $length int length
	 * @param $options string options
	 * @throws Exception when name is not valid
	 */
	public function __construct($name,$type,$description=null,$default=null,$length=null,$options=null) {
		// Check if name is valid
		if (!PHP::valid_name($name)) { throw new Exception('The name <b>'.$name.'</b> for a scalar is not permitted'); }
		
		// Call parent constructor
		parent::__construct($name,$description);
		
		// Save attributes
		$this->type = $type;
		$this->default = $default;
		$this->length = $length;
		$this->options = $options;
	}
	
	/**
	 * Check an option
	 * @param $option string option
	 * @return bool scalar has option ?
	 */
	public function hasOption($option) {
		return $this->options != null && stripos($this->options,$option) !== false;
	}
	
	/**
	 * Get owner
	 * @return Object owner 
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * Set owner
	 * @param $owner Object owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}

	/**
	 * Get length
	 * @return int length
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * Get default value
	 * @return ? default value
	 */
	public function getDefault() {
		return $this->default;
	}

	/**
	 * Get type
	 * @return ? type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Check a type
	 * @param $type ? type
	 * @return bool scalar if type of given type ?
	 */
	public function isType($type) {
		return $type & $this->type;
	}
	
	/**
	 * Set type
	 * @param $type ? type
	 */
	public function setType($type) {
		$this->type = $type;
	}
}
