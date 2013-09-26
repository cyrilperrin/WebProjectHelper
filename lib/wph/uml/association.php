<?php

/**
 * Association
 */
class Association {
	/** @var $counter int counter */
	private static $counter = 1;
	
	/** @var $correspondances array correspondances */
	private static $correspondances = array();
	
	/** @var $id int identifier */
	private $id;
	
	/** @var $name string name */
	private $name;
	
	/** @var $attribute Attribute owner */
	private $attribute;
	
	/** @var $correspondence int correspondence */
	private $correspondence;
	
	/**
	 * Constructor
	 * @param $name string name
	 * @param $correspondence int correspondence
	 * @throws Exception when correspondence is defined more than twice
	 */
	public function __construct($name,$correspondence=null) {
		// Check if correspondence is defined more than twice
		if ($correspondence != null) {
			if (isset(Association::$correspondances[$correspondence])) {
				if (count(Association::$correspondances[$correspondence]) > 1) {
					throw new Exception('The association correspondence <b>'.$correspondence.'</b> is defined more than twice');
				}
				Association::$correspondances[$correspondence][] = $this;
			} else {
				Association::$correspondances[$correspondence] = array($this);
			}
		}
		
		// Save attributes
		$this->id = Association::$counter++;		
		$this->name = $name;
		$this->correspondence = $correspondence;
	}
	
	/**
	 * Get identifier
	 * @return int identifier
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Get name
	 * @return string name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get correspondence
	 * @return Association correspondence
	 */
	public function getCorrespondence() {
		if (isset(Association::$correspondances[$this->correspondence])) {
			$associations = Association::$correspondances[$this->correspondence];
			if (isset($associations[0]) && isset($associations[1])) {
				if ($this->equals($associations[0])) { return $associations[1]; }
				if ($this->equals($associations[1])) { return $associations[0]; }
			}
		}
		return null;
	}
	
	/**
	 * Get given correspondence
	 * @return int given correspondence
	 */
	public function getCorrespondenceGiven() {
		return $this->correspondence;
	}
	
	/**
	 * Check if correspondence is given
	 * @return bool is correspondence given ?
	 */
	public function isCorrespondenceGiven() {
		return $this->correspondence != null;
	}
	
	/**
	 * Equals implementation
	 * @param $association Association association
	 * @return bool association is equals than receptor ?
	 */
	public function equals($association) {
		if ($association == null || !($association instanceof Association)) { return false; }
		return $this->id == $association->id;
	}
	
	/**
	 * Set owner
	 * @param $attribute Attribute attribute
	 */
	public function setAttribute($attribute) {
		$this->attribute = $attribute;
	}

	/**
	 * Get owner
	 * @return Attribute owner 
	 */
	public function getAttribute() {
		return $this->attribute;
	}
}