<?php

/**
 * Attribute
 */
class Attribute {
	
	/** @var $counter int counter */
	private static $counter = 1;
	
	/** @ar $id int identifier */
	private $id;
	
	/** @var $owner Object owner */
	private $owner;
	
	/** @var $element Element element */
	private $element;
	
	/** @var $association Association association */
	private $association;
	
	/** @var $multiplicity ? multiplicity */
	private $multiplicity;
	
	/** @var $options string options */
	private $options;
	
	/** @var $isId bool is an identifier ? */
	private $isId;
	
	/** @var $isExplicit bool is defined by user ? */
	private $isExplicit;
	
	// Multiplicities
	const M_OPT 	= 1; // Optionnal
	const M_ONE 	= 2; // One
	const M_SEVERAL = 4; // Several
	
	// Options
	const OPT_UNIQUE	= 'u'; // Make attribute unique 
	const OPT_KEYWORD	= 's'; // TODO Use as keyword in search method 
	const OPT_SELECT_BY	= 'b'; // Add selectBy method
	
	/**
	 * Constructor
	 * @param $element Element element
	 * @param $multiplicity ? multiplicity
	 * @param $isId bool is identifier ?
	 * @param $association Association association
	 * @param $options string options
	 * @param $isExplicit bool is defined by user ?
	 */
	public function __construct(Element $element,$multiplicity=Attribute::M_OPT,$isId=false,$association=null,$options=null,$isExplicit=true) {
		// Save attributes
		$this->id = Attribute::$counter++;
		$this->element = $element;
		$this->multiplicity = $multiplicity;
		$this->isId = $isId;
		$this->association = $association;
		$this->owner = null;
		$this->isExplicit = $isExplicit;
		$this->options = $options;
		
		// Set association attribute
		if ($this->association != null) { $this->association->setAttribute($this); }
	}
	
	/**
	 * Check if attribute has an option
	 * @param $option string option
	 * @return bool attribute has option ?
	 */
	public function hasOption($option) {
		return $this->options != null && stripos($this->options,$option) !== false;
	}

	/**
	 * Get identifier
	 * @return int identifier
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get element
	 * @return Object element
	 */
	public function getElem() {
		return $this->element;
	}

	/**
	 * Get multiplicity
	 * @return ? multiplicity
	 */
	public function getMult() {
		return $this->multiplicity;
	}
	
	/**
	 * Check muliplicity 
	 * @param $multiplicity ? multiplicity
	 * @return bool multiplicity matches ?
	 */
	public function isMult($multiplicity) {
		return $multiplicity & $this->multiplicity;
	}

	/**
	 * Check if attribute is an identifier
	 * @return bool attribute is an identifier ?
	 */
	public function isId() {
		return $this->isId;
	}
	
	/**
	 * Check if attribute is defined by user ?
	 * @return bool attribute is defined by user ?
	 */
	public function isExplicit() {
		return $this->isExplicit;
	}
	
	/**
	 * Get association
	 * @return Association association
	 */
	public function getAssoc() {
		return $this->association;
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
	 * @throws Exception when errors occur
	 */
	public function setOwner(Object $owner) {
		// Checks if object and element are in same family
		if ($this->element instanceof Object && $owner->isInFamily($this->element,true)) {
			// Check if there is an identifier loop
			if ($this->isId) {
				if ($owner->equals($this->element)) {
					throw new Exception('The object <b>'.$owner->getName().'</b> has itself for id');
				} else {
					throw new Exception('The object <b>'.$owner->getName().'</b> owns a member of its family <b>'.$this->element->getName().'</b> as id');
				}
			}
			
			// Check if association is missing
			if ($this->association == null) {
				if ($owner->equals($this->element)) {
					throw new Exception('The object <b>'.$owner->getName().'</b> owns itself without association');
				} else {
					throw new Exception('The object <b>'.$owner->getName().'</b> owns a member of its family <b>'.$this->element->getName().'</b> without association');
				}
			}
		}
		
		// Set owner
		$this->owner = $owner;
	}
	
	/**
	 * Get correspondent attribute
	 * @throws Exception when no correspondance found in case of correspondance needed
	 * @return Attribute correspondent attribute
	 */
	public function getCorrespondence() {
		// No correspondance possible if scalar
		if ($this->element instanceof Scalar) { return null; }
		
		// Reflexive association without correspondence
		if ($this->getElem()->equals($this->owner) && !$this->isAssociated()) {
			return null;
		}
		
		// No correspondence needed
		$matches = $this->element->getAttrsMatch($this->owner);
		if (count($matches) == 1) {
			return array_shift($matches);
		}
		
		// Correspondence needed
		if ($this->association != null) {
			$correspondance = $this->association->getCorrespondence();
			if ($correspondance != null) {
				return $correspondance->getAttribute();
			}
		}
		
		// No correspodence found
		throw new Exception('The object <b>'.$this->owner->getName().'</b> owns the object <b>'.$this->element->getName().'</b> without explicit association correspondence');
	}
	
	/**
	 * Check if attribute has a correspondent
	 * @return bool attribute has a correspondent 
	 */
	public function isAssociated() {
		return $this->association != null && $this->association->isCorrespondenceGiven();
	}
	
	/**
	 * Extract elements from attributes
	 * @param $attributes array attributes
	 * @return array elements
	 */
	public static function elements($attributes) {
		$elements = array();
		foreach ($attributes as $attribute) {
			$elements[] = $attribute->getElement();
		}
		return $elements;
	}
}