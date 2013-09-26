<?php

/**
 * Variable
 */
class Variable extends Element {
	/** @var $type ? type */
	private $type;
	
	/** @var $isScalar bool is scalar ? */
	private $isScalar;
	
	/** @var $default ? default value */
	private $default;
	
	// Variable types
	const T_BOOL 	= 1;
	const T_INT 	= 2;
	const T_FLOAT 	= 4;
	const T_STRING 	= 16;
	const T_ARRAY 	= 32;
	
	/**
	 * Constructor
	 * @param $name string name
	 * @param $type ? type
	 * @param $isScalar bool is scalar ?
	 * @param $description string description
	 * @param $default ? default value
	 */
	public function __construct($name,$type,$isScalar,$description=null,$default=null) {
		// Call parent constructor
		parent::__construct($name,$description);
		
		// Save attributes
		$this->type = $type;
		$this->isScalar = $isScalar;
		$this->default = $default;
	}

	/**
	 * Get type
	 * @return ? type
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Check if scalar
	 * @return bool is scalar ?
	 */
	public function isScalar() {
		return $this->isScalar;
	}
	
	/**
	 * Get default value
	 * @return ? default value
	 */
	public function getDefault() {
		return $this->default;
	}
	
	/**
	 * Convert an element to variable
	 * @param $element Element element 
	 * @param $association Association association
	 * @return Variable corresponding variable
	 */
	public static function convert(Element $element,$association=null) {
		if ($element instanceof Variable) { return $element; }
		return new Variable($association?lcfirst($association->getName()):lcfirst($element->getName()),
		                    Variable::convert_type($element),$element instanceof Scalar,
		                    $element->getDescription(),$element instanceof Scalar ? $element->getDefault() : null);
	}
	
	/**
	 * Convert an element array to variable array
	 * @param $elements array element array
	 * @param $association Association association
	 * @return array corresponding variable array
	 */
	public static function convert_all($elements,$association=null) {
		foreach ($elements as $key => $element) {
			$elements[$key] = Variable::convert($element,$association);
		}
		return $elements;
	}
	
	/**
	 * Get variable type from an element
	 * @param $element Element element
	 * @return ? variable type
	 */
	private static function convert_type(Element $element) {
		if ($element instanceof Variable) { return $element->getType(); }
		elseif ($element instanceof Scalar) {
			switch ($element->getType()) {
				case Scalar::T_BOOL : return Variable::T_BOOL; break;
				case Scalar::T_INT : return Variable::T_INT; break;
				case Scalar::T_INT_AI : return Variable::T_INT; break;
				case Scalar::T_FLOAT : return Variable::T_FLOAT; break;
				case Scalar::T_DATE : return Variable::T_INT; break;
				case Scalar::T_TIME : return Variable::T_INT; break;
				case Scalar::T_DATETIME : return Variable::T_INT; break;
				case Scalar::T_TEXT : return Variable::T_STRING; break;
			}
		}
		else { return PHP::name_class($element); }
	}
	
	/**
	 * Get PDO variable
	 * @param $name string variable name
	 * @return Variable PDO variable
	 */
	public static function var_pdo($name='pdo') {
		return new Variable($name,'PDO',false);
	}
	
	/**
	 * Get PDO statement variable
	 * @param $name string variable name
	 * @return Variable PDO statement variable
	 */
	public static function var_pdo_statement($name='pdoStatement') {
		return new Variable($name,'PDOStatement',false);
	}
	
	/**
	 * Get success variable
	 * @param $name string variable name
	 */
	public static function var_success($name='success') {
		return new Variable($name,Variable::T_BOOL,true,tr('Successful operation ?'));
	}
	
	/**
	 * Get equals variable
	 * @param $name string variable name
	 * @return Variable equals variable
	 */
	public static function var_equals($name='equals') {
		return new Variable($name,Variable::T_BOOL,true,tr('Objects are equals ?'));
	}
	
	/**
	 * Get iterator as statement variable
	 * @param $element Element element
	 * @param $association Association association
	 * @param $name string variable name
	 * @return Variable iterator as statement variable
	 */
	public static function var_iterator_statement(Element $element,$association=null,$name='iteratorSelect') {
		return new Variable($name,'PDOStatement',false,tr('Select for the iterator implementation'));
	}
	
	/**
	 * Get iterator as array variable
	 * @param $element Element element
	 * @param $association Association association
	 * @param $name string variable name
	 * @return Variable iterator as array variable
	 */
	public static function var_iterator_array(Element $element,$association=null,$name='iteratorArray') {
		return new Variable($name,Variable::convert_type($element).'[]',true,tr('Array of '.svrl(lcfirst(PHP::name($element,$association))).' for the iterator implementation'));
	}
	
	/**
	 * Get current element variable
	 * @param $element Element element
	 * @param $association Association association
	 * @param $name string variable name
	 * @return Variable current element variable
	 */
	public static function var_iterator_current(Element $element,$association=null,$name='iteratorCurrent') {
		return new Variable($name,Variable::convert_type($element),$element instanceof Scalar,tr('Current element for the iterator implementation'));
	}
	
	/**
	 * Get current element key variable
	 * @param $scalar Scalar scalar 
	 * @param $association Association association
	 * @param $name string variable name
	 * @return Variable current element key variable
	 */
	public static function var_iterator_key(Scalar $scalar,$association=null,$name='iteratorKey') {
		return new Variable($name,Variable::T_INT,true,tr('Key of current element for the iterator implementation'),0);
	}
	
	/**
	 * Get lazy load array variable
	 * @param $name string variable name
	 * @return Variable lazy load array variable 
	 */
	public static function var_lazy_load($name='lazyload') {
		return new Variable($name,Variable::T_ARRAY,true,tr('Array for lazy load'));
	}
	
	/**
	 * Get lazy load indicator variable 
	 * @param $default bool default value
	 * @param $name string variable name
	 * @return Variable lazy load indicator variable 
	 */
	public static function var_lazy_load_enable($default,$name='lazyload') {
		return new Variable($name,Variable::T_BOOL,true,tr('Enable lazy load ?'),$default);
	}
	
	/**
	 * Get element array variable
	 * @param $element Element element
	 * @param $name string variable name
	 * @param $association Association association
	 * @return Variable element array variable
	 */
	public static function var_array($element=null,$name=null,$association=null) {
		if ($name == null) { $name = $element == null || svrl(PHP::name($element,$association)) == PHP::name($element,$association) ? 'array' : svrl(PHP::name($element,$association)); }
		return new Variable($name,$element == null ? Variable::T_ARRAY : Variable::convert_type($element).'[]',true,$element == null ? tr('Array') : tr('Array of '.svrl(lcfirst(PHP::name($element,$association)))));
	}
	
	/**
	 * Get element count variable
	 * @param $element Element element
	 * @param $name string variable name
	 * @return Variable element count variable
	 */
	public static function var_count(Element $element,$name='count') {
		return new Variable($name,Variable::T_INT,true,tr('Number of '.svrl(lcfirst($element->getName()))));
	}
	
	/**
	 * Get element string representation variable
	 * @param $element Element element
	 * @param $name string variable name
	 * @return element string representation variable
	 */
	public static function var_tostring(Element $element,$name='string') {
		return new Variable($name,Variable::T_STRING,true,tr('String representation of '.lcfirst($element->getName())));
	}
	
	/**
	 * Get element serialize variable
	 * @param $element Element element
	 * @param $name string variable name
	 * @return Variable element serialize variable
	 */
	public static function var_serialize(Element $element,$name='string') {
		return new Variable($name,Variable::T_STRING,true,tr('Serialization of '.lcfirst($element->getName())));
	}
	
	/**
	 * Get serialize indicator variable
	 * @param $default bool default value
	 * @param $name string variable name
	 * @return Variable serialize indicator variable
	 */
	public static function var_serialize_enable($default,$name='serialize') {
		return new Variable($name,Variable::T_BOOL,true,tr('Enable serialize ?'),$default);
	}
	
	/**
	 * Get exists variable
	 * @param $object Object object
	 * @param $name string variable name
	 * @return Variable exists variable
	 */
	public static function var_exists(Object $object,$name='exists') {
		return new Variable($name,Variable::T_BOOL,true,tr('The '.lcfirst($object->getName()).' exists in database ?'));
	}
	
	/**
	 * Get number of affected rows variable
	 * @param $name string variable name
	 * @return Variable number of rows affected variable
	 */
	public static function var_affected_rows($name='count') {
		return new Variable($name,Variable::T_INT,true,tr('Number of affected rows'));
	}
	
	/**
	 * Get execute update query variable
	 * @param $name string variable name
	 * @return Variable execute update query variable
	 */
	public static function var_execute_update_query($name='execute') {
		return new Variable('execute',Variable::T_BOOL,true,tr('Execute update query ?'),true);
	}
	
	/**
	 * Get attribute variable
	 * @param $element Element element
	 * @param $association Association association
	 * @return Variable attribute variable
	 */
	public static function var_attribute(Element $element,$association=null) {
		if ($element instanceof Scalar) { return Variable::convert($element); }
		$ids = MySQL::ids($element);
		if (count($ids) > 1) { return new Variable(PHP::name($element,$association),Variable::T_ARRAY,true,tr(PHP::name($element,$association).'\'s ids')); }
		else { return new Variable(PHP::name($element,$association),Variable::convert_type(array_shift($ids)),true,tr(PHP::name($element,$association).'\'s id')); }
	}
	
	/**
	 * Get scalar variable
	 * @param $object Object object
	 * @return Variable scalar variable
	 */
	public static function var_scalar(Object $object) {
		return new Variable($object->getName(),Variable::convert_type($object),true,$object->getDescription());
	}
}

?>