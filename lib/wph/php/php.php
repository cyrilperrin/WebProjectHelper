<?php

/**
 * PHP
 */
class PHP {
	
	/** @var boolean fieldnames must be defined into base classes ? */
	private static $fieldnamesIntoBaseClasses = true;
	
	/** @var boolean generate only base classes ? */
	private static $generateOnyBaseClasses = false;
	
	/** @var string PHP classes prefix */
	private static $classesPrefix = '';
	
	/** @var string PHP files prefix */
	private static $filesPrefix = '';
	
	/**
	 * Define if fieldnames must be defined into base classes
	 * @param $fieldnamesIntoBaseClasses boolean fieldnames must be defined into base classes 
	 */
	public static function set_fieldnames_into_base_classes($fieldnamesIntoBaseClasses) {
		self::$fieldnamesIntoBaseClasses = $fieldnamesIntoBaseClasses;
	}
	
	/**
	 * Check if fieldnames must be defined into base classes
	 * @return boolean fieldnames must be defined into base classes ?
	 */
	public static function get_fieldnames_into_base_classes() {
		return self::$fieldnamesIntoBaseClasses;
	}
	
	/**
	 * Define if generate only base clases
	 * @param $generateOnlyBaseClasses boolean generate only base clases ?
	 */
	public static function set_generate_only_base_classes($generateOnlyBaseClasses) {
		self::$generateOnyBaseClasses = $generateOnlyBaseClasses;
	}
	
	/**
	 * Check if generate only base clases
	 * @return boolean generate only base clases ?
	 */
	public static function get_generate_only_base_classes() {
		return self::$generateOnyBaseClasses;
	}
	
	/**
	 * Set PHP classes prefix
	 * @param $classesPrefix string PHP classes prefix 
	 */
	public static function set_classes_prefix($classesPrefix) {
		self::$classesPrefix = $classesPrefix;
	}
	
	/**
	 * Get PHP classes prefix
	 * @return string PHP classes prefix
	 */
	public static function get_classes_prefix() {
		return self::$classesPrefix == '' ? '' : ucfirst(substr(self::$classesPrefix,-1) == '_' ? self::$classesPrefix : self::$classesPrefix.'_');
	}
	
	/**
	 * Set PHP files prefix
	 * @param $filesPrefix string PHP classes prefix 
	 */
	public static function set_files_prefix($filesPrefix) {
		self::$filesPrefix = $filesPrefix;
	}
	
	/**
	 * Get PHP files prefix
	 * @return string PHP files prefix
	 */
	public static function get_files_prefix() {
		return self::$filesPrefix == '' ? '' : ucfirst(substr(self::$filesPrefix,-1) == '_' ? self::$filesPrefix : self::$filesPrefix.'_');
	}
	
	/**
	 * Valid a name
	 * @param $name string name to valid
	 * @return bool name is valid ?
	 */
	public static function valid_name($name) {
		return !in_array(strtolower($name),array('pdo','pdostatement','select','execute','lazyload','iteratorselect','iteratorcurrent','iteratorkey','value'));
	}
	
	/**
	 * Get PHP type from an element
	 * @param $element Element element
	 * @return string PHP type
	 */
	public static function type(Element $element) {
		$variable = Variable::convert($element);
		switch ($variable->getType()) {
			case Variable::T_BOOL : return 'bool'; break;
			case Variable::T_INT : return 'int'; break;
			case Variable::T_FLOAT : return 'float'; break;
			case Variable::T_STRING : return 'string'; break;
			case Variable::T_ARRAY : return 'array'; break;
		}
		return $variable->getType();
	}
	
	/**
	 * 
	 * Get PHP default value
	 * @param $value ? value
	 * @param $element Element associated element
	 * @throws Exception when no PHP default value found
	 * @return string PHP default value
	 */
	public static function default_value($value,$element=null) {
		if ($element != null && ($default = PHP::default_value_special($element))) { return $default; }
		if (is_bool($value)) { return $value?'true':'false'; }
		if (is_numeric($value)) { return $value; }
		if (is_null($value)) { return 'null'; }
		if (is_string($value)) { return '\''.$value.'\''; }
		throw new Exception('Error while searching for a default value');
	}
	
	/**
	 * Get PHP default value from an element
	 * @param $element Element element
	 * @return string|null PHP default value, null if default value is not special
	 */
	public static function default_value_special(Element $element) {
		if ($element instanceof Scalar && $element->isType(Scalar::T_DATE|Scalar::T_DATETIME) && $element->getDefault() === Scalar::DEF_NOW ||
		    $element instanceof Variable && $element->getType() == Variable::T_INT && $element->getDefault() === Scalar::DEF_NOW) {
		   	return 'CURRENT_TIME';
		}
		return null;
	}
	
	/**
	 * Get element or association name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string element or association name
	 */
	public static function name(Element $element,$association=null) {
		return $association == null ? $element->getName() : $association->getName();
	}

	/**
	 * Get class name
	 * @param $object Object object
	 * @param $usePrefix boolean use prefix ?
	 * @return string class name
	 */
	public static function name_class(Object $object,$usePrefix=true) {
		return ($usePrefix ? self::get_classes_prefix() : '').implode('_',array_map('ucfirst',explode('_',$object->getName())));
	}

	/**
	 * Get base class name
	 * @param $object Object object
	 * @param $usePrefix boolean use prefix ?
	 * @return string base class name
	 */
	public static function name_class_base(Object $object,$usePrefix=true) {
		return ($usePrefix ? self::get_classes_prefix() : '').'Base_'.implode('_',array_map('ucfirst',explode('_',$object->getName())));
	}
	
	/**
	 * Get association class name
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @param $usePrefix boolean use prefix ?
	 * @return string association class name
	 */
	public static function name_assoc_class(Object $object, $obj_association, Element $element, $elem_association,$usePrefix=true) {
		// Build names
		$names = array(
			($obj_association == null ? '' : implode('',array_map('ucfirst',explode('_',$obj_association->getName()))).'_').implode('',array_map('ucfirst',explode('_',$object->getName()))),
			($elem_association == null ? '' : implode('',array_map('ucfirst',explode('_',$elem_association->getName()))).'_').implode('',array_map('ucfirst',explode('_',$element->getName())))
		);
			
		// Sort names
		sort($names);
		
		// Assemble names
		return ($usePrefix ? self::get_classes_prefix() : '').'Association_'.implode('',$names);
	}
	
	/**
	 * Get variable name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string variable name
	 */
	public static function name_variable(Element $element,$association=null) {
		return '$'.lcfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get parameter name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string parameter name
	 */
	public static function name_parameter(Element $element,$association=null) {
		return '$'.lcfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get attribute name
	 * @param $element Element element
	 * @param $association association
	 * @param $dollar display dollar ?
	 * @return string attribute name ?
	 */
	public static function name_attribute(Element $element,$association=null,$dollar=true) {
		return ($dollar?'$':'').'_'.lcfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get attribute access
	 * @param $element Element element
	 * @param $association Association association
	 * @param $receptor string receptor
	 * @return string attribute access
	 */
	public static function attribute(Element $element,$association=null,$receptor='$this') {
		return $receptor.'->'.PHP::name_attribute($element,$association,false);
	}
	
	/**
	 * Get getter method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string getter method name
	 */
	public static function method_getter(Element $element,$association=null) {
		return 'get'.ucfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get getter id method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string getter method name
	 */
	public static function method_getter_id(Element $element,$association=null) {
		return 'get'.ucfirst(PHP::name($element,$association)).'Id';
	}
	
	/**
	 * Get setter method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string setter method name
	 */
	public static function method_setter(Element $element,$association=null) {
		return 'set'.ucfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get setter by id method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string setter method name
	 */
	public static function method_setter_by_id(Element $element,$association=null) {
		return 'set'.ucfirst(PHP::name($element,$association)).'ById';
	}
	
	/**
	 * Get set method name
	 * @return string set method name
	 */
	public static function method_set() {
		return '_set';
	}
	
	/**
	 * Get update method name
	 * @return string update method name
	 */
	public static function method_update() {
		return 'update';
	}
	
	/**
	 * Get reload method name
	 * @return string reload method name
	 */
	public static function method_reload() {
		return 'reload';
	}
	
	/**
	 * Get exists method name
	 * @return string exists method name
	 */
	public static function method_exists() {
		return 'exists';
	}
	
	/**
	 * Get equals method name
	 * @return string equals method name
	 */
	public static function method_equals() {
		return 'equals';
	}
	
	/**
	 * Get adder method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string adder method name
	 */
	public static function method_adder(Element $element,$association=null) {
		return 'add'.ucfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get adder by id method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string adder method name
	 */
	public static function method_adder_by_id(Element $element,$association=null) {
		return 'add'.ucfirst(PHP::name($element,$association)).'ById';
	}
	
	/**
	 * Get adder list of method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string adder list of method name
	 */
	public static function method_adder_list_of(Element $element,$association=null) {
		return 'addListOf'.ucfirst(svrl(PHP::name($element,$association)));
	}
	
	/**
	 * Get remover method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string remover method name
	 */
	public static function method_remover(Element $element,$association=null) {
		return 'remove'.ucfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get remover by id method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string remover method name
	 */
	public static function method_remover_by_id(Element $element,$association=null) {
		return 'remove'.ucfirst(PHP::name($element,$association)).'ById';
	}
	
	/**
	 * Get remover list of method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string remover list of method name
	 */
	public static function method_remover_list_of(Element $element,$association=null) {
		return 'removeListOf'.ucfirst(svrl(PHP::name($element,$association)));
	}
	
	/**
	 * Get remove all method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string remover method name
	 */
	public static function method_remove_all(Element $element,$association=null) {
		return 'removeAll'.svrl(ucfirst(PHP::name($element,$association)));
	}
	
	/**
	 * Get lister method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string lister method name
	 */
	public static function method_lister(Element $element,$association=null) {
		return 'select'.svrl(ucfirst(PHP::name($element,$association)));
	}
	
	/**
	 * Get loader method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string loader method name
	 */
	public static function method_loader(Element $element,$association=null) {
		return 'load'.svrl(ucfirst(PHP::name($element,$association)));
	}
	
	/**
	 * Get lister by method name
	 * @param $element Element element
	 * @param $association Association
	 * @return string lister by method name
	 */
	public static function method_lister_by(Element $element,$association=null) {
		return 'selectBy'.ucfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get create method name
	 * @return string create method name
	 */
	public static function method_create() {
		return 'create';
	}
	
	/**
	 * Get fetch method name
	 * @return string fetch method name
	 */
	public static function method_fetch() {
		return 'fetch';
	}
	
	/**
	 * Get fetch all method name
	 * @return string fetch all method name
	 */
	public static function method_fetch_all() {
		return 'fetchAll';
	}
	
	/**
	 * Get load method name
	 * @return string load method name
	 */
	public static function method_load() {
		return 'load';
	}
	
	/**
	 * Get load by method name
	 * @param $element Element element
	 * @param $association Association association
	 * @return string load by method name
	 */
	public static function method_load_by(Element $element,$association=null) {
		return 'loadBy'.ucfirst(PHP::name($element,$association));
	}
	
	/**
	 * Get load all method name
	 * @return string load all method name
	 */
	public static function method_load_all() {
		return 'loadAll';
	}
	
	/**
	 * Get select method name
	 * @return string select method name
	 */
	public static function method_select() {
		return '_select';
	}
	
	/**
	 * Get select all method name
	 * @return string select all method name
	 */
	public static function method_select_all() {
		return 'selectAll';
	}
	
	/**
	 * Get count method name
	 * @return string count method name
	 */
	public static function method_count() {
		return 'count';
	}
	
	/**
	 * Get delete method name
	 * @return string delete method name
	 */
	public static function method_delete() {
		return 'delete';
	}
	
	/**
	 * Get serialize method name
	 * @return string serialize method name
	 */
	public static function method_serialize() {
		return 'serialize';
	}
	
	/**
	 * Get unserialize method name
	 * @return string unserialize method name
	 */
	public static function method_unserialize() {
		return 'unserialize';
	}

	/**
	 * Get field constant name
	 * @param $element Element element
	 * @return string field constant name
	 */
	public static function const_field(Element $element) {
		return 'FIELDNAME_'.(strtoupper($element->getName()));
	}
	
	/**
	 * Get foreign field constant name
	 * @param $object Object owner object
	 * @param $element Element owned element
	 * @param $association Association association
	 * @return string foreign field constant name
	 */
	public static function const_field_foreign(Object $object, Element $element,$association=null) {
		return 'FIELDNAME_'.strtoupper(($association != null ? $association->getName() : $object->getName())).'_'.(strtoupper($element->getName()));
	}
	
	/**
	 * Get parent's field constant name
	 * @param $element Element element
	 * @return string parent's field constant name
	 */
	public static function const_field_parent(Element $element) {
		return 'FIELDNAME_PARENT_'.(strtoupper($element->getName()));
	}
	
	/**
	 * Get table constant name
	 * @return string table constant name
	 */
	public static function const_table_name() {
		return 'TABLENAME';
	}
	
	/**
	 * Get file name for class
	 * @param $object Object object
	 * @return string file name for class
	 */
	public static function file_class(Object $object) {
		return self::get_files_prefix().self::name_class($object, false).'.php';
	}
	
	/**
	 * Get file name for base class
	 * @param $object Object object
	 * @return string file name for base class
	 */
	public static function file_class_base(Object $object) {
		return self::get_files_prefix().self::name_class_base($object, false).'.php';
	}
	
	/**
	 * Get file name for association class
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string file name for association class
	 */
	public static function file_assoc_class($object, $obj_association, $element, $elem_association) {
		return self::get_files_prefix().self::name_assoc_class($object, $obj_association, $element, $elem_association, false).'.php';
	}
	
	/**
	 * Get file name for association classes
	 * @return string file name for association classes
	 */
	public static function file_assoc_classes() {
		return 'associations.php';
	}
	
	/**
	 * Get file name for requires
	 * @return string file name for requires
	 */
	public static function file_requires() {
		return 'requires.php';
	}
	
	/**
	 * Get key to save an id into an array
	 * @param $element Element element
	 * @param $association Association association
	 * @return string key
	 */
	public static function key(Element $element,$association=null) {
		return strtolower(PHP::name($element,$association));
	}
	
	/**
	 * Get keys to object's ids into an array
	 * @param $object Object object
	 * @param $separator string separator
	 * @return array keys
	 */
	public static function keys(Object $object,$separator='-') {
		// Init array
		$keys = array();
		
		// Get keys
		foreach ($object->getAncestor()->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$keys[] = PHP::key($attr->getElem(),$attr->getAssoc());
			} else {
				foreach (PHP::keys($attr->getElem(),$separator) as $key) {
					$keys[] = PHP::key($attr->getElem(),$attr->getAssoc()).$separator.$key;
				}
			}
		}
		
		// Return array
		return $keys;
	}
	
	/**
	 * Get recursive load
	 * @param $attributeName string attribute name
	 * @param $objet Objet object
	 * @param $association Association association
	 * @param $key string key
	 * @return string recursive load
	 */
	public static function recursive_load($attributeName,Object $object,$association=null,$key='') {
		// Get params
		$params = array();
		foreach ($object->getAncestor()->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$params[] = $attributeName.'[\''.$key.PHP::key($attr->getElem()).'\']';
			} else {
				$params[] = PHP::recursive_load($attributeName,$attr->getElem(),$attr->getAssoc(),$key.PHP::key($object,$association).'-');
			}
		}
		
		// Build/Return load
		return PHP::name_class($object).'::'.PHP::method_load().'('.PHP::attribute(Variable::var_pdo()).','.implode(',',$params).')';
	}
	
	/**
	 * Get scalar ids of an object
	 * @param $object Object object
	 * @return Scalar[] scalar ids of the object
	 */
	public static function scalar_ids(Object $object) {
		// Init array
		$ids = array();
		
		// Get scalar ids
		foreach ($object->getAncestor()->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$ids[] = $attr->getElem();
			} else {
				$ids = array_merge($ids,PHP::scalar_ids($attr->getElem()));
			}
		}
		
		// Return array
		return $ids;
	}
	
	/**
	 * Get paths to access object's ids
	 * @param $object Object object
	 * @param $receptor string receptor
	 * @param $null bool receptor can be null ?
	 * @param $query bool adapt paths to query ? @todo adapt values such as dates
	 * @param $keys bool use keys to index paths ?
	 * @return array paths
	 */
	public static function paths_ids(Object $object,$receptor='$this',$null=false,$query=false,$keys=true) {
		// Parent
		if ($object->getParent() != null) {
			return PHP::paths_ids($object->getParent(),$receptor,$null);
		}
		
		// Null ?
		if ($null) { $receptor = $receptor.' == null ? null : '.$receptor; }
		
		// Init paths
		$paths = array();
		
		// Get paths
		$i = 0;
		foreach ($object->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$key = $keys ? PHP::key($attr->getElem()) : $i;
				$paths[$key] = $receptor.'->'.PHP::method_getter($attr->getElem()).'()';
				$i++;
			} else {
				foreach (PHP::paths_ids($attr->getElem(),'') as $key => $path) {
					$key = $keys ? PHP::key($attr->getElem(),$attr->getAssoc()).'-'.$key : $i;
					$paths[$key] = $receptor.'->'.PHP::method_getter($attr->getElem(),$attr->getAssoc()).'()'.$path;
					$i++;
				}
			}
		}
		
		// Return paths
		return $paths;
	}

	/**
	 * Get object's ids as variables
	 * @param $object Object object
	 * @return array object's ids as variables
	 */
	public static function vars_ids(Object $object) {
		// Parent
		if ($object->getParent() != null) {
			return PHP::vars_ids($object->getParent(),$receptor,$null);
		}
	
		// Init vars
		$vars = array();
	
		// Get vars
		$i = 0;
		foreach ($object->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$key = $keys ? PHP::key($attr->getElem()) : $i;
				$paths[$key] = $receptor.'->'.PHP::method_getter($attr->getElem()).'()';
				$i++;
			} else {
				foreach (PHP::paths_ids($attr->getElem(),'') as $key => $path) {
					$key = $keys ? PHP::key($attr->getElem(),$attr->getAssoc()).'-'.$key : $i;
					$paths[$key] = $receptor.'->'.PHP::method_getter($attr->getElem(),$attr->getAssoc()).'()'.$path;
					$i++;
				}
			}
		}
	
		// Return vars
		return $vars;
	}

	/**
	 * Get defaults values
	 * @param $defaults array elements
	 * @param $shift int indexing shift
	 * @return array defaults values
	 */
	public static function defaults_values($defaults,$shift=0) {
		$defaults_values = array();
		for($i=0;$i<count($defaults);$i++) {
			if ($defaults[$i] instanceof Scalar || $defaults[$i] instanceof Variable && $defaults[$i]->isScalar()) {
				$defaults_values[$i+$shift] = $defaults[$i]->getDefault();
			} else { $defaults_values[$i+$shift] = null; }
		}
		return $defaults_values;
	}
	
	/**
	 * Extract ids, attributes and optionnals from an object
	 * @param $object Object object
	 * @param $join_parent bool merge parent attributes ?
	 * @param $keep_ai bool keep auto increment ?
	 * @return array ids, attributes and optionnals
	 */
	public static function ids_attrs_opts(Object $object,$join_parent=false,$keep_ai=true) {
		// Init arrays
		$ids = array(); $attrs = array(); $opts = array();
		$ids_parent = array(); $attrs_parent = array(); $opts_parent = array();
		
		// Ids, attrs and opts of parent
		if ($object->getParent() != null) {
			list($ids_parent,$attrs_parent,$opts_parent) = PHP::ids_attrs_opts($object->getParent(),true,$keep_ai);
		}
		else {
			// Ids
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					if ($keep_ai || $attr->getElem()->getType() != Scalar::T_INT_AI) {
						$ids[] = $attr;
					}
				} else {
					$ids[] = $attr;
				}
			}
		}
		
		// Get attrs and opts
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			if (!$attr->isId()) {
				$keep = true;
				
				if ($attr->getElem() instanceof Scalar && !$keep_ai && $attr->getElem()->getType() == Scalar::T_INT_AI) {
					$keep = false;
				}
				
				if ($keep) {
					if ($attr->isMult(Attribute::M_OPT) || $attr->getElem() instanceof Scalar && $attr->getElem()->getDefault() !== null) {
						$opts[] = $attr;
					} else { $attrs[] = $attr; }
				}
			}
		}
		
		// Return arrays
		if ($join_parent) {
			return array(array_merge($ids_parent,$ids),
			             array_merge($attrs_parent,$attrs),
			             array_merge($opts_parent,$opts),
			             $ids_parent,$attrs_parent,$opts_parent);
		} else {
			return array($ids,$attrs,$opts,$ids_parent,$attrs_parent,$opts_parent);
		}
	}
	
	/**
	 * Convert elements to variables
	 * @param $elements array elements
	 * @return array variables
	 */
	public static function to_vars($elements) {
		$array = array();
		foreach ($elements as $key => $element) {
			if (is_array($element)) {
				$array[$key] = PHP::to_vars($element);
			} else {
				$array[$key] = Variable::convert($element->getElem(),$element->getAssoc());
			}
		}
		return $array;
	}
	
	/**
	 * Convert elements to attributes
	 * @param $elements array elements
	 * @return array attributes
	 */
	public static function to_attrs($elements) {
		$array = array();
		foreach ($elements as $key => $element) {
			if (is_array($element)) {
				$array[$key] = PHP::to_attrs($element);
			} else {
				$array[$key] = Variable::var_attribute($element->getElem(),$element->getAssoc());
			}
		}
		return $array;
	}
	
	/**
	 * Get parameters from an attribute to insert an object into database
	 * @param $params array parameters
	 * @param $attr Attribute object's attribute
	 */
	public static function create_add_param_execute(&$params,Attribute $attr) {
		// Parameter name
		$param = PHP::name_parameter($attr->getElem(),$attr->getAssoc());
		
		// Scalar/Object ?
		if ($attr->getElem() instanceof Scalar) {
			$params[] = PHP::value($attr->getElem(),$param,$attr->isMult(Attribute::M_OPT));
		} else {
			$params = array_merge($params,PHP::paths_ids($attr->getElem(),$param,$attr->isMult(Attribute::M_OPT),true,false));
		}
	}
	
	/**
	 * Get parameters from an attribute to 
	 * @param $params array
	 * @param $attr Attribute attribute
	 * @param $parent bool attribute from parent ?
	 * @param $pdo Variable pdo variable
	 */
	public static function create_add_param_new(&$params,Attribute $attr,$parent,Variable $pdo) {
		// Scalar/Object ?
		if ($attr->getElem() instanceof Scalar) {
			if ($attr->getElem()->getType() == Scalar::T_INT_AI) {
				if ($parent) {
					$params[] = PHP::name_variable($attr->getElem());
				} else {
					$params[] = 'intval('.PHP::name_variable($pdo).'->lastInsertId())';
				}
			} else {
				$params[] = PHP::name_parameter($attr->getElem());
			}
		} else {
			$paths = PHP::paths_ids($attr->getElem(),PHP::name_parameter($attr->getElem(),$attr->getAssoc()),$attr->isMult(Attribute::M_OPT));
			if (count($paths) > 1) {
				$ids = array();
				foreach ($paths as $key => $path) {
					$ids[] = '\''.$key.'\' => '.$path;
				}
				$params[] = 'array('.implode(', ',$ids).')';
			} else {
				$params[] = array_shift($paths);
			}
		}
	}
	
	/**
	 * Adapt value to query
	 * @param $scalar Scalar associated scalar
	 * @param $value string scalar name
	 * @param $null value can be null ?
	 * @return string adapted value
	 */
	public static function value(Scalar $scalar,$value,$null=false) {
		$test_null = $null ? $value.' === null ? null : ' : '';
		switch ($scalar->getType()) {
			case Scalar::T_DATETIME :
				$value = $test_null.'date(\'Y-m-d H:i:s\','.$value.')';
				break;
			case Scalar::T_DATE :
				$value = $test_null.'date(\'Y-m-d\','.$value.')';
				break;
		}
		return $value;
	}
}
