<?php

/**
 * Element
 */
class Element
{
	/** @var int counter */
	private static $counter = 1;
	
	/** @var int identifier */
	private $id;
	
	/** @var string name */
	private $name;
	
	/** @var string description */
	private $description;
	
	/**
	 * Constructor
	 * @param $name string name
	 * @param $description string description
	 */
	public function __construct($name,$description=null) {
		$this->id = Element::$counter++;
		$this->name = $name;
		$this->description = $description;
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
	 * Set name
	 * @param $name string name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get description
	 * @return string description
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Equals implementation
	 * @param $element ? element
	 * @return bool element is equals than receptor ?
	 */
	public function equals($element) {
		if ($element == null || !($element instanceof Element)) { return false; }
		return $this->id == $element->id;
	}
}
