<?php

/**
 * Tool to load project from a string
 */
class Loader
{
	
	/**
	 * Load a projet from a string
	 * @param $string string
	 * @throws Exception when error occurs
	 * @return Project project 
	 */
	public static function load($string) {	
		// Construct project
		$project = new Project('Project');
		
		// Construct regular expression
		$flag = '((\*)|(\?)|(-)|(#))';
		
		$correspondence = '([0-9]+)';
		$name = '([a-zA-Z][a-zA-Z0-9_-]*)';
		$description = '("([^"]+)")';
		
		$options = '(\(([a-zA-Z]+)\))';
		
		$scalar_size = '(\/([0-9]+))';
		$scalar_type = '((bool)|(int'.$scalar_size.'?)|(float)|(date)|(time)|(datetime)|(string'.$scalar_size.'?))';
		$scalar_default = '(((true)|(false))|([0-9]+)|("([^"]+)")|(now))';
		$scalar = '('.$flag.'?\|'.$name.'\s*,\s*'.$scalar_type.'(\s*,\s*'.$description.'?(\s*,\s*'.$scalar_default.')?)?'.'\|)';
		
		$association = '(\{'.$name.'(\s*,\s*'.$correspondence.')?\})';
		
		$element = $options.'?('.$scalar.'|('.$flag.'?'.$association.'?'.$name.'))';
		
		$object_content = $name.'(\s*,\s*'.$description.')?(\s*:(\s*,?\s*'.$element.')+)?';
		$object_inheritance = '('.$name.'<-)';
		$object = '('.$options.'?\[\s*'.$object_inheritance.'?'.$object_content.'\s*\])';
		
		$comment = '(((\/\/)|(#)|(--)).*?)';
		
		// Check syntaxe (crashes ...)
		/*if (!preg_match('/^('.$object.'|(\s)|'.$comment.')+$/',$string)) {
			throw new Exception('Syntaxe error');
		}*/
		
		// Get all objects
		$nb_objects = preg_match_all('/'.$object.'/',$string,$matches_objects,PREG_SET_ORDER);
		
		// Nb objects found
		if (!$nb_objects) {
			throw new Exception('No well formed definition has been found');
		}
		
		// Create objects
		$objects = array();
		for($i=0;$i<$nb_objects;$i++) {
			// Get options
			$options = isset($matches_objects[$i][3])?$matches_objects[$i][3]:null;
			
			// Get description
			$description = isset($matches_objects[$i][9])?$matches_objects[$i][9]:null;
			
			// Add object into project
			$objects[$matches_objects[$i][6]] = $project->addObject($matches_objects[$i][6],$description,$options);
		}
		
		// Inheritance
		$inheritances = array();
		for($i=0;$i<$nb_objects;$i++) {
			// Get parent and child name
			$object_child_name = $matches_objects[$i][6];
			$object_parent_name = $matches_objects[$i][5];
			
			if ($object_parent_name != null) {
				// Inexiting parent
				if (!isset($objects[$object_parent_name])) {
					throw new Exception('The object <b>'.$object_child_name.'</b>  inherites the inexisting object <b>'.$object_parent_name.'</b>, please define it');
				}
				
				// Add parent
				$objects[$object_child_name]->setParent($objects[$object_parent_name]);
			}
		}
		
		// Fill objects
		for($i=0;$i<$nb_objects;$i++) {
			if (isset($matches_objects[$i][10])) {
				// Object
				$object_owner_name = $matches_objects[$i][6];
				$object_owner = $objects[$object_owner_name];
				
				// Number of elements
				$nb_elements = preg_match_all('/'.$element.'/',$matches_objects[$i][10],$matches_elements,PREG_SET_ORDER);
				
				for($j=0;$j<$nb_elements;$j++) {
					// Get options
					$options = !empty($matches_elements[$j][2])?$matches_elements[$j][2]:null;
					
					if (isset($matches_elements[$j][37])) { // Object
						// Flag
						$flag = $matches_elements[$j][36];
						
						// Association
						if ($matches_elements[$j][42]) {
							$association = new Association($matches_elements[$j][42],
							                               $matches_elements[$j][44]?$matches_elements[$j][44]:null);
						} else {
							$association = null;
						}
						
						// Name
						$element_owned_name = $matches_elements[$j][45];
						
						// Inexisting object
						if (!isset($objects[$element_owned_name])) {
							throw new Exception('The object <b>'.$object_owner_name.'</b> owns the inexisting object <b>'.$element_owned_name.'</b>, please define it');
						}
						
						// Object
						$element_owned = $objects[$element_owned_name];
						
						// Modify flag if necessary
						if ($element_owned_name == $object_owner_name && $flag == '') { $flag = '?'; }
					} else { // Scalar					
						// Flag
						$flag = $matches_elements[$j][5];
						
						// Association
						$association = null;
						
						// Type and length
						switch ($matches_elements[$j][11]) {
							case 'bool': $type = Scalar::T_BOOL; break;
							case 'int': $type = Scalar::T_INT; break;
							case 'float': $type = Scalar::T_FLOAT; break;
							case 'date': $type = Scalar::T_DATE; break;
							case 'time': $type = Scalar::T_TIME; break;
							case 'datetime': $type = Scalar::T_DATETIME; break;
							case 'string': $type = Scalar::T_TEXT; break;
						}
						$length = null;
						if (preg_match('/^int'.$scalar_size.'$/',$matches_elements[$j][11],$matches_type)) { $type = Scalar::T_INT; $length = $matches_type[2]; }
						if (preg_match('/^string'.$scalar_size.'$/',$matches_elements[$j][11],$matches_type)) { $type = Scalar::T_TEXT; $length = $matches_type[2]; }
						
						// Description
						$description = !empty($matches_elements[$j][25])?$matches_elements[$j][25]:null;
						
						// Default
						$default = isset($matches_elements[$j][27]) && $matches_elements[$j][27] !== '' ? $matches_elements[$j][27] : ( !empty($matches_elements[$j][33]) ? $matches_elements[$j][33] : null );
						if ($default !== null) {
							switch ($type) {
								case Scalar::T_BOOL: $default = $default == 'true'; break;
								case Scalar::T_TIME: $default = intval($default); break;
								case Scalar::T_DATE: $default = $default === Scalar::DEF_NOW ? $default : $default = intval($default); break;
								case Scalar::T_DATETIME: $default = $default === Scalar::DEF_NOW ? $default : intval($default); break;
								case Scalar::T_INT: $default = intval($default); break;
								case Scalar::T_FLOAT: $default = floatval($default); break;
								case Scalar::T_TEXT: $default = $default; break;
								default: $default = null;
							}
						}
						
						// Element
						$element_owned = new Scalar($matches_elements[$j][10],$type,$description,$default,$length,$options);
					}
					
					// Add element into object
					switch ($flag) {
						case '*':
							$object_owner->addElem($element_owned,Attribute::M_SEVERAL,false,$association,false,$options);
							break;
						case '?':
							$object_owner->addElem($element_owned,Attribute::M_OPT,false,$association,false,$options);
							break;
						case '-':
							$object_owner->addElem($element_owned,Attribute::M_SEVERAL,false,$association,true,$options);
							break;
						case '#':
							$object_owner->addElem($element_owned,Attribute::M_ONE,true,$association,false,$options);
							break;
						default:
							$object_owner->addElem($element_owned,Attribute::M_ONE,false,$association,false,$options);
					}
				}
			}
		}
		
		// Return project
		return $project;
	}

}
