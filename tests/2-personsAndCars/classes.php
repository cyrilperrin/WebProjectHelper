<?php

/**
 * @name Base_Person
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Person implements Iterator
{
    // Table name
    const TABLENAME = 'person';
    
    // Fields name
    const FIELDNAME_IDPERSON = 'idperson';
    const FIELDNAME_FIRSTNAME = 'firstname';
    const FIELDNAME_LASTNAME = 'lastname';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var PDOStatement select for the iterator implementation */
    protected $_iteratorSelect;
    
    /** @var Car current element for the iterator implementation */
    protected $_iteratorCurrent;
    
    /** @var int  */
    protected $_idPerson;
    
    /** @var string  */
    protected $_firstname;
    
    /** @var string  */
    protected $_lastname;
    
    /**
     * Construct a person
     * @param $pdo PDO 
     * @param $idPerson int 
     * @param $firstname string 
     * @param $lastname string 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idPerson,$firstname,$lastname,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idPerson = $idPerson;
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idPerson] = $this;
        }
    }
    
    /**
     * Create a person
     * @param $pdo PDO 
     * @param $firstname string 
     * @param $lastname string 
     * @param $lazyload bool Enable lazy load ?
     * @return Person 
     */
    public static function create(PDO $pdo,$firstname,$lastname,$lazyload=true)
    {
        // Add the person into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Person::TABLENAME.' ('.Person::FIELDNAME_FIRSTNAME.','.Person::FIELDNAME_LASTNAME.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($firstname,$lastname))) {
            throw new Exception('Error while inserting a person into database');
        }
        
        // Construct the person
        return new Person($pdo,intval($pdo->lastInsertId()),$firstname,$lastname,$lazyload);
    }
    
    /**
     * Count persons
     * @param $pdo PDO 
     * @return int Number of persons
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Person::FIELDNAME_IDPERSON.') FROM '.Person::TABLENAME))) {
            throw new Exception('Error while counting persons in database');
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
        return $pdo->prepare('SELECT DISTINCT '.Person::TABLENAME.'.'.Person::FIELDNAME_IDPERSON.', '.Person::TABLENAME.'.'.Person::FIELDNAME_FIRSTNAME.', '.Person::TABLENAME.'.'.Person::FIELDNAME_LASTNAME.' '.
                             'FROM '.Person::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a person
     * @param $pdo PDO 
     * @param $idPerson int 
     * @param $lazyload bool Enable lazy load ?
     * @return Person 
     */
    public static function load(PDO $pdo,$idPerson,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idPerson])) {
            return self::$_lazyload[$idPerson];
        }
        
        // Load the person
        $pdoStatement = self::_select($pdo,Person::FIELDNAME_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($idPerson))) {
            throw new Exception('Error while loading a person from database');
        }
        
        // Fetch the person from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Person::FIELDNAME_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($this->_idPerson))) {
            throw new Exception('Error while reloading data of a person from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idPerson,$firstname,$lastname) = $values;
        
        // Save values
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
    }
    
    /**
     * Load all persons
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Person[] Array of persons
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all persons
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the persons
        $persons = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $persons;
    }
    
    /**
     * Select all persons
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all persons from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next person from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Person 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idPerson,$firstname,$lastname) = $values;
        
        // Construct the person
        return $lazyload && isset(self::$_lazyload[intval($idPerson)]) ? self::$_lazyload[intval($idPerson)] :
               new Person($pdo,intval($idPerson),$firstname,$lastname,$lazyload);
    }
    
    /**
     * Fetch all the persons from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Person[] Array of persons
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $persons = array();
        while ($person = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $persons[] = $person;
        }
        return $persons;
    }
    
    /**
     * Equality test
     * @param $person Person 
     * @return bool Objects are equals ?
     */
    public function equals($person)
    {
        // Test if null
        if ($person == null) { return false; }
        
        // Test class
        if (!($person instanceof Person)) { return false; }
        
        // Test ids
        return $this->_idPerson == $person->_idPerson;
    }
    
    /**
     * Check if the person exists in database
     * @return bool The person exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Person::FIELDNAME_IDPERSON.') FROM '.Person::TABLENAME.' WHERE '.Person::FIELDNAME_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPerson()))) {
            throw new Exception('Error while checking that a person exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete person
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated cars
        $this->removeAllCars();
        
        // Delete person
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Person::TABLENAME.' WHERE '.Person::FIELDNAME_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPerson()))) {
            throw new Exception('Error while deleting a person in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idPerson])) {
            unset(self::$_lazyload[$this->_idPerson]);
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
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Person::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Person::FIELDNAME_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdPerson())))) {
            throw new Exception('Error while updating a person\'s field in database');
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
        return $this->_set(array(Person::FIELDNAME_FIRSTNAME,Person::FIELDNAME_LASTNAME),array($this->_firstname,$this->_lastname));
    }
    
    /**
     * Get the idPerson
     * @return int 
     */
    public function getIdPerson()
    {
        return $this->_idPerson;
    }
    
    /**
     * Get the firstname
     * @return string 
     */
    public function getFirstname()
    {
        return $this->_firstname;
    }
    
    /**
     * Set the firstname
     * @param $firstname string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setFirstname($firstname,$execute=true)
    {
        // Save into object
        $this->_firstname = $firstname;
        
        // Save into database (or not)
        return $execute ? Person::_set(array(Person::FIELDNAME_FIRSTNAME),array($firstname)) : true;
    }
    
    /**
     * Get the lastname
     * @return string 
     */
    public function getLastname()
    {
        return $this->_lastname;
    }
    
    /**
     * Set the lastname
     * @param $lastname string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setLastname($lastname,$execute=true)
    {
        // Save into object
        $this->_lastname = $lastname;
        
        // Save into database (or not)
        return $execute ? Person::_set(array(Person::FIELDNAME_LASTNAME),array($lastname)) : true;
    }
    
    /**
     * Add a car
     * @param $car Car 
     * @return bool Successful operation ?
     */
    public function addCar(Car $car)
    {
        $pdoStatement = $this->_pdo->prepare('INSERT INTO '.Association_CarPerson::TABLENAME.' ('.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.','.Association_CarPerson::FIELDNAME_CAR_IDCAR.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($this->getIdPerson(),$car->getIdCar()))) {
            throw new Exception('Error while adding a person\'s car in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Add a list of cars
     * @param $cars Car[] Array of cars
     * @return bool Successful operation ?
     */
    public function addListOfCars($cars)
    {
        $pdoStatement = $this->_pdo->prepare('INSERT INTO '.Association_CarPerson::TABLENAME.' ('.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.','.Association_CarPerson::FIELDNAME_CAR_IDCAR.') VALUES '.implode(',',array_fill(0,count($cars),'(?,?)')));
        $values = array();
        foreach($cars as $car) {
            $values[] = $this->getIdPerson();
            $values[] = $car->getIdCar();
        }
        if (!$pdoStatement->execute($values)) {
            throw new Exception('Error while adding a list of person\'s cars in database');
        }
        return $pdoStatement->rowCount() == count($cars);
    }
    
    /**
     * Add a car by id
     * @param $idCar int 
     * @return bool Successful operation ?
     */
    public function addCarById($idCar)
    {
        $pdoStatement = $this->_pdo->prepare('INSERT INTO '.Association_CarPerson::TABLENAME.' ('.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.','.Association_CarPerson::FIELDNAME_CAR_IDCAR.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($this->getIdPerson(),$idCar))) {
            throw new Exception('Error while adding a person\'s car in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Remove a car
     * @param $car Car 
     * @return bool Successful operation ?
     */
    public function removeCar(Car $car)
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ? AND '.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPerson(),$car->getIdCar()))) {
            throw new Exception('Error while deleting a person\'s car in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Remove a list of cars
     * @param $cars Car[] Array of cars
     * @return bool Successful operation ?
     */
    public function removeListOfCars($cars)
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.implode(' OR ',array_fill(0,count($cars),'('.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ? AND '.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ?)')));
        $values = array();
        foreach($cars as $car) {
            $values[] = $this->getIdPerson();
            $values[] = $car->getIdCar();
        }
        if (!$pdoStatement->execute($values)) {
            throw new Exception('Error while deleting a list of person\'s cars in database');
        }
        return $pdoStatement->rowCount() == count($cars);
    }
    
    /**
     * Remove a car by id
     * @param $idCar int 
     * @return bool Successful operation ?
     */
    public function removeCarById($idCar)
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ? AND '.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPerson(),$idCar))) {
            throw new Exception('Error while deleting a person\'s car in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Remove all cars
     * @return int Number of affected rows
     */
    public function removeAllCars()
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($this->getIdPerson()))) {
            throw new Exception('Error while deleting all person\'s cars in database');
        }
        return $pdoStatement->rowCount();
    }
    
    /**
     * Select cars
     * @return PDOStatement 
     */
    public function selectCars()
    {
        return Car::selectByPerson($this->_pdo,$this);
    }
    
    /**
     * Select persons by car
     * @param $pdo PDO 
     * @param $car Car 
     * @return PDOStatement 
     */
    public static function selectByCar(PDO $pdo,Car $car)
    {
        $pdoStatement = self::_select($pdo,Association_CarPerson::TABLENAME.'.'.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ? AND '.Association_CarPerson::TABLENAME.'.'.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = '.Person::TABLENAME.'.'.Person::FIELDNAME_IDPERSON,null,null,array(Association_CarPerson::TABLENAME));
        if (!$pdoStatement->execute(array($car->getIdCar()))) {
            throw new Exception('Error while selecting all persons by car in database');
        }
        return $pdoStatement;
    }
    
    /**
     * ToString
     * @return string String representation of person
     */
    public function __toString()
    {
        return '[Person idPerson="'.$this->_idPerson.'" firstname="'.$this->_firstname.'" lastname="'.$this->_lastname.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of person
     */
    public function serialize($serialize=true)
    {
        // Serialize the person
        $array = array('idperson' => $this->_idPerson,'firstname' => $this->_firstname,'lastname' => $this->_lastname);
        
        // Return the serialized (or not) person
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of person
     * @param $lazyload bool Enable lazy load ?
     * @return Person 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the person
        return $lazyload && isset(self::$_lazyload[$array['idperson']]) ? self::$_lazyload[$array['idperson']] :
               new Person($pdo,$array['idperson'],$array['firstname'],$array['lastname'],$lazyload);
    }
    
    
    // Iterator implementation
    public function rewind() { $this->_iteratorSelect = $this->selectCars();  $this->next(); }
    public function key() { return $this->_iteratorCurrent == null ? null : $this->_iteratorCurrent->getIdCar(); }
    public function next() { $this->_iteratorCurrent = Car::fetch($this->_pdo,$this->_iteratorSelect); }
    public function current() { return $this->_iteratorCurrent; }
    public function valid() { return $this->_iteratorCurrent != null; }
}

/**
 * @name Person
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Person extends Base_Person
{
    
    // Put your code here...
    
}

/**
 * @name Base_Car
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
abstract class Base_Car
{
    // Table name
    const TABLENAME = 'car';
    
    // Fields name
    const FIELDNAME_IDCAR = 'idcar';
    const FIELDNAME_MODEL = 'model';
    const FIELDNAME_BRAND = 'brand';
    
    /** @var PDO  */
    protected $_pdo;
    
    /** @var array array for lazy load */
    protected static $_lazyload;
    
    /** @var int  */
    protected $_idCar;
    
    /** @var string  */
    protected $_model;
    
    /** @var string  */
    protected $_brand;
    
    /**
     * Construct a car
     * @param $pdo PDO 
     * @param $idCar int 
     * @param $model string 
     * @param $brand string 
     * @param $lazyload bool Enable lazy load ?
     */
    protected function __construct(PDO $pdo,$idCar,$model,$brand,$lazyload=true)
    {
        // Save pdo
        $this->_pdo = $pdo;
        
        // Save attributes
        $this->_idCar = $idCar;
        $this->_model = $model;
        $this->_brand = $brand;
        
        // Save for lazy load
        if ($lazyload) {
            self::$_lazyload[$idCar] = $this;
        }
    }
    
    /**
     * Create a car
     * @param $pdo PDO 
     * @param $model string 
     * @param $brand string 
     * @param $lazyload bool Enable lazy load ?
     * @return Car 
     */
    public static function create(PDO $pdo,$model,$brand,$lazyload=true)
    {
        // Add the car into database
        $pdoStatement = $pdo->prepare('INSERT INTO '.Car::TABLENAME.' ('.Car::FIELDNAME_MODEL.','.Car::FIELDNAME_BRAND.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($model,$brand))) {
            throw new Exception('Error while inserting a car into database');
        }
        
        // Construct the car
        return new Car($pdo,intval($pdo->lastInsertId()),$model,$brand,$lazyload);
    }
    
    /**
     * Count cars
     * @param $pdo PDO 
     * @return int Number of cars
     */
    public static function count(PDO $pdo)
    {
        if (!($pdoStatement = $pdo->query('SELECT COUNT('.Car::FIELDNAME_IDCAR.') FROM '.Car::TABLENAME))) {
            throw new Exception('Error while counting cars in database');
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
        return $pdo->prepare('SELECT DISTINCT '.Car::TABLENAME.'.'.Car::FIELDNAME_IDCAR.', '.Car::TABLENAME.'.'.Car::FIELDNAME_MODEL.', '.Car::TABLENAME.'.'.Car::FIELDNAME_BRAND.' '.
                             'FROM '.Car::TABLENAME.($from != null ? ', '.(is_array($from) ? implode(', ',$from) : $from) : '').
                             ($where != null ? ' WHERE '.(is_array($where) ? implode(' AND ',$where) : $where) : '').
                             ($orderby != null ? ' ORDER BY '.(is_array($orderby) ? implode(', ',$orderby) : $orderby) : '').
                             ($limit != null ? ' LIMIT '.(is_array($limit) ? implode(', ', $limit) : $limit) : ''));
    }
    
    /**
     * Load a car
     * @param $pdo PDO 
     * @param $idCar int 
     * @param $lazyload bool Enable lazy load ?
     * @return Car 
     */
    public static function load(PDO $pdo,$idCar,$lazyload=true)
    {
        // Already loaded ?
        if ($lazyload && isset(self::$_lazyload[$idCar])) {
            return self::$_lazyload[$idCar];
        }
        
        // Load the car
        $pdoStatement = self::_select($pdo,Car::FIELDNAME_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($idCar))) {
            throw new Exception('Error while loading a car from database');
        }
        
        // Fetch the car from result set
        return self::fetch($pdo,$pdoStatement,$lazyload);
    }
    
    /**
     * Reload data from database
     */
    public function reload()
    {
        // Reload data
        $pdoStatement = self::_select($this->_pdo,Car::FIELDNAME_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($this->_idCar))) {
            throw new Exception('Error while reloading data of a car from database');
        }
        
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idCar,$model,$brand) = $values;
        
        // Save values
        $this->_model = $model;
        $this->_brand = $brand;
    }
    
    /**
     * Load all cars
     * @param $pdo PDO 
     * @param $lazyload bool Enable lazy load ?
     * @return Car[] Array of cars
     */
    public static function loadAll(PDO $pdo,$lazyload=true)
    {
        // Select all cars
        $pdoStatement = self::selectAll($pdo);
        
        // Fetch all the cars
        $cars = self::fetchAll($pdo,$pdoStatement,$lazyload);
        
        // Return array
        return $cars;
    }
    
    /**
     * Select all cars
     * @param $pdo PDO 
     * @return PDOStatement 
     */
    public static function selectAll(PDO $pdo)
    {
        $pdoStatement = self::_select($pdo);
        if (!$pdoStatement->execute()) {
            throw new Exception('Error while loading all cars from database');
        }
        return $pdoStatement;
    }
    
    /**
     * Fetch the next car from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Car 
     */
    public static function fetch(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        // Extract values
        $values = $pdoStatement->fetch(PDO::FETCH_NUM);
        if (!$values) { return null; }
        list($idCar,$model,$brand) = $values;
        
        // Construct the car
        return $lazyload && isset(self::$_lazyload[intval($idCar)]) ? self::$_lazyload[intval($idCar)] :
               new Car($pdo,intval($idCar),$model,$brand,$lazyload);
    }
    
    /**
     * Fetch all the cars from a result set
     * @param $pdo PDO 
     * @param $pdoStatement PDOStatement 
     * @param $lazyload bool Enable lazy load ?
     * @return Car[] Array of cars
     */
    public static function fetchAll(PDO $pdo,PDOStatement $pdoStatement,$lazyload=true)
    {
        $cars = array();
        while ($car = self::fetch($pdo,$pdoStatement,$lazyload)) {
            $cars[] = $car;
        }
        return $cars;
    }
    
    /**
     * Equality test
     * @param $car Car 
     * @return bool Objects are equals ?
     */
    public function equals($car)
    {
        // Test if null
        if ($car == null) { return false; }
        
        // Test class
        if (!($car instanceof Car)) { return false; }
        
        // Test ids
        return $this->_idCar == $car->_idCar;
    }
    
    /**
     * Check if the car exists in database
     * @return bool The car exists in database ?
     */
    public function exists()
    {
        $pdoStatement = $this->_pdo->prepare('SELECT COUNT('.Car::FIELDNAME_IDCAR.') FROM '.Car::TABLENAME.' WHERE '.Car::FIELDNAME_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($this->getIdCar()))) {
            throw new Exception('Error while checking that a car exists in database');
        }
        return $pdoStatement->fetchColumn() == 1;
    }
    
    /**
     * Delete car
     * @return bool Successful operation ?
     */
    public function delete()
    {
        // Delete associated persons
        $this->removeAllPersons();
        
        // Delete car
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Car::TABLENAME.' WHERE '.Car::FIELDNAME_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($this->getIdCar()))) {
            throw new Exception('Error while deleting a car in database');
        }
        
        // Remove from lazy load array
        if (isset(self::$_lazyload[$this->_idCar])) {
            unset(self::$_lazyload[$this->_idCar]);
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
        $pdoStatement = $this->_pdo->prepare('UPDATE '.Car::TABLENAME.' SET '.implode(', ', $updates).' WHERE '.Car::FIELDNAME_IDCAR.' = ?');
        if (!$pdoStatement->execute(array_merge($values,array($this->getIdCar())))) {
            throw new Exception('Error while updating a car\'s field in database');
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
        return $this->_set(array(Car::FIELDNAME_MODEL,Car::FIELDNAME_BRAND),array($this->_model,$this->_brand));
    }
    
    /**
     * Get the idCar
     * @return int 
     */
    public function getIdCar()
    {
        return $this->_idCar;
    }
    
    /**
     * Get the model
     * @return string 
     */
    public function getModel()
    {
        return $this->_model;
    }
    
    /**
     * Set the model
     * @param $model string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setModel($model,$execute=true)
    {
        // Save into object
        $this->_model = $model;
        
        // Save into database (or not)
        return $execute ? Car::_set(array(Car::FIELDNAME_MODEL),array($model)) : true;
    }
    
    /**
     * Get the brand
     * @return string 
     */
    public function getBrand()
    {
        return $this->_brand;
    }
    
    /**
     * Set the brand
     * @param $brand string 
     * @param $execute bool Execute update query ?
     * @return bool Successful operation ?
     */
    public function setBrand($brand,$execute=true)
    {
        // Save into object
        $this->_brand = $brand;
        
        // Save into database (or not)
        return $execute ? Car::_set(array(Car::FIELDNAME_BRAND),array($brand)) : true;
    }
    
    /**
     * Add a person
     * @param $person Person 
     * @return bool Successful operation ?
     */
    public function addPerson(Person $person)
    {
        $pdoStatement = $this->_pdo->prepare('INSERT INTO '.Association_CarPerson::TABLENAME.' ('.Association_CarPerson::FIELDNAME_CAR_IDCAR.','.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($this->getIdCar(),$person->getIdPerson()))) {
            throw new Exception('Error while adding a car\'s person in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Add a list of persons
     * @param $persons Person[] Array of persons
     * @return bool Successful operation ?
     */
    public function addListOfPersons($persons)
    {
        $pdoStatement = $this->_pdo->prepare('INSERT INTO '.Association_CarPerson::TABLENAME.' ('.Association_CarPerson::FIELDNAME_CAR_IDCAR.','.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.') VALUES '.implode(',',array_fill(0,count($persons),'(?,?)')));
        $values = array();
        foreach($persons as $person) {
            $values[] = $this->getIdCar();
            $values[] = $person->getIdPerson();
        }
        if (!$pdoStatement->execute($values)) {
            throw new Exception('Error while adding a list of car\'s persons in database');
        }
        return $pdoStatement->rowCount() == count($persons);
    }
    
    /**
     * Add a person by id
     * @param $idPerson int 
     * @return bool Successful operation ?
     */
    public function addPersonById($idPerson)
    {
        $pdoStatement = $this->_pdo->prepare('INSERT INTO '.Association_CarPerson::TABLENAME.' ('.Association_CarPerson::FIELDNAME_CAR_IDCAR.','.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.') VALUES (?,?)');
        if (!$pdoStatement->execute(array($this->getIdCar(),$idPerson))) {
            throw new Exception('Error while adding a car\'s person in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Remove a person
     * @param $person Person 
     * @return bool Successful operation ?
     */
    public function removePerson(Person $person)
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ? AND '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($this->getIdCar(),$person->getIdPerson()))) {
            throw new Exception('Error while deleting a car\'s person in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Remove a list of persons
     * @param $persons Person[] Array of persons
     * @return bool Successful operation ?
     */
    public function removeListOfPersons($persons)
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.implode(' OR ',array_fill(0,count($persons),'('.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ? AND '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ?)')));
        $values = array();
        foreach($persons as $person) {
            $values[] = $this->getIdCar();
            $values[] = $person->getIdPerson();
        }
        if (!$pdoStatement->execute($values)) {
            throw new Exception('Error while deleting a list of car\'s persons in database');
        }
        return $pdoStatement->rowCount() == count($persons);
    }
    
    /**
     * Remove a person by id
     * @param $idPerson int 
     * @return bool Successful operation ?
     */
    public function removePersonById($idPerson)
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ? AND '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ?');
        if (!$pdoStatement->execute(array($this->getIdCar(),$idPerson))) {
            throw new Exception('Error while deleting a car\'s person in database');
        }
        return $pdoStatement->rowCount() == 1;
    }
    
    /**
     * Remove all persons
     * @return int Number of affected rows
     */
    public function removeAllPersons()
    {
        $pdoStatement = $this->_pdo->prepare('DELETE FROM '.Association_CarPerson::TABLENAME.' WHERE '.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = ?');
        if (!$pdoStatement->execute(array($this->getIdCar()))) {
            throw new Exception('Error while deleting all car\'s persons in database');
        }
        return $pdoStatement->rowCount();
    }
    
    /**
     * Select persons
     * @return PDOStatement 
     */
    public function selectPersons()
    {
        return Person::selectByCar($this->_pdo,$this);
    }
    
    /**
     * Select cars by person
     * @param $pdo PDO 
     * @param $person Person 
     * @return PDOStatement 
     */
    public static function selectByPerson(PDO $pdo,Person $person)
    {
        $pdoStatement = self::_select($pdo,Association_CarPerson::TABLENAME.'.'.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' = ? AND '.Association_CarPerson::TABLENAME.'.'.Association_CarPerson::FIELDNAME_CAR_IDCAR.' = '.Car::TABLENAME.'.'.Car::FIELDNAME_IDCAR,null,null,array(Association_CarPerson::TABLENAME));
        if (!$pdoStatement->execute(array($person->getIdPerson()))) {
            throw new Exception('Error while selecting all cars by person in database');
        }
        return $pdoStatement;
    }
    
    /**
     * ToString
     * @return string String representation of car
     */
    public function __toString()
    {
        return '[Car idCar="'.$this->_idCar.'" model="'.$this->_model.'" brand="'.$this->_brand.'"]';
    }
    /**
     * Serialize
     * @param $serialize bool Enable serialize ?
     * @return string Serialization of car
     */
    public function serialize($serialize=true)
    {
        // Serialize the car
        $array = array('idcar' => $this->_idCar,'model' => $this->_model,'brand' => $this->_brand);
        
        // Return the serialized (or not) car
        return $serialize ? serialize($array) : $array;
    }
    
    /**
     * Unserialize
     * @param $pdo PDO 
     * @param $string string Serialization of car
     * @param $lazyload bool Enable lazy load ?
     * @return Car 
     */
    public static function unserialize(PDO $pdo,$string,$lazyload=true)
    {
        // Unserialize string
        $array = unserialize($string);
        
        // Construct the car
        return $lazyload && isset(self::$_lazyload[$array['idcar']]) ? self::$_lazyload[$array['idcar']] :
               new Car($pdo,$array['idcar'],$array['model'],$array['brand'],$lazyload);
    }
    
}

/**
 * @name Car
 * @version 01/28/2014 (mm/dd/yyyy)
 */
class Car extends Base_Car
{
    
    // Put your code here...
    
}

/**
 * Association class between car and person
 * @name Association_CarPerson
 * @version 01/28/2014 (mm/dd/yyyy)
 * @author WebProjectHelper (http://www.elfangels.fr/webprojecthelper/)
 */
class Association_CarPerson
{
    
    // Table name
    const TABLENAME = 'car_person';
    
    // Fields name
    const FIELDNAME_CAR_IDCAR = 'fk_idcar';
    const FIELDNAME_PERSON_IDPERSON = 'fk_idperson';
    
}

