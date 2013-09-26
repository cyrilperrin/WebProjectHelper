<?php

/**
 * Tools to generate MySQL
 */
class MySQL {
	/** @var string mysql tables prefix */
	private static $prefix = '';
	
	/**
	 * Set mysql tables prefix
	 * @param $prefix string mysql tables prefix
	 */
	public static function set_prefix($prefix) {
		self::$prefix = $prefix;
	}
	
	/**
	 * Get mysql tables prefix
	 * @return string mysql tables prefix
	 */
	public static function get_prefix() {
		return self::$prefix == '' ? '' : strtolower(substr(self::$prefix,-1) == '_' ? self::$prefix : self::$prefix.'_');
	}
	
	/**
	 * Get mysql element name
	 * @param $name string default name
	 * @param $element Element associated element
	 * @return string mysql element name
	 */
	public static function name($name,Element $element) {
		if ($element instanceof Scalar && $element->hasOption(Scalar::OPT_DATABASE)) { return $element->getName(); }
		elseif ($element instanceof Object && $element->hasOption(Object::OPT_DATABASE)) { return $element->getName(); }
		else { return $name; }
	}
	
	/**
	 * Get mysql table name
	 * @param $element Element mysql table element 
	 * @param $association Association association 
	 * @param $prefix boolean use mysql table prefix ?
	 * @return string mysql table name
	 */
	public static function name_table(Element $element,$association=null,$prefix=true) {
		return ($prefix?self::get_prefix():'').($association != null ? strtolower($association->getName()).'_' : '').self::name(strtolower($element->getName()),$element);
	}

	/**
	 * Get mysql association table name
	 * @param $object Object object owner
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @param $prefix boolean use mysql table prefix ?
	 * @return string mysql association table name
	 */
	public static function name_assoc_table(Object $object, $obj_association, Element $element, $elem_association,$prefix=true) {
		// Check if object equals element 
		if (!$object->equals($element) || $obj_association == null) {
			// Build names
			$names = array(self::name_table($object,$obj_association,false),self::name_table($element,$elem_association,false));
			
			// Sort names
			sort($names);
			
			// Assemble names
			return ($prefix?self::get_prefix():'').implode('_',$names);
		} else {
			// Build names
			$associations = array(strtolower($obj_association->getName()),strtolower($elem_association->getName()));
			
			// Sort names
			sort($associations);
			
			// Assemble names
			return ($prefix?self::get_prefix():'').implode('_',$associations).'_'.self::name_table($object,null,false);
		}
	}
		
	/**
	 * Get field name
	 * @param $scalar Scalar scalaer
	 * @return string field name
	 */
	public static function name_field(Scalar $scalar) {
		return self::name(strtolower($scalar->getName()),$scalar);
	}
	
	/**
	 * Get parent field name
	 * @param $scalar Scalar scalar
	 * @return string parent field name
	 */
	public static function name_field_parent(Scalar $scalar) {
		return 'parent_'.self::name(strtolower($scalar->getName()),$scalar);
	}
	
	/**
	 * Get foreign field name
	 * @param $scalar Scalar scalar
	 * @param $association Association association
	 * @return string foreign field name
	 */
	public static function name_field_foreign(Scalar $scalar,$association=null) {
		if ($association != null) { return strtolower($association->getName()).'_'.self::name(strtolower($scalar->getName()),$scalar); }
		return 'fk_'.self::name(strtolower($scalar->getName()),$scalar);
	}
	
	/**
	 * Get default value of a scalar
	 * @param $scalar Scalar scalar
	 * @return string default value
	 */
	public static function default_value(Scalar $scalar) {
		switch ($scalar->getType()) {
			case Scalar::T_BOOL :		return $scalar->getDefault()?'TRUE':'FALSE'; break;
			case Scalar::T_TIME :		return ''.$scalar->getDefault(); break;
			case Scalar::T_DATE :		return '\''.date('Y-m-d',$scalar->getDefault()).'\''; break;
			case Scalar::T_DATETIME :	return '\''.date('Y-m-d H:i:s',$scalar->getDefault()).'\''; break;
			case Scalar::T_INT :		return ''.$scalar->getDefault(); break;
			case Scalar::T_FLOAT :		return ''.$scalar->getDefault(); break;
			default :					return '\''.$scalar->getDefault().'\'';
		}
	}
	
	/**
	 * Get scalar mysql type
	 * @param $scalar Scalar scalar
	 * @param $ai bool keep auto-increment type ?
	 * @return string type
	 */
	public static function type(Scalar $scalar,$ai=true) {
		// Don't keep auto-increment if necessary
		if (!$ai && $scalar->getType() == Scalar::T_INT_AI) {
			$scalar = clone $scalar;
			$scalar->setType(Scalar::T_INT);
		}
		
		// Switch on scalar type
		switch ($scalar->getType()) {
			// Boolean
			case Scalar::T_BOOL :
				return 'TINYINT(1)';
				break;
			// Integer
			case Scalar::T_INT :
				if ($scalar->getLength() == null) return 'INT';
				if ($scalar->getLength() <= 255) return 'TINYINT';
				if ($scalar->getLength() <= 65535) return 'SMALLINT';
				if ($scalar->getLength() <= 16777215) return 'MEDIUMINT';
				if ($scalar->getLength() <= 4294967295) return 'INT';
				return 'BIGINT';
				break;
			// Auto-increment 
			case Scalar::T_INT_AI :
				return 'INT AUTO_INCREMENT';
				break;
			// Float
			case Scalar::T_FLOAT :
				return 'DECIMAL(12,8)';
				break;
			// Time
			case Scalar::T_TIME :
				return 'TIME';
				break;
			// Date
			case Scalar::T_DATE :
				return 'DATE';
				break;
			// Datetime
			case Scalar::T_DATETIME :
				return 'DATETIME';
				break;
			// Text
			case Scalar::T_TEXT :
				if ($scalar->getLength() == null) return 'VARCHAR(250)';
				if ($scalar->getLength() <= 255) return 'VARCHAR('.$scalar->getLength().')';
				if ($scalar->getLength() <= 65535) return 'TEXT';
				if ($scalar->getLength() <= 16777215) return 'MEDIUMTEXT';
				return 'LONGTEXT';
				break;
		}
	}
	
	/**
	 * Get identifiers from an object
	 * @param $object Object object
	 * @return array object identifiers
	 */
	public static function ids(Object $object) {
		// Parent ?
		if ($object->getParent() != null) {
			// Return parent's ids
			return self::ids($object->getParent());
		} else {
			// Get object's ids
			$ids = array();
			foreach ($object->getAttrsIds() as $attr) {
				// Scalar/Object ?
				if ($attr->getElem() instanceof Scalar) {
					$ids[] = $attr->getElem();
				} else {
					$ids = array_merge($ids,self::ids($attr->getElem()));
				}
			}
			
			// Return object's ids
			return $ids;
		}
	}
}
