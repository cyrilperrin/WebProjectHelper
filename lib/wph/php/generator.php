<?php

/**
 * PHP Generator
 */
class PHP_Generator {
	
	/** @var Project project */
	private $project;
	
	/** @var MySQL_Generator mysql generator */
	private $mysql;
	
	/**
	 * Constructor
	 * @param Project $project
	 * @param MySQL_Generator $mysql
	 */
	public function __construct(Project $project,MySQL_Generator $mysql) {
		$this->project = $project;
		$this->mysql = $mysql;
	}
	
	/**
	 * Generate requires
	 * @return string requires
	 */
	public function generate_requires() {
		// Init string
		$string = '';
		
		// Require
		$string .= nl('// '.tr('Require class files'));
		
		// Require associations
		$associations = false;
		foreach ($this->project->getObjects() as $object) {
			foreach ($object->getAttrsMult(Attribute::M_SEVERAL) as $attr) {
				if (!($attr->getElem() instanceof Scalar) && !($object->equals($attr->getElem()) && !$attr->isAssociated()) && $attr->getCorrespondence()->isMult(Attribute::M_SEVERAL)) {
					$associations = true;
				}
			}
		}
		if($associations) {
			$string .= nl('require(\''.PHP::file_assoc_classes().'\');');
		}
		
		// Require classes
		foreach ($this->project->getObjects() as $object) {
			$string .= nl('require(\''.PHP::file_class_base($object).'\');');
			$string .= nl('require(\''.PHP::file_class($object).'\');');
		}
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate files
	 * @param $dirname string directory path where to put generated files
	 * @throws Exception when file exists at directory path
	 */
	public function generate_files($dirname='./result/php/') {
		// Check dirname
		if (!file_exists($dirname)) { mkdir($dirname,0755,true); }
		elseif (!is_dir($dirname)) { throw new Exception('File exists at location'); }

		// Create and fill php files
		foreach ($this->project->getObjects() as $object) {
			file_put_contents($dirname.PHP::file_class_base($object),$this->generate_class_base(0,$object));
			if(!PHP::get_generate_only_base_classes()) {
				file_put_contents($dirname.PHP::file_class($object),$this->generate_class(0,$object));
			}
		}
		
		// Generate requires
		/*if(!PHP::get_generate_only_base_classes()) {
			file_put_contents($dirname.PHP::file_requires(),nl('<?php').nl().$this->generate_requires());
		}*/
		
		// Generate association classes
		if(!PHP::get_generate_only_base_classes()) {
			foreach($this->generate_assoc_classes(0) as $fileName => $fileContent) {
				file_put_contents($dirname.$fileName,$fileContent);
			}
		}
	}
	
	/**
	 * Generate a single file for all classes
	 * @param $filename string file path
	 * @throws Exception when file already exists at location
	 */
	public function generate_file($filename) {
		// Check filename
		if (file_exists($filename)) { throw new Exception('File already exists at location'); }
		
		// Puts tables into file
		file_put_contents($filename,nl('<?php').nl().$this->generate_classes().nl());
	}
	
	/**
	 * Generate classes
	 * @return string classes
	 */
	public function generate_classes() {
		// Init string
		$string = '';
		
		// Init classes array
		$classes = array();
		
		// Generate classes
		foreach ($this->project->getObjects() as $object) {
			$classes[] = $this->generate_class_base(0,$object);
			if(!PHP::get_generate_only_base_classes()) {
				$classes[] = $this->generate_class(0,$object);
			}
		}
		
		// Generate association classes
		if(!PHP::get_generate_only_base_classes()) {
			$assoc_classes = implode(nl(),$this->generate_assoc_classes(0));
			if ($assoc_classes != '') {
				$classes[] = $assoc_classes;
			}
		}
		
		// Add classes to string
		$string .= implode(nl(),$classes);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate zip file
	 * @return string zip file content
	 */
	public function generate_zip() {
		// Create zip file
		$zip = new ZipFile();
		
		// Generate classes
		foreach ($this->project->getObjects() as $object) {
			$zip->addfile(nl('<?php').nl().$this->generate_class_base(0,$object).nl(), PHP::file_class_base($object));
			if(!PHP::get_generate_only_base_classes()) {
				$zip->addfile(nl('<?php').nl().$this->generate_class(0,$object).nl(), PHP::file_class($object));
			}
		}
		
		// Generate requires
		/*if(!PHP::get_generate_only_base_classes()) {
			$zip->addfile(nl('<?php').nl().$this->generate_requires(), PHP::file_requires());
		}*/
		
		// Generate association classes
		if(!PHP::get_generate_only_base_classes()) {
			foreach($this->generate_assoc_classes(0) as $fileName => $fileContent) {
				$zip->addfile(nl('<?php').nl().$fileContent, $fileName);
			}
		}
		
		// Return zip file
		return $zip->file(); 
	}
	
	/**
	 * Generate association classes
	 * @param $nb_indents int indent account
	 * @return array association classes
	 */
	public function generate_assoc_classes($nb_indents) {
		// Init strings
		$strings = array();
		
		// Generate association classes
		$objects_done = array();
		foreach ($this->project->getObjects() as $object) {
			$assoc_done = array();
			foreach ($object->getAttrsMult(Attribute::M_SEVERAL) as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$strings[PHP::file_assoc_class($object,null,$attr->getElem(),null)] = $this->generate_assoc_class($nb_indents,$object,null,$attr->getElem(),null);
				}
				elseif ($object->equals($attr->getElem()) && !$attr->isAssociated()) {
					$strings[PHP::file_assoc_class($object,null,$attr->getElem(),$attr->getAssoc())] = $this->generate_assoc_class($nb_indents,$object,null,$attr->getElem(),$attr->getAssoc());
				}
				elseif ($attr->getCorrespondence()->isMult(Attribute::M_SEVERAL)) {
					if (!$object->equals($attr->getElem()) && in_array($attr->getElem()->getId(),$objects_done) || $object->equals($attr->getElem()) &&  in_array($attr->getCorrespondence()->getAssoc()->getId(),$assoc_done)) {
						$strings[PHP::file_assoc_class($object,$attr->getCorrespondence()->getAssoc(),$attr->getElem(),$attr->getAssoc())] = $this->generate_assoc_class($nb_indents,$object,$attr->getCorrespondence()->getAssoc(),$attr->getElem(),$attr->getAssoc());
					}
					if ($attr->getAssoc() != null) { $assoc_done[] = $attr->getAssoc()->getId(); }
				}
			}
			$objects_done[] = $object->getId();
		}
		
		// Return strings
		return $strings;
	}
	
	/**
	 * Generate association class
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string association class
	 */
	public function generate_assoc_class($nb_indents,Object $object, $obj_association, Element $element, $elem_association) {
		// Init string
		$string = '';
		
		// Documentation
		$string .= nl('/**',$nb_indents).
		           nl(' * '.tr('Association class between '.PHP::name($object,$obj_association).' and '.PHP::name($element,$elem_association)),$nb_indents).
		           nl(' * @name '.PHP::name_assoc_class($object, $obj_association, $element, $elem_association),$nb_indents).
		           nl(' * @version '.tr(date('m/d/Y').' (mm/dd/yyyy)'),$nb_indents).
		           nl(' * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)',$nb_indents).
		           nl(' */',$nb_indents);
		
		// Header
		$string .= nl('class '.PHP::name_assoc_class($object, $obj_association, $element, $elem_association),$nb_indents).nl('{',$nb_indents);
		
		// New line
		$string .= nl('',$nb_indents+1);
		
		// Table name
		$string .= nl('// '.tr('Table name'),$nb_indents+1).
		           nl('const '.PHP::const_table_name().' = \''.MySQL::name_assoc_table($object, $obj_association, $element, $elem_association, false).'\';',$nb_indents+1);
		
		// New line
		$string .= nl('',$nb_indents+1);
		
		// Fields name
		$string .= nl('// '.tr('Fields name'),$nb_indents+1);
		foreach(MySQL::ids($object) as $id) {
			$string .= nl('const '.PHP::const_field_foreign($object, $id, $obj_association).' = \''.MySQL::name_field_foreign($id, $obj_association).'\';',$nb_indents+1);
		}
		if($element instanceof Scalar) {
			$string .= nl('const '.PHP::const_field($element).' = \''.MySQL::name_field($element).'\';',$nb_indents+1);
		} else {
			foreach(MySQL::ids($element) as $id) {
				$string .= nl('const '.PHP::const_field_foreign($element, $id, $elem_association).' = \''.MySQL::name_field_foreign($id, $elem_association).'\';',$nb_indents+1);
			}
		}
		
		// New line
		$string .= nl('',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate class
	 * @param $nb_indents int indent account 
	 * @param $object Object object
	 * @return string class
	 */
	public function generate_class($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Documentation
		$documentation = nl('/**',$nb_indents).
						($object->getDescription() != '' ? nl(' * '.$object->getDescription(),$nb_indents) : '').
		                 nl(' * @name '.PHP::name_class($object),$nb_indents).
		                 nl(' * @version '.tr(date('m/d/Y').' (mm/dd/yyyy)'),$nb_indents).
		                 nl(' */',$nb_indents);
		
		// Header
		$string .= $documentation.nl(($object->isAbstract() ? 'abstract ' : '').'class '.PHP::name_class($object).' extends '.PHP::name_class_base($object),$nb_indents).nl('{',$nb_indents);
		
		// New line
		$string .= nl('',$nb_indents+1);
		
		if(!PHP::get_fieldnames_into_base_classes()) {
			// Table
			$string .= nl('// '.tr('Table name'),$nb_indents+1).nl('const '.PHP::const_table_name().' = \''.MySQL::name_table($object,null).'\';',$nb_indents+1).nl('',$nb_indents+1);
			
			// Fieldnames
			$fields = $this->generate_fields($nb_indents+1, $object);
			if ($fields != '') { $string .= $fields.nl('',$nb_indents+1); }
		}
		
		// User code
		$string .= nl('// '.tr('Put your code here').'...',$nb_indents+1);
		
		// New line
		$string .= nl('',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate base class
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string base class
	 */
	public function generate_class_base($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_class_header($nb_indents,$object);
		
		if(PHP::get_fieldnames_into_base_classes()) {
			// Table
			$string .= nl('// '.tr('Table name'),$nb_indents+1).nl('const '.PHP::const_table_name().' = \''.MySQL::name_table($object,null).'\';',$nb_indents+1).nl('',$nb_indents+1);
			
			// Fieldnames
			$fields = $this->generate_fields($nb_indents+1, $object);
			if ($fields != '') { $string .= $fields.nl('',$nb_indents+1); }
		}
		
		// Attributes
		$attributes = $this->generate_attributes($nb_indents+1,$object);
		if ($attributes != '') { $string .= $attributes.nl('',$nb_indents+1); }
		
		// Constructor
		$string .= $this->generate_construct($nb_indents+1,$object).nl('',$nb_indents+1);
		
		// Create
		$string .= $this->generate_create($nb_indents+1,$object).nl('',$nb_indents+1);
		
		// Count
		$string .= $this->generate_count($nb_indents+1,$object).nl('',$nb_indents+1);
		
		// Loaders and fetchers
		if (!$object->isAbstract()) {
			$string .= $this->generate_select($nb_indents+1,$object).nl('',$nb_indents+1);
		}
		$string .= $this->generate_load($nb_indents+1,$object).nl('',$nb_indents+1);
		if (!$object->isAbstract()) {
			$loads = $this->generate_loads_by($nb_indents+1,$object);
			if($loads != '') {
				$string .= $loads.nl('',$nb_indents+1);
			}
			$reload = $this->generate_reload($nb_indents+1,$object);
			if($reload != '') {
				$string .= $reload.nl('',$nb_indents+1);
			}
			$string .= $this->generate_load_all($nb_indents+1,$object).nl('',$nb_indents+1).
			           $this->generate_select_all($nb_indents+1,$object).nl('',$nb_indents+1).
			           $this->generate_fetch($nb_indents+1,$object).nl('',$nb_indents+1).
			           $this->generate_fetch_all($nb_indents+1,$object).nl('',$nb_indents+1);
		}
		
		// Equals
		$string .= $this->generate_equals($nb_indents+1,$object).nl('',$nb_indents+1);
		
		// Exists
		if (!$object->isAbstract()) {
			$string .= $this->generate_exists($nb_indents+1,$object).nl('',$nb_indents+1);
		}
		
		// Delete
		$string .= $this->generate_delete($nb_indents+1,$object).nl('',$nb_indents+1);
		
		// Update
		$update = $this->generate_update($nb_indents+1,$object);
		if ($update != '') {
			$string .= $this->generate_set($nb_indents+1,$object).nl('',$nb_indents+1).
			           $update.nl('',$nb_indents+1);
		}
		
		// Accessors
		$string .= $this->generate_accessors($nb_indents+1,$object);
		
		// ToString
		$string .= nl('',$nb_indents+1).$this->generate_to_string($nb_indents+1,$object);
		
		// Serialize and unserialize
		$string .= $this->generate_serialize($nb_indents+1,$object).nl('',$nb_indents+1);
		
		// Iterator implementation
		if ($object->getListOf() != null) {
			$string .= nl('',$nb_indents+1).$this->generate_iterator_implementation($nb_indents+1,$object->getListOf()->getElem(),$object->getListOf()->getAssoc());
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate class header
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string class header
	 */
	public function generate_class_header($nb_indents,Object $object) {
		// Documentation
		$documentation = nl('/**',$nb_indents).
		                 nl(' * @name '.PHP::name_class_base($object),$nb_indents).
		                 nl(' * @version '.tr(date('m/d/Y').' (mm/dd/yyyy)'),$nb_indents).
		                 nl(' * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)',$nb_indents).
		                 nl(' */',$nb_indents);
		                 
		// Init header
		$header = '';
		
		// Class name
		$header .= 'abstract class '.PHP::name_class_base($object);
		
		// Parent ?
		if ($object->getParent() != null) { $header .= ' extends '.PHP::name_class($object->getParent()); }
		
		// List of ?
		if ($object->getListOf() != null) { $header .= ' implements Iterator'; }
		
		// Return documentation and header
		return $documentation.nl($header,$nb_indents).nl('{',$nb_indents);
	}
	
	/**
	 * Generate fields
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string fields
	 */
	public function generate_fields($nb_indents,Object $object) {
		// Init arrays
		$ids = array();
		$attributes = array();
		
		// Fields
		if($object->getParent() != null) {
			foreach (MySQL::ids($object->getParent()) as $id) {
				$const_name = PHP::const_field_parent($id);
				$ids[] = nl('const '.$const_name.' = \''.MySQL::name_field_parent($id).'\';',$nb_indents);
			}
		}
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			// Scalar/Object ?
			if($attr->getElem() instanceof Scalar) {
				// Add field
				$const_name = PHP::const_field($attr->getElem());
				$field = nl('const '.$const_name.' = \''.MySQL::name_field($attr->getElem()).'\';',$nb_indents);
				if($attr->isId()) { $ids[] = $field; } else { $attributes[] = $field; }
			} else {
				// Add fields
				foreach (MySQL::ids($attr->getElem()) as $id) {
					$const_name = PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc());
					$field = nl('const '.$const_name.' = \''.MySQL::name_field_foreign($id,$attr->getAssoc()).'\';',$nb_indents);
					if($attr->isId()) { $ids[] = $field; } else { $attributes[] = $field; }
				}
			}
		}
		
		// Return fields
		return $ids || $attributes ? nl('// '.tr('Fields name'),$nb_indents).implode($ids).implode($attributes) : '';
	}
	
	/**
	 * Generate attributes
	 * @param $nb_indents indent account
	 * @param $object Object object
	 * @return string attributes
	 */
	public function generate_attributes($nb_indents,Object $object) {
		// Init arrays
		$ids = array();
		$attrs = array();
		$usage = array();
		
		// Pdo
		if ($object->getParent() == null) {
			$usage[] = $this->generate_attribute($nb_indents,Variable::var_pdo());
		}
		
		// Lazy load
		if (!$object->isAbstract()) {
			$usage[] = $this->generate_attribute($nb_indents,Variable::var_lazy_load(),null,true,true);
		}
		
		// Iterator implementation
		if ($object->getListOf() != null) {
			if ($object->getListOf()->getElem() instanceof Scalar || !$object->getListOf()->getElem()->isAbstract()) {
				$usage[] = $this->generate_attribute($nb_indents,Variable::var_iterator_statement($object->getListOf()->getElem(),$object->getListOf()->getAssoc()));
				$usage[] = $this->generate_attribute($nb_indents,Variable::var_iterator_current($object->getListOf()->getElem(),$object->getListOf()->getAssoc()));
			} else {
				$usage[] = $this->generate_attribute($nb_indents,Variable::var_iterator_array($object->getListOf()->getElem(),$object->getListOf()->getAssoc()));
			}
			
			if ($object->getListOf()->getElem() instanceof Scalar) {
				$usage[] = $this->generate_attribute($nb_indents,Variable::var_iterator_key($object->getListOf()->getElem(),$object->getListOf()->getAssoc()));
			}
		}
		
		// Attributes
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			$line = $this->generate_attribute($nb_indents,$attr->getElem(),$attr->getAssoc());
			
			if ($attr->isId()) {
				$ids[] = $line;
			} else { $attrs[] = $line; }
		}
		
		// Return attributes
		return implode(nl('',$nb_indents),array_merge($usage,$ids,$attrs));
	}
	
	/**
	 * Generate attribute line
	 * @param $nb_indents indent account
	 * @param $element Element element
	 * @param $association Association
	 * @param $protected bool protected attribute ?
	 * @param $static bool static attribute ?
	 * @return string attribute line
	 */
	public function generate_attribute($nb_indents,Element $element,$association=null,$protected=true,$static=false) {
		// Convert objects
		if ($element instanceof Object) { $element = Variable::var_attribute($element,$association); }
		
		// Return attribute
		return nl('/** @var '.PHP::type($element).' '.strtolower($element->getDescription()).' */',$nb_indents).
		       nl(($protected?'protected':'private').' '.($static?'static ':'').PHP::name_attribute($element,$association).';',$nb_indents);
	}
	
	/**
	 * Generate construct method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string construct method
	 */
	public function generate_construct($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Params
		list($ids,$attrs,$opts,$ids_parent,$attrs_parent,$opts_parent) = PHP::to_attrs(PHP::ids_attrs_opts($object,false,true,true));

		// Pdo and lazyload
		$pdo = Variable::var_pdo();
		$lazy_load = Variable::var_lazy_load();
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Parameters
		$params = array_merge(array($pdo),$ids_parent,$ids,$attrs_parent,$attrs,$opts_parent,$opts);
		if(!$object->isAbstract()) {
			$params[] = $lazy_load_enable;
		}
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'protected',false,'__construct',tr('Construct a '.lcfirst($object->getName())),$params,
		                                         PHP::defaults_values(array_merge($opts_parent,$opts,array($lazy_load_enable)),1+count($ids_parent)+count($ids)+count($attrs_parent)+count($attrs)));                       
		
		if ($object->getParent() != null) {
			// Call parent constructor
			$string .= nl('// '.tr('Call parent constructor'),$nb_indents+1).
			           nl('parent::__construct('.implode(',',array_map('PHP::name_parameter',array_merge(array($pdo),$ids_parent,$attrs_parent,$opts_parent))).');',$nb_indents+1);
		} else {
			// Save pdo
			$string .= nl('// '.tr('Save pdo'),$nb_indents+1).
			           nl(PHP::attribute($pdo).' = '.PHP::name_parameter($pdo).';',$nb_indents+1);
		}
		
		// Save attributes
		if (count(array_merge($ids,$attrs,$opts))) {
			$string .= nl('',$nb_indents+1).nl('// '.tr('Save attributes'),$nb_indents+1);
			foreach (array_merge($ids,$attrs,$opts) as $attr) {
				$string .= nl(PHP::attribute($attr).' = '.PHP::name_parameter($attr).';',$nb_indents+1);
			}
		}
		
		// Lazy load
		if(!$object->isAbstract()) {
			// Key for lazy load
			$elems = array();
			foreach ($object->getAncestor()->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar || count($keys = PHP::keys($attr->getElem())) == 1) {
					$elems[] = PHP::name_parameter($attr->getElem(),$attr->getAssoc());
				} else {
					foreach ($keys as $key) {
						$elems[] = PHP::name_parameter($attr->getElem(),$attr->getAssoc()).'[\''.$key.'\']';
					}
				}
			}
			$key_lazyload = implode('.\'-\'.',$elems);
			
			// Save for lazy load
			$string .= nl('',$nb_indents+1).nl('// '.tr('Save for lazy load'),$nb_indents+1).
			           nl('if ('.PHP::name_parameter($lazy_load_enable).') {',$nb_indents+1).
			           nl('self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.'] = $this;',$nb_indents+2).
			           nl('}',$nb_indents+1);
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate create method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string create method
	 */
	public function generate_create($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Ids, attrs and opts
		list($ids,$attrs,$opts,$ids_parent,$attrs_parent,$opts_parent) = PHP::to_vars(PHP::ids_attrs_opts($object,false,false));

		// Pdo and statement
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Params and defaults
		$params = array_merge(array($pdo),$ids_parent,$ids,$attrs_parent,$attrs,$opts_parent,$opts);
		$defaults = array_merge($opts_parent,$opts);
		
		// Add lazy load if necessary
		if (!$object->isAbstract()) {
			$params[] = $lazy_load_enable;
			$defaults[] = $lazy_load_enable;
		}
		
		// Header
		$string .= $this->generate_method_header($nb_indents,$object->isAbstract()?'protected':'public',true,PHP::method_create(),tr('Create a '.lcfirst($object->getName())),
		                                         $params,PHP::defaults_values($defaults,1+count(array_merge($ids_parent,$ids,$attrs_parent,$attrs))),$object);
		                                         
		// Create parent
		if ($object->getParent() != null) {
			$ais = $object->getParent()->getAttrsScalar(Scalar::T_INT_AI,true);
			if (count($ais)) {
				$ai = array_shift($ais);
				$return = PHP::name_variable(Variable::convert($ai->getElem(),$ai->getAssoc())).' = ';
			} else { $return = ''; }
			
			$string .= nl('// '.tr('Create parent'),$nb_indents+1).
			           nl($return.'parent::create('.implode(',',array_map('PHP::name_parameter',array_merge(array($pdo),$ids_parent,$attrs_parent,$opts_parent))).');',$nb_indents+1).
			           nl('',$nb_indents+1);
		}
		
		// Ids, attrs and opts
		list($ids,$attrs,$opts) = PHP::ids_attrs_opts($object,false,false);
		
		// Params
		$params_execute = array();
		if ($object->getParent() != null) {
			foreach ($object->getAncestor()->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar && $attr->getElem()->getType() == Scalar::T_INT_AI) {
					$params_execute[] = PHP::name_variable($attr->getElem(),$attr->getAssoc());
				} else {
					PHP::create_add_param_execute($params_execute,$attr);
				}
			}
		}
		foreach (array_merge($ids,$attrs,$opts) as $key => $attr) {
			PHP::create_add_param_execute($params_execute,$attr);
		}
		
		// Prepare and execute query
		$string .= nl('// '.tr('Add the '.lcfirst($object->getName()).' into database'),$nb_indents+1).
		           nl(PHP::name_variable($statement).' = '.PHP::name_parameter($pdo).'->prepare('.Query::insert($object).');',$nb_indents+1).
		           nl('if (!'.PHP::name_variable($statement).'->execute(array('.implode(',',$params_execute).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while inserting a '.lcfirst($object->getName()).' into database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1);
		
		if (!$object->isAbstract()) {
			// Ids, attrs and opts
			list($ids,$attrs,$opts,$ids_parent,$attrs_parent,$opts_parent) = PHP::ids_attrs_opts($object,false,true);
			
			// Params
			$params_new = array();
			foreach ($ids_parent as $attr) { PHP::create_add_param_new($params_new,$attr,true,$pdo); }
			foreach ($ids as $attr) { PHP::create_add_param_new($params_new,$attr,false,$pdo); }
			foreach ($attrs_parent as $attr) { PHP::create_add_param_new($params_new,$attr,true,$pdo); }
			foreach ($attrs as $attr) { PHP::create_add_param_new($params_new,$attr,false,$pdo); }
			foreach ($opts_parent as $attr) { PHP::create_add_param_new($params_new,$attr,true,$pdo); }
			foreach ($opts as $attr) { PHP::create_add_param_new($params_new,$attr,false,$pdo); }
					
			// Create and return new object
			$string .= nl('',$nb_indents+1).
			           nl('// '.tr('Construct the '.lcfirst($object->getName())),$nb_indents+1).
			           nl('return new '.($object->getChildren() ? PHP::name_class($object) : PHP::name_class($object)).'('.implode(',',array_merge(array(PHP::name_parameter($pdo)),$params_new)).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1);
		} else {
			// Return auto increment
			$ais = $object->getAttrsScalar(Scalar::T_INT_AI);
			if (count($ais) != 0) {
				$attr = array_shift($ais);
				$string .= nl('',$nb_indents+1).
			               nl('// '.tr('Return '.lcfirst(PHP::name($attr->getElem(),$attr->getAssoc()))),$nb_indents+1).
			               nl('return intval('.PHP::name_parameter($pdo).'->lastInsertId());',$nb_indents+1);
			}
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate count method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string count method
	 */
	public function generate_count($nb_indents,Object $object) {
		// Init string
		$string = '';

		// Pdo and statement
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_count(),tr('Count '.lcfirst(svrl($object->getName()))),
		                                         array($pdo),array(),Variable::var_count($object));
		
		// Execute and return query
		$string .= nl('if (!('.PHP::name_variable($statement).' = '.PHP::name_parameter($pdo).'->query('.Query::count($object).'))) {',$nb_indents+1).
				   nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while counting '.lcfirst(svrl($object->getName())).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.PHP::name_variable($statement).'->fetchColumn();',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate load method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string load method
	 */
	public function generate_load($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Params
		list($ids,$attrs,$opts,$ids_parent,$attrs_parent,$opts_parent) = PHP::to_vars(PHP::ids_attrs_opts($object));

		// Pdo, statement and lazy load
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$lazy_load = Variable::var_lazy_load();
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_load(),tr('Load a '.lcfirst($object->getName())),
		                                         array_merge(array($pdo),$ids_parent,$ids,$object->isAbstract()?array():array($lazy_load_enable)),array(1+count($ids_parent)+count($ids) => $lazy_load_enable->getDefault()),$object);
		
		if ($object->isAbstract()) {
			$variable = PHP::name_variable($object);
			$params = array_map('PHP::name_parameter',array_merge(array($pdo),$ids_parent,$ids));
			foreach ($object->getLeafs() as $leaf) {
				$string .= nl('if ('.$variable.' = '.PHP::name_class($leaf).'::load('.implode(',',$params).')) { return '.$variable.'; }',$nb_indents+1);
			}
			$string .= nl('return null;',$nb_indents+1);
		} else {
			// Params
			$params = array();
			foreach ($object->getAncestor()->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$params[] = PHP::name_parameter($attr->getElem(),$attr->getAssoc());
				} else {
					$params = array_merge($params,PHP::paths_ids($attr->getElem(),PHP::name_parameter($attr->getElem(),$attr->getAssoc())));
				}
			}
			
			// Key for lazy load
			$key_lazyload = implode('.\'-\'.',$params);
			
			// Already loaded ?
			$string .= nl('// '.tr('Already loaded ?'),$nb_indents+1).
			           nl('if ('.PHP::name_parameter($lazy_load_enable).' && isset(self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.'])) {',$nb_indents+1).
			           nl('return self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.'];',$nb_indents+2).
			           nl('}',$nb_indents+1).
			           nl('',$nb_indents+1);
			
			// Prepare and execute query
			$string .= nl('// '.tr('Load the '.lcfirst($object->getName())),$nb_indents+1).
			           nl(PHP::name_variable($statement).' = self::'.PHP::method_select().'('.PHP::name_parameter($pdo).','.Query::clean('\''.implode(' AND ',Query::wheres($object)).'\'').');',$nb_indents+1).
			           nl('if (!'.PHP::name_variable($statement).'->execute(array('.implode(',',$params).'))) {',$nb_indents+1).
			           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while loading a '.lcfirst($object->getName()).' from database')).'\');',$nb_indents+2).
			           nl('}',$nb_indents+1).
			           nl('',$nb_indents+1);
			           
			// Fetch from result set
			$string .= nl('// '.tr('Fetch the '.lcfirst($object->getName()).' from result set'),$nb_indents+1).
			           nl('return self::'.PHP::method_fetch().'('.PHP::name_parameter($pdo).','.PHP::name_variable($statement).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1);
		}
		                                         
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate load by methods
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string load by methods
	 */
	public function generate_loads_by($nb_indents,Object $object) {		
		// Pdo, statement and lazy load
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Init methods array
		$methods = array();
		
		// Generate load by methods
		foreach($object->getAttrs() as $attr) {
			if($attr->hasOption(Attribute::OPT_UNIQUE)) {
				// Unique
				$unique = Variable::convert($attr->getElem());
		
				// Init method string
				$string = '';
				
				// Header
				$string .= PHP_Generator::generate_method_header($nb_indents,'public',true,PHP::method_load_by($attr->getElem()),tr('Load a '.lcfirst($object->getName()).' by its '.lcfirst($attr->getElem()->getName())),
				                                                 array($pdo,$unique,$lazy_load_enable),array(2 => $lazy_load_enable->getDefault()),$object);
				
				// Wheres
				$wheres = array();
				if($attr->getElem() instanceof Scalar) {
					$wheres[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\' = ?';
				} else {
					foreach(MySQL::ids($attr->getElem()) as $id) {
						$wheres[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(), $id, $attr->getAssoc()).'.\' = ?';
					}
				}
				
				// Prepare and execute query
				$string .= nl('// '.tr('Load the '.lcfirst($object->getName())),$nb_indents+1).
				           nl(PHP::name_variable($statement).' = self::'.PHP::method_select().'('.PHP::name_parameter($pdo).','.Query::clean('\''.implode(' AND ',$wheres).'\'').');',$nb_indents+1).
				           nl('if (!'.PHP::name_variable($statement).'->execute(array('.($attr->getElem() instanceof Scalar ? PHP::name_parameter($unique) : implode(',',PHP::paths_ids($attr->getElem(),PHP::name_parameter($unique),false,true))).'))) {',$nb_indents+1).
				           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while loading a '.lcfirst($object->getName()).' by its '.lcfirst($attr->getElem()->getName()).' from database')).'\');',$nb_indents+2).
				           nl('}',$nb_indents+1).
				           nl('',$nb_indents+1);
				           
				// Fetch from result set
				$string .= nl('// '.tr('Fetch the '.lcfirst($object->getName()).' from result set'),$nb_indents+1).
				           nl('return self::'.PHP::method_fetch().'('.PHP::name_parameter($pdo).','.PHP::name_variable($statement).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1);
		          
				// End
				$string .= nl('}',$nb_indents);
				
				// Add method
				$methods[] = $string;
			}
		}
		
		// Return string
		return implode(nl('',$nb_indents),$methods);
	}
	
	/**
	 * Generate load all method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string load all method
	 */
	public function generate_load_all($nb_indents,Object $object) {
		// Init string
		$string = '';

		// Pdo, statement, array and lazy load
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$array = Variable::var_array($object);
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_load_all(),tr('Load all '.lcfirst(svrl($object->getName()))),
		                                         array($pdo,$lazy_load_enable),array(1 => $lazy_load_enable->getDefault()),$array);

		// Select all
		$string .= nl('// '.tr('Select all '.lcfirst(svrl($object->getName()))),$nb_indents+1).
		           nl(PHP::name_variable($statement).' = self::'.PHP::method_select_all().'('.PHP::name_parameter($pdo).');',$nb_indents+1).
		           nl('',$nb_indents+1);
		           
		// Fetch all
		$string .= nl('// '.tr('Fetch all the '.lcfirst(svrl($object->getName()))),$nb_indents+1).
		           nl(PHP::name_variable($array).' = self::'.PHP::method_fetch_all().'('.PHP::name_parameter($pdo).','.PHP::name_variable($statement).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1).
		           nl('',$nb_indents+1);
		           
		// Return array
		$string .= nl('// '.tr('Return array'),$nb_indents+1).
		           nl('return '.PHP::name_variable($array).';',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate select method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string select method
	 */
	public function generate_select($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Variables
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$where = new Variable('where','string|array',true);
		$orderby = new Variable('orderby','string|array',true);
		$limit = new Variable('limit','string|array',true);
		$from = new Variable('from','string|array',true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'protected',true,PHP::method_select(),tr('Select query'),
		                                         array($pdo,$where,$orderby,$limit,$from),array(1 => null, 2 => null, 3 => null, 4 => null),$statement);
		
		// Ids, attrs and opts
		list($ids,$attrs,$opts) = Query::fields($object, true);
		
		// Family
		$family = $object->getFamily(true);
			
		// Tables
		$tables = array();
		foreach ($family as $member) { $tables[] = '\'.'.PHP::name_class($member).'::'.PHP::const_table_name().'.\''; }
		
		// Joins
		$joins = Query::joins($object);
		
		// Query
		$query_select = '\'SELECT DISTINCT '.implode(', ',array_merge($ids,$attrs,$opts)).' \'.';
		$query_from = '\'FROM '.substr(implode(', ',$tables),0,-2).'.('.PHP::name_parameter($from).' != null ? \', \'.(is_array('.PHP::name_parameter($from).') ? implode(\', \','.PHP::name_parameter($from).') : '.PHP::name_parameter($from).') : \'\').';
		if (count($joins)) { $query_where = '\' WHERE '.implode(' AND ',$joins).'\'.('.PHP::name_parameter($where).' != null ? \' AND (\'.'.PHP::name_parameter($where).'.\')\' : \'\').'; }
		else { $query_where = '('.PHP::name_parameter($where).' != null ? \' WHERE \'.(is_array('.PHP::name_parameter($where).') ? implode(\' AND \','.PHP::name_parameter($where).') : '.PHP::name_parameter($where).') : \'\').'; }
		$query_orderby = '('.PHP::name_parameter($orderby).' != null ? \' ORDER BY \'.(is_array('.PHP::name_parameter($orderby).') ? implode(\', \','.PHP::name_parameter($orderby).') : '.PHP::name_parameter($orderby).') : \'\').';
		$query_limit = '('.PHP::name_parameter($limit).' != null ? \' LIMIT \'.(is_array('.PHP::name_parameter($limit).') ? implode(\', \', '.PHP::name_parameter($limit).') : '.PHP::name_parameter($limit).') : \'\')';
		                                         
		// Prepare, execute and return query
		$string .= nlb('return '.PHP::name_parameter($pdo).'->prepare(',$query_select,$query_from,$query_where,$query_orderby,$query_limit.');',$nb_indents+1);
		                                         
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate select all method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string select all method
	 */
	public function generate_select_all($nb_indents,Object $object) {
		// Init string
		$string = '';

		// Pdo and statement
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_select_all(),tr('Select all '.lcfirst(svrl($object->getName()))),
		                                         array($pdo),array(),$statement);
		
		// Prepare, execute and return query
		$string .= nl(PHP::name_variable($statement).' = self::'.PHP::method_select().'('.PHP::name_parameter($pdo).');',$nb_indents+1).
		           nl('if (!'.PHP::name_variable($statement).'->execute()) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while loading all '.lcfirst(svrl($object->getName())).' from database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.PHP::name_variable($statement).';',$nb_indents+1);
		                                         
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate fetch method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string fetch method
	 */
	public function generate_fetch($nb_indents,Object $object) {
		// Init string
		$string = '';

		// Pdo, statement and lazy load
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$lazy_load = Variable::var_lazy_load();
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_fetch(),tr('Fetch the next '.lcfirst($object->getName()).' from a result set'),
		                                         array($pdo,$statement,$lazy_load_enable),array(2 => $lazy_load_enable->getDefault()),$object);
		                                         
		// Ids, attrs and opts
		list($ids,$attrs,$opts) = PHP::ids_attrs_opts($object,true);
		                                         
		// Extract and construct values
		$extr_values = array();
		$cons_values = array(PHP::name_parameter($pdo));
		$keys_lazyload = array();
		foreach (array_merge($ids,$attrs,$opts) as $attr) {
			if ($attr->getElem() instanceof Scalar || count(MySQL::ids($attr->getElem())) == 1) {
				$extr_value = PHP::name_variable($attr->getElem(),$attr->getAssoc());
				$extr_values[] = $extr_value;
				if ($attr->getElem() instanceof Scalar && $attr->getElem()->isType(Scalar::T_DATE|Scalar::T_DATETIME)) {
					if($attr->isMult(Attribute::M_OPT)) {
						$cons_value = $extr_value.' === null ? null : strtotime('.$extr_value.')';
					} else {
						$cons_value = 'strtotime('.$extr_value.')';
					}
				} else if($attr->getElem() instanceof Scalar && $attr->getElem()->isType(Scalar::T_INT|Scalar::T_INT_AI)) {
					if($attr->isMult(Attribute::M_OPT)) {
						$cons_value = $extr_value.' === null ? null : intval('.$extr_value.')';
					} else {
						$cons_value = 'intval('.$extr_value.')';
					}
				}  else if($attr->getElem() instanceof Scalar && $attr->getElem()->isType(Scalar::T_FLOAT)) {
					if($attr->isMult(Attribute::M_OPT)) {
						$cons_value = $extr_value.' === null ? null : floatval('.$extr_value.')';
					} else {
						$cons_value = 'floatval('.$extr_value.')';
					}
				} else if($attr->getElem() instanceof Scalar && $attr->getElem()->isType(Scalar::T_BOOL)) {
					if($attr->isMult(Attribute::M_OPT)) {
						$cons_value = $extr_value.' === null ? null : boolval('.$extr_value.')';
					} else {
						$cons_value = 'boolval('.$extr_value.')';
					}
				} else {
					$cons_value = $extr_value;
				}
				$cons_values[] = $cons_value;
				if($attr->isId()) {
					$keys_lazyload[] = $cons_value;
				}
			} else {
				$attr_ids = array();
				foreach (PHP::keys($attr->getElem()) as $key) {
					$extr_value = '$'.PHP::key($attr->getElem(),$attr->getAssoc()).'_'.str_replace('-','_',$key);
					$extr_values[] = $extr_value;
					$attr_ids[] = '\''.$key.'\' => '.$extr_value;
					if($attr->isId()) {
						$keys_lazyload[] = $extr_value;
					}
				}
				$cons_values[] = 'array('.implode(', ',$attr_ids).')';
			}
		}
		
		// Extract values
		$string .= nl('// '.tr('Extract values'),$nb_indents+1).
		           nl('$values = '.PHP::name_parameter($statement).'->fetch(PDO::FETCH_NUM);',$nb_indents+1).
		           nl('if (!$values) { return null; }',$nb_indents+1).
		           nl('list('.implode(',',$extr_values).') = $values;',$nb_indents+1).
		           nl('',$nb_indents+1);
		
		// Key for lazy load
		$key_lazyload = implode('.\'-\'.',$keys_lazyload);
		           
		// Construct and return object
		$string .= nl('// '.tr('Construct the '.lcfirst($object->getName())),$nb_indents+1).
		           nlb('return ',PHP::name_parameter($lazy_load_enable).' && isset(self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.']) ? self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.'] :',
		                         'new '.($object->getChildren() ? PHP::name_class($object) : PHP::name_class($object)).'('.implode(',',$cons_values).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate fetch all method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string fetch all method
	 */
	public function generate_fetch_all($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Pdo, statement and lazy load
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$array = Variable::var_array($object);
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_fetch_all(),tr('Fetch all the '.lcfirst(svrl($object->getName())).' from a result set'),
		                                         array($pdo,$statement,$lazy_load_enable),array(2 => $lazy_load_enable->getDefault()),$array);
		
		// Fetch all
		$string .= nl(PHP::name_variable($array).' = array();',$nb_indents+1).
		           nl('while ('.PHP::name_variable($object).' = self::'.PHP::method_fetch().'('.PHP::name_parameter($pdo).','.PHP::name_variable($statement).','.PHP::name_parameter($lazy_load_enable).')) {',$nb_indents+1).
		           nl(PHP::name_variable($array).'[] = '.PHP::name_variable($object).';',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.PHP::name_variable($array).';',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate reload method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string reload method
	 */
	public function generate_reload($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Pdo and statement
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_reload(),tr('Reload data from database'),array(),array());
		
		// Params
		$params = array();
		foreach ($object->getAncestor()->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$params[] = PHP::attribute($attr->getElem(),$attr->getAssoc());
			} else {
				$params = array_merge($params,PHP::paths_ids($attr->getElem(),PHP::attribute($attr->getElem(),$attr->getAssoc())));
			}
		}
			
		// Prepare and execute query
		$string .= nl('// '.tr('Reload data'),$nb_indents+1).
		           nl(PHP::name_variable($statement).' = self::'.PHP::method_select().'('.PHP::attribute($pdo).','.Query::clean('\''.implode(' AND ',Query::wheres($object)).'\'').');',$nb_indents+1).
		           nl('if (!'.PHP::name_variable($statement).'->execute(array('.implode(',',$params).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while reloading data of a '.lcfirst($object->getName()).' from database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('',$nb_indents+1);
	
		// Ids, attrs and opts
		list($ids,$attrs,$opts) = PHP::ids_attrs_opts($object,true);
		                                         
		// Extract and construct values
		$extr_values = array();
		$save_values = array();
		foreach (array_merge($ids,$attrs,$opts) as $attr) {
			if ($attr->getElem() instanceof Scalar || count(MySQL::ids($attr->getElem())) == 1) {
				$extr_value = PHP::name_variable($attr->getElem(),$attr->getAssoc());
				$extr_values[] = $extr_value;
				if ($attr->getElem() instanceof Scalar && $attr->getElem()->isType(Scalar::T_DATE|Scalar::T_DATETIME)) {
					$save_value = $extr_value.' === null ? null : strtotime('.$extr_value.')';
					
				} else {
					$save_value = $extr_value;
				}
			} else {
				$attr_ids = array();
				foreach (PHP::keys($attr->getElem()) as $key) {
					$extr_value = '$'.PHP::key($attr->getElem(),$attr->getAssoc()).'_'.str_replace('-','_',$key);
					$extr_values[] = $extr_value;
					$attr_ids[] = '\''.$key.'\' => '.$extr_value;
				}
				$save_value[] = 'array('.implode(', ',$attr_ids).')';
			}
			if(!$attr->isId()) {
				$save_values[] = PHP::attribute($attr->getElem(),$attr->getAssoc()).' = '.$save_value.';';
			}
		}
		
		// Return empty values if there is no values to save
		if(count($save_values) == 0) {
			return '';
		}
		
		// Extract values
		$string .= nl('// '.tr('Extract values'),$nb_indents+1).
		           nl('$values = '.PHP::name_variable($statement).'->fetch(PDO::FETCH_NUM);',$nb_indents+1).
		           nl('if (!$values) { return null; }',$nb_indents+1).
		           nl('list('.implode(',',$extr_values).') = $values;',$nb_indents+1).
		           nl('',$nb_indents+1);
		
		// Save values
		$string .= nl('// '.tr('Save values'),$nb_indents+1);
		foreach($save_values as $save_value) {
			$string .= nl($save_value,$nb_indents+1);
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate exists method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string exists method
	 */
	public function generate_exists($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_exists(),tr('Check if the '.lcfirst($object->getName()).' exists in database'),
		                                         array(),array(),Variable::var_exists($object));
		
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
	
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::exists($object).');',$nb_indents+1).
		           nl('if (!'.$statement.'->execute(array('.implode(',',PHP::paths_ids($object)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while checking that a '.lcfirst($object->getName()).' exists in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->fetchColumn() == 1;',$nb_indents+1);
		
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate delete method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string delete method
	 */
	public function generate_delete($nb_indents,Object $object) {
		// Init string
		$string = '';

		// Pdo, statement and lazy load
		$pdo = Variable::var_pdo();
		$statement = Variable::var_pdo_statement();
		$lazy_load = Variable::var_lazy_load();
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_delete(),tr('Delete '.lcfirst($object->getName())),
		                                         array(),array(),Variable::var_success());
		
		// Associated objects
		foreach ($object->getAttrs() as $attr) {
			if ($attr->getElem() instanceof Object) {
				$correspondence = $attr->getCorrespondence();
				if ($correspondence != null) {
				 	if ($correspondence->isMult(Attribute::M_ONE|Attribute::M_OPT)) {
						if ($attr->isMult(Attribute::M_SEVERAL)) {
							$element = PHP::name_variable(Variable::convert($attr->getElem(),$attr->getAssoc()));
							if (!$attr->getElem()->isAbstract()) {
								$select = PHP::name_variable(Variable::var_pdo_statement('select'));
								$string .= nl('// '.tr('Delete associated '.svrl(PHP::name($attr->getElem(),$attr->getAssoc()))),$nb_indents+1).
								           nl($select.' = $this->'.PHP::method_lister($attr->getElem(),$attr->getAssoc()).'();',$nb_indents+1).
								           nl('while ('.$element.' = '.PHP::name_class($attr->getElem()).'::'.PHP::method_fetch().'('.PHP::attribute($pdo).','.$select.')) {',$nb_indents+1);
								if ($correspondence->isMult(Attribute::M_ONE)) {
									$string .= nl($element.'->delete();',$nb_indents+2);
								} else {
									$string .= nl($element.'->'.PHP::method_setter($object,$correspondence->getAssoc()).'(null);',$nb_indents+2);
								}
								$string .= nl('}',$nb_indents+1).
								           nl('',$nb_indents+1);
							} else {
								$string .= nl('// '.tr('Delete associated '.svrl(PHP::name($attr->getElem(),$attr->getAssoc()))),$nb_indents+1).
								           nl('foreach ($this->'.PHP::method_loader($attr->getElem(),$attr->getAssoc()).'() as '.$element.') {',$nb_indents+1);
								if ($correspondence->isMult(Attribute::M_ONE)) {
									$string .= nl($element.'->delete();',$nb_indents+2);
								} else {
									$string .= nl($element.'->'.PHP::method_setter($object,$correspondence->getAssoc()).'(null);',$nb_indents+2);
								}
								$string .= nl('}',$nb_indents+1).
								           nl('',$nb_indents+1);
							}
						} else {
							$element = PHP::name_variable($attr->getElem(),$attr->getAssoc());
							$string .= nl('// '.tr('Delete associated '.PHP::name($attr->getElem(),$attr->getAssoc())),$nb_indents+1);
							if ($correspondence->isMult(Attribute::M_ONE)) {
								$string .= nl($element.' = $this->'.PHP::method_getter($attr->getElem(),$attr->getAssoc()).'();',$nb_indents+1).
										   nl('if('.$element.' != null) {',$nb_indents+1).
										   nl($element.'->delete();',$nb_indents+2).
										   nl('}',$nb_indents+1);
							} else {
								$string .= nl($element.' = $this->'.PHP::method_getter($attr->getElem(),$attr->getAssoc()).'();',$nb_indents+1).
								           nl('if('.$element.' != null) {',$nb_indents+1).
								           nl($element.'->'.PHP::method_setter($object,$correspondence->getAssoc()).'(null);',$nb_indents+2).
								           nl('}',$nb_indents+1);
							}
							$string .= nl('',$nb_indents+1);
						}
					} elseif ($correspondence->isMult(Attribute::M_SEVERAL) && $attr->isMult(Attribute::M_SEVERAL)) {
						$string .= nl('// '.tr('Delete associated '.svrl(PHP::name($attr->getElem(),$attr->getAssoc()))),$nb_indents+1).
						   nl('$this->'.PHP::method_remove_all($attr->getElem(),$attr->getAssoc()).'();',$nb_indents+1).
				           nl('',$nb_indents+1);
					}
				}
			} elseif ($attr->isMult(Attribute::M_SEVERAL)) {
				$string .= nl('// '.tr('Delete associated '.svrl(PHP::name($attr->getElem(),$attr->getAssoc()))),$nb_indents+1).
						   nl('$this->'.PHP::method_remove_all($attr->getElem(),$attr->getAssoc()).'();',$nb_indents+1).
				           nl('',$nb_indents+1);
			}
		}
		
		// Delete object
		$string .= nl('// '.tr('Delete '.lcfirst($object->getName())),$nb_indents+1).
		           nl(PHP::name_variable($statement).' = '.PHP::attribute($pdo).'->prepare('.Query::delete($object).');',$nb_indents+1).
		           nl('if (!'.PHP::name_variable($statement).'->execute(array('.implode(',',PHP::paths_ids($object,'$this',false,true)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while deleting a '.lcfirst($object->getName()).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1);
		
		// Lazy load
		if(!$object->isAbstract()) {
			// Key for lazy load
			$elems = array();
			foreach ($object->getAncestor()->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar || count($keys = PHP::keys($attr->getElem())) == 1) {
					$elems[] = PHP::attribute($attr->getElem(),$attr->getAssoc());
				} else {
					foreach ($keys as $key) {
						$elems[] = PHP::attribute($attr->getElem(),$attr->getAssoc()).'[\''.$key.'\']';
					}
				}
			}
			$key_lazyload = implode('.\'-\'.',$elems);
			
			// Remove from lazy load array
			$string .= nl('',$nb_indents+1).nl('// '.tr('Remove from lazy load array'),$nb_indents+1).
			nl('if (isset(self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.'])) {',$nb_indents+1).
			nl('unset(self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.']);',$nb_indents+2).
			nl('}',$nb_indents+1);
		}
		
		// Delete parent/Successful operation ?
		if ($object->getParent() != null) {
			$string .= nl('if ('.PHP::name_variable($statement).'->rowCount() != 1) { return false; }',$nb_indents+1).
			           nl('',$nb_indents+1).
			           nl('// '.tr('Delete parent'),$nb_indents+1).
			           nl('return parent::delete();',$nb_indents+1); 
		} else {
			$string .= nl('',$nb_indents+1).
			           nl('// '.tr('Successful operation ?'),$nb_indents+1).
			           nl('return '.PHP::name_variable($statement).'->rowCount() == 1;',$nb_indents+1);
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate equals method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string equals method
	 */
	public function generate_equals($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_equals(),tr('Equality test'),
		                                         array(Variable::var_scalar($object)),array(),Variable::var_equals());
		                                         
		// Parameter
		$param = PHP::name_parameter($object);
		
		// null ?
		$string .= nl('// '.tr('Test if null'),$nb_indents+1).
		           nl('if ('.$param.' == null) { return false; }',$nb_indents+1).
		           nl('',$nb_indents+1);
		           
		// Same class ?
		$string .= nl('// '.tr('Test class'),$nb_indents+1).
		           nl('if (!('.$param.' instanceof '.PHP::name_class($object).')) { return false; }',$nb_indents+1).
		           nl('',$nb_indents+1);
		           
		if ($object->getParent() != null) {
			// Parent
			$string .= nl('// Test parent',$nb_indents+1).
			           nl('return parent::equals();',$nb_indents+1);
		} else {           
			// Tests
			$tests = array();
			foreach ($object->getAttrsIds() as $attr) {
				$tests[] = PHP::attribute($attr->getElem(),$attr->getAssoc()).' == '.PHP::attribute($attr->getElem(),$attr->getAssoc(),$param);
			}
			
			// Ids
			$string .= nl('// '.tr('Test ids'),$nb_indents+1);
			$string .= nl('return '.implode(' && ',$tests).';',$nb_indents+1);
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate to string method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string to string method
	 */
	public function generate_to_string($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,'__toString',tr('ToString'),
		                                         array(),array(),Variable::var_tostring($object));
		                                         
		// Parent ?
		$parent = $object->getParent() != null ? 'parent::__toString().\'<-\'.' : '' ;
		
		// Attrs
		$attrs_1 = array(); $attrs_2 = array();
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			$name_1 = lcfirst(PHP::name($attr->getElem(),$attr->getAssoc()));
			$name_2 = PHP::attribute($attr->getElem(),$attr->getAssoc());
			if ($attr->getElem() instanceof Scalar) {
				if ($attr->getElem()->getType() == Scalar::T_DATE) {
					$temp = $name_1.'="\'.date(\''.tr('m/d/Y').'\','.$name_2.').\'"';
				} elseif ($attr->getElem()->getType() == Scalar::T_DATETIME) {
					$temp = $name_1.'="\'.date(\''.tr('m/d/Y').' H:i:s\','.$name_2.').\'"';
				} elseif ($attr->getElem()->getType() == Scalar::T_BOOL) {
					$temp = $name_1.'="\'.('.$name_2.'?\'true\':\'false\').\'"';
				} else {
					$temp = $name_1.'="\'.'.$name_2.'.\'"';
				}
			} else {
				if (count(MySQL::ids($attr->getElem())) == 1) { $temp = $name_1.'="\'.'.$name_2.'.\'"'; }
				else {
					$keys = array();
					foreach (PHP::keys($attr->getElem()) as $key) { $keys[] = $key.' : \'.'.$name_2.'[\''.$key.'\'].\''; }
					$temp = $name_1.'="'.implode(', ',$keys).'"';
				}
			}
			if ($attr->isId()) {
				$attrs_1[] = $temp;
			} else { $attrs_2[] = $temp; }
		}
		$attrs = array_merge($attrs_1,$attrs_2);
		
		// Tostring
		$tostring = '\'['.PHP::name_class($object).(count($attrs) ? ' '.implode(' ',$attrs) : '').']\'';
		
		// Return string
		$string .= nl('return '.$parent.$tostring.';',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate serialize and unserialize methods
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string serialize and unserialize methods
	 */
	public function generate_serialize($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Ids, attrs and opts
		list($ids,$attrs,$opts,$ids_parent,$attrs_parent,$opts_parent) = PHP::ids_attrs_opts($object);
		
		// Values for serialize
		$values = array();
		foreach(array_merge($ids,$attrs,$opts) as $attr) {
			$values[] = '\''.PHP::key($attr->getElem(),$attr->getAssoc()).'\' => '.PHP::attribute($attr->getElem(),$attr->getAssoc());
		}
		
		// Serialization
		$serialization = Variable::var_serialize($object);
		$serialization_enable = Variable::var_serialize_enable(true);
		
		// Serialize header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_serialize(),tr('Serialize'),
		                                         array($serialization_enable),array($serialization_enable->getDefault()),$serialization);
		
		// Serialize object
		$string .= nl('// '.tr('Serialize the '.lcfirst($object->getName())),$nb_indents+1);
		if($object->getParent() != null) {
			$string .= nl('$array = array_merge(parent::serialize(false),array('.implode(',',$values).'));',$nb_indents+1);
		} else {
			$string .= nl('$array = array('.implode(',',$values).');',$nb_indents+1);
		}
		$string .= nl('',$nb_indents+1);
		
		// Return serialized object
		$string .= nl('// '.tr('Return the serialized (or not) '.lcfirst($object->getName())),$nb_indents+1).
		           nl('return '.PHP::name_parameter($serialization_enable).' ? serialize($array) : $array;',$nb_indents+1);
		                                         
		// Serialize end
		$string .= nl('}',$nb_indents);
		
		// Unserialize
		if(!$object->isAbstract()) {
		
			// New line
			$string .= nl('',$nb_indents);
			
			// Values for unserialize
			$values = array();
			foreach(array_merge($ids_parent,$ids,$attrs_parent,$attrs,$opts_parent,$opts) as $attr) {
				$values[] = '$array[\''.PHP::key($attr->getElem(),$attr->getAssoc()).'\']';
			}
			
			// Pdo and lazyload
			$pdo = Variable::var_pdo();
			$lazy_load = Variable::var_lazy_load();
			$lazy_load_enable = Variable::var_lazy_load_enable(true);
			
			// Key for lazy load
			$keys_lazyload = array();
			foreach (array_merge($ids_parent,$ids) as $attr) {
				if ($attr->getElem() instanceof Scalar || count(MySQL::ids($attr->getElem())) == 1) {
					$keys_lazyload[] = '$array[\''.PHP::key($attr->getElem(),$attr->getAssoc()).'\']';
				} else {
					$keys_lazyload[] = 'implode(\'-\',$array[\''.PHP::key($attr->getElem(),$attr->getAssoc()).'\'])';
				}
			}
			$key_lazyload = implode('.\'-\'.',$keys_lazyload);
					
			// Unserialize header
			$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_unserialize(),tr('Unserialize'),
			                                         array($pdo,$serialization,$lazy_load_enable),array(2 => $lazy_load_enable->getDefault()),$object);
			                                         
			// Unserialize string
			$string .= nl('// '.tr('Unserialize string'),$nb_indents+1).
			           nl('$array = unserialize('.PHP::name_parameter($serialization).');',$nb_indents+1).
			           nl('',$nb_indents+1);
			                                         
			// Construct object
			$string .= nl('// '.tr('Construct the '.lcfirst($object->getName())),$nb_indents+1).
			           nlb('return ',PHP::name_parameter($lazy_load_enable).' && isset(self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.']) ? self::'.PHP::name_attribute($lazy_load).'['.$key_lazyload.'] :',
			                         'new '.($object->getChildren() ? PHP::name_class($object) : PHP::name_class($object)).'('.PHP::name_parameter($pdo).','.implode(',',$values).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1);
			                                         
			// Unserialize end
			$string .= nl('}',$nb_indents);
		}
		                                         
		// Return string
		return $string;
	}
	
	/**
	 * Generate accessors methods
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string accessors methods
	 */
	public function generate_accessors($nb_indents,Object $object) {
		// Init arrays
		$ids = array();
		$attrs = array();
		
		// Attributes
		foreach ($object->getAttrs() as $attr) {
			// Correspondence
			$correspondence_assoc = $attr->getElem() instanceof Scalar || $attr->getCorrespondence() == null ? null : $attr->getCorrespondence()->getAssoc();
			
			// Check if it is an identifier
			if ($attr->isId()) {
				// Generate only getter
				$ids[] = $this->generate_getter($nb_indents,$attr->getElem(),$attr->getAssoc());
			} else {
				// Check how many elements can be owned 
				if ($attr->isMult(Attribute::M_ONE|Attribute::M_OPT)) {
					// Generate getter and setter
					$attrs[] = $this->generate_getter($nb_indents,$attr->getElem(),$attr->getAssoc(),$attr->isMult(Attribute::M_OPT));
					if($attr->getElem() instanceof Object) {
						$attrs[] = $this->generate_getter_id($nb_indents,$attr->getElem(),$attr->getAssoc(),$attr->isMult(Attribute::M_OPT));
					}
					$attrs[] = $this->generate_setter($nb_indents,$object,$attr->getElem(),$attr->getAssoc(),$attr->isMult(Attribute::M_OPT));
					if($attr->getElem() instanceof Object) {
						$attrs[] = $this->generate_setter_by_id($nb_indents,$object,$attr->getElem(),$attr->getAssoc(),$attr->isMult(Attribute::M_OPT));
					}
				} else {
					// Generate adder and remover
					if ($attr->getElem() instanceof Scalar || $attr->getCorrespondence() == null || $attr->getCorrespondence()->isMult(Attribute::M_SEVERAL)) {
						$attrs[] = $this->generate_adder($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
						$attrs[] = $this->generate_adder_list_of($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
						if($attr->getElem() instanceof Object) {
							$attrs[] = $this->generate_adder_by_id($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
						}
			       		$attrs[] = $this->generate_remover($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
			       		$attrs[] = $this->generate_remover_list_of($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
			       		if($attr->getElem() instanceof Object) {
			       			$attrs[] = $this->generate_remover_by_id($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
			       		}
			       		$attrs[] = $this->generate_remove_all($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
					}
					
					// Generate lister or loader
					if ($attr->getElem() instanceof Scalar || !$attr->getElem()->isAbstract()) {
						$attrs[] = $this->generate_lister($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
					} else {
						$attrs[] = $this->generate_loader($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc());
					}
				}
			}
			
			// Generate lister by
			if (!$object->isAbstract() && $attr->getCorrespondence() != null && $attr->getCorrespondence()->isMult(Attribute::M_SEVERAL)) {
				$attrs[] = $this->generate_lister_by($nb_indents,$object,$correspondence_assoc,$attr->getElem(),$attr->getAssoc(),$attr->isMult(Attribute::M_SEVERAL));
			}
			
			// Check if it is a reflexive association without correspondence
			if ($attr->getElem()->equals($attr->getOwner()) && !$attr->isAssociated()) {
				if ($attr->isMult(Attribute::M_SEVERAL)) {
					$attrs[] = $this->generate_adder($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
					$attrs[] = $this->generate_adder_list_of($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
					$attrs[] = $this->generate_adder_by_id($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
					$attrs[] = $this->generate_remover($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
					$attrs[] = $this->generate_remover_list_of($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
					$attrs[] = $this->generate_remover_by_id($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
					$attrs[] = $this->generate_remove_all($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null);
				}
				if (!$attr->getElem()->isAbstract()) {
					$attrs[] = $this->generate_lister($nb_indents,$attr->getElem(),$attr->getAssoc(),$object,null,$attr->isMult(Attribute::M_SEVERAL));
				} else {
					// TODO Loader
				}
			}
		}
		
		// Generate listers by from ancestors
		if (!$object->isAbstract() && $object->getParent() != null) {
			foreach ($object->getAncestors() as $member) {
				foreach ($member->getAttrs() as $attr) {
					if ($attr->getCorrespondence() != null && $attr->getCorrespondence()->isMult(Attribute::M_SEVERAL)) {
						$attrs[] = $this->generate_lister_by($nb_indents,$object,$attr->getCorrespondence()->getAssoc(),$attr->getElem(),$attr->getAssoc(),$attr->isMult(Attribute::M_SEVERAL),$member);
					}
				}
			}
		}
		
		// Return methods
		return implode(nl('',$nb_indents),array_merge($ids,$attrs));
	}
	
	/**
	 * Generate getter method
	 * @param $nb_indents int indent account
	 * @param $element Element element
	 * @param $association Association association
	 * @param $null bool element can be null ?
	 * @return string getter method
	 */
	public function generate_getter($nb_indents,Element $element,$association=null,$null=false) {
		// Init string
		$string = '';
		
		// Lazy load
		$lazy_load_enable = Variable::var_lazy_load_enable(true);
		
		// Parameters
		$params = $element instanceof Scalar ? array() : array($lazy_load_enable);
		$defaults = $element instanceof Scalar ? array() : array($lazy_load_enable->getDefault());
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_getter($element,$association),tr('Get the '.lcfirst(PHP::name($element,$association))),$params,$defaults,$element);
		
		// Scalar/object ?
		if ($element instanceof Scalar) { $string .= nl('return '.PHP::attribute($element).';',$nb_indents+1); }
		else {
			// Return null if necessary
			if ($null) {
				$string .= nl('// '.tr('Return null if necessary'),$nb_indents+1).
				           nl('if ('.PHP::attribute($element,$association).' === null) { return null; }',$nb_indents+1).
				           nl('',$nb_indents+1).
				           nl('// '.tr('Load and return '.lcfirst($element->getName())),$nb_indents+1);
			}
			
			// Params
			$params = array();
			if (count($keys = PHP::keys($element)) > 1) {
				foreach ($keys as $key) {
					$params[] = PHP::attribute($element,$association).'[\''.$key.'\']';
				}
			} else { $params[] = PHP::attribute($element,$association); }
			
			// Load and return object
			$string .= nl('return '.PHP::name_class($element).'::'.PHP::method_load().'('.PHP::attribute(Variable::var_pdo()).','.implode(',',$params).','.PHP::name_parameter($lazy_load_enable).');',$nb_indents+1);
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate getter id method
	 * @param $nb_indents int indent account
	 * @param $element Object element
	 * @param $association Association association
	 * @param $null bool element can be null ?
	 * @return string getter method
	 */
	public function generate_getter_id($nb_indents,Object $element,$association=null,$null=false) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_getter_id($element,$association),tr('Get the '.lcfirst(PHP::name($element,$association)).'\'s id'),array(),array(),Variable::var_attribute($element,$association));
			
		// Return object's id
		$string .= nl('return '.PHP::attribute($element,$association).';',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate set method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string set method
	 */
	public function generate_set($nb_indents,Object $object) {
		// Init string
		$string = '';
		
		// Variables
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		$fields = new Variable('fields',Variable::T_ARRAY,true);
		$values = new Variable('values',Variable::T_ARRAY,true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'protected',false,PHP::method_set(),tr('Update a field in database'),array($fields,$values),array(),Variable::var_success());
		
		// Prepare update
		$string .= nl('// '.tr('Prepare update'),$nb_indents+1).
		           nl('$updates = array();',$nb_indents+1).
		           nl('foreach ('.PHP::name_parameter($fields).' as $field) {',$nb_indents+1).
		           nl('$updates[] = $field.\' = ?\';',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('',$nb_indents+1);
		           
			           
		// Update field
		$string .= nl('// '.tr('Update field'),$nb_indents+1).
		           nl($statement.' = '.$pdo.'->prepare(\'UPDATE \'.'.PHP::name_class($object).'::'.PHP::const_table_name().'.\' SET \'.implode(\', \', $updates).\' WHERE '.implode(' AND ',Query::wheres($object)).'\');',$nb_indents+1).
		           nl('if (!'.$statement.'->execute(array_merge('.PHP::name_parameter($values).',array('.implode(',',PHP::paths_ids($object,'$this',false,true,false)).')))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while updating a '.lcfirst($object->getName()).'\\\'s field in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('',$nb_indents+1);
		           
		// Successful operation ?
		$string .= nl('// '.tr('Successful operation ?'),$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == 1;',$nb_indents+1);  
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate setter method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $element Element element
	 * @param $association Association association
	 * @param $null bool element can be null ?
	 * @return string setter method
	 */
	public function generate_setter($nb_indents,Object $object,Element $element,$association=null,$null=false) {
		// Init string
		$string = '';
		
		// Execute update query ?
		$execute = Variable::var_execute_update_query();
		
		// Defaults
		$defaults = array();
		if ($element instanceof Scalar && $element->getDefault() != null) { $defaults[0] = $element->getDefault(); }
		elseif ($null) { $defaults[0] = null; }
		$defaults[1] = $execute->getDefault();
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_setter($element,$association),tr('Set the '.lcfirst(PHP::name($element,$association))),array(Variable::convert($element,$association),$execute),$defaults,Variable::var_success());
		
		// Parameter name
		$parameter = PHP::name_parameter($element,$association);
		
		// Scalar/object ?
		if ($element instanceof Scalar) {			
			// Save into object
			$string .= nl('// '.tr('Save into object'),$nb_indents+1).
			           nl(PHP::attribute($element).' = '.$parameter.';',$nb_indents+1).
			           nl('',$nb_indents+1);
			
			// Field
			$fields = array(PHP::name_class($object).'::'.PHP::const_field($element));
			
			// Value
			$values = array(PHP::value($element,$parameter,$null));
		}
		else {
			// Test null
			$test_null = $null ? $parameter.' == null ? null : ' : '';
		
			// Ids
			$paths = PHP::paths_ids($element,$parameter);
			if (count($paths) > 1) {
				$ids = array();
				foreach ($paths as $key => $path) {
					$ids[] = '\''.$key.'\' => '.$path;
				}
				$value = 'array('.implode(',',$ids).')';
			} else {
				$value = array_shift($paths);
			}
			
			// Save into object
			$string .= nl('// '.tr('Save into object'),$nb_indents+1).
			           nl(PHP::attribute($element,$association).' = '.$test_null.$value.';',$nb_indents+1).
			           nl('',$nb_indents+1);
			           
			// Fields
			$fields = array();
			foreach (MySQL::ids($element) as $id) {
				$fields[] = PHP::name_class($object).'::'.PHP::const_field_foreign($element,$id,$association);
			}
			
			// Values
			$values = PHP::paths_ids($element,$parameter,$null,true,false);
		}
			           
		// Save into database
		$string .= nl('// '.tr('Save into database (or not)'),$nb_indents+1).
		           nl('return '.PHP::name_parameter($execute).' ? '.PHP::name_class($object).'::'.PHP::method_set().'(array('.implode(',',$fields).'),array('.implode(',',$values).')) : true;',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate setter by id method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $element object element
	 * @param $association Association association
	 * @param $null bool element can be null ?
	 * @return string setter by id method
	 */
	public function generate_setter_by_id($nb_indents,Object $object,Object $element,$association=null,$null=false) {
		// Init string
		$string = '';
		
		// Execute update query ?
		$execute = Variable::var_execute_update_query();
		
		// Ids
		list($ids) = PHP::ids_attrs_opts($element,true);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_setter_by_id($element,$association),tr('Set the '.lcfirst(PHP::name($element,$association)).' by id'),array_merge(PHP::to_vars($ids),array($execute)),array(count($ids) => true),Variable::var_success());

		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		
		// Values
		$values = array();
		foreach($ids as $id) {
			if($id->getElem() instanceof Scalar) {
				$values[PHP::key($id->getElem())] = PHP::name_parameter($id->getElem());
			} else {
				foreach(PHP::paths_ids($id->getElem(),PHP::name_parameter($id->getElem(),$id->getAssoc())) as $key => $path) {
					$values[PHP::key($id->getElem(),$id->getAssoc()).'-'.$key] = $path;
				}
			}
		}
		
		// Save into object
		$string .= nl('// '.tr('Save into object'),$nb_indents+1).
		           nl(
		           		PHP::attribute($element,$association).' = '.
		           			($null ? implode(' && ',array_map(function($value) { return $value.' === null'; },$values)).' ? null : ' : '').
		           			(count($values) > 1 ? 'array('.implode(',',array_map(function($key,$value) { return '\''.$key.'\' => '.$value; },array_keys($values),array_values($values))).')' : array_shift($values)).';',
		           		$nb_indents+1
				   ).
		           nl('',$nb_indents+1);
		           
		// Fields
		$fields = array();
		foreach (MySQL::ids($element) as $id) {
			$fields[] = PHP::name_class($object).'::'.PHP::const_field_foreign($element,$id,$association);
		}
		
		// Values
		$values = array();
		foreach($ids as $id) {
			if($id->getElem() instanceof Scalar) {
				$values[PHP::key($id->getElem())] = PHP::name_parameter($id->getElem());
			} else {
				foreach(PHP::paths_ids($id->getElem(),PHP::name_parameter($id->getElem(),$id->getAssoc()),$null,true) as $key => $path) {
					$values[PHP::key($id->getElem(),$id->getAssoc()).'-'.$key] = $path;
				}
			}
		}
		
		// Save into database
		$string .= nl('// '.tr('Save into database (or not)'),$nb_indents+1).
		           nl('return '.PHP::name_parameter($execute).' ? '.PHP::name_class($object).'::'.PHP::method_set().'(array('.implode(',',$fields).'),array('.implode(',',$values).')) : true;',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate update method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @return string update method
	 */
	public function generate_update($nb_indents,Object $object) {		
		// Init string
		$string = '';
		
		// Pdo and statement
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_update(),tr('Update all fields in database'),array(),array(),Variable::var_success());
		
		// Update parent if necessary
		if($object->getParent() != null) {
			$string .= nl('// '.tr('Update parent'),$nb_indents+1).
			           nl('parent::update();',$nb_indents+1).
			           nl('',$nb_indents+1);
		}
		
		// Fields
		$fields = array();
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			if (!$attr->isId()) {
				if ($attr->getElem() instanceof Scalar) {
					$fields[] = PHP::name_class($object).'::'.PHP::const_field($attr->getElem());
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$fields[] = PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc());
					}
				}
			}
		}
		
		// Values
		$values = array();
		foreach($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			if(!$attr->isId()) {
				if ($attr->getElem() instanceof Scalar) {
					// Add value
					$values[] = PHP::value($attr->getElem(),PHP::attribute($attr->getElem()),$attr->isMult(Attribute::M_OPT));
				} else {
					// Test null
					$test_null = $attr->isMult(Attribute::M_OPT) ? PHP::attribute($attr->getElem(),$attr->getAssoc()).' == null ? null : ' : '';
				
					// Add element's id(s)
					if (count($keys = PHP::keys($attr->getElem())) > 1) {
						foreach ($keys as $key) {
							$values[] = $test_null.PHP::attribute($attr->getElem(),$attr->getAssoc()).'[\''.$key.'\']';
						}
					} else {
						$values[] = PHP::attribute($attr->getElem(),$attr->getAssoc());
					}
				}
			}
		}
		
		// Check if values are present
		if(!count($values)) { return ''; }
		
		// Prepare and execute query
		if($object->getParent() != null) {
			$string .= nl('// '.tr('Update all fields in database'),$nb_indents+1);
		}
		$string .= nl('return $this->'.PHP::method_set().'(array('.implode(',',$fields).'),array('.implode(',',$values).'));',$nb_indents+1);  
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate adder method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string adder method
	 */
	public function generate_adder($nb_indents,Object $object,$obj_association,Element $element,$elem_association) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_adder($element,$elem_association),tr('Add a '.lcfirst(PHP::name($element,$elem_association))),array(Variable::convert($element,$elem_association)),array(),Variable::var_success());
		
		// Pdo and statement
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::adder($object,$obj_association,$element,$elem_association).');',$nb_indents+1);
		
		// Parameter name
		$parameter = PHP::name_parameter($element,$elem_association);

		// Values
		if ($element instanceof Scalar) { $values = array(PHP::value($element,$parameter)); }
		else { $values = PHP::paths_ids($element,$parameter,false,true,false); }
		
		// Execute query
		$string .= nl('if (!'.$statement.'->execute(array('.implode(',',array_merge(PHP::paths_ids($object,'$this',false,true,false),$values)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while adding a '.lcfirst($object->getName()).'\\\'s '.lcfirst(PHP::name($element,$elem_association)).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == 1;',$nb_indents+1);   
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate adder list of method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string adder list of method
	 */
	public function generate_adder_list_of($nb_indents,Object $object,$obj_association,Element $element,$elem_association) {
		// Init string
		$string = '';
		
		// List of elements
		$list = Variable::var_array($element,null,$elem_association);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_adder_list_of($element,$elem_association),tr('Add a list of '.lcfirst(svrl(PHP::name($element,$elem_association)))),array($list),array(),Variable::var_success());
		
		// Pdo and statement
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::adder_list_of($object,$obj_association,$element,$elem_association,'count('.PHP::name_parameter($list).')').');',$nb_indents+1);

		// Values
		$string .= nl('$values = array();',$nb_indents+1).
		           nl('foreach('.PHP::name_parameter($list).' as '.PHP::name_variable($element,$elem_association).') {',$nb_indents+1);
		foreach(PHP::paths_ids($object,'$this',false,true) as $path_id) {
			$string .= nl('$values[] = '.$path_id.';',$nb_indents+2);
		}
		if ($element instanceof Scalar) {
			$string .= nl('$values[] = '.PHP::name_variable($element).';',$nb_indents+2);
		} else {
			foreach(PHP::paths_ids($element,PHP::name_variable($element,$elem_association),false,true) as $path_id) {
				$string .= nl('$values[] = '.$path_id.';',$nb_indents+2);
			}
		}
		$string .= nl('}',$nb_indents+1);
		
		// Execute query
		$string .= nl('if (!'.$statement.'->execute($values)) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while adding a list of '.lcfirst($object->getName()).'\\\'s '.lcfirst(svrl(PHP::name($element,$elem_association))).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == count('.PHP::name_parameter($list).');',$nb_indents+1);   
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate adder by id method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Object element
	 * @param $elem_association Association element association from object
	 * @return string adder by id method
	 */
	public function generate_adder_by_id($nb_indents,Object $object,$obj_association,Object $element,$elem_association) {
		// Init string
		$string = '';
	
		// Ids
		list($ids) = PHP::ids_attrs_opts($element,true);
	
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_adder_by_id($element,$elem_association),tr('Add a '.lcfirst(PHP::name($element,$elem_association)).' by id'),PHP::to_vars($ids),array(),Variable::var_success());
	
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
	
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::adder($object,$obj_association,$element,$elem_association).');',$nb_indents+1);
		
		// Values
		$values = array();
		foreach($ids as $id) {
			if($id->getElem() instanceof Scalar) {
				$values[] = PHP::name_parameter($id->getElem());
			} else {
				$values = array_merge($values,PHP::paths_ids($id->getElem(),PHP::name_parameter($id->getElem(),$id->getAssoc()),false,true));
			}
		}
	
		// Execute query
		$string .= nl('if (!'.$statement.'->execute(array('.implode(',',array_merge(PHP::paths_ids($object,'$this',false,true,false),$values)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while adding a '.lcfirst($object->getName()).'\\\'s '.lcfirst(PHP::name($element,$elem_association)).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == 1;',$nb_indents+1);   
	
		// End
		$string .= nl('}',$nb_indents);
	
		// Return string
		return $string;
	}
	
	/**
	 * Generate remover method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string remover method
	 */
	public function generate_remover($nb_indents,Object $object,$obj_association,Element $element,$elem_association) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_remover($element,$elem_association),tr('Remove a '.lcfirst(PHP::name($element,$elem_association))),array(Variable::convert($element,$elem_association)),array(),Variable::var_success());
		
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::remover($object,$obj_association,$element,$elem_association).');',$nb_indents+1);
		
		// Parameter name
		$parameter = PHP::name_parameter($element,$elem_association);

		// Values
		if ($element instanceof Scalar) { $values = array($parameter); }
		else { $values = PHP::paths_ids($element,$parameter,false,false,false); }
		
		// Execute query
		$string .= nl('if (!'.$statement.'->execute(array('.implode(',',array_merge(PHP::paths_ids($object,'$this',false,true,false),$values)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while deleting a '.lcfirst($object->getName()).'\\\'s '.lcfirst(PHP::name($element,$elem_association)).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == 1;',$nb_indents+1);   
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate remover list of method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string remover list of method
	 */
	public function generate_remover_list_of($nb_indents,Object $object,$obj_association,Element $element,$elem_association) {
		// Init string
		$string = '';
		
		// List of elements
		$list = Variable::var_array($element,null,$elem_association);
	
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_remover_list_of($element,$elem_association),tr('Remove a list of '.lcfirst(svrl(PHP::name($element,$elem_association)))),array(Variable::convert($list)),array(),Variable::var_success());
	
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
	
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::remover_list_of($object,$obj_association,$element,$elem_association,'count('.PHP::name_parameter($list).')').');',$nb_indents+1);
	
		// Values
		$string .= nl('$values = array();',$nb_indents+1).
		           nl('foreach('.PHP::name_parameter($list).' as '.PHP::name_variable($element,$elem_association).') {',$nb_indents+1);
		foreach(PHP::paths_ids($object,'$this',false,true) as $path_id) {
			$string .= nl('$values[] = '.$path_id.';',$nb_indents+2);
		}
		if ($element instanceof Scalar) {
			$string .= nl('$values[] = '.PHP::name_variable($element).';',$nb_indents+2);
		} else {
			foreach(PHP::paths_ids($element,PHP::name_variable($element,$elem_association),false,true) as $path_id) {
				$string .= nl('$values[] = '.$path_id.';',$nb_indents+2);
			}
		}
		$string .= nl('}',$nb_indents+1);
	
		// Execute query
		$string .= nl('if (!'.$statement.'->execute($values)) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while deleting a list of '.lcfirst($object->getName()).'\\\'s '.lcfirst(svrl(PHP::name($element,$elem_association))).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == count('.PHP::name_parameter($list).');',$nb_indents+1);
	
		// End
		$string .= nl('}',$nb_indents);
	
		// Return string
		return $string;
	}
		
	/**
	 * Generate remover by id method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Object element
	 * @param $elem_association Association element association from object
	 * @return string remover by id method
	 */
	public function generate_remover_by_id($nb_indents,Object $object,$obj_association,Object $element,$elem_association) {
		// Init string
		$string = '';

		// Ids
		list($ids) = PHP::ids_attrs_opts($element,true);
	
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_remover_by_id($element,$elem_association),tr('Remove a '.lcfirst(PHP::name($element,$elem_association)).' by id'),PHP::to_vars($ids),array(),Variable::var_success());
	
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
	
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::remover($object,$obj_association,$element,$elem_association).');',$nb_indents+1);
	
		// Values
		$values = array();
		foreach($ids as $id) {
			if($id->getElem() instanceof Scalar) {
				$values[] = PHP::name_parameter($id->getElem());
			} else {
				$values = array_merge($values,PHP::paths_ids($id->getElem(),PHP::name_parameter($id->getElem(),$id->getAssoc()),false,true));
			}
		}
	
		// Execute query
		$string .= nl('if (!'.$statement.'->execute(array('.implode(',',array_merge(PHP::paths_ids($object,'$this',false,true,false),$values)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while deleting a '.lcfirst($object->getName()).'\\\'s '.lcfirst(PHP::name($element,$elem_association)).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount() == 1;',$nb_indents+1);
	
		// End
		$string .= nl('}',$nb_indents);
	
		// Return string
		return $string;
	}
	
	/**
	 * Generate remove all method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string remove all method
	 */
	public function generate_remove_all($nb_indents,Object $object,$obj_association,Element $element,$elem_association) {
		// Init string
		$string = '';
	
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_remove_all($element,$elem_association),tr('Remove all '.svrl(lcfirst(PHP::name($element,$elem_association)))),array(),array(),Variable::var_affected_rows());
	
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
	
		// Prepare query
		$string .= nl($statement.' = '.$pdo.'->prepare('.Query::removeAll($object,$obj_association,$element,$elem_association).');',$nb_indents+1);
	
		// Parameter name
		$parameter = PHP::name_parameter($element,$elem_association);
	
		// Execute query
		$string .= nl('if (!'.$statement.'->execute(array('.implode(',',PHP::paths_ids($object,'$this',false,true,false)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while deleting all '.lcfirst($object->getName()).'\\\'s '.svrl(lcfirst(PHP::name($element,$elem_association))).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.'->rowCount();',$nb_indents+1);
	
		// End
		$string .= nl('}',$nb_indents);
	
		// Return string
		return $string;
	}
	
	/**
	* Generate lister method
	* @param $nb_indents int indent account
	* @param $object Object object
	* @param $obj_association Association object association from element
	* @param $element Element element
	* @param $elem_association Association element association from object
	* @param $reciprocal bool reciprocal association ?
	* @return string lister method
	*/
	public function generate_lister($nb_indents,Object $object,$obj_association,Element $element,$elem_association,$reciprocal=true) {
		// Init string
		$string = '';
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_lister($element,$elem_association),tr('Select '.svrl(lcfirst(PHP::name($element,$elem_association)))),array(),array(),Variable::var_pdo_statement());
	
		// Pdo
		$pdo = PHP::attribute(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		
		// Object/Scalar ?
		if ($element instanceof Scalar) {			
			// Prepare, execute and return query
			$string .= nl($statement.' = '.$pdo.'->prepare('.Query::lister($object,null,$element,null).');',$nb_indents+1).
			           nl('if (!'.$statement.'->execute(array('.implode(',',PHP::paths_ids($object,'$this',false,true)).'))) {',$nb_indents+1).
			           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while selecting '.lcfirst($object->getName()).'\\\'s '.lcfirst(svrl($element->getName())).' in database')).'\');',$nb_indents+2).
			           nl('}',$nb_indents+1).
			           nl('return '.$statement.';',$nb_indents+1);
		} else if($object->equals($element) && $elem_association == null) {
			// Tables
			$tables = array();
			if($reciprocal) {
				$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
				$assoc_table = $assoc_class.'::'.PHP::const_table_name();
				$tables[] = $assoc_table;
			}
			
			// Wheres
			$wheres = array();
			if(!$reciprocal) {
				foreach (MySQL::ids($object) as $id) {
					$wheres[] = '\'.'.PHP::name_class($element).'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
				}
			} else {
				foreach (MySQL::ids($object) as $id) {
					$wheres[] = '\'.'.$assoc_table.'.\'.\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
				}
				foreach (MySQL::ids($element) as $id) {
					$field = $element->getParent() == null ? PHP::const_field($id) : PHP::const_field_parent($id);
					$wheres[] = '\'.'.$assoc_table.'.\'.\'.'.$assoc_class.'::'.PHP::const_field_foreign($element,$id,$elem_association).'.\' = \'.'.PHP::name_class( $element).'::'.PHP::const_table_name().'.\'.\'.'.PHP::name_class($element).'::'.$field.'.\'';
				}
			}
			
			// Params
			$params = array();
			foreach ($object->getAncestor()->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$params[] = PHP::attribute($attr->getElem(),$attr->getAssoc());
				} else {
					$params = array_merge($params,PHP::paths_ids($attr->getElem(),PHP::attribute($attr->getElem(),$attr->getAssoc())));
				}
			}
				
			// Prepare, execute and return query
			$string .= nl($statement.' = self::'.PHP::method_select().'('.$pdo.','.Query::clean('\''.implode(' AND ',$wheres).'\'').($tables ? ',null,null,array('.implode(',',$tables).')' : '').');',$nb_indents+1).
			           nl('if (!'.$statement.'->execute(array('.implode(',',$params).'))) {',$nb_indents+1).
			           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while selecting '.lcfirst($object->getName()).'\\\'s '.lcfirst(svrl(PHP::name($element,$elem_association))).' in database')).'\');',$nb_indents+2).
			           nl('}',$nb_indents+1).
			           nl('return '.$statement.';',$nb_indents+1);
		} else {
			// Return list
			$string .= nl('return '.PHP::name_class($element).'::'.PHP::method_lister_by($object,$obj_association).'('.$pdo.',$this);',$nb_indents+1);
		}
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	* Generate loader method
	* @param $nb_indents int indent account
	* @param $object Object object
	* @param $obj_association Association object association from element
	* @param $element Object element
	* @param $elem_association Association element association from object
	* @return string loader method
	*/
	public function generate_loader($nb_indents,Object $object,$obj_association,Object $element,$elem_association) {
		// Init string
		$string = '';
		
		// Array
		$array = Variable::var_array($element,null,$elem_association);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',false,PHP::method_loader($element,$elem_association),tr('Load '.svrl(lcfirst(PHP::name($element,$elem_association)))),array(),array(),$array);
		
		// Init array of objects
		$string .= nl('// '.tr('Init array'),$nb_indents+1).
		           nl(PHP::name_variable($array).' = array();',$nb_indents+1).
		           nl('',$nb_indents+1);

		// Add objects from each leads
		$pdo = PHP::attribute(Variable::var_pdo());
		foreach ($element->getLeafs() as $leaf) {
			$variable = PHP::name_variable($leaf);
			$select = PHP::name_variable(Variable::var_pdo_statement('select'));
			$string .= nl('// '.tr('Add '.lcfirst(svrl($leaf->getName())).' to array'),$nb_indents+1).
			           nl($select.' = '.PHP::name_class($leaf).'::'.PHP::method_lister_by($object,$obj_association).'('.$pdo.',$this);',$nb_indents+1).
			           nl('while ('.$variable.' = '.PHP::name_class($leaf).'::'.PHP::method_fetch().'('.$pdo.','.$select.')) {',$nb_indents+1).
			           nl(PHP::name_variable($array).'[] = '.$variable.';',$nb_indents+2).
			           nl('}',$nb_indents+1).
			           nl('',$nb_indents+1);
		}
		           
		// Return array
		$string .= nl('// '.tr('Return array'),$nb_indents+1).
		           nl('return '.PHP::name_variable($array).';',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Get lister by method
	 * @param $nb_indents int indent account
	 * @param $object Object object
	 * @param $obj_association Association association
	 * @param $by Object objet
	 * @param $by_association Association association
	 * @param $reciprocal bool reciprocal association ?
	 * @param $ancestor Object owner ancestor
	 * @return string lister by method
	 */
	public function generate_lister_by($nb_indents,Object $object,$obj_association,Object $by,$by_association,$reciprocal,$ancestor=null) {
		// Init string
		$string = '';
		
		// Convert into variable
		$var_by = Variable::convert($by,$by_association);
		
		// Header
		$string .= $this->generate_method_header($nb_indents,'public',true,PHP::method_lister_by($by,$by_association),tr('Select '.svrl(lcfirst(PHP::name($object))).' by '.lcfirst(PHP::name($by,$by_association))),array(Variable::var_pdo(),$var_by),array(),Variable::var_pdo_statement());
	
		// Pdo
		$pdo = PHP::name_parameter(Variable::var_pdo());
		$statement = PHP::name_variable(Variable::var_pdo_statement());
		
		// Tables
		$tables = array();
		if($reciprocal) {
			$assoc_class = PHP::name_assoc_class($by, $by_association, $object, $obj_association);
			$assoc_table = $assoc_class.'::'.PHP::const_table_name();
			$tables[] = $assoc_table;
		}
			
		// Wheres
		$wheres = array();
		if(!$reciprocal) {
			foreach (MySQL::ids($by) as $id) {
				$wheres[] = '\'.'.PHP::name_class($ancestor ? $ancestor : $object).'::'.PHP::const_field_foreign($by,$id,$by_association).'.\' = ?';
			}
		} else {
			foreach (MySQL::ids($by) as $id) {
				$wheres[] = '\'.'.$assoc_table.'.\'.\'.'.$assoc_class.'::'.PHP::const_field_foreign($by,$id,$by_association).'.\' = ?';
			}
			foreach (MySQL::ids($object) as $id) {
				$field = $object->getParent() == null ? PHP::const_field($id) : PHP::const_field_parent($id);
				$wheres[] = '\'.'.$assoc_table.'.\'.\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = \'.'.PHP::name_class($ancestor ? $ancestor : $object).'::'.PHP::const_table_name().'.\'.\'.'.PHP::name_class($ancestor ? $ancestor : $object).'::'.$field.'.\'';
			}
		}
		
		// Prepare, execute and return query
		$string .= nl($statement.' = self::'.PHP::method_select().'('.$pdo.','.Query::clean('\''.implode(' AND ',$wheres).'\'').($tables ? ',null,null,array('.implode(',',$tables).')' : '').');',$nb_indents+1).
		           nl('if (!'.$statement.'->execute(array('.implode(',',PHP::paths_ids($by,PHP::name_parameter($var_by),false,true)).'))) {',$nb_indents+1).
		           nl('throw new Exception(\''.str_replace('\'','\\\'',tr('Error while selecting all '.svrl(lcfirst(PHP::name($object))).' by '.lcfirst(PHP::name($by,$by_association)).' in database')).'\');',$nb_indents+2).
		           nl('}',$nb_indents+1).
		           nl('return '.$statement.';',$nb_indents+1);
		
		// End
		$string .= nl('}',$nb_indents);
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate iterator implementation
	 * @param $nb_indents int indent account
	 * @param $element Element element
	 * @param $association Association
	 * @return string iterator implementation
	 */
	public function generate_iterator_implementation($nb_indents,Element $element,$association=null) {
		// Init string
		$string = '';
		
		// Comment
		$string .= nl('// '.tr('Iterator implementation'),$nb_indents);
		
		if ($element instanceof Scalar || !$element->isAbstract()) {
			// Iterator select, current and key
			$select = PHP::attribute(Variable::var_iterator_statement($element,$association));
			$current = PHP::attribute(Variable::var_iterator_current($element,$association));
			if ($element instanceof Scalar) { $key = PHP::attribute(Variable::var_iterator_key($element,$association)); }
		
			// Rewind method
			$string .= nl('public function rewind() { '.$select.' = $this->'.PHP::method_lister($element,$association).'(); '.($element instanceof Scalar ? $key.' = -1;' : '').' $this->next(); }',$nb_indents);
			
			// Key method
			if ($element instanceof Scalar) { $content = $key; }
			else {
				$path_ids = PHP::paths_ids($element,$current);
				$value = (count($path_ids) > 1 ? 'array(' : '').implode(', ',$path_ids).(count($path_ids) > 1 ? ')' : '');
				$content = $current.' == null ? null : '.$value;
			}
			$string .= nl('public function key() { return '.$content.'; }',$nb_indents);
			
			// Next method
			if ($element instanceof Scalar) {
				$string .= nl('public function next() { if (('.$current.' = '.$select.'->fetchColumn()) !== null) { '.$key.'++; } }',$nb_indents);
			} else {
				$string .= nl('public function next() { '.$current.' = '.PHP::name_class($element).'::'.PHP::method_fetch().'('.PHP::attribute(Variable::var_pdo()).','.$select.'); }',$nb_indents);
			}
		
			// Current method
			$string .= nl('public function current() { return '.$current.'; }',$nb_indents);
			
			// Valid method
			$string .= nl('public function valid() { return '.$current.' != null; }',$nb_indents);
		
		} else {
			// Array
			$array = PHP::attribute(Variable::var_iterator_array($element,$association));
			
			// Rewind method
			$string .= nl('public function rewind() { '.$array.' = $this->'.PHP::method_loader($element,$association).'(); }',$nb_indents);
			
			// Key method
			$string .= nl('public function key() { return key('.$array.'); }',$nb_indents);
			
			// Next method
			$string .= nl('public function next() { return next('.$array.'); }',$nb_indents);
			
			// Current method
			$string .= nl('public function current() { return current('.$array.'); }',$nb_indents);
			
			// Valid method
			$string .= nl('public function valid() { return current('.$array.') !== false; }',$nb_indents);
		}
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate method header
	 * @param $nb_indents int indent account
	 * @param $accessibility string accessibility
	 * @param $static bool static method ?
	 * @param $name string name
	 * @param $brief string description
	 * @param $params array parameters
	 * @param $defaults array default values
	 * @param $return Element returned element
	 * @return string method header
	 */
	public function generate_method_header($nb_indents,$accessibility,$static,$name,$brief,$params=array(),$defaults=array(),$return=null) {
		// Reorder params
		$temp1 = array(); $temp2 = array();
		foreach ($params as $key => $param) {
			if (array_key_exists($key,$defaults) || PHP::default_value_special($param) !== null) {
				$temp2[$key] = $param;
			} else { $temp1[$key] = $param; }
		}
		$params = array_merge_keys($temp1,$temp2);
		
		// Documentation
		$documentation = nl('/**',$nb_indents).
		          nl(' * '.$brief,$nb_indents);
		foreach ($params as $param) { $documentation .= nl(' * @param '.PHP::name_parameter($param).' '.PHP::type($param).' '.$param->getDescription(),$nb_indents); }
		if ($return) { $documentation .= nl(' * @return '.PHP::type($return).' '.$return->getDescription(),$nb_indents); }
		$documentation .= nl(' */',$nb_indents);
		
		// Parameters
		$parameters = array();
		foreach ($params as $key => $param) {
			// Default
			$default = array_key_exists($key,$defaults) ? '='.PHP::default_value($defaults[$key],$param) : '';

			// Type
			$type = $default != '' || $param instanceof Scalar || $param instanceof Variable && $param->isScalar() ? '' : $type = PHP::type($param).' ';
			
			// Add parameter
			$parameters[] = $type.PHP::name_parameter($param).$default;
		}
		
		// Set underscore if function is private or protected
		if (($accessibility == 'private' || $accessibility == 'protected') && substr($name, 0, 1) != '_') {
			$name = '_'.$name;
		}
		
		// Return method header
		return $documentation.nl($accessibility.' '.($static?'static ':'').'function '.$name.'('.implode(',',$parameters).')',$nb_indents).nl('{',$nb_indents);
	}
}