<?php

/**
 * Object
 */
class Object extends Element {
	
	/** @var $parent Object parent */
	private $parent = null;
	
	/** @var $children array children */
	private $children = array();
	
	/** @var $attributes array attributes */
	private $attributes = array();
	
	/** @var $options string options */
	private $options = null;
	
	/** @var $isAbstract bool object is abstract ? */
	private $isAbstract = false;
	
	/** @var $listOf Object contained object */
	private $listOf = null;
	
	// Options
	const OPT_COUNT			= 'c'; // Add count method
	const OPT_SERIALIZE		= 'z'; // Add serialize and unserialize methods
	const OPT_TO_XML		= 'x'; // TODO Add toXml method
	const OPT_TO_STRING		= 's'; // Add toString method
	const OPT_EQUALS		= 'e'; // Add equals method
	const OPT_PAGINATE		= 'p'; // TODO Add paginate method
	const OPT_DATABASE		= 'd'; // Don't rename object's name in database
	
	/**
	 * Constructor
	 * @param $name string name
	 * @param $description string description
	 * @param $options string options
	 * @throws Exception when name is not valid
	 */
	public function __construct($name,$description=null,$options=null) {
		// Check if name is valid
		if (!PHP::valid_name($name)) { throw new Exception('The name <b>'.$name.'</b> for an object is not permitted'); }
		
		// Call parent constructor
		parent::__construct($name,$description);
		
		// Save attributes
		$this->options = $options;
	}
	
	// Adders
	
	/**
	 * Add an element
	 * @param $element Element element
	 * @param $multiplicity ? multiplicity
	 * @param $isId bool is identifier ?
	 * @param $association Association association
	 * @param $isListOf bool is owned as a list content ?
	 * @param $options string options
	 * @param $isExplicit bool is defined by user ?
	 */
	public function addElem(Element $element,$multiplicity=Attribute::M_OPT,$isId=false,$association=null,$isListOf=false,$options=null,$isExplicit=true) {
		// Add elemen as an attribute
		$this->addAttr(new Attribute($element,$multiplicity,$isId,$association,$options,$isExplicit),$isListOf);
	}
	
	/**
	 * Add an attribute
	 * @param $attribute Attribute attribute
	 * @param $isListOf bool is owned as a list content ?
	 * @throws Exception when attribute is not valid
	 */
	public function addAttr(Attribute $attribute,$isListOf=false) {
		// Set attribute owner
		$attribute->setOwner($this);
		
		// Check if parent is set and attribute is an identifier
		if ($this->parent != null && $attribute->isId()) {
			throw new Exception('The object <b>'.$this->getName().'</b> inherite an object and owns an id');
		}
		
		// Check if scalar is an identifier and type of time
		if ($attribute->isId() && $attribute->getElem() instanceof Scalar && in_array($attribute->getElem()->getType(),array(Scalar::T_DATE,Scalar::T_DATETIME,Scalar::T_TIME))) {
			throw new Exception('The object <b>'.$this->getName().'</b> owns a time scalar as an id, sorry but it\'s not supported');
		}
		
		// Checks on association if object already own element
		if ($this->has($attribute->getElem())) {
			// Check if association is given
			if ($attribute->getAssoc() == null) {
				throw new Exception('The object <b>'.$this->getName().'</b> owns the same object <b>'.$attribute->getElem()->getName().'</b> several times without association');
			} else {
				$matches = $this->getAttrsMatch($attribute->getElem());
				foreach ($matches as $match) {
					// Check if association is given
					if ($match->getAssoc() == null) {
						throw new Exception('The object <b>'.$this->getName().'</b> owns the same object <b>'.$attribute->getElem()->getName().'</b> several times without association');
					}
					
					// Check if associations are equals
					if ($match->getAssoc()->getName() == $attribute->getAssoc()->getName()) {
						throw new Exception('The object <b>'.$this->getName().'</b> owns the same object <b>'.$attribute->getElem()->getName().'</b> several times with the same association');						
					}
					
					// Check if correspondance is given
					if (!$this->equals($attribute->getElem()) && !$match->getAssoc()->isCorrespondenceGiven()) {
						throw new Exception('The object <b>'.$this->getName().'</b> owns the same object <b>'.$attribute->getElem()->getName().'</b> several times without correspondence association');
					}
				}
				
				// Check if correspondance is given
				if (!$this->equals($attribute->getElem()) && !$attribute->getAssoc()->isCorrespondenceGiven()) {
					throw new Exception('The object <b>'.$this->getName().'</b> owns the same object <b>'.$attribute->getElem()->getName().'</b> several times without correspondence association');
				}
			}
		}
		
		// Check if association is given when family already own element
		if ($this->hasFamily($attribute->getElem()) && $attribute->getAssoc() == null) {
			throw new Exception('The object <b>'.$this->getName().'</b> owns the object <b>'.$attribute->getElem()->getName().'</b> already owned by family without association');
		}
		
		// Check names conflicts
		if ($attribute->getAssoc() == null) {
			if ($this->has_S($attribute->getElem()->getName())) {
				throw new Exception('The object <b>'.$this->getName().'</b> owns the object <b>'.$attribute->getElem()->getName().'</b> and it causes a name conflict');
			}
			if ($this->hasFamily_S($attribute->getElem()->getName())) {
				throw new Exception('The object <b>'.$this->getName().'</b> owns the object <b>'.$attribute->getElem()->getName().'</b> and it causes a name conflict in family');
			}
		} else {
			if ($this->has_S($attribute->getAssoc()->getName())) {
				throw new Exception('The object <b>'.$this->getName().'</b> owns the object <b>'.$attribute->getElem()->getName().'</b> with the association <b>'.$attribute->getAssoc()->getName().'</b> and it causes a name conflict');
			}
			if ($this->hasFamily_S($attribute->getAssoc()->getName())) {
				throw new Exception('The object <b>'.$this->getName().'</b> owns the object <b>'.$attribute->getElem()->getName().'</b> with the association <b>'.$attribute->getAssoc()->getName().'</b> and it causes a name conflict in family');
			}
			if (in_array($attribute->getAssoc()->getName(),array('fk','ext','id'))) {
				throw new Exception('The object <b>'.$this->getName().'</b> owns the object <b>'.$attribute->getElem()->getName().'</b> with an association named as a reserved word <b>'.$attribute->getAssoc()->getName().'</b>');
			}
		}
		
		// Add attribute
		$this->attributes[] = $attribute;
		
		// List of ?
		if ($isListOf) {
			// Check if a list content is not already set
			if($this->listOf != null) {
				throw new Exception('The object <b>'.$this->getName().'</b> cannot be a list of two objects, <b>'.$this->listOf->getElem()->getName().'</b> and <b>'.$attribute->getElem()->getName().'</b>, at the same time');
			}
			
			// Set attribute owned as a list content
			$this->listOf = $attribute;
		}
		
		// Check identifier and one multiplicity loops
		if ($attribute->getElem() instanceof Object) {
			if ($attribute->isId()) { $this->checkId($this); }
			if ($attribute->getMult() == Attribute::M_ONE) { $this->checkOne($this); }
		}
	}
	
	// Setters
	
	/**
	 * Set object's parent
	 * @param $parent Object parent
	 * @throws Exception when errors occur
	 */
	public function setParent(Object $parent) {
		/**
		 * Check if object inherites not itself
		 */
		if ($this->equals($parent)) {
			throw new Exception('The object <b>'.$this->getName().'</b> inherites itself, this may be problematic ...');
		}
		
		/**
		 * Check if there no family loop
		 */
		if ($this->isInFamily($parent)) {
			throw new Exception('The object <b>'.$this->getName().'</b> inherites the object <b>'.$parent->getName().'</b> already in family, this may be problematic ...');
		}
		
		/**
		 * Check if object has not identifiers
		 */
		if ($this->getAttrsIds()) {
			throw new Exception('The object <b>'.$this->getName().'</b> inherites an object and owns an id');
		}
		
		// Save parent
		$this->parent = $parent;
		
		// Add object to parent's children 
		$parent->children[] = $this;
		
		// Set abstract
		$parent->setAbstract();
	}

	/**
	 * Set object abstract
	 * @param $isAbstract bool object abstract ?
	 */
	public function setAbstract($isAbstract=true) {
		$this->isAbstract = $isAbstract;
	}
	
	// Checkers
	
	/**
	 * Check identifier loop
	 * @param $object Object initial object
	 * @param $path array path
	 * @throws Exception when an identifier loop is detected
	 */
	private function checkId(Object $object,$path=array()) {
		foreach ($this->getAncestor()->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Object) {
				$merge = array_merge($path,array($attr->getElem()->getName()));
				if ($object->equals($attr->getElem())) { throw new Exception('There is an id loop <b>'.implode('-',$merge).'</b>, this may be problematic  ...'); }
				$attr->getElem()->checkId($object,$merge);
			}
		}
	}
	
	/**
	 * Check one multiplicity loop
	 * @param $object Object initial object
	 * @param $path array path
	 * @throws Exception when an one multiplicity loop is detected
	 */
	private function checkOne(Object $object,$path=array()) {
		foreach ($this->getFamily(true) as $member) {
			foreach ($member->getAttrsMult(Attribute::M_ONE) as $attr) {
				if ($attr->getElem() instanceof Object) {
					$merge = array_merge($path,array($attr->getElem()->getName()));
					if (in_array_equals($attr->getElem(),$object->getFamily(true))) { throw new Exception('There is an "one multiplicity" loop <b>'.implode('-',$merge).'</b>, there is no possibility to create an object in this way ...'); }
					$attr->getElem()->checkOne($object,$merge);
				}
			}
		}
	}
	
	// Getters

	/**
	 * Check if object is abstract
	 * @return bool object is abstract ?
	 */
	public function isAbstract() {
		return $this->isAbstract;
	}

	/**
	 * Get content list
	 * @return Object content list
	 */
	public function getListOf() {
		return $this->listOf;
	}
	
	// Attributes
	
	/**
	 * Get attributes list
	 * @return array attributes list
	 */
	public function getAttrs() {
		return $this->attributes;
	}
	
	/**
	 * Get attributes list by multiplicity
	 * @param $mult ? multiplicity
	 * @return array attributes list
	 */
	public function getAttrsMult($mult) {
		$attributes = array();
		foreach (array_merge($this->attributes) as $attribute) {
			if ($mult & $attribute->getMult()) { $attributes[] = $attribute; }
		}
		return $attributes;
	}
	
	/**
	 * Get identifiers attributes list
	 * @return array identifiers attributes list
	 */
	public function getAttrsIds() {
		$attributes = array();
		foreach (array_merge($this->attributes) as $attribute) {
			if ($attribute->isId()) { $attributes[] = $attribute; }
		}
		return $attributes;
	}
	
	/**
	 * Get list of attributes that match to an element
	 * @param $element Element element
	 * @return array list of attributes that match to an element
	 */
	public function getAttrsMatch(Element $element) {
		$attributes = array();
		foreach (array_merge($this->attributes) as $attribute) {
			if ($attribute->getElem()->equals($element)) { $attributes[] = $attribute; }
		}
		return $attributes;
	}
	
	/**
	 * Get list of scalar attributes that match to a type
	 * @param $type ? type
	 * @param $ancestors bool include ancestors ?
	 */
	public function getAttrsScalar($type,$ancestors=false) {
		$attributes = array();
		if ($ancestors) {
			foreach ($this->getAncestors(false) as $ancestor) {
				$attributes = array_merge($attributes,$ancestor->getAttrsScalar($type,true));
			}
		}
		foreach (array_merge($this->attributes) as $attribute) {
			if ($attribute->getElem() instanceof Scalar && $attribute->getElem()->getType() == $type) { $attributes[] = $attribute; }
		}
		return $attributes;
	}
	
	// Content
	
	/**
	 * Check if object has an element
	 * @param $element Element element
	 * @return bool object has element ?
	 */
	public function has(Element $element) {
		foreach ($this->attributes as $attribute) {
			if ($attribute->getElem()->equals($element)) { return true; }
		}
		return false;
	}
	
	/**
	 * Check if object has an element
	 * @param $string string element's name
	 * @return bool object has element ?
	 */
	public function has_S($string) {
		foreach ($this->attributes as $attribute) {
			$name = $attribute->getAssoc() != null ? $attribute->getAssoc()->getName() : $attribute->getElem()->getName();
			if (strtolower($name) == strtolower($string)) { return true; }
		}
		return false;
	}
	
	/**
	 * Check if object's family has an element
	 * @param $element Element element
	 * @param $itself bool include object itself ?
	 * @return bool object's family has element ?
	 */
	public function hasFamily(Element $element,$itself=false) {
		foreach ($this->getFamily($itself) as $member) {
			if ($member->has($element)) { return true; }
		}
		return false;
	}
	
	/**
	 * Check if object's family has an element
	 * @param $string string element's name
	 * @param $itself bool include object itself ?
	 * @return bool object's family has element ?
	 */
	public function hasFamily_S($string,$itself=false) {
		foreach ($this->getFamily($itself) as $member) {
			if ($member->has_S($string)) { return true; }
		}
		return false;
	}

	// Options
	
	/**
	 * Check if object has an option
	 * @param $option string option
	 * @return bool object has option ?
	 */
	public function hasOption($option) {
		return $this->options != null && stripos($this->options,$option) !== false;
	}

	/**
	 * Check if object's family has an option
	 * @param $option string option
	 * @return bool object family has option ?
	 */
	public function familyHasOption($option) {
		foreach($this->getFamily(true) as $member) {
			if($member->hasOption($option)) { return true; }
		}
		return false;
	}
	
	// Family
	
	/**
	 * Get parent
	 * @return Object parent
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * Get ancestor
	 * @return Object ancestor
	 */
	public function getAncestor() {
		return $this->getParent() == null ? $this : $this->getParent()->getAncestor();
	}
	
	/**
	 * Check if an object is an ancestor
	 * @param $object Object potential ancestor
	 * @param $itself include itself
	 * @return bool object is an ancestor ?
	 */
	public function isAncestor(Object $object,$itself=false) {
		return $itself && $this->equals($object) || $this->parent != null && $this->parent->isAncestor($object,true); 
	}

	/**
	 * Get children list
	 * @return array children list
	 */
	public function getChildren() {
		return $this->children;
	}
	
	/**
	 * Check if an object is a descendant
	 * @param $object Object object
	 * @return bool object is a descendant 
	 */
	public function isDescendant(Object $object) {
		return $object->isAncestor($this);
	}
	
	/**
	 * Check if an object is in family
	 * @param $object Object object
	 * @param $itself true include itself ?
	 * @return bool object is in family ? 
	 */
	public function isInFamily(Object $object,$itself=false) {
		return $this->isDescendant($object,$itself) || $this->isAncestor($object,$itself);
	}
	
	/**
	 * Get ancestor list
	 * @param $itself bool include itself ?
	 * @return array ancestors list
	 */
	public function getAncestors($itself=false) {
		$ancestors = array();
		if ($this->parent != null) { $ancestors = $this->parent->getAncestors(true); }
		if ($itself) { $ancestors[] = $this; }
		return $ancestors;
	}
	
	/**
	 * Get descendants list
	 * @param $itself bool descendants list
	 */
	public function getDescendants($itself=false) {
		$descendants = array();
		if ($itself) { $descendants[] = $this; }
		foreach ($this->children as $child) { $descendants = array_merge($descendants,$child->getDescendants(true)); }
		return $descendants;
	}
	
	/**
	 * Get family
	 * @param $itself bool include itself ?
	 * @return array family
	 */
	public function getFamily($itself=false) {
		return array_merge($this->getAncestors($itself),$this->getDescendants(false));
	}
	
	/**
	 * Get leaf descendants
	 * @param $itself bool include itself
	 * @return array leaf descendants
	 */
	public function getLeafs($itself=false) {
		$leafs = array();
		if ($itself && !count($this->children)) { $leafs[] = $this; }
		foreach ($this->children as $child) { $leafs = array_merge($leafs,$child->getLeafs(true)); }
		return $leafs;
	}
}
