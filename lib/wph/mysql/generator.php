<?php

/**
 * MySQL generator
 */
class MySQL_Generator {
	
	// ENGINES
	const ENGINE_INNODB = 'INNODB';
	const ENGINE_MYISAM = 'MYISAM';
	
	// CHARSETS
	const CHARSET_UTF8 = 'UTF8';
	
	/** @var Project associated project */
	private $project;
	
	/** @var string used engine */
	private $engine;
	
	/** @var string used charset */
	private $charset;
	
	/** @var array generated objects */
	private $done;
	
	/** @var int maximal field name length */
	private $maxlength;
	
	/**
	 * Constructor
	 * @param $project Project associated project
	 * @param $engine string used engine
	 * @param $charset string used charset
	 */
	public function __construct(Project $project,$engine=MySQL_Generator::ENGINE_MYISAM,$charset=MySQL_Generator::CHARSET_UTF8) {
		// Save into attributes
		$this->project = $project;
		$this->engine = $engine;
		$this->charset = $charset;
		
		// Calcutate maxlength
		$this->maxlength = 0;
		foreach ($project->getObjects() as $object) {
			$this->maxlength = max($this->maxlength,$this->maximal_length($object));
		}
		$this->maxlength++;
	}
	
	/**
	 * Generate tables
	 * @return string mysql script
	 */
	public function generate_tables() {
		// Init string
		$string = '';
		
		// Init the done array
		$this->done = array();
		
		// Don't check foreign keys
		if ($this->engine == MySQL_Generator::ENGINE_INNODB) { $string .= nl('SET FOREIGN_KEY_CHECKS = 0;').nl(); }
				
		// Generate tables
		$string .= implode(nl(),array_map(array($this,'generate_table'),$this->project->getObjects()));
		
		// Check foreign keys
		if ($this->engine == MySQL_Generator::ENGINE_INNODB) { $string .= nl('SET FOREIGN_KEY_CHECKS = 1;'); }
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate a single script for all tables
	 * @param $filename string file path
	 * @throws Exception when file already exists at location
	 */
	public function generate_file($filename) {
		// Check filename
		if (file_exists($filename)) { throw new Exception('File already exists at location'); }
		
		// Puts tables into file
		file_put_contents($filename,$this->generate_tables());
	}
	
	/**
	 * Generate a script for each table
	 * @param $dirname string directory path
	 * @throws Exception when file already exists at location
	 */
	public function generate_files($dirname='./result/mysql/') {
		// Check dirname
		if (!file_exists($dirname)) { mkdir($dirname,0755,true); }
		elseif (!is_dir($dirname)) { throw new Exception('File already exists at location'); }
		
		// Don't check foreign keys
		if ($this->engine == MySQL_Generator::ENGINE_INNODB) {
			file_put_contents($dirname.'0_disable_fk.sql','SET FOREIGN_KEY_CHECKS = 0;');
		}
		
		// Init the done array
		$this->done = array();

		// Create and fill mysql scripts
		$num = 0;
		foreach ($this->project->getObjects() as $object) {
			file_put_contents($dirname.($num+1).'_'.strtolower($object->getName()).'.sql',$this->generate_table($object));
			$num++;
		}
		
		// Check foreign keys
		if ($this->engine == MySQL_Generator::ENGINE_INNODB) {
			file_put_contents($dirname.'n_enable_fk.sql','SET FOREIGN_KEY_CHECKS = 1;');
		}
	}
	
	/**
	 * Generate a zip file containing a script for each table
	 * @return string file content
	 */
	public function generate_zip() {
		// Create zipfile
		$zip = new ZipFile();
		
		// Don't check foreign keys
		if ($this->engine == MySQL_Generator::ENGINE_INNODB) {
			$zip->addfile('SET FOREIGN_KEY_CHECKS = 0;', '0_disable_fk.sql');
		}
		
		// Init the done array
		$this->done = array();

		// Create and fill mysql scripts
		foreach (array_merge($this->project->getObjects()) as $key => $object) {
			$zip->addfile($this->generate_table($object), ($key+1).'_'.strtolower($object->getName()).'.sql');
		}
		
		// Check foreign keys
		if ($this->engine == MySQL_Generator::ENGINE_INNODB) {
			$zip->addfile('SET FOREIGN_KEY_CHECKS = 1;', 'n_enable_fk.sql');
		}
		
		// Return file
		return $zip->file(); 
	}

	/**
	 * Generate a table
	 * @param $object Object object
	 * @return string mysql script
	 */
	public function generate_table(Object $object) {
		// Init string
		$string = '';
		
		// Init arrays
		$ids = array();
		$fields = array();
		$extras_pk = array();
		$extras_fk = array(); 
		$extras_un = array();
		
		// Object's description
		if ($object->getDescription() != null) { $string .= nl('-- '.$object->getDescription()); }
		
		// CREATE start
		$string .= 'CREATE TABLE '.MySQL::name_table($object).' (';
		
		// Parent's ids
		if ($object->getParent() != null) {
			foreach ($object->getParent()->getAttrsIds() as $attr) {
				// Scalar/Object ?
				if ($attr->getElem() instanceof Scalar) {
					// Add field
					$ids[] = $this->line_field_parent($attr->getElem());
					
					// Add extras
					$extras_pk[] = MySQL::name_field_parent($attr->getElem());
					$extras_fk[] = $this->line_extra_fp(MySQL::name_field_parent($attr->getElem()),$object->getParent(),MySQL::name_field($attr->getElem()));
				} else {
					// Parent ?
					if ($attr->getElem()->getParent() != null) {
						// Add parent's ids
						foreach (MySQL::ids($attr->getElem()->getParent()) as $id) {
							// Add id
							$ids[] = $this->line_field_parent($id);
					
							// Add extras
							$extras_pk[] = MySQL::name_field_parent($id);
							$extras_fk[] = $this->line_extra_fp(MySQL::name_field_parent($id),$object->getParent(),MySQL::name_field_parent($attr->getElem()));
						}
					} else {
						// Add object's ids
						foreach (MySQL::ids($attr->getElem()) as $id) {
							// Add id
							$ids[] = $this->line_field_parent($id);
					
							// Add extras
							$extras_pk[] = MySQL::name_field_parent($id);
							$extras_fk[] = $this->line_extra_fp(MySQL::name_field_parent($id),$object->getParent(),MySQL::name_field_foreign($id,$attr->getAssoc()));
						}
					}
				}
			}
		}
		
		// Fields
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attr) {
			// Init temporary arrays
			$tmp_ids = array();
			$tmp_fields = array();
			
			// Null ?
			$null = $attr->getMult() == Attribute::M_OPT;
			
			// Scalar/Object ?
			if ($attr->getElem() instanceof Scalar) {
				// Add field
				$tmp_fields[] = $this->line_field($attr->getElem(),$null);
				
				// Add extras
				$tmp_ids[] = MySQL::name_field($attr->getElem());
			} else {
				// Parent ?
				if ($attr->getElem()->getParent() != null) {
					// Add parent's ids
					foreach (MySQL::ids($attr->getElem()->getParent()) as $id) {
						// Add id
						$tmp_fields[] = $this->line_field_foreign($id,$attr->getAssoc(),$null);
						
						// Add extras
						$tmp_ids[] = MySQL::name_field_foreign($id,$attr->getAssoc());
						$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($id,$attr->getAssoc()),$attr->getElem(),MySQL::name_field_parent($id));						
					}
				} else {
					// Add object ids
					foreach ($attr->getElem()->getAttrsIds() as $attr_id) {
						// Scalar/Object ?
						if ($attr_id->getElem() instanceof Scalar) {
							// Add id
							$tmp_fields[] = $this->line_field_foreign($attr_id->getElem(),$attr->getAssoc(),$null);
							
							// Add extras
							$tmp_ids[] = MySQL::name_field_foreign($attr_id->getElem(),$attr->getAssoc());
							$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($attr_id->getElem(),$attr->getAssoc()),$attr->getElem(),MySQL::name_field($attr_id->getElem()));
						} else {
							// Add object's ids
							foreach (MySQL::ids($attr_id->getElem()) as $attr_id_id) {
								// Add id
								$tmp_fields[] = $this->line_field_foreign($attr_id_id,$attr->getAssoc(),$null);
							
								// Add extras
								$tmp_ids[] = MySQL::name_field_foreign($attr_id_id,$attr->getAssoc());
								$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($attr_id_id,$attr->getAssoc()),$attr->getElem(),MySQL::name_field_foreign($attr_id_id,$attr_id->getAssoc()));
							}
						}
					}
				}
			}
			
			// Add fields
			if ($attr->isId()) {
				$ids = array_merge($ids,$tmp_fields);
				$extras_pk = array_merge($extras_pk,$tmp_ids);
			} else {
				$fields = array_merge($fields,$tmp_fields);
			}
			
			// Unique extra
			if($attr->hasOption(Attribute::OPT_UNIQUE)) {
				$extras_un[] = $this->line_extra_un($tmp_ids);
			}
		}
		
		// PRIMARY KEY
		$extras_pk = array($this->line_extra_pk($extras_pk));
		
		// Add ids/fields/extras
		$string .= nl()."\t".implode(','.nl()."\t",array_merge($ids,$fields,$extras_pk,$extras_fk,$extras_un));
		
		// CREATE end
		$string .= nl(')').nl($this->table_end().';');
		
		// Associations tables
		$assoc_done = array();
		foreach ($object->getAttrsMult(Attribute::M_SEVERAL) as $attr) {
			if ($attr->getElem() instanceof Scalar) {
				$string .= nl().$this->generate_assoc_table($object,null,$attr->getElem(),null);
			}
			elseif ($object->equals($attr->getElem()) && !$attr->isAssociated()) {
				$string .= nl().$this->generate_assoc_table($object,null,$attr->getElem(),$attr->getAssoc());
			}
			elseif ($attr->getCorrespondence()->isMult(Attribute::M_SEVERAL)) {
				if (!$object->equals($attr->getElem()) && in_array($attr->getElem()->getId(),$this->done) || $object->equals($attr->getElem()) &&  in_array($attr->getCorrespondence()->getAssoc()->getId(),$assoc_done)) {
					$string .= nl().$this->generate_assoc_table($object,$attr->getCorrespondence()->getAssoc(),$attr->getElem(),$attr->getAssoc());
				}
				if ($attr->getAssoc() != null) { $assoc_done[] = $attr->getAssoc()->getId(); }
			}
		}
		
		// Object done
		$this->done[] = $object->getId();
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate an association table
	 * @param $object Object object
	 * @param $obj_association Association object association from element
	 * @param $element Element element
	 * @param $elem_association Association element association from object
	 * @return string association table
	 */
	private function generate_assoc_table(Object $object, $obj_association, Element $element, $elem_association) {
		// Init string, ids and extras
		$string = '';
		$extras_pk = array();
		$extras_fk = array();
		
		// CREATE start
		$string .= nl('CREATE TABLE '.MySQL::name_assoc_table($object,$obj_association,$element,$elem_association).' (');
		
		// Auto increment if necessary
		if ($element instanceof Scalar) {
			$id = Scalar::id($element);
			$string .= nl($this->line_field($id).',',1);
			$extras_pk[] = MySQL::name_field($id);
		} 
		
		// Object's ids
		if ($object->getParent() != null) {
			foreach (MySQL::ids($object->getParent()) as $id) {
				$string .= nl($this->line_field_foreign($id,$obj_association).',',1);
				if (!($element instanceof Scalar)) { $extras_pk[] = MySQL::name_field_foreign($id,$obj_association); }
				$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($id,$obj_association),$object,MySQL::name_field_parent($id)); 
			}
		} else {
			foreach ($object->getAttrsIds() as $attr) {
				if ($attr->getElem() instanceof Scalar) {
					$string .= nl($this->line_field_foreign($attr->getElem(),$obj_association).',',1);
					if (!($element instanceof Scalar)) { $extras_pk[] = MySQL::name_field_foreign($attr->getElem(),$obj_association); }
					$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($attr->getElem(),$obj_association),$object,MySQL::name_field($attr->getElem()));
				} else {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$string .= nl($this->line_field_foreign($id,$obj_association).',',1);
						if (!($element instanceof Scalar)) { $extras_pk[] = MySQL::name_field_foreign($id,$obj_association); }
						$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($id,$obj_association),$object,MySQL::name_field_foreign($id,$attr->getAssociation()));
					}
				}
			}
		}
		
		// Element's ids or value
		if ($element instanceof Scalar) {
			$string .= nl($this->line_field($element).',',1);
		} else {
			if ($element->getParent() != null) {
				foreach (MySQL::ids($element->getParent()) as $id) {
					$string .= nl($this->line_field_foreign($id,$elem_association).',',1);
					$extras_pk[] = MySQL::name_field_foreign($id,$elem_association);
					$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($id,$elem_association),$element,MySQL::name_field_parent($id)); 
				}
			} else {
				foreach ($element->getAttrsIds() as $attr) {
					if ($attr->getElem() instanceof Scalar) {
						$string .= nl($this->line_field_foreign($attr->getElem(),$elem_association).',',1);
						$extras_pk[] = MySQL::name_field_foreign($attr->getElem(),$elem_association);
						$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($attr->getElem(),$elem_association),$element,MySQL::name_field($attr->getElem()));
					} else {
						foreach (MySQL::ids($attr->getElem()) as $id) {
							$string .= nl($this->line_field_foreign($id,$elem_association).',',1);
							$extras_pk[] = MySQL::name_field_foreign($id,$elem_association);
							$extras_fk[] = $this->line_extra_fp(MySQL::name_field_foreign($id,$elem_association),$element,MySQL::name_field_foreign($id,$attr->getAssociation()));
						}
					}
				}
			}
		}
		
		// PRIMARY KEY
		$string .= nl($this->line_extra_pk($extras_pk).',',1);
		
		// FOREIGN KEYS
		$string .= "\t".implode(','.nl()."\t",$extras_fk);
		
		// CREATE end
		$string .= nl(')').nl($this->table_end().';');
		
		// Return string
		return $string;
	}
	
	/**
	 * Generate table end
	 * @return string table end
	 */
	private function table_end() {
		return ($this->engine != null ? 'ENGINE = '.$this->engine : '').
		       ($this->engine != null && $this->charset != null ? ' ' : '').
		       ($this->charset != null ? 'CHARACTER SET '.$this->charset : '');
	}
	
	/**
	 * Generate a field line
	 * @param $scalar Scalar scalar 
	 * @param $null bool can be null ?
	 * @return string field line
	 */
	private function line_field(Scalar $scalar,$null=false) {
		// Name
		$name = MySQL::name_field($scalar);
		
		// Spaces
		$spaces = rpt(' ',$this->maxlength-strlen($name)+1);
		
		// Type
		$type = MySQL::type($scalar);
		
		// Default
		$default = $scalar->getDefault() === null || $scalar->getDefault() === Scalar::DEF_NOW ? '' : ' DEFAULT '.MySQL::default_value($scalar);
		
		// Strict
		$strict = $null ? '' : ' NOT NULL';
		
		// Return line
		return $name.$spaces.$type.$default.$strict;
	}
	
	/**
	 * Generate a parent field line
	 * @param $scalar Scalar scalar
	 * @return string parent field line
	 */
	private function line_field_parent(Scalar $scalar) {
		// Name
		$name = MySQL::name_field_parent($scalar);
		
		// Spaces
		$spaces = rpt(' ',$this->maxlength-strlen($name)+1);
		
		// Type
		$type = MySQL::type($scalar,false);
		
		// Strict
		$strict = ' NOT NULL';
		
		// Return line
		return $name.$spaces.$type.$strict;
	}
	
	/**
	 * Generate a foreign field line
	 * @param $scalar Scalar scalar
	 * @param $association Association  association
	 * @param $null bool can be null ?
	 * @return string foreign field line
	 */
	private function line_field_foreign(Scalar $scalar,$association=null,$null=false) {
		// Name
		$name = MySQL::name_field_foreign($scalar,$association);
		
		// Spaces
		$spaces = rpt(' ',$this->maxlength-strlen($name)+1);
		
		// Type
		$type = MySQL::type($scalar,false);
		
		// Strict
		$strict = $null ? '' : ' NOT NULL';
		
		// Return line
		return $name.$spaces.$type.$strict;
	}
	
	/**
	 * Generate a primary key line
	 * @param $names array fields names
	 * @return primary key line
	 */
	private function line_extra_pk($names) {
		return 'PRIMARY KEY ('.implode(',',$names).')';
	}
	
	/**
	 * Generate a foreign key line
	 * @param $name string field name
	 * @param $object Object foreign object
	 * @param $id string foreign field name
	 * @return string foreign key line
	 */
	private function line_extra_fp($name,Object $object,$id) {
		return 'FOREIGN KEY ('.$name.') REFERENCES '.MySQL::name_table($object).' ('.$id.')';
	}
	
	/**
	 * Generate a unique field line
	 * @param $names array fields names
	 * @return unique field line
	 */
	private function line_extra_un($names) {
		return 'UNIQUE ('.implode(',',$names).')';
	}
	
	/**
	 * Get maximal field name length
	 * @param $object Object object
	 * @return int maximal field name length 
	 */
	private function maximal_length(Object $object) {
		// Init max length
		$max_length = 0;
		
		// Parent's ids
		if ($object->getParent() != null) {
			foreach (MySQL::ids($object->getParent()) as $id) {
				$max_length = max($max_length,strlen(MySQL::name_field_parent($id)));
			}
		}
		
		// Fields
		foreach ($object->getAttrsMult(Attribute::M_ONE|Attribute::M_OPT) as $attribute) {
			if ($attribute->getElem() instanceof Scalar) {
				$max_length = max($max_length,strlen(MySQL::name_field($attribute->getElem())));
			} else {
				foreach (MySQL::ids($attribute->getElem()) as $id) {
					$max_length = max($max_length,strlen(MySQL::name_field_foreign($id,$attribute->getAssoc())));
				}
			}
		}
		
		// Association tables
		foreach ($object->getAttrsMult(Attribute::M_SEVERAL) as $attr) {
			if ($attr->getElem() instanceof Scalar || $attr->getCorrespondence() == null || $attr->getCorrespondence()->getMult() == Attribute::M_SEVERAL) {
				foreach (MySQL::ids($object) as $id) {
					$max_length = max($max_length,strlen(MySQL::name_field_foreign($id,$attr->getElem() instanceof Scalar || $attr->getCorrespondence() == null ? null : $attr->getCorrespondence()->getAssoc())));
				}
				if ($attr->getElem() instanceof Object) {
					foreach (MySQL::ids($attr->getElem()) as $id) {
						$max_length = max($max_length,strlen(MySQL::name_field_foreign($id,$attr->getAssoc())));
					}
				}
			}
		}
		
		// Return max length
		return $max_length;
	}
}