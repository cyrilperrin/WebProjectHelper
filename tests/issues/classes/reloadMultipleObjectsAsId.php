<?php

/**
 * @name Base_A_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_A_Issue1
{
    // Table name
    const TABLENAME = 'a_issue1';
    
    // Fields name
    const FIELDNAME_IDA_ISSUE1 = 'ida_issue1';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idA_issue1;
    
    /**
     * Construct a a_issue1
     * @param $pdo PDO 
     * @param $idA_issue1 int 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idA_issue1,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idA_issue1 = $idA_issue1;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idA_issue1] = $this;
        }
    }
    
    /**
     * Create a a_issue1
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1 
     */
    public static function create(PDO $pdo,$lazyload=true)
    {
        // Add the a_issue1 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.A_Issue1::TABLENAME.' () VALUES ()');
        if (!$pdoStatement->execute(array())) {
            throw new Exception('Error while inserting a a_issue1 into database');
        }
        
        // Construct the a_issue1
        return new A_Issue1($pdo,intval($pdo->lastInsertId()),$lazyload);
    }
    
    /**
     * Count a_issue1s
     * @param $pdo PDO 
     * @return int Number of a_issue1s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.A_Issue1::FIELDNAME_IDA_ISSUE1.') FROM '.A_Issue1::TABLENAME))) {
            throw new Exception('Error while counting a_issue1s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.A_Issue1::TABLENAME.'.'.A_Issue1::FIELDNAME_IDA_ISSUE1.' '.
                             'FROM '.A_Issue1::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a a_issue1
     * @param $pdo PDO 
     * @param $idA_issue1 int 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1 
     */
    public static function load(PDO $pdo,$idA_issue1,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idA_issue1])) {
            return self::$_lazyload[$idA_issue1];
        }
        
        // Load the a_issue1
        $pdoStatement = self::_select($pdo,A_Issue1::FIELDNAME_IDA_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($idA_issue1))) {
            throw new Exception('Error while loading a a_issue1 from database');
        }
        
        // Fetch the a_issue1 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all a_issue1s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1[] Array of a_issue1s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all a_issue1s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the a_issue1s
        $a_issue1s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $a_issue1s;
    }
    
    /**
     * Select all a_issue1s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all a_issue1s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next a_issue1 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idA_issue1) = $values;
        
        // Construct the a_issue1
        return $lazyload && isset(self::$_lazyload[intval($idA_issue1)]) ? self::$_lazyload[intval($idA_issue1)] :
               new A_Issue1($pdo,intval($idA_issue1),$lazyload);
    }
    
    /**
     * Fetch all the a_issue1s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1[] Array of a_issue1s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $a_issue1s = array();
        while ($a_issue1 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $a_issue1s[] = $a_issue1;
        }
        return $a_issue1s;
    }
    
    /**
     * Equality test
     * @param $a_issue1 A_Issue1 
     * @return bool Objects are equals ?
     */
    public function equals($a_issue1)
    {
        // Test if null
        if ($a_issue1 == null) { return false; }
        
        // Test class
        if (!($a_issue1 instanceof A_Issue1)) { return false; }
        
        // Test ids
        return $this->_idA_issue1 == $a_issue1->_idA_issue1;
    }
    
    /**
     * Check if the a_issue1 exists in database
     * @return bool The a_issue1 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.A_Issue1::FIELDNAME_IDA_ISSUE1.') FROM '.A_Issue1::TABLENAME.' WHERE '.A_Issue1::FIELDNAME_IDA_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getIdA_issue1()))) {
            throw new Exception('Error while checking that a a_issue1 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete a_issue1
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated c_issue1s
        $select = $this->selectC_issue1s();
        while ($c_issue1 = C_Issue1::fetch($this->_pdo,$select)) {
            $c_issue1->delete();
        }
        
        // Delete a_issue1
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.A_Issue1::TABLENAME.' WHERE '.A_Issue1::FIELDNAME_IDA_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getIdA_issue1()))) {
            throw new Exception('Error while deleting a a_issue1 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idA_issue1])) {
            unset(self::$_lazyload[$this->_idA_issue1]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the idA_issue1
     * @return int 
     */
    public function getIdA_issue1()
    {
        return $this->_idA_issue1;
    }
    
    /**
     * Select c_issue1s
     * @return PDOStatement 
     */
    public function selectC_issue1s()
    {
        return C_Issue1::selectByA_issue1($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of a_issue1
     */
    public function __toString()
    {
        return '[A_Issue1 idA_issue1="'.$this->_idA_issue1.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of a_issue1
     */
    public function serialize($serialize=true)
    {
        // Serialize the a_issue1
        $array = array('ida_issue1' => $this->_idA_issue1);
        
        // Return the serialized (or not) a_issue1
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of a_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the a_issue1
        return $lazyload && isset(self::$_lazyload[$array['ida_issue1']]) ? self::$_lazyload[$array['ida_issue1']] :
               new A_Issue1($pdo,$array['ida_issue1'],$lazyload);
    }
    
}

/**
 * @name A_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class A_Issue1 extends Base_A_Issue1
{
    
    // Put your code here...
    
}

/**
 * @name Base_B_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_B_Issue1
{
    // Table name
    const TABLENAME = 'b_issue1';
    
    // Fields name
    const FIELDNAME_IDB_ISSUE1 = 'idb_issue1';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idB_issue1;
    
    /**
     * Construct a b_issue1
     * @param $pdo PDO 
     * @param $idB_issue1 int 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idB_issue1,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idB_issue1 = $idB_issue1;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idB_issue1] = $this;
        }
    }
    
    /**
     * Create a b_issue1
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1 
     */
    public static function create(PDO $pdo,$lazyload=true)
    {
        // Add the b_issue1 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.B_Issue1::TABLENAME.' () VALUES ()');
        if (!$pdoStatement->execute(array())) {
            throw new Exception('Error while inserting a b_issue1 into database');
        }
        
        // Construct the b_issue1
        return new B_Issue1($pdo,intval($pdo->lastInsertId()),$lazyload);
    }
    
    /**
     * Count b_issue1s
     * @param $pdo PDO 
     * @return int Number of b_issue1s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.B_Issue1::FIELDNAME_IDB_ISSUE1.') FROM '.B_Issue1::TABLENAME))) {
            throw new Exception('Error while counting b_issue1s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.B_Issue1::TABLENAME.'.'.B_Issue1::FIELDNAME_IDB_ISSUE1.' '.
                             'FROM '.B_Issue1::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a b_issue1
     * @param $pdo PDO 
     * @param $idB_issue1 int 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1 
     */
    public static function load(PDO $pdo,$idB_issue1,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idB_issue1])) {
            return self::$_lazyload[$idB_issue1];
        }
        
        // Load the b_issue1
        $pdoStatement = self::_select($pdo,B_Issue1::FIELDNAME_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($idB_issue1))) {
            throw new Exception('Error while loading a b_issue1 from database');
        }
        
        // Fetch the b_issue1 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all b_issue1s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1[] Array of b_issue1s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all b_issue1s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the b_issue1s
        $b_issue1s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $b_issue1s;
    }
    
    /**
     * Select all b_issue1s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all b_issue1s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next b_issue1 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idB_issue1) = $values;
        
        // Construct the b_issue1
        return $lazyload && isset(self::$_lazyload[intval($idB_issue1)]) ? self::$_lazyload[intval($idB_issue1)] :
               new B_Issue1($pdo,intval($idB_issue1),$lazyload);
    }
    
    /**
     * Fetch all the b_issue1s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1[] Array of b_issue1s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $b_issue1s = array();
        while ($b_issue1 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $b_issue1s[] = $b_issue1;
        }
        return $b_issue1s;
    }
    
    /**
     * Equality test
     * @param $b_issue1 B_Issue1 
     * @return bool Objects are equals ?
     */
    public function equals($b_issue1)
    {
        // Test if null
        if ($b_issue1 == null) { return false; }
        
        // Test class
        if (!($b_issue1 instanceof B_Issue1)) { return false; }
        
        // Test ids
        return $this->_idB_issue1 == $b_issue1->_idB_issue1;
    }
    
    /**
     * Check if the b_issue1 exists in database
     * @return bool The b_issue1 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.B_Issue1::FIELDNAME_IDB_ISSUE1.') FROM '.B_Issue1::TABLENAME.' WHERE '.B_Issue1::FIELDNAME_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getIdB_issue1()))) {
            throw new Exception('Error while checking that a b_issue1 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete b_issue1
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated c_issue1s
        $select = $this->selectC_issue1s();
        while ($c_issue1 = C_Issue1::fetch($this->_pdo,$select)) {
            $c_issue1->delete();
        }
        
        // Delete b_issue1
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.B_Issue1::TABLENAME.' WHERE '.B_Issue1::FIELDNAME_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getIdB_issue1()))) {
            throw new Exception('Error while deleting a b_issue1 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idB_issue1])) {
            unset(self::$_lazyload[$this->_idB_issue1]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the idB_issue1
     * @return int 
     */
    public function getIdB_issue1()
    {
        return $this->_idB_issue1;
    }
    
    /**
     * Select c_issue1s
     * @return PDOStatement 
     */
    public function selectC_issue1s()
    {
        return C_Issue1::selectByB_issue1($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of b_issue1
     */
    public function __toString()
    {
        return '[B_Issue1 idB_issue1="'.$this->_idB_issue1.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of b_issue1
     */
    public function serialize($serialize=true)
    {
        // Serialize the b_issue1
        $array = array('idb_issue1' => $this->_idB_issue1);
        
        // Return the serialized (or not) b_issue1
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of b_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the b_issue1
        return $lazyload && isset(self::$_lazyload[$array['idb_issue1']]) ? self::$_lazyload[$array['idb_issue1']] :
               new B_Issue1($pdo,$array['idb_issue1'],$lazyload);
    }
    
}

/**
 * @name B_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class B_Issue1 extends Base_B_Issue1
{
    
    // Put your code here...
    
}

/**
 * @name Base_C_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_C_Issue1
{
    // Table name
    const TABLENAME = 'c_issue1';
    
    // Fields name
    const FIELDNAME_A_ISSUE1_IDA_ISSUE1 = 'fk_ida_issue1';
    const FIELDNAME_B_ISSUE1_IDB_ISSUE1 = 'fk_idb_issue1';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int a_issue1's id */
    protected $_a_issue1;
    
    /** @var int b_issue1's id */
    protected $_b_issue1;
    
    /**
     * Construct a c_issue1
     * @param $pdo PDO 
     * @param $a_issue1 int a_issue1's id
     * @param $b_issue1 int b_issue1's id
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$a_issue1,$b_issue1,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_a_issue1 = $a_issue1;
        $this->_b_issue1 = $b_issue1;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$a_issue1.'-'.$b_issue1] = $this;
        }
    }
    
    /**
     * Create a c_issue1
     * @param $pdo PDO 
     * @param $a_issue1 A_Issue1 
     * @param $b_issue1 B_Issue1 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1 
     */
    public static function create(PDO $pdo,A_Issue1 $a_issue1,B_Issue1 $b_issue1,$lazyload=true)
    {
        // Add the c_issue1 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.C_Issue1::TABLENAME.' ('.C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.','.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($a_issue1->getIdA_issue1(),$b_issue1->getIdB_issue1()))) {
            throw new Exception('Error while inserting a c_issue1 into database');
        }
        
        // Construct the c_issue1
        return new C_Issue1($pdo,$a_issue1->getIdA_issue1(),$b_issue1->getIdB_issue1(),$lazyload);
    }
    
    /**
     * Count c_issue1s
     * @param $pdo PDO 
     * @return int Number of c_issue1s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.','.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.') FROM '.C_Issue1::TABLENAME))) {
            throw new Exception('Error while counting c_issue1s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.C_Issue1::TABLENAME.'.'.C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.', '.C_Issue1::TABLENAME.'.'.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.' '.
                             'FROM '.C_Issue1::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a c_issue1
     * @param $pdo PDO 
     * @param $a_issue1 A_Issue1 
     * @param $b_issue1 B_Issue1 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1 
     */
    public static function load(PDO $pdo,A_Issue1 $a_issue1,B_Issue1 $b_issue1,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$a_issue1->getIdA_issue1().'-'.$b_issue1->getIdB_issue1()])) {
            return self::$_lazyload[$a_issue1->getIdA_issue1().'-'.$b_issue1->getIdB_issue1()];
        }
        
        // Load the c_issue1
        $pdoStatement = self::_select($pdo,C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.' = ? AND '.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($a_issue1->getIdA_issue1(),$b_issue1->getIdB_issue1()))) {
            throw new Exception('Error while loading a c_issue1 from database');
        }
        
        // Fetch the c_issue1 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all c_issue1s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1[] Array of c_issue1s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all c_issue1s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the c_issue1s
        $c_issue1s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $c_issue1s;
    }
    
    /**
     * Select all c_issue1s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all c_issue1s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next c_issue1 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($a_issue1,$b_issue1) = $values;
        
        // Construct the c_issue1
        return $lazyload && isset(self::$_lazyload[$a_issue1.'-'.$b_issue1]) ? self::$_lazyload[$a_issue1.'-'.$b_issue1] :
               new C_Issue1($pdo,$a_issue1,$b_issue1,$lazyload);
    }
    
    /**
     * Fetch all the c_issue1s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1[] Array of c_issue1s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $c_issue1s = array();
        while ($c_issue1 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $c_issue1s[] = $c_issue1;
        }
        return $c_issue1s;
    }
    
    /**
     * Equality test
     * @param $c_issue1 C_Issue1 
     * @return bool Objects are equals ?
     */
    public function equals($c_issue1)
    {
        // Test if null
        if ($c_issue1 == null) { return false; }
        
        // Test class
        if (!($c_issue1 instanceof C_Issue1)) { return false; }
        
        // Test ids
        return $this->_a_issue1 == $c_issue1->_a_issue1 && $this->_b_issue1 == $c_issue1->_b_issue1;
    }
    
    /**
     * Check if the c_issue1 exists in database
     * @return bool The c_issue1 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.','.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.') FROM '.C_Issue1::TABLENAME.' WHERE '.C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.' = ? AND '.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getA_issue1()->getIdA_issue1(),$this->getB_issue1()->getIdB_issue1()))) {
            throw new Exception('Error while checking that a c_issue1 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete c_issue1
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated d_issue1s
        $select = $this->selectD_issue1s();
        while ($d_issue1 = D_Issue1::fetch($this->_pdo,$select)) {
            $d_issue1->delete();
        }
        
        // Delete c_issue1
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.C_Issue1::TABLENAME.' WHERE '.C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.' = ? AND '.C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getA_issue1()->getIdA_issue1(),$this->getB_issue1()->getIdB_issue1()))) {
            throw new Exception('Error while deleting a c_issue1 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_a_issue1.'-'.$this->_b_issue1])) {
            unset(self::$_lazyload[$this->_a_issue1.'-'.$this->_b_issue1]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the a_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue1 
     */
    public function getA_issue1($lazyload=true)
    {
        return A_Issue1::load($this->_pdo,$this->_a_issue1,$lazyload);
    }
    
    /**
     * Get the b_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue1 
     */
    public function getB_issue1($lazyload=true)
    {
        return B_Issue1::load($this->_pdo,$this->_b_issue1,$lazyload);
    }
    
    /**
     * Select c_issue1s by a_issue1
     * @param $pdo PDO 
     * @param $a_issue1 A_Issue1 
     * @return PDOStatement 
     */
    public static function selectByA_issue1(PDO $pdo,A_Issue1 $a_issue1)
    {
        $pdoStatement = self::_select($pdo,C_Issue1::FIELDNAME_A_ISSUE1_IDA_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($a_issue1->getIdA_issue1()))) {
            throw new Exception('Error while selecting all c_issue1s by a_issue1 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Select c_issue1s by b_issue1
     * @param $pdo PDO 
     * @param $b_issue1 B_Issue1 
     * @return PDOStatement 
     */
    public static function selectByB_issue1(PDO $pdo,B_Issue1 $b_issue1)
    {
        $pdoStatement = self::_select($pdo,C_Issue1::FIELDNAME_B_ISSUE1_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($b_issue1->getIdB_issue1()))) {
            throw new Exception('Error while selecting all c_issue1s by b_issue1 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Select d_issue1s
     * @return PDOStatement 
     */
    public function selectD_issue1s()
    {
        return D_Issue1::selectByC_issue1($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of c_issue1
     */
    public function __toString()
    {
        return '[C_Issue1 a_issue1="'.$this->_a_issue1.'" b_issue1="'.$this->_b_issue1.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of c_issue1
     */
    public function serialize($serialize=true)
    {
        // Serialize the c_issue1
        $array = array('a_issue1' => $this->_a_issue1,'b_issue1' => $this->_b_issue1);
        
        // Return the serialized (or not) c_issue1
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of c_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the c_issue1
        return $lazyload && isset(self::$_lazyload[$array['a_issue1'].'-'.$array['b_issue1']]) ? self::$_lazyload[$array['a_issue1'].'-'.$array['b_issue1']] :
               new C_Issue1($pdo,$array['a_issue1'],$array['b_issue1'],$lazyload);
    }
    
}

/**
 * @name C_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class C_Issue1 extends Base_C_Issue1
{
    
    // Put your code here...
    
}

/**
 * @name Base_D_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_D_Issue1
{
    // Table name
    const TABLENAME = 'd_issue1';
    
    // Fields name
    const FIELDNAME_IDD_ISSUE1 = 'idd_issue1';
    const FIELDNAME_E = 'e';
    const FIELDNAME_C_ISSUE1_IDA_ISSUE1 = 'fk_ida_issue1';
    const FIELDNAME_C_ISSUE1_IDB_ISSUE1 = 'fk_idb_issue1';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idD_issue1;
    
    /** @var string  */
    protected $_e;
    
    /** @var array c_issue1's ids */
    protected $_c_issue1;
    
    /**
     * Construct a d_issue1
     * @param $pdo PDO 
     * @param $idD_issue1 int 
     * @param $e string 
     * @param $c_issue1 array c_issue1's ids
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idD_issue1,$e,$c_issue1,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idD_issue1 = $idD_issue1;
        $this->_e = $e;
        $this->_c_issue1 = $c_issue1;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idD_issue1] = $this;
        }
    }
    
    /**
     * Create a d_issue1
     * @param $pdo PDO 
     * @param $e string 
     * @param $c_issue1 C_Issue1 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue1 
     */
    public static function create(PDO $pdo,$e,C_Issue1 $c_issue1,$lazyload=true)
    {
        // Add the d_issue1 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.D_Issue1::TABLENAME.' ('.D_Issue1::FIELDNAME_E.','.D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1.','.D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1.') VALUES (?,?,?)');
        if (!$pdoStatement->execute(array($e,$c_issue1->getA_issue1()->getIdA_issue1(),$c_issue1->getB_issue1()->getIdB_issue1()))) {
            throw new Exception('Error while inserting a d_issue1 into database');
        }
        
        // Construct the d_issue1
        return new D_Issue1($pdo,intval($pdo->lastInsertId()),$e,array('a_issue1-ida_issue1' => $c_issue1->getA_issue1()->getIdA_issue1(), 'b_issue1-idb_issue1' => $c_issue1->getB_issue1()->getIdB_issue1()),$lazyload);
    }
    
    /**
     * Count d_issue1s
     * @param $pdo PDO 
     * @return int Number of d_issue1s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.D_Issue1::FIELDNAME_IDD_ISSUE1.') FROM '.D_Issue1::TABLENAME))) {
            throw new Exception('Error while counting d_issue1s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.D_Issue1::TABLENAME.'.'.D_Issue1::FIELDNAME_IDD_ISSUE1.', '.D_Issue1::TABLENAME.'.'.D_Issue1::FIELDNAME_E.', '.D_Issue1::TABLENAME.'.'.D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1.', '.D_Issue1::TABLENAME.'.'.D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1.' '.
                             'FROM '.D_Issue1::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a d_issue1
     * @param $pdo PDO 
     * @param $idD_issue1 int 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue1 
     */
    public static function load(PDO $pdo,$idD_issue1,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idD_issue1])) {
            return self::$_lazyload[$idD_issue1];
        }
        
        // Load the d_issue1
        $pdoStatement = self::_select($pdo,D_Issue1::FIELDNAME_IDD_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($idD_issue1))) {
            throw new Exception('Error while loading a d_issue1 from database');
        }
        
        // Fetch the d_issue1 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,D_Issue1::FIELDNAME_IDD_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->_idD_issue1))) {
            throw new Exception('Error while reloading data of a d_issue1 from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idD_issue1,$e,$c_issue1_a_issue1_ida_issue1,$c_issue1_b_issue1_idb_issue1) = $values;
        
        // Save values
        $this->_e = $e;
        $this->_c_issue1 = array('a_issue1-ida_issue1' => $c_issue1_a_issue1_ida_issue1, 'b_issue1-idb_issue1' => $c_issue1_b_issue1_idb_issue1);
    }
    
    /**
     * Load all d_issue1s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue1[] Array of d_issue1s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all d_issue1s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the d_issue1s
        $d_issue1s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $d_issue1s;
    }
    
    /**
     * Select all d_issue1s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all d_issue1s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next d_issue1 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue1 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idD_issue1,$e,$c_issue1_a_issue1_ida_issue1,$c_issue1_b_issue1_idb_issue1) = $values;
        
        // Construct the d_issue1
        return $lazyload && isset(self::$_lazyload[intval($idD_issue1)]) ? self::$_lazyload[intval($idD_issue1)] :
               new D_Issue1($pdo,intval($idD_issue1),$e,array('a_issue1-ida_issue1' => $c_issue1_a_issue1_ida_issue1, 'b_issue1-idb_issue1' => $c_issue1_b_issue1_idb_issue1),$lazyload);
    }
    
    /**
     * Fetch all the d_issue1s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue1[] Array of d_issue1s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $d_issue1s = array();
        while ($d_issue1 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $d_issue1s[] = $d_issue1;
        }
        return $d_issue1s;
    }
    
    /**
     * Equality test
     * @param $d_issue1 D_Issue1 
     * @return bool Objects are equals ?
     */
    public function equals($d_issue1)
    {
        // Test if null
        if ($d_issue1 == null) { return false; }
        
        // Test class
        if (!($d_issue1 instanceof D_Issue1)) { return false; }
        
        // Test ids
        return $this->_idD_issue1 == $d_issue1->_idD_issue1;
    }
    
    /**
     * Check if the d_issue1 exists in database
     * @return bool The d_issue1 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.D_Issue1::FIELDNAME_IDD_ISSUE1.') FROM '.D_Issue1::TABLENAME.' WHERE '.D_Issue1::FIELDNAME_IDD_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getIdD_issue1()))) {
            throw new Exception('Error while checking that a d_issue1 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete d_issue1
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete d_issue1
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.D_Issue1::TABLENAME.' WHERE '.D_Issue1::FIELDNAME_IDD_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($this->getIdD_issue1()))) {
            throw new Exception('Error while deleting a d_issue1 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idD_issue1])) {
            unset(self::$_lazyload[$this->_idD_issue1]);
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
        $pdoStatement = $this->_pdo->prepare('UPDATE '.D_Issue1::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.D_Issue1::FIELDNAME_IDD_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdD_issue1())))) {
            throw new Exception('Error while updating a d_issue1\'s field in database');
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
        return $this->_set(array(D_Issue1::FIELDNAME_E,D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1,D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1),array($this->_e,$this->_c_issue1['a_issue1-ida_issue1'],$this->_c_issue1['b_issue1-idb_issue1']));
    }
    
    /**
     * Get the idD_issue1
     * @return int 
     */
    public function getIdD_issue1()
    {
        return $this->_idD_issue1;
    }
    
    /**
     * Get the e
     * @return string 
     */
    public function getE()
    {
        return $this->_e;
    }
    
    /**
     * Set the e
     * @param $e string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setE($e,$execute=true)
    {
        // Save into object
        $this->_e = $e;
        
        // Save into database (or not)
        return $execute ? D_Issue1::_set(array(D_Issue1::FIELDNAME_E),array($e)) : true;
    }
    
    /**
     * Get the c_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue1 
     */
    public function getC_issue1($lazyload=true)
    {
        return C_Issue1::load($this->_pdo,A_Issue1::load($this->_pdo,$this->_c_issue1['a_issue1-ida_issue1']),B_Issue1::load($this->_pdo,$this->_c_issue1['b_issue1-idb_issue1']),$lazyload);
    }
    
    /**
     * Get the c_issue1's id
     * @return array c_issue1's ids
     */
    public function getC_issue1Id()
    {
        return $this->_c_issue1;
    }
    
    /**
     * Set the c_issue1
     * @param $c_issue1 C_Issue1 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setC_issue1(C_Issue1 $c_issue1,$execute=true)
    {
        // Save into object
        $this->_c_issue1 = array('a_issue1-ida_issue1' => $c_issue1->getA_issue1()->getIdA_issue1(),'b_issue1-idb_issue1' => $c_issue1->getB_issue1()->getIdB_issue1());
        
        // Save into database (or not)
        return $execute ? D_Issue1::_set(array(D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1,D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1),array($c_issue1->getA_issue1()->getIdA_issue1(),$c_issue1->getB_issue1()->getIdB_issue1())) : true;
    }
    
    /**
     * Set the c_issue1 by id
     * @param $a_issue1 A_Issue1 
     * @param $b_issue1 B_Issue1 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setC_issue1ById(A_Issue1 $a_issue1,B_Issue1 $b_issue1,$execute=true)
    {
        // Save into object
        $this->_c_issue1 = array('a_issue1-ida_issue1' => $a_issue1->getIdA_issue1(),'b_issue1-idb_issue1' => $b_issue1->getIdB_issue1());
        
        // Save into database (or not)
        return $execute ? D_Issue1::_set(array(D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1,D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1),array($a_issue1->getIdA_issue1(),$b_issue1->getIdB_issue1())) : true;
    }
    
    /**
     * Select d_issue1s by c_issue1
     * @param $pdo PDO 
     * @param $c_issue1 C_Issue1 
     * @return PDOStatement 
     */
    public static function selectByC_issue1(PDO $pdo,C_Issue1 $c_issue1)
    {
        $pdoStatement = self::_select($pdo,D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1.' = ? AND '.D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1.' = ?');
        if (!$pdoStatement->execute(array($c_issue1->getA_issue1()->getIdA_issue1(),$c_issue1->getB_issue1()->getIdB_issue1()))) {
            throw new Exception('Error while selecting all d_issue1s by c_issue1 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * ToString
     * @return string String representation of d_issue1
     */
    public function __toString()
    {
        return '[D_Issue1 idD_issue1="'.$this->_idD_issue1.'" e="'.$this->_e.'" c_issue1="a_issue1-ida_issue1 : '.$this->_c_issue1['a_issue1-ida_issue1'].', b_issue1-idb_issue1 : '.$this->_c_issue1['b_issue1-idb_issue1'].'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of d_issue1
     */
    public function serialize($serialize=true)
    {
        // Serialize the d_issue1
        $array = array('idd_issue1' => $this->_idD_issue1,'e' => $this->_e,'c_issue1' => $this->_c_issue1);
        
        // Return the serialized (or not) d_issue1
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of d_issue1
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue1 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the d_issue1
        return $lazyload && isset(self::$_lazyload[$array['idd_issue1']]) ? self::$_lazyload[$array['idd_issue1']] :
               new D_Issue1($pdo,$array['idd_issue1'],$array['e'],$array['c_issue1'],$lazyload);
    }
    
}

/**
 * @name D_Issue1
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class D_Issue1 extends Base_D_Issue1
{
    
    // Put your code here...
    
}

