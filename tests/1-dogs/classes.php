<?php

/**
 * @name Base_Dog
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Dog
{
    // Table name
    const TABLENAME = 'dog';
    
    // Fields name
    const FIELDNAME_IDDOG = 'iddog';
    const FIELDNAME_NAME = 'name';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idDog;
    
    /** @var string  */
    protected $_name;
    
    /**
     * Construct a dog
     * @param $pdo PDO 
     * @param $idDog int 
     * @param $name string 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idDog,$name,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idDog = $idDog;
        $this->_name = $name;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idDog] = $this;
        }
    }
    
    /**
     * Create a dog
     * @param $pdo PDO 
     * @param $name string 
     * @param $lazyload bool Enable lazy load ?
     * @return Dog 
     */
    public static function create(PDO $pdo,$name,$lazyload=true)
    {
        // Add the dog into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Dog::TABLENAME.' ('.Dog::FIELDNAME_NAME.') VALUES (?)');
        if (!$pdoStatement->execute(array($name))) {
            throw new Exception('Error while inserting a dog into database');
        }
        
        // Construct the dog
        return new Dog($pdo,intval($pdo->lastInsertId()),$name,$lazyload);
    }
    
    /**
     * Count dogs
     * @param $pdo PDO 
     * @return int Number of dogs
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Dog::FIELDNAME_IDDOG.') FROM '.Dog::TABLENAME))) {
            throw new Exception('Error while counting dogs in database');
        }
        return $pdoStatement->fetchColumn();
    }
    
    /**
     * Select query
     * @param $pdo PDO 
     * @param $where string|array 
     * @param $orderby string|array 
     * @param $limit string|array 
     * @param $from string|array 
     * @return PDOStatement 
     */
    protected static function _select(PDO $pdo,$where=null,$orderby=null,$limit=null,$from=null)
    {
        return $pdo->prepare('SELECT DISTINCT '.Dog::TABLENAME.'.'.Dog::FIELDNAME_IDDOG.', '.Dog::TABLENAME.'.'.Dog::FIELDNAME_NAME.' '.
                             'FROM '.Dog::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a dog
     * @param $pdo PDO 
     * @param $idDog int 
     * @param $lazyload bool Enable lazy load ?
     * @return Dog 
     */
    public static function load(PDO $pdo,$idDog,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idDog])) {
            return self::$_lazyload[$idDog];
        }
        
        // Load the dog
        $pdoStatement = self::_select($pdo,Dog::FIELDNAME_IDDOG.' = ?');
        if (!$pdoStatement->execute(array($idDog))) {
            throw new Exception('Error while loading a dog from database');
        }
        
        // Fetch the dog from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Dog::FIELDNAME_IDDOG.' = ?');
        if (!$pdoStatement->execute(array($this->_idDog))) {
            throw new Exception('Error while reloading data of a dog from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idDog,$name) = $values;
        
        // Save values
        $this->_name = $name;
    }
    
    /**
     * Load all dogs
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Dog[] Array of dogs
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all dogs
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the dogs
        $dogs = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $dogs;
    }
    
    /**
     * Select all dogs
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all dogs from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next dog from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Dog 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idDog,$name) = $values;
        
        // Construct the dog
        return $lazyload && isset(self::$_lazyload[intval($idDog)]) ? self::$_lazyload[intval($idDog)] :
               new Dog($pdo,intval($idDog),$name,$lazyload);
    }
    
    /**
     * Fetch all the dogs from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Dog[] Array of dogs
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $dogs = array();
        while ($dog = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $dogs[] = $dog;
        }
        return $dogs;
    }
    
    /**
     * Equality test
     * @param $dog Dog 
     * @return bool Objects are equals ?
     */
    public function equals($dog)
    {
        // Test if null
        if ($dog == null) { return false; }
        
        // Test class
        if (!($dog instanceof Dog)) { return false; }
        
        // Test ids
        return $this->_idDog == $dog->_idDog;
    }
    
    /**
     * Check if the dog exists in database
     * @return bool The dog exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Dog::FIELDNAME_IDDOG.') FROM '.Dog::TABLENAME.' WHERE '.Dog::FIELDNAME_IDDOG.' = ?');
        if (!$pdoStatement->execute(array($this->getIdDog()))) {
            throw new Exception('Error while checking that a dog exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete dog
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete dog
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Dog::TABLENAME.' WHERE '.Dog::FIELDNAME_IDDOG.' = ?');
        if (!$pdoStatement->execute(array($this->getIdDog()))) {
            throw new Exception('Error while deleting a dog in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idDog])) {
            unset(self::$_lazyload[$this->_idDog]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update a field in database
     * @param $fields array 
     * @param $values array 
     * @return bool Successful operation ?
     */
    protected function _set($fields,$values)
    {
        // Prepare update
        $updates = array();
        foreach ($fields as $field) {
            $updates[] = $field.' = ?';
        }
        
        // Update field
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Dog::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Dog::FIELDNAME_IDDOG.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdDog())))) {
            throw new Exception('Error while updating a dog\'s field in database');
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Update all fields in database
     * @return bool Successful operation ?
     */
    public function update()
    {
        return $this->_set(array(Dog::FIELDNAME_NAME),array($this->_name));
    }
    
    /**
     * Get the idDog
     * @return int 
     */
    public function getIdDog()
    {
        return $this->_idDog;
    }
    
    /**
     * Get the name
     * @return string 
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Set the name
     * @param $name string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setName($name,$execute=true)
    {
        // Save into object
        $this->_name = $name;
        
        // Save into database (or not)
        return $execute ? Dog::_set(array(Dog::FIELDNAME_NAME),array($name)) : true;
    }
    
    /**
     * ToString
     * @return string String representation of dog
     */
    public function __toString()
    {
        return '[Dog idDog="'.$this->_idDog.'" name="'.$this->_name.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of dog
     */
    public function serialize($serialize=true)
    {
        // Serialize the dog
        $array = array('iddog' => $this->_idDog,'name' => $this->_name);
        
        // Return the serialized (or not) dog
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of dog
     * @param $lazyload bool Enable lazy load ?
     * @return Dog 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the dog
        return $lazyload && isset(self::$_lazyload[$array['iddog']]) ? self::$_lazyload[$array['iddog']] :
               new Dog($pdo,$array['iddog'],$array['name'],$lazyload);
    }
    
}

/**
 * @name Dog
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Dog extends Base_Dog
{
    
    // Put your code here...
    
}

