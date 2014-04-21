<?php

/**
 * Query
 */
class Query {
	
	/**
	 * Get update query
	 * @param $object Object owner object
	 * @param $element Element owned element
	 * @param $association Association association
	 * @return string update query
	 */
	public static function update(Object $object,Element $element=null,$association=null) {
		// Get fields
		$updates = array();
		if($element != null) {
			if ($element instanceof Scalar) {
				$updates[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field($element).'.\' = ?';
			} else {
				foreach (MySQL::ids($element) as $id) {
					$updates[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($element,$id,$association).'.\' = ?';
				}
			}
		} else {
			foreach($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
				if(!$attr->isId()) {
					if ($attr->getElem() instanceof Scalar) {
						$updates[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\' = ?';
					} else {
						foreach (MySQL::ids($attr->getElem()) as $id) {
							$updates[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\' = ?';
						}
					}
				}
			}
		}
		
		// Return query
		return Query::clean('\'UPDATE '.MySQL::name_table($object).' SET '.implode(', ',$updates).' WHERE '.implode(' AND ',Query::wheres($object)).'\'');
	}
	
	/**
	 * Get adder query
	 * @param Object $object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @return string adder query
	 */
	public static function adder(Object $object,$obj_association,Element $element,$elem_association) {
		// Association class
		$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
		
		// Gets adds
		$adds = array();
		foreach (MySQL::ids($object) as $id) {
			$adds[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\'';
		}
		if ($element instanceof Scalar) {
			$adds[] = '\'.'.$assoc_class.'::'.PHP::const_field($element).'.\'';
		} else {
			foreach (MySQL::ids($element) as $id) {
				$adds[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($element,$id,$elem_association).'.\'';
			}
		}
		
		// Return query
		return '\'INSERT INTO \'.'.$assoc_class.'::'.PHP::const_table_name().'.\' ('.implode(',',$adds).') '.
		         'VALUES ('.implode(',',array_fill(0,count($adds),'?')).')\'';
	}
	
	/**
	 * Get adder list of query
	 * @param Object $object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @param $count string count variable name
	 * @return string adder list of query
	 */
	public static function adder_list_of(Object $object,$obj_association,Element $element,$elem_association,$count) {
		// Association class
		$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
		
		// Gets adds
		$adds = array();
		foreach (MySQL::ids($object) as $id) {
			$adds[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\'';
		}
		if ($element instanceof Scalar) {
			$adds[] = '\'.'.$assoc_class.'::'.PHP::const_field($element).'.\'';
		} else {
			foreach (MySQL::ids($element) as $id) {
				$adds[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($element,$id,$elem_association).'.\'';
			}
		}
		
		// Return query
		return '\'INSERT INTO \'.'.$assoc_class.'::'.PHP::const_table_name().'.\' ('.implode(',',$adds).') '.
		         'VALUES \'.implode(\',\',array_fill(0,'.$count.',\'('.implode(',',array_fill(0,count($adds),'?')).')\'))';
	}
	
	/**
	 * Get remover query
	 * @param $object Object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @return string remover query
	 */
	public static function remover(Object $object,$obj_association,Element $element,$elem_association) {
		// Association class
		$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
		
		// Get dels
		$wheres = array();
		foreach (MySQL::ids($object) as $id) {
			$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
		}
		if ($element instanceof Scalar) {
			$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field($element).'.\' = ?';
		} else {
			foreach (MySQL::ids($element) as $id) {
				$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($element,$id,$elem_association).'.\' = ?';
			}
		}
		
		// Return query
		return Query::clean('\'DELETE FROM \'.'.$assoc_class.'::'.PHP::const_table_name().'.\' WHERE '.implode(' AND ',$wheres).'\'');
	}
	
	/**
	 * Get remover list of query
	 * @param $object Object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @param $count string count variable name
	 * @return string remover list of query
	 */
	public static function remover_list_of(Object $object,$obj_association,Element $element,$elem_association,$count) {
		// Association class
		$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
		
		// Get dels
		$wheres = array();
		foreach (MySQL::ids($object) as $id) {
			$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
		}
		if ($element instanceof Scalar) {
			$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field($element).'.\' = ?';
		} else {
			foreach (MySQL::ids($element) as $id) {
				$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($element,$id,$elem_association).'.\' = ?';
			}
		}
		
		// Return query
		return Query::clean('\'DELETE FROM \'.'.$assoc_class.'::'.PHP::const_table_name().'.\' WHERE \'.implode(\' OR \',array_fill(0,'.$count.',\'('.implode(' AND ',$wheres).')\'))');
	}
	
	/**
	 * Get remove all query
	 * @param $object Object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @return string remover query
	 */
	public static function removeAll(Object $object,$obj_association,Element $element,$elem_association) {
		// Association class
		$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
		
		// Get dels
		$wheres = array();
		foreach (MySQL::ids($object) as $id) {
			$wheres[] = '\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
		}
		
		// Return query
		return Query::clean('\'DELETE FROM \'.'.$assoc_class.'::'.PHP::const_table_name().'.\' WHERE '.implode(' AND ',$wheres).'\'');
	}
	
	/**
	 * Get select query
	 * @param $object Object object
	 * @return string select query
	 */
	public static function select(Object $object) {
		// Ids, attrs and opts
		list($ids,$attrs,$opts) = Query::fields($object);
			
		// Tables
		$tables = array();
		foreach ($object->getFamily(true) as $member) {
			$tables[] = '\'.'.PHP::name_class($member).'::'.PHP::const_table_name().'.\'';
		}
		
		// Joins
		$joins = Query::joins($object);
		
		// Return query
		return Query::clean('\'SELECT '.implode(', ',array_merge($ids,$attrs,$opts)).' FROM '.implode(',',$tables).(count($joins) ? ' WHERE '.implode(' AND ',$joins) : '').'\'');
	}
	
	/**
	 * Get select query with where
	 * @param $object Object object
	 * @return string select query with where
	 */
	public static function select_where(Object $object) {
		// Ids, attrs and opts
		list($ids,$attrs,$opts) = Query::fields($object);
			
		// Tables
		$tables = array();
		foreach ($object->getFamily(true) as $member) {
			$tables[] = '\'.'.PHP::name_class($member).'.'.PHP::const_table_name().'.\'';
		}
			
		// Joins
		$joins = Query::joins($object);
			
		// Wheres
		$wheres = Query::wheres($object);
		
		// Return query
		return Query::clean('\'SELECT '.implode(', ',array_merge($ids,$attrs,$opts)).' FROM '.implode(',',$tables).' WHERE '.implode(' AND ',array_merge($joins,$wheres)).'\'');
	}
	
	/**
	 * Get lister query
	 * @param $object Object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @param $reciprocal bool reciprocal association ?
	 * @param $ancestor Object owner ancestor
	 * @return string lister query
	 */
	public static function lister(Object $object,$obj_association,Element $element,$elem_association,$reciprocal=true,$ancestor=null) {
		if($element instanceof Scalar) {
			// Wheres
			$wheres = array();
			foreach (MySQL::ids($object) as $id) {
				$wheres[] = '\'.'.PHP::name_assoc_class($object, $obj_association, $element, $elem_association).'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
			}
			
			// Return query
			return Query::clean('\'SELECT \'.'.PHP::name_assoc_class($object, $obj_association, $element, $elem_association).'::'.PHP::const_field($element).'.\' '.
			                      'FROM \'.'.PHP::name_assoc_class($object, $obj_association, $element, $elem_association).'::'.PHP::const_table_name().'.\' '.
			                      'WHERE '.implode(' AND ',$wheres).'\'');
		} else {
			// Ids, attrs and opts
			list($ids,$attrs,$opts) = Query::fields($element,$reciprocal);
				
			// Tables
			$tables = array();
			foreach ($element->getFamily(true) as $member) {
				$tables[] = '\'.'.PHP::name_class($member).'::'.PHP::const_table_name().'.\'';
			}
			if($reciprocal) {
				$assoc_class = PHP::name_assoc_class($object, $obj_association, $element, $elem_association);
				$assoc_table = $assoc_class.'::'.PHP::const_table_name();
				$tables[] = '\'.'.$assoc_table.'.\'';
			}
				
			// Wheres
			$wheres = Query::joins($element,$reciprocal);
			if(!$reciprocal) {
				foreach (MySQL::ids($object) as $id) {
					$wheres[] = '\'.'.PHP::name_class($ancestor ? $ancestor : $element).'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
				}
			} else {
				foreach (MySQL::ids($object) as $id) {
					$wheres[] = '\'.'.$assoc_table.'.\'.\'.'.$assoc_class.'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
				}
				foreach (MySQL::ids($element) as $id) {
					$field = $element->getParent() == null ? PHP::const_field($id) : PHP::const_field_parent($id);
					$wheres[] = '\'.'.$assoc_table.'.\'.\'.'.$assoc_class.'::'.PHP::const_field_foreign($element,$id,$elem_association).'.\' = \'.'.PHP::name_class($ancestor ? $ancestor : $element).'::'.PHP::const_table_name().'.\'.\'.'.PHP::name_class($ancestor ? $ancestor : $element).'::'.$field.'.\'';
				}
			}
			
			// Return query
			return Query::clean('\'SELECT '.implode(', ',array_merge($ids,$attrs,$opts)).' FROM '.implode(', ',$tables).' WHERE '.implode(' AND ',$wheres).'\'');
		}
	}
	
	/**
	 * Get count query
	 * @param $object Object object
	 * @return string count query
	 */
	public static function count(Object $object) {
		// Get ids
		$ids = array();
		if ($object->getParent() != null) {
			foreach (MySQL::ids($object->getParent()) as $id) {
				$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_parent($id).'.\'';
			}
		} else {
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$ids[] =  '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\'';
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\'';
					}
				}
			}
		}
		
		// Return query
		return '\'SELECT COUNT('.implode(',',$ids).') FROM \'.'.PHP::name_class($object).'::'.PHP::const_table_name();
	}
	
	/**
	 * Get exists query
	 * @param $object Object object
	 * @return string exists query
	 */
	public static function exists(Object $object) {
		// Get ids
		$ids = array();
		if ($object->getParent() != null) {
			foreach (MySQL::ids($object->getParent()) as $id) {
				$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_parent($id).'.\'';
			}
		} else {
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$ids[] =  '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\'';
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\'';
					}
				}
			}
		}
			
		// Wheres
		$wheres = Query::wheres($object);
		
		// Return query
		return '\'SELECT COUNT('.implode(',',$ids).') FROM \'.'.PHP::name_class($object).'::'.PHP::const_table_name().'.\' WHERE '.implode(' AND ',$wheres).'\'';
	}
	
	/**
	 * Get delete query
	 * @param $object Object object
	 * @return string delete query
	 */
	public static function delete(Object $object) {
		// Return query
		return Query::clean('\'DELETE FROM \'.'.PHP::name_class($object).'::'.PHP::const_table_name($object).'.\' WHERE '.implode(' AND ',Query::wheres($object)).'\'');
	}
	
	/**
	 * Get delete by query
	 * @param $object Object owner object
	 * @param $obj_association Association object association from element
	 * @param $element Element owned element
	 * @param $elem_association Association element association from object
	 * @return string delete by query 
	 */
	public static function delete_by(Object $object,$obj_association,Element $element,$elem_association) {
		// Wheres
		$wheres = array();
		foreach (MySQL::ids($object) as $id) {
			$wheres[] = '\'.'.PHP::name_assoc_class($object,$obj_association,$element,$elem_association).'::'.PHP::const_field_foreign($object,$id,$obj_association).'.\' = ?';
		}
		
		// Return query
		return Query::clean('\'DELETE FROM \'.'.PHP::name_assoc_class($object,$obj_association,$element,$elem_association).'::'.PHP::const_table_name().'.\' WHERE '.implode(' AND ',$wheres).'\'');
	}
	
	/**
	 * Get insert query
	 * @param $object Object object
	 * @return string insert query
	 */
	public static function insert(Object $object) {
		// Ids
		$ids = array();
		if ($object->getParent() != null) {
			foreach (MySQL::ids($object) as $id) {
				$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_parent($id).'.\'';
			}
		} else {
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					if ($attr->getElem()->getType() != Scalar::T_INT_AI) {
						$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\'';
					}
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$ids[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\'';
					}
				}
			}
		}
		
		// Attrs and opts
		$attrs = array(); $opts = array();
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			if (!$attr->isId()) {
				$fields = array();
				
				if ($attr->getElem() instanceof Scalar) {
					$fields[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\'';
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$fields[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\'';
					}
				}
				
				if ($attr->isMult(Attribute::M_OPT) || $attr->getElem() instanceof Scalar && $attr->getElem()->getDefault() !== null) {
					$opts = array_merge($opts,$fields);
				} else {
					$attrs = array_merge($attrs,$fields);
				}
			}
		}
		
		
		// Return query
		return '\'INSERT INTO \'.'.PHP::name_class($object).'::'.PHP::const_table_name().'.\' ('.implode(',',array_merge($ids,$attrs,$opts)).') '.
		       'VALUES ('.(count(array_merge($ids,$attrs,$opts)) ? implode(',',array_fill(0,count(array_merge($ids,$attrs,$opts)),'?')) : '').')\'';
	}
	
	/**
	 * Get fields array
	 * @param $object Object object
	 * @param $tablenames bool include table names ?
	 * @return array fields array
	 */
	public static function fields(Object $object,$tables=false) {
		// Init arrays
		$ids = array();
		$attrs = array();
		$opts = array();
		
		// Table name
		$table = $tables ? PHP::name_class($object).'::'.PHP::const_table_name().'.\'.\'.' : '';
		
		// Get ids and fields from parent
		if ($object->getParent() != null) {
			list($ids,$attrs,$opts) = Query::fields($object->getParent(),$tables);
		} else {
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$ids[] = '\'.'.$table.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\'';
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$ids[] = '\'.'.$table.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\'';
					}
				}
			}
		}
		
		// Get attrs and opts
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			if (!$attr->isId()) {
				// Init fields array
				$fields = array();
				
				// Get fields
				if ($attr->getElem() instanceof Scalar) {
					$fields[] = '\'.'.$table.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\'';
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$fields[] = '\'.'.$table.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\'';
					}
				}
				
				// Put fields into attrs or opts 
				if ($attr->isMult(Attribute::M_OPT) || $attr->getElem() instanceof Scalar && $attr->getElem()->getDefault() !== null) {
					$opts = array_merge($opts,$fields);
				} else {
					$attrs = array_merge($attrs,$fields);
				}
			}
		}
		
		
		// Return array
		return array($ids,$attrs,$opts);
	}
	
	/**
	 * Get family joins array
	 * @param $object Object object
	 * @param $tablenames bool include table names ?
	 * @return array joins array
	 */
	public static function joins(Object $object,$tables=false) {
		// Check if object has a parent
		if($object->getParent() == null) {
			return array();
		}
		
		// Ancestor
		$ancestor = $object->getAncestor();
		
		// Tables name
		$anc_table = $tables ? PHP::name_class($ancestor).'::'.PHP::const_table_name().'.\'.\'.' : '';
		$obj_table = $tables ? PHP::name_class($object).'::'.PHP::const_table_name().'.\'.\'.' : '';
		
		// Joins
		$joins = array();
		foreach ($ancestor->getAttrsIds() as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$joins[] = '\'.'.$anc_table.PHP::name_class($ancestor).'::'.PHP::const_field($attr->getElem()).'.\' = \'.'.$obj_table.PHP::name_class($object).'::'.PHP::const_field_parent($attr->getElem()).'.\'';
			} else {
				foreach (MySQL::ids($attr->getElem()) as $id) {
					$joins[] = '\'.'.$anc_table.PHP::name_class($ancestor).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\' = \'.'.$obj_table.PHP::name_class($object).'::'.PHP::const_field_parent($id).'.\'';
				}
			}
		}
		
		// Return array
		return array_merge(self::joins($object->getParent(),$tables),$joins);
	}
	
	/**
	 * Get wheres array
	 * @param $object Object object
	 * @return array where array
	 */
	public static function wheres(Object $object) {
		// Get wheres
		$wheres = array();
		if ($object->getParent() != null) {
			foreach (MySQL::ids($object) as $id) {
				$wheres[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_parent($id).'.\' = ?';
			}
		} else {
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$wheres[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field($attr->getElem()).'.\' = ?';
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$wheres[] = '\'.'.PHP::name_class($object).'::'.PHP::const_field_foreign($attr->getElem(),$id,$attr->getAssoc()).'.\' = ?';
					}
				}
			}
		}
		
		// Return wheres
		return $wheres;
	}
	
	/**
	 * Clean a query
	 * @param $query string query to clean
	 * @return string cleaned query
	 */
	public static function clean($query) {
		if(substr($query, 0, 3) == '\'\'.') {
			$query = substr($query,3);
		}
		if(substr($query, -3) == '.\'\'') {
			$query = substr($query, 0, -3);
		}
		return $query;
	}
}