<?php

/**
 * @name Base_A_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_A_Issue2
{
    // Table name
    const TABLENAME = 'a_issue2';
    
    // Fields name
    const FIELDNAME_IDA_ISSUE2 = 'ida_issue2';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idA_issue2;
    
    /**
     * Construct a a_issue2
     * @param $pdo PDO 
     * @param $idA_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idA_issue2,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idA_issue2 = $idA_issue2;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idA_issue2] = $this;
        }
    }
    
    /**
     * Create a a_issue2
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2 
     */
    public static function create(PDO $pdo,$lazyload=true)
    {
        // Add the a_issue2 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.A_Issue2::TABLENAME.' () VALUES ()');
        if (!$pdoStatement->execute(array())) {
            throw new Exception('Error while inserting a a_issue2 into database');
        }
        
        // Construct the a_issue2
        return new A_Issue2($pdo,intval($pdo->lastInsertId()),$lazyload);
    }
    
    /**
     * Count a_issue2s
     * @param $pdo PDO 
     * @return int Number of a_issue2s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.A_Issue2::FIELDNAME_IDA_ISSUE2.') FROM '.A_Issue2::TABLENAME))) {
            throw new Exception('Error while counting a_issue2s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.A_Issue2::TABLENAME.'.'.A_Issue2::FIELDNAME_IDA_ISSUE2.' '.
                             'FROM '.A_Issue2::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a a_issue2
     * @param $pdo PDO 
     * @param $idA_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2 
     */
    public static function load(PDO $pdo,$idA_issue2,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idA_issue2])) {
            return self::$_lazyload[$idA_issue2];
        }
        
        // Load the a_issue2
        $pdoStatement = self::_select($pdo,A_Issue2::FIELDNAME_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($idA_issue2))) {
            throw new Exception('Error while loading a a_issue2 from database');
        }
        
        // Fetch the a_issue2 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all a_issue2s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2[] Array of a_issue2s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all a_issue2s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the a_issue2s
        $a_issue2s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $a_issue2s;
    }
    
    /**
     * Select all a_issue2s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all a_issue2s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next a_issue2 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idA_issue2) = $values;
        
        // Construct the a_issue2
        return $lazyload && isset(self::$_lazyload[intval($idA_issue2)]) ? self::$_lazyload[intval($idA_issue2)] :
               new A_Issue2($pdo,intval($idA_issue2),$lazyload);
    }
    
    /**
     * Fetch all the a_issue2s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2[] Array of a_issue2s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $a_issue2s = array();
        while ($a_issue2 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $a_issue2s[] = $a_issue2;
        }
        return $a_issue2s;
    }
    
    /**
     * Equality test
     * @param $a_issue2 A_Issue2 
     * @return bool Objects are equals ?
     */
    public function equals($a_issue2)
    {
        // Test if null
        if ($a_issue2 == null) { return false; }
        
        // Test class
        if (!($a_issue2 instanceof A_Issue2)) { return false; }
        
        // Test ids
        return $this->_idA_issue2 == $a_issue2->_idA_issue2;
    }
    
    /**
     * Check if the a_issue2 exists in database
     * @return bool The a_issue2 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.A_Issue2::FIELDNAME_IDA_ISSUE2.') FROM '.A_Issue2::TABLENAME.' WHERE '.A_Issue2::FIELDNAME_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdA_issue2()))) {
            throw new Exception('Error while checking that a a_issue2 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete a_issue2
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated b_issue2s
        $select = $this->selectB_issue2s();
        while ($b_issue2 = B_Issue2::fetch($this->_pdo,$select)) {
            $b_issue2->delete();
        }
        
        // Delete a_issue2
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.A_Issue2::TABLENAME.' WHERE '.A_Issue2::FIELDNAME_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdA_issue2()))) {
            throw new Exception('Error while deleting a a_issue2 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idA_issue2])) {
            unset(self::$_lazyload[$this->_idA_issue2]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the idA_issue2
     * @return int 
     */
    public function getIdA_issue2()
    {
        return $this->_idA_issue2;
    }
    
    /**
     * Select b_issue2s
     * @return PDOStatement 
     */
    public function selectB_issue2s()
    {
        return B_Issue2::selectByA_issue2($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of a_issue2
     */
    public function __toString()
    {
        return '[A_Issue2 idA_issue2="'.$this->_idA_issue2.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of a_issue2
     */
    public function serialize($serialize=true)
    {
        // Serialize the a_issue2
        $array = array('ida_issue2' => $this->_idA_issue2);
        
        // Return the serialized (or not) a_issue2
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of a_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the a_issue2
        return $lazyload && isset(self::$_lazyload[$array['ida_issue2']]) ? self::$_lazyload[$array['ida_issue2']] :
               new A_Issue2($pdo,$array['ida_issue2'],$lazyload);
    }
    
}

/**
 * @name A_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class A_Issue2 extends Base_A_Issue2
{
    
    // Put your code here...
    
}

/**
 * @name Base_B_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_B_Issue2
{
    // Table name
    const TABLENAME = 'b_issue2';
    
    // Fields name
    const FIELDNAME_A_ISSUE2_IDA_ISSUE2 = 'fk_ida_issue2';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int a_issue2's id */
    protected $_a_issue2;
    
    /**
     * Construct a b_issue2
     * @param $pdo PDO 
     * @param $a_issue2 int a_issue2's id
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$a_issue2,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_a_issue2 = $a_issue2;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$a_issue2] = $this;
        }
    }
    
    /**
     * Create a b_issue2
     * @param $pdo PDO 
     * @param $a_issue2 A_Issue2 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2 
     */
    public static function create(PDO $pdo,A_Issue2 $a_issue2,$lazyload=true)
    {
        // Add the b_issue2 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.B_Issue2::TABLENAME.' ('.B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.') VALUES (?)');
        if (!$pdoStatement->execute(array($a_issue2->getIdA_issue2()))) {
            throw new Exception('Error while inserting a b_issue2 into database');
        }
        
        // Construct the b_issue2
        return new B_Issue2($pdo,$a_issue2->getIdA_issue2(),$lazyload);
    }
    
    /**
     * Count b_issue2s
     * @param $pdo PDO 
     * @return int Number of b_issue2s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.') FROM '.B_Issue2::TABLENAME))) {
            throw new Exception('Error while counting b_issue2s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.B_Issue2::TABLENAME.'.'.B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.' '.
                             'FROM '.B_Issue2::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a b_issue2
     * @param $pdo PDO 
     * @param $a_issue2 A_Issue2 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2 
     */
    public static function load(PDO $pdo,A_Issue2 $a_issue2,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$a_issue2->getIdA_issue2()])) {
            return self::$_lazyload[$a_issue2->getIdA_issue2()];
        }
        
        // Load the b_issue2
        $pdoStatement = self::_select($pdo,B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($a_issue2->getIdA_issue2()))) {
            throw new Exception('Error while loading a b_issue2 from database');
        }
        
        // Fetch the b_issue2 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all b_issue2s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2[] Array of b_issue2s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all b_issue2s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the b_issue2s
        $b_issue2s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $b_issue2s;
    }
    
    /**
     * Select all b_issue2s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all b_issue2s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next b_issue2 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($a_issue2) = $values;
        
        // Construct the b_issue2
        return $lazyload && isset(self::$_lazyload[$a_issue2]) ? self::$_lazyload[$a_issue2] :
               new B_Issue2($pdo,$a_issue2,$lazyload);
    }
    
    /**
     * Fetch all the b_issue2s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2[] Array of b_issue2s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $b_issue2s = array();
        while ($b_issue2 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $b_issue2s[] = $b_issue2;
        }
        return $b_issue2s;
    }
    
    /**
     * Equality test
     * @param $b_issue2 B_Issue2 
     * @return bool Objects are equals ?
     */
    public function equals($b_issue2)
    {
        // Test if null
        if ($b_issue2 == null) { return false; }
        
        // Test class
        if (!($b_issue2 instanceof B_Issue2)) { return false; }
        
        // Test ids
        return $this->_a_issue2 == $b_issue2->_a_issue2;
    }
    
    /**
     * Check if the b_issue2 exists in database
     * @return bool The b_issue2 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.') FROM '.B_Issue2::TABLENAME.' WHERE '.B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getA_issue2()->getIdA_issue2()))) {
            throw new Exception('Error while checking that a b_issue2 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete b_issue2
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated f_issue2s
        $select = $this->selectF_issue2s();
        while ($f_issue2 = F_Issue2::fetch($this->_pdo,$select)) {
            $f_issue2->delete();
        }
        
        // Delete b_issue2
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.B_Issue2::TABLENAME.' WHERE '.B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getA_issue2()->getIdA_issue2()))) {
            throw new Exception('Error while deleting a b_issue2 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_a_issue2])) {
            unset(self::$_lazyload[$this->_a_issue2]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the a_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return A_Issue2 
     */
    public function getA_issue2($lazyload=true)
    {
        return A_Issue2::load($this->_pdo,$this->_a_issue2,$lazyload);
    }
    
    /**
     * Select b_issue2s by a_issue2
     * @param $pdo PDO 
     * @param $a_issue2 A_Issue2 
     * @return PDOStatement 
     */
    public static function selectByA_issue2(PDO $pdo,A_Issue2 $a_issue2)
    {
        $pdoStatement = self::_select($pdo,B_Issue2::FIELDNAME_A_ISSUE2_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($a_issue2->getIdA_issue2()))) {
            throw new Exception('Error while selecting all b_issue2s by a_issue2 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Select f_issue2s
     * @return PDOStatement 
     */
    public function selectF_issue2s()
    {
        return F_Issue2::selectByB_issue2($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of b_issue2
     */
    public function __toString()
    {
        return '[B_Issue2 a_issue2="'.$this->_a_issue2.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of b_issue2
     */
    public function serialize($serialize=true)
    {
        // Serialize the b_issue2
        $array = array('a_issue2' => $this->_a_issue2);
        
        // Return the serialized (or not) b_issue2
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of b_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the b_issue2
        return $lazyload && isset(self::$_lazyload[$array['a_issue2']]) ? self::$_lazyload[$array['a_issue2']] :
               new B_Issue2($pdo,$array['a_issue2'],$lazyload);
    }
    
}

/**
 * @name B_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class B_Issue2 extends Base_B_Issue2
{
    
    // Put your code here...
    
}

/**
 * @name Base_C_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_C_Issue2
{
    // Table name
    const TABLENAME = 'c_issue2';
    
    // Fields name
    const FIELDNAME_IDC_ISSUE2 = 'idc_issue2';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idC_issue2;
    
    /**
     * Construct a c_issue2
     * @param $pdo PDO 
     * @param $idC_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idC_issue2,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idC_issue2 = $idC_issue2;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idC_issue2] = $this;
        }
    }
    
    /**
     * Create a c_issue2
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2 
     */
    public static function create(PDO $pdo,$lazyload=true)
    {
        // Add the c_issue2 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.C_Issue2::TABLENAME.' () VALUES ()');
        if (!$pdoStatement->execute(array())) {
            throw new Exception('Error while inserting a c_issue2 into database');
        }
        
        // Construct the c_issue2
        return new C_Issue2($pdo,intval($pdo->lastInsertId()),$lazyload);
    }
    
    /**
     * Count c_issue2s
     * @param $pdo PDO 
     * @return int Number of c_issue2s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.C_Issue2::FIELDNAME_IDC_ISSUE2.') FROM '.C_Issue2::TABLENAME))) {
            throw new Exception('Error while counting c_issue2s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.C_Issue2::TABLENAME.'.'.C_Issue2::FIELDNAME_IDC_ISSUE2.' '.
                             'FROM '.C_Issue2::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a c_issue2
     * @param $pdo PDO 
     * @param $idC_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2 
     */
    public static function load(PDO $pdo,$idC_issue2,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idC_issue2])) {
            return self::$_lazyload[$idC_issue2];
        }
        
        // Load the c_issue2
        $pdoStatement = self::_select($pdo,C_Issue2::FIELDNAME_IDC_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($idC_issue2))) {
            throw new Exception('Error while loading a c_issue2 from database');
        }
        
        // Fetch the c_issue2 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all c_issue2s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2[] Array of c_issue2s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all c_issue2s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the c_issue2s
        $c_issue2s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $c_issue2s;
    }
    
    /**
     * Select all c_issue2s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all c_issue2s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next c_issue2 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idC_issue2) = $values;
        
        // Construct the c_issue2
        return $lazyload && isset(self::$_lazyload[intval($idC_issue2)]) ? self::$_lazyload[intval($idC_issue2)] :
               new C_Issue2($pdo,intval($idC_issue2),$lazyload);
    }
    
    /**
     * Fetch all the c_issue2s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2[] Array of c_issue2s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $c_issue2s = array();
        while ($c_issue2 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $c_issue2s[] = $c_issue2;
        }
        return $c_issue2s;
    }
    
    /**
     * Equality test
     * @param $c_issue2 C_Issue2 
     * @return bool Objects are equals ?
     */
    public function equals($c_issue2)
    {
        // Test if null
        if ($c_issue2 == null) { return false; }
        
        // Test class
        if (!($c_issue2 instanceof C_Issue2)) { return false; }
        
        // Test ids
        return $this->_idC_issue2 == $c_issue2->_idC_issue2;
    }
    
    /**
     * Check if the c_issue2 exists in database
     * @return bool The c_issue2 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.C_Issue2::FIELDNAME_IDC_ISSUE2.') FROM '.C_Issue2::TABLENAME.' WHERE '.C_Issue2::FIELDNAME_IDC_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdC_issue2()))) {
            throw new Exception('Error while checking that a c_issue2 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete c_issue2
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated e_issue2s
        $select = $this->selectE_issue2s();
        while ($e_issue2 = E_Issue2::fetch($this->_pdo,$select)) {
            $e_issue2->delete();
        }
        
        // Delete c_issue2
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.C_Issue2::TABLENAME.' WHERE '.C_Issue2::FIELDNAME_IDC_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdC_issue2()))) {
            throw new Exception('Error while deleting a c_issue2 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idC_issue2])) {
            unset(self::$_lazyload[$this->_idC_issue2]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the idC_issue2
     * @return int 
     */
    public function getIdC_issue2()
    {
        return $this->_idC_issue2;
    }
    
    /**
     * Select e_issue2s
     * @return PDOStatement 
     */
    public function selectE_issue2s()
    {
        return E_Issue2::selectByC_issue2($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of c_issue2
     */
    public function __toString()
    {
        return '[C_Issue2 idC_issue2="'.$this->_idC_issue2.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of c_issue2
     */
    public function serialize($serialize=true)
    {
        // Serialize the c_issue2
        $array = array('idc_issue2' => $this->_idC_issue2);
        
        // Return the serialized (or not) c_issue2
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of c_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the c_issue2
        return $lazyload && isset(self::$_lazyload[$array['idc_issue2']]) ? self::$_lazyload[$array['idc_issue2']] :
               new C_Issue2($pdo,$array['idc_issue2'],$lazyload);
    }
    
}

/**
 * @name C_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class C_Issue2 extends Base_C_Issue2
{
    
    // Put your code here...
    
}

/**
 * @name Base_D_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_D_Issue2
{
    // Table name
    const TABLENAME = 'd_issue2';
    
    // Fields name
    const FIELDNAME_IDD_ISSUE2 = 'idd_issue2';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idD_issue2;
    
    /**
     * Construct a d_issue2
     * @param $pdo PDO 
     * @param $idD_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idD_issue2,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idD_issue2 = $idD_issue2;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idD_issue2] = $this;
        }
    }
    
    /**
     * Create a d_issue2
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2 
     */
    public static function create(PDO $pdo,$lazyload=true)
    {
        // Add the d_issue2 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.D_Issue2::TABLENAME.' () VALUES ()');
        if (!$pdoStatement->execute(array())) {
            throw new Exception('Error while inserting a d_issue2 into database');
        }
        
        // Construct the d_issue2
        return new D_Issue2($pdo,intval($pdo->lastInsertId()),$lazyload);
    }
    
    /**
     * Count d_issue2s
     * @param $pdo PDO 
     * @return int Number of d_issue2s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.D_Issue2::FIELDNAME_IDD_ISSUE2.') FROM '.D_Issue2::TABLENAME))) {
            throw new Exception('Error while counting d_issue2s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.D_Issue2::TABLENAME.'.'.D_Issue2::FIELDNAME_IDD_ISSUE2.' '.
                             'FROM '.D_Issue2::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a d_issue2
     * @param $pdo PDO 
     * @param $idD_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2 
     */
    public static function load(PDO $pdo,$idD_issue2,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idD_issue2])) {
            return self::$_lazyload[$idD_issue2];
        }
        
        // Load the d_issue2
        $pdoStatement = self::_select($pdo,D_Issue2::FIELDNAME_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($idD_issue2))) {
            throw new Exception('Error while loading a d_issue2 from database');
        }
        
        // Fetch the d_issue2 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all d_issue2s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2[] Array of d_issue2s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all d_issue2s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the d_issue2s
        $d_issue2s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $d_issue2s;
    }
    
    /**
     * Select all d_issue2s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all d_issue2s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next d_issue2 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idD_issue2) = $values;
        
        // Construct the d_issue2
        return $lazyload && isset(self::$_lazyload[intval($idD_issue2)]) ? self::$_lazyload[intval($idD_issue2)] :
               new D_Issue2($pdo,intval($idD_issue2),$lazyload);
    }
    
    /**
     * Fetch all the d_issue2s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2[] Array of d_issue2s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $d_issue2s = array();
        while ($d_issue2 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $d_issue2s[] = $d_issue2;
        }
        return $d_issue2s;
    }
    
    /**
     * Equality test
     * @param $d_issue2 D_Issue2 
     * @return bool Objects are equals ?
     */
    public function equals($d_issue2)
    {
        // Test if null
        if ($d_issue2 == null) { return false; }
        
        // Test class
        if (!($d_issue2 instanceof D_Issue2)) { return false; }
        
        // Test ids
        return $this->_idD_issue2 == $d_issue2->_idD_issue2;
    }
    
    /**
     * Check if the d_issue2 exists in database
     * @return bool The d_issue2 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.D_Issue2::FIELDNAME_IDD_ISSUE2.') FROM '.D_Issue2::TABLENAME.' WHERE '.D_Issue2::FIELDNAME_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdD_issue2()))) {
            throw new Exception('Error while checking that a d_issue2 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete d_issue2
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated e_issue2s
        $select = $this->selectE_issue2s();
        while ($e_issue2 = E_Issue2::fetch($this->_pdo,$select)) {
            $e_issue2->delete();
        }
        
        // Delete d_issue2
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.D_Issue2::TABLENAME.' WHERE '.D_Issue2::FIELDNAME_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdD_issue2()))) {
            throw new Exception('Error while deleting a d_issue2 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idD_issue2])) {
            unset(self::$_lazyload[$this->_idD_issue2]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the idD_issue2
     * @return int 
     */
    public function getIdD_issue2()
    {
        return $this->_idD_issue2;
    }
    
    /**
     * Select e_issue2s
     * @return PDOStatement 
     */
    public function selectE_issue2s()
    {
        return E_Issue2::selectByD_issue2($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of d_issue2
     */
    public function __toString()
    {
        return '[D_Issue2 idD_issue2="'.$this->_idD_issue2.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of d_issue2
     */
    public function serialize($serialize=true)
    {
        // Serialize the d_issue2
        $array = array('idd_issue2' => $this->_idD_issue2);
        
        // Return the serialized (or not) d_issue2
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of d_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the d_issue2
        return $lazyload && isset(self::$_lazyload[$array['idd_issue2']]) ? self::$_lazyload[$array['idd_issue2']] :
               new D_Issue2($pdo,$array['idd_issue2'],$lazyload);
    }
    
}

/**
 * @name D_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class D_Issue2 extends Base_D_Issue2
{
    
    // Put your code here...
    
}

/**
 * @name Base_E_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_E_Issue2
{
    // Table name
    const TABLENAME = 'e_issue2';
    
    // Fields name
    const FIELDNAME_C_ISSUE2_IDC_ISSUE2 = 'fk_idc_issue2';
    const FIELDNAME_D_ISSUE2_IDD_ISSUE2 = 'fk_idd_issue2';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int c_issue2's id */
    protected $_c_issue2;
    
    /** @var int d_issue2's id */
    protected $_d_issue2;
    
    /**
     * Construct a e_issue2
     * @param $pdo PDO 
     * @param $c_issue2 int c_issue2's id
     * @param $d_issue2 int d_issue2's id
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$c_issue2,$d_issue2,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_c_issue2 = $c_issue2;
        $this->_d_issue2 = $d_issue2;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$c_issue2.'-'.$d_issue2] = $this;
        }
    }
    
    /**
     * Create a e_issue2
     * @param $pdo PDO 
     * @param $c_issue2 C_Issue2 
     * @param $d_issue2 D_Issue2 
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2 
     */
    public static function create(PDO $pdo,C_Issue2 $c_issue2,D_Issue2 $d_issue2,$lazyload=true)
    {
        // Add the e_issue2 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.E_Issue2::TABLENAME.' ('.E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.','.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($c_issue2->getIdC_issue2(),$d_issue2->getIdD_issue2()))) {
            throw new Exception('Error while inserting a e_issue2 into database');
        }
        
        // Construct the e_issue2
        return new E_Issue2($pdo,$c_issue2->getIdC_issue2(),$d_issue2->getIdD_issue2(),$lazyload);
    }
    
    /**
     * Count e_issue2s
     * @param $pdo PDO 
     * @return int Number of e_issue2s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.','.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.') FROM '.E_Issue2::TABLENAME))) {
            throw new Exception('Error while counting e_issue2s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.E_Issue2::TABLENAME.'.'.E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.', '.E_Issue2::TABLENAME.'.'.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.' '.
                             'FROM '.E_Issue2::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a e_issue2
     * @param $pdo PDO 
     * @param $c_issue2 C_Issue2 
     * @param $d_issue2 D_Issue2 
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2 
     */
    public static function load(PDO $pdo,C_Issue2 $c_issue2,D_Issue2 $d_issue2,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$c_issue2->getIdC_issue2().'-'.$d_issue2->getIdD_issue2()])) {
            return self::$_lazyload[$c_issue2->getIdC_issue2().'-'.$d_issue2->getIdD_issue2()];
        }
        
        // Load the e_issue2
        $pdoStatement = self::_select($pdo,E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.' = ? AND '.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($c_issue2->getIdC_issue2(),$d_issue2->getIdD_issue2()))) {
            throw new Exception('Error while loading a e_issue2 from database');
        }
        
        // Fetch the e_issue2 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Load all e_issue2s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2[] Array of e_issue2s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all e_issue2s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the e_issue2s
        $e_issue2s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $e_issue2s;
    }
    
    /**
     * Select all e_issue2s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all e_issue2s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next e_issue2 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($c_issue2,$d_issue2) = $values;
        
        // Construct the e_issue2
        return $lazyload && isset(self::$_lazyload[$c_issue2.'-'.$d_issue2]) ? self::$_lazyload[$c_issue2.'-'.$d_issue2] :
               new E_Issue2($pdo,$c_issue2,$d_issue2,$lazyload);
    }
    
    /**
     * Fetch all the e_issue2s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2[] Array of e_issue2s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $e_issue2s = array();
        while ($e_issue2 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $e_issue2s[] = $e_issue2;
        }
        return $e_issue2s;
    }
    
    /**
     * Equality test
     * @param $e_issue2 E_Issue2 
     * @return bool Objects are equals ?
     */
    public function equals($e_issue2)
    {
        // Test if null
        if ($e_issue2 == null) { return false; }
        
        // Test class
        if (!($e_issue2 instanceof E_Issue2)) { return false; }
        
        // Test ids
        return $this->_c_issue2 == $e_issue2->_c_issue2 && $this->_d_issue2 == $e_issue2->_d_issue2;
    }
    
    /**
     * Check if the e_issue2 exists in database
     * @return bool The e_issue2 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.','.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.') FROM '.E_Issue2::TABLENAME.' WHERE '.E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.' = ? AND '.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getC_issue2()->getIdC_issue2(),$this->getD_issue2()->getIdD_issue2()))) {
            throw new Exception('Error while checking that a e_issue2 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete e_issue2
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated f_issue2s
        $select = $this->selectF_issue2s();
        while ($f_issue2 = F_Issue2::fetch($this->_pdo,$select)) {
            $f_issue2->delete();
        }
        
        // Delete e_issue2
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.E_Issue2::TABLENAME.' WHERE '.E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.' = ? AND '.E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getC_issue2()->getIdC_issue2(),$this->getD_issue2()->getIdD_issue2()))) {
            throw new Exception('Error while deleting a e_issue2 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_c_issue2.'-'.$this->_d_issue2])) {
            unset(self::$_lazyload[$this->_c_issue2.'-'.$this->_d_issue2]);
        }
        
        // Successful operation ?
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Get the c_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return C_Issue2 
     */
    public function getC_issue2($lazyload=true)
    {
        return C_Issue2::load($this->_pdo,$this->_c_issue2,$lazyload);
    }
    
    /**
     * Get the d_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return D_Issue2 
     */
    public function getD_issue2($lazyload=true)
    {
        return D_Issue2::load($this->_pdo,$this->_d_issue2,$lazyload);
    }
    
    /**
     * Select e_issue2s by c_issue2
     * @param $pdo PDO 
     * @param $c_issue2 C_Issue2 
     * @return PDOStatement 
     */
    public static function selectByC_issue2(PDO $pdo,C_Issue2 $c_issue2)
    {
        $pdoStatement = self::_select($pdo,E_Issue2::FIELDNAME_C_ISSUE2_IDC_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($c_issue2->getIdC_issue2()))) {
            throw new Exception('Error while selecting all e_issue2s by c_issue2 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Select e_issue2s by d_issue2
     * @param $pdo PDO 
     * @param $d_issue2 D_Issue2 
     * @return PDOStatement 
     */
    public static function selectByD_issue2(PDO $pdo,D_Issue2 $d_issue2)
    {
        $pdoStatement = self::_select($pdo,E_Issue2::FIELDNAME_D_ISSUE2_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($d_issue2->getIdD_issue2()))) {
            throw new Exception('Error while selecting all e_issue2s by d_issue2 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Select f_issue2s
     * @return PDOStatement 
     */
    public function selectF_issue2s()
    {
        return F_Issue2::selectByE_issue2($this->_pdo,$this);
    }
    
    /**
     * ToString
     * @return string String representation of e_issue2
     */
    public function __toString()
    {
        return '[E_Issue2 c_issue2="'.$this->_c_issue2.'" d_issue2="'.$this->_d_issue2.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of e_issue2
     */
    public function serialize($serialize=true)
    {
        // Serialize the e_issue2
        $array = array('c_issue2' => $this->_c_issue2,'d_issue2' => $this->_d_issue2);
        
        // Return the serialized (or not) e_issue2
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of e_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the e_issue2
        return $lazyload && isset(self::$_lazyload[$array['c_issue2'].'-'.$array['d_issue2']]) ? self::$_lazyload[$array['c_issue2'].'-'.$array['d_issue2']] :
               new E_Issue2($pdo,$array['c_issue2'],$array['d_issue2'],$lazyload);
    }
    
}

/**
 * @name E_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class E_Issue2 extends Base_E_Issue2
{
    
    // Put your code here...
    
}

/**
 * @name Base_F_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.cyrilperrin.fr/webprojecthelper/)
 */
abstract class Base_F_Issue2
{
    // Table name
    const TABLENAME = 'f_issue2';
    
    // Fields name
    const FIELDNAME_IDF_ISSUE2 = 'idf_issue2';
    const FIELDNAME_B_ISSUE2_IDA_ISSUE2 = 'fk_ida_issue2';
    const FIELDNAME_E_ISSUE2_IDC_ISSUE2 = 'fk_idc_issue2';
    const FIELDNAME_E_ISSUE2_IDD_ISSUE2 = 'fk_idd_issue2';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idF_issue2;
    
    /** @var int b_issue2's id */
    protected $_b_issue2;
    
    /** @var array e_issue2's ids */
    protected $_e_issue2;
    
    /**
     * Construct a f_issue2
     * @param $pdo PDO 
     * @param $idF_issue2 int 
     * @param $b_issue2 int b_issue2's id
     * @param $e_issue2 array e_issue2's ids
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idF_issue2,$b_issue2,$e_issue2,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idF_issue2 = $idF_issue2;
        $this->_b_issue2 = $b_issue2;
        $this->_e_issue2 = $e_issue2;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idF_issue2] = $this;
        }
    }
    
    /**
     * Create a f_issue2
     * @param $pdo PDO 
     * @param $b_issue2 B_Issue2 
     * @param $e_issue2 E_Issue2 
     * @param $lazyload bool Enable lazy load ?
     * @return F_Issue2 
     */
    public static function create(PDO $pdo,B_Issue2 $b_issue2,E_Issue2 $e_issue2,$lazyload=true)
    {
        // Add the f_issue2 into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.F_Issue2::TABLENAME.' ('.F_Issue2::FIELDNAME_B_ISSUE2_IDA_ISSUE2.','.F_Issue2::FIELDNAME_E_ISSUE2_IDC_ISSUE2.','.F_Issue2::FIELDNAME_E_ISSUE2_IDD_ISSUE2.') VALUES (?,?,?)');
        if (!$pdoStatement->execute(array($b_issue2->getA_issue2()->getIdA_issue2(),$e_issue2->getC_issue2()->getIdC_issue2(),$e_issue2->getD_issue2()->getIdD_issue2()))) {
            throw new Exception('Error while inserting a f_issue2 into database');
        }
        
        // Construct the f_issue2
        return new F_Issue2($pdo,intval($pdo->lastInsertId()),$b_issue2->getA_issue2()->getIdA_issue2(),array('c_issue2-idc_issue2' => $e_issue2->getC_issue2()->getIdC_issue2(), 'd_issue2-idd_issue2' => $e_issue2->getD_issue2()->getIdD_issue2()),$lazyload);
    }
    
    /**
     * Count f_issue2s
     * @param $pdo PDO 
     * @return int Number of f_issue2s
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.F_Issue2::FIELDNAME_IDF_ISSUE2.') FROM '.F_Issue2::TABLENAME))) {
            throw new Exception('Error while counting f_issue2s in database');
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
        return $pdo->prepare('SELECT DISTINCT '.F_Issue2::TABLENAME.'.'.F_Issue2::FIELDNAME_IDF_ISSUE2.', '.F_Issue2::TABLENAME.'.'.F_Issue2::FIELDNAME_B_ISSUE2_IDA_ISSUE2.', '.F_Issue2::TABLENAME.'.'.F_Issue2::FIELDNAME_E_ISSUE2_IDC_ISSUE2.', '.F_Issue2::TABLENAME.'.'.F_Issue2::FIELDNAME_E_ISSUE2_IDD_ISSUE2.' '.
                             'FROM '.F_Issue2::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a f_issue2
     * @param $pdo PDO 
     * @param $idF_issue2 int 
     * @param $lazyload bool Enable lazy load ?
     * @return F_Issue2 
     */
    public static function load(PDO $pdo,$idF_issue2,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idF_issue2])) {
            return self::$_lazyload[$idF_issue2];
        }
        
        // Load the f_issue2
        $pdoStatement = self::_select($pdo,F_Issue2::FIELDNAME_IDF_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($idF_issue2))) {
            throw new Exception('Error while loading a f_issue2 from database');
        }
        
        // Fetch the f_issue2 from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,F_Issue2::FIELDNAME_IDF_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->_idF_issue2))) {
            throw new Exception('Error while reloading data of a f_issue2 from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idF_issue2,$b_issue2,$e_issue2_c_issue2_idc_issue2,$e_issue2_d_issue2_idd_issue2) = $values;
        
        // Save values
        $this->_b_issue2 = $b_issue2;
        $this->_e_issue2 = array('c_issue2-idc_issue2' => $e_issue2_c_issue2_idc_issue2, 'd_issue2-idd_issue2' => $e_issue2_d_issue2_idd_issue2);
    }
    
    /**
     * Load all f_issue2s
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return F_Issue2[] Array of f_issue2s
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all f_issue2s
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the f_issue2s
        $f_issue2s = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $f_issue2s;
    }
    
    /**
     * Select all f_issue2s
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all f_issue2s from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next f_issue2 from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return F_Issue2 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idF_issue2,$b_issue2,$e_issue2_c_issue2_idc_issue2,$e_issue2_d_issue2_idd_issue2) = $values;
        
        // Construct the f_issue2
        return $lazyload && isset(self::$_lazyload[intval($idF_issue2)]) ? self::$_lazyload[intval($idF_issue2)] :
               new F_Issue2($pdo,intval($idF_issue2),$b_issue2,array('c_issue2-idc_issue2' => $e_issue2_c_issue2_idc_issue2, 'd_issue2-idd_issue2' => $e_issue2_d_issue2_idd_issue2),$lazyload);
    }
    
    /**
     * Fetch all the f_issue2s from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return F_Issue2[] Array of f_issue2s
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $f_issue2s = array();
        while ($f_issue2 = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $f_issue2s[] = $f_issue2;
        }
        return $f_issue2s;
    }
    
    /**
     * Equality test
     * @param $f_issue2 F_Issue2 
     * @return bool Objects are equals ?
     */
    public function equals($f_issue2)
    {
        // Test if null
        if ($f_issue2 == null) { return false; }
        
        // Test class
        if (!($f_issue2 instanceof F_Issue2)) { return false; }
        
        // Test ids
        return $this->_idF_issue2 == $f_issue2->_idF_issue2;
    }
    
    /**
     * Check if the f_issue2 exists in database
     * @return bool The f_issue2 exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.F_Issue2::FIELDNAME_IDF_ISSUE2.') FROM '.F_Issue2::TABLENAME.' WHERE '.F_Issue2::FIELDNAME_IDF_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdF_issue2()))) {
            throw new Exception('Error while checking that a f_issue2 exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete f_issue2
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete f_issue2
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.F_Issue2::TABLENAME.' WHERE '.F_Issue2::FIELDNAME_IDF_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($this->getIdF_issue2()))) {
            throw new Exception('Error while deleting a f_issue2 in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idF_issue2])) {
            unset(self::$_lazyload[$this->_idF_issue2]);
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
        $pdoStatement = $this->_pdo->prepare('UPDATE '.F_Issue2::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.F_Issue2::FIELDNAME_IDF_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdF_issue2())))) {
            throw new Exception('Error while updating a f_issue2\'s field in database');
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
        return $this->_set(array(F_Issue2::FIELDNAME_B_ISSUE2_IDA_ISSUE2,F_Issue2::FIELDNAME_E_ISSUE2_IDC_ISSUE2,F_Issue2::FIELDNAME_E_ISSUE2_IDD_ISSUE2),array($this->_b_issue2,$this->_e_issue2['c_issue2-idc_issue2'],$this->_e_issue2['d_issue2-idd_issue2']));
    }
    
    /**
     * Get the idF_issue2
     * @return int 
     */
    public function getIdF_issue2()
    {
        return $this->_idF_issue2;
    }
    
    /**
     * Get the b_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return B_Issue2 
     */
    public function getB_issue2($lazyload=true)
    {
        return B_Issue2::load($this->_pdo,A_Issue2::load($this->_pdo,$this->_b_issue2),$lazyload);
    }
    
    /**
     * Get the b_issue2's id
     * @return int b_issue2's id
     */
    public function getB_issue2Id()
    {
        return $this->_b_issue2;
    }
    
    /**
     * Set the b_issue2
     * @param $b_issue2 B_Issue2 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setB_issue2(B_Issue2 $b_issue2,$execute=true)
    {
        // Save into object
        $this->_b_issue2 = $b_issue2->getA_issue2()->getIdA_issue2();
        
        // Save into database (or not)
        return $execute ? F_Issue2::_set(array(F_Issue2::FIELDNAME_B_ISSUE2_IDA_ISSUE2),array($b_issue2->getA_issue2()->getIdA_issue2())) : true;
    }
    
    /**
     * Set the b_issue2 by id
     * @param $a_issue2 A_Issue2 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setB_issue2ById(A_Issue2 $a_issue2,$execute=true)
    {
        // Save into object
        $this->_b_issue2 = $a_issue2->getIdA_issue2();
        
        // Save into database (or not)
        return $execute ? F_Issue2::_set(array(F_Issue2::FIELDNAME_B_ISSUE2_IDA_ISSUE2),array($a_issue2->getIdA_issue2())) : true;
    }
    
    /**
     * Select f_issue2s by b_issue2
     * @param $pdo PDO 
     * @param $b_issue2 B_Issue2 
     * @return PDOStatement 
     */
    public static function selectByB_issue2(PDO $pdo,B_Issue2 $b_issue2)
    {
        $pdoStatement = self::_select($pdo,F_Issue2::FIELDNAME_B_ISSUE2_IDA_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($b_issue2->getA_issue2()->getIdA_issue2()))) {
            throw new Exception('Error while selecting all f_issue2s by b_issue2 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * Get the e_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return E_Issue2 
     */
    public function getE_issue2($lazyload=true)
    {
        return E_Issue2::load($this->_pdo,C_Issue2::load($this->_pdo,$this->_e_issue2['c_issue2-idc_issue2']),D_Issue2::load($this->_pdo,$this->_e_issue2['d_issue2-idd_issue2']),$lazyload);
    }
    
    /**
     * Get the e_issue2's id
     * @return array e_issue2's ids
     */
    public function getE_issue2Id()
    {
        return $this->_e_issue2;
    }
    
    /**
     * Set the e_issue2
     * @param $e_issue2 E_Issue2 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setE_issue2(E_Issue2 $e_issue2,$execute=true)
    {
        // Save into object
        $this->_e_issue2 = array('c_issue2-idc_issue2' => $e_issue2->getC_issue2()->getIdC_issue2(),'d_issue2-idd_issue2' => $e_issue2->getD_issue2()->getIdD_issue2());
        
        // Save into database (or not)
        return $execute ? F_Issue2::_set(array(F_Issue2::FIELDNAME_E_ISSUE2_IDC_ISSUE2,F_Issue2::FIELDNAME_E_ISSUE2_IDD_ISSUE2),array($e_issue2->getC_issue2()->getIdC_issue2(),$e_issue2->getD_issue2()->getIdD_issue2())) : true;
    }
    
    /**
     * Set the e_issue2 by id
     * @param $c_issue2 C_Issue2 
     * @param $d_issue2 D_Issue2 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setE_issue2ById(C_Issue2 $c_issue2,D_Issue2 $d_issue2,$execute=true)
    {
        // Save into object
        $this->_e_issue2 = array('c_issue2-idc_issue2' => $c_issue2->getIdC_issue2(),'d_issue2-idd_issue2' => $d_issue2->getIdD_issue2());
        
        // Save into database (or not)
        return $execute ? F_Issue2::_set(array(F_Issue2::FIELDNAME_E_ISSUE2_IDC_ISSUE2,F_Issue2::FIELDNAME_E_ISSUE2_IDD_ISSUE2),array($c_issue2->getIdC_issue2(),$d_issue2->getIdD_issue2())) : true;
    }
    
    /**
     * Select f_issue2s by e_issue2
     * @param $pdo PDO 
     * @param $e_issue2 E_Issue2 
     * @return PDOStatement 
     */
    public static function selectByE_issue2(PDO $pdo,E_Issue2 $e_issue2)
    {
        $pdoStatement = self::_select($pdo,F_Issue2::FIELDNAME_E_ISSUE2_IDC_ISSUE2.' = ? AND '.F_Issue2::FIELDNAME_E_ISSUE2_IDD_ISSUE2.' = ?');
        if (!$pdoStatement->execute(array($e_issue2->getC_issue2()->getIdC_issue2(),$e_issue2->getD_issue2()->getIdD_issue2()))) {
            throw new Exception('Error while selecting all f_issue2s by e_issue2 in database');
        }
        return $pdoStatement;
    }
    
    /**
     * ToString
     * @return string String representation of f_issue2
     */
    public function __toString()
    {
        return '[F_Issue2 idF_issue2="'.$this->_idF_issue2.'" b_issue2="'.$this->_b_issue2.'" e_issue2="c_issue2-idc_issue2 : '.$this->_e_issue2['c_issue2-idc_issue2'].', d_issue2-idd_issue2 : '.$this->_e_issue2['d_issue2-idd_issue2'].'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of f_issue2
     */
    public function serialize($serialize=true)
    {
        // Serialize the f_issue2
        $array = array('idf_issue2' => $this->_idF_issue2,'b_issue2' => $this->_b_issue2,'e_issue2' => $this->_e_issue2);
        
        // Return the serialized (or not) f_issue2
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of f_issue2
     * @param $lazyload bool Enable lazy load ?
     * @return F_Issue2 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the f_issue2
        return $lazyload && isset(self::$_lazyload[$array['idf_issue2']]) ? self::$_lazyload[$array['idf_issue2']] :
               new F_Issue2($pdo,$array['idf_issue2'],$array['b_issue2'],$array['e_issue2'],$lazyload);
    }
    
}

/**
 * @name F_Issue2
 * @version 01/31/2015 (mm/dd/yyyy)
 */
class F_Issue2 extends Base_F_Issue2
{
    
    // Put your code here...
    
}

