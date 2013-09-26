<?php

/**
 * Project
 */
class Project {
	/** @var $name string name */
	private $name;
	
	/** @var $objects array objects */
	private $objects = array();
	
	/**
	 * Constructor
	 * @param $name string name
	 */
	public function __construct($name=null) {
		$this->name = $name;
	}
	
	/**
	 * Get name
	 * @return string name
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Add object into project
	 * @param $name string object's name
	 * @param $description string object's description
	 * @param $options string object's options
	 * @throws Exception when object is defined several times
	 * @return Object added object
	 */
	public function addObject($name,$description=null,$options=null) {
		// Check if object is not already defined
		foreach (array_merge($this->objects) as $object) {
			if (strtolower($object->getName()) == strtolower($name)) {
				throw new Exception('The object <b>'.$name.'</b> is defined several times');
			}
		}
		
		// Add object into project
		return $this->objects[] = new Object($name,$description,$options);
	}
	
	/**
	 * Get objects list
	 * @return array objects list
	 */
	public function getObjects() {
		return $this->objects;
	}
	
	/**
	 * Get objects count
	 * @return int objects count
	 */
	public function count() {
		return count($this->objects);
	}
	
	/**
	 * Get owners of an object
	 * @param $object Object object
	 * @return array owners
	 */
	public function getOwners(Object $object) {
		$owners = array();
		foreach (array_merge($this->objects) as $owner) {
			if ($owner->has($object)) { $owners[] = $owner; }
		}
		return $owners;
	}
	
	/**
	 * Compile project
	 * @throws Exception when a correspondance pointing to nothing
	 */
	public function compile() {
		// Process on each object
		foreach (array_merge($this->objects) as $object) {
			// Check correspondences
			foreach ($object->getAttrs() as $attr) {
				if ($attr->getAssoc() != null && $attr->getAssoc()->isCorrespondenceGiven() &&  $attr->getAssoc()->getCorrespondence() == null) {
					throw new Exception('The object <b>'.$object->getName().'</b> owns the object <b>'.$attr->getElem()->getName().'</b> with an association correspondence pointing to nothing');
				}
			}
			
			// Add missing links
			foreach ($this->getOwners($object) as $owner) {
				if (!$object->has($owner)) {
					$attrs = $owner->getAttrsMatch($object);
					foreach ($attrs as $attr) {
						if ($attr->isMult(Attribute::M_SEVERAL)) {
							$object->addElem($owner,Attribute::M_OPT,false,null,false,null,false);
						} else {
							$object->addElem($owner,Attribute::M_SEVERAL,false,null,false,null,false);
						}
					}
				}
			}
			
			// Add missing identifiers
			if (!count($object->getAttrsIds()) && $object->getParent() == null) {
				$object->addElem(Scalar::id($object),Attribute::M_ONE,true);
			}
		}
		
		// Return project
		return $this;
	}
}
