<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Dog class test
 */
class DogTest extends GenericTest
{
	
    /**
     * Set up before class
     */
    public static function setUpBeforeClass()
    {
    	// Build PDO variable
    	self::buildPdo();
    	
    	// Drop all MySQL tables
    	self::dropAllMysqlTablesFromXmlDataSet(__DIR__.'/../datasets/initial.xml');
    	
    	// Execute MySQL script
    	self::executeMysqlScript(__DIR__.'/../script.sql');
    	
    	// Require classes
    	require_once(__DIR__.'/../classes.php');
    }
    
    /**
     * Tear down after class
     */
    public static function tearDownAfterClass()
    {
    	// Drop all MySQL tables
    	self::dropAllMysqlTablesFromXmlDataSet(__DIR__.'/../datasets/initial.xml');
    }
    
    /**
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__.'/../datasets/initial.xml');
    }
    
    /**
     * Create test
     */
    public function testCreate()
    {
    	// Create entry
    	$bill = Dog::create(self::$pdo, 'Bill');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Dog', $bill);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testCreate/1-afterCreateBill.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Create entry
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Dog', $droopy);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testCreate/2-afterCreateDroopy.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Create entry
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Dog', $milou);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testCreate/3-afterCreateMilou.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * Delete test
     * @depends testCreate
     */
    public function testDelete()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Delete entry
    	$this->assertTrue($bill->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testDelete/1-afterDeleteBill.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($droopy->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testDelete/2-afterDeleteDroopy.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($milou->delete());
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('dog'));
    }
    
    /**
     * GetName test
     * @depends testCreate
     */
    public function testGetIdDog()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert getIdDog
    	$this->assertEquals('integer', gettype($bill->getIdDog()));
    	$this->assertGreaterThanOrEqual(0, $bill->getIdDog());
    	$this->assertEquals('integer', gettype($droopy->getIdDog()));
    	$this->assertGreaterThanOrEqual(0, $droopy->getIdDog());
    	$this->assertEquals('integer', gettype($milou->getIdDog()));
    	$this->assertGreaterThanOrEqual(0, $milou->getIdDog());
    	$this->assertGreaterThan($bill->getIdDog(), $droopy->getIdDog());
    	$this->assertGreaterThan($droopy->getIdDog(), $milou->getIdDog());
    }
    
    /**
     * GetName test
     * @depends testCreate
     */
    public function testGetName()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert name
    	$this->assertSame('Bill', $bill->getName());
    	$this->assertSame('Droopy', $droopy->getName());
    	$this->assertSame('Milou', $milou->getName());
    }
    
    /**
     * SetName/Update test
     * @depends testCreate
     * @depends testGetName
     */
    public function testSetNameAndUpdate()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Set name with execute
    	$this->assertTrue($bill->setName('Bill2',true));
    	$this->assertTrue($droopy->setName('Droopy2',true));
    	$this->assertTrue($milou->setName('Milou2',true));
    	
    	// Assert getName
    	$this->assertSame('Bill2', $bill->getName());
    	$this->assertSame('Droopy2', $droopy->getName());
    	$this->assertSame('Milou2', $milou->getName());
		
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testSetNameAndUpdate/1-afterSetNameWithExecute.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Set name without execute
    	$this->assertTrue($bill->setName('Bill3',false));
    	$this->assertTrue($droopy->setName('Droopy3',false));
    	$this->assertTrue($milou->setName('Milou3',false));
    	
    	// Assert getName
    	$this->assertSame('Bill3', $bill->getName());
    	$this->assertSame('Droopy3', $droopy->getName());
    	$this->assertSame('Milou3', $milou->getName());
		
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testSetNameAndUpdate/2-afterSetNameWithoutExecute.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Update
    	$this->assertTrue($bill->update());
    	$this->assertTrue($droopy->update());
    	$this->assertTrue($milou->update());
		
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/dogTest/testSetNameAndUpdate/3-afterUpdate.xml')->getTable('dog');
    	$actual = $this->getConnection()->createQueryTable('dog', 'SELECT '.Dog::FIELDNAME_NAME.' FROM '.Dog::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * Load test
     * @depends testCreate
     * @depends testDelete
     * @depends testGetIdDog
     * @depends testGetName
     */
    public function testLoad()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Load entries
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog());
    	$loadedDroopy = Dog::load(self::$pdo, $droopy->getIdDog());
    	$loadedMilou = Dog::load(self::$pdo, $milou->getIdDog());
    	
    	// Assert instance of
    	$this->assertInstanceOf('Dog', $loadedBill);
    	$this->assertInstanceOf('Dog', $loadedDroopy);
    	$this->assertInstanceOf('Dog', $loadedMilou);
    	
    	// Assert attributes
    	$this->assertSame($bill->getIdDog(), $loadedBill->getIdDog());
    	$this->assertSame($droopy->getIdDog(), $loadedDroopy->getIdDog());
    	$this->assertSame($milou->getIdDog(), $loadedMilou->getIdDog());
    	$this->assertSame($bill->getName(), $loadedBill->getName());
    	$this->assertSame($droopy->getName(), $loadedDroopy->getName());
    	$this->assertSame($milou->getName(), $loadedMilou->getName());
    	
    	// Delete entries
    	$bill->delete();
    	$droopy->delete();
    	$milou->delete();
		
    	// Load entries
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog());
    	$loadedDroopy = Dog::load(self::$pdo, $bill->getIdDog());
    	$loadedMilou = Dog::load(self::$pdo, $bill->getIdDog());
    	
    	// Assert null
    	$this->assertNull($loadedBill);
    	$this->assertNull($loadedDroopy);
    	$this->assertNull($loadedMilou);
    }
    
    /**
     * LoadAll test
     * @depends testCreate
     * @depends testDelete
     */
    public function testLoadAll()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Load all
    	$dogs = Dog::loadAll(self::$pdo);
    	
    	// Assert array content
    	$this->assertCount(3, $dogs);
    	$this->assertContains($bill, $dogs);
    	$this->assertContains($droopy, $dogs);
    	$this->assertContains($milou, $dogs);
    	
    	// Delete entries
    	$bill->delete();
    	$droopy->delete();
    	$milou->delete();
    	
    	// Load all
    	$dogs = Dog::loadAll(self::$pdo);
    	
    	// Assert array content
    	$this->assertCount(0, $dogs);
    }
    
    /**
     * SelectAll/Fetch test
     * @depends testCreate
     * @depends testDelete
     */
    public function testSelectAllAndFetch()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Select all and fetch
    	$dogs = array();
    	$statement = Dog::selectAll(self::$pdo);
    	while($dog = Dog::fetch(self::$pdo, $statement)) {
    		$dogs[] = $dog;
    	}
    	
    	// Assert array content
    	$this->assertCount(3, $dogs);
    	$this->assertContains($bill, $dogs);
    	$this->assertContains($droopy, $dogs);
    	$this->assertContains($milou, $dogs);
    	
    	// Delete entries
    	$bill->delete();
    	$droopy->delete();
    	$milou->delete();
    	
    	// Select all and fetch
    	$dogs = array();
    	$statement = Dog::selectAll(self::$pdo);
    	while($dog = Dog::fetch(self::$pdo, $statement)) {
    		$dogs[] = $dog;
    	}
    	
    	// Assert array content
    	$this->assertCount(0, $dogs);
    }
    
    /**
     * SelectAll/FetchAll test
     * @depends testCreate
     * @depends testDelete
     */
    public function testSelectAllAndFetchAll()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    
    	// Select all and fetch all
    	$statement = Dog::selectAll(self::$pdo);
    	$dogs = Dog::fetchAll(self::$pdo, $statement);
    	
    	// Assert type
    	$this->assertEquals('array', gettype($dogs));
    	
    	// Assert array content
    	$this->assertCount(3, $dogs);
    	$this->assertContains($bill, $dogs);
    	$this->assertContains($droopy, $dogs);
    	$this->assertContains($milou, $dogs);
    	
    	// Delete entries
    	$bill->delete();
    	$droopy->delete();
    	$milou->delete();
    	
    	// Select all and fetch all
    	$statement = Dog::selectAll(self::$pdo);
    	$dogs = Dog::fetchAll(self::$pdo, $statement);
    	
    	// Assert array content
    	$this->assertCount(0, $dogs);
    }
    
    /**
     * Serialize/Unserialize test
     * @depends testCreate
     * @depends testGetIdDog
     * @depends testGetName
     */
    public function testSerializeAndUnserialize()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Serialize
    	$serializedBill = $bill->serialize();
    	$serializedDroopy = $droopy->serialize();
    	$serializedMilou = $milou->serialize();
    	
    	// Assert type
    	$this->assertEquals('string',gettype($serializedBill));
    	$this->assertEquals('string',gettype($serializedDroopy));
    	$this->assertEquals('string',gettype($serializedMilou));
    	
    	// Unserialize
    	$unserializedBill = Dog::unserialize(self::$pdo, $serializedBill);
    	$unserializedDroopy = Dog::unserialize(self::$pdo, $serializedDroopy);
    	$unserializedMilou = Dog::unserialize(self::$pdo, $serializedMilou);
		
    	// Assert instance of
    	$this->assertInstanceOf('Dog', $unserializedBill);
    	$this->assertInstanceOf('Dog', $unserializedDroopy);
    	$this->assertInstanceOf('Dog', $unserializedMilou);
    	 
    	// Assert attributes
    	$this->assertSame($bill->getIdDog(), $unserializedBill->getIdDog());
    	$this->assertSame($droopy->getIdDog(), $unserializedDroopy->getIdDog());
    	$this->assertSame($milou->getIdDog(), $unserializedMilou->getIdDog());
    	$this->assertSame($bill->getName(), $unserializedBill->getName());
    	$this->assertSame($droopy->getName(), $unserializedDroopy->getName());
    	$this->assertSame($milou->getName(), $unserializedMilou->getName());
    }
    
    /**
     * Exists test
     * @depends testCreate
     * @depends testDelete
     */
    public function testExists()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert exists
    	$this->assertTrue($bill->exists());
    	$this->assertTrue($droopy->exists());
    	$this->assertTrue($milou->exists());
    	
    	// Delete entry
    	$bill->delete();
    	
    	// Assert exists
    	$this->assertFalse($bill->exists());
    	$this->assertTrue($droopy->exists());
    	$this->assertTrue($milou->exists());
    	
    	// Delete entry
    	$droopy->delete();
    	
    	// Assert exists
    	$this->assertFalse($bill->exists());
    	$this->assertFalse($droopy->exists());
    	$this->assertTrue($milou->exists());
    	
    	// Delete entry
    	$milou->delete();
    	
    	// Assert exists
    	$this->assertFalse($bill->exists());
    	$this->assertFalse($droopy->exists());
    	$this->assertFalse($milou->exists());
    }

    /**
     * Equals test
     * @depends testCreate
     * @depends testLoad
     * @depends testGetIdDog
     */
    public function testEquals()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert equals
    	$this->assertTrue($bill->equals($bill));
    	$this->assertTrue($droopy->equals($droopy));
    	$this->assertTrue($milou->equals($milou));
    	
    	// Assert not equals
    	$this->assertFalse($milou->equals($bill));
    	$this->assertFalse($bill->equals($droopy));
    	$this->assertFalse($droopy->equals($milou));
    	$this->assertFalse($bill->equals('Bill'));
    	$this->assertFalse($droopy->equals(2));
    	$this->assertFalse($milou->equals(null));
    	
    	// Load entries
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog());
    	$loadedDroopy = Dog::load(self::$pdo, $droopy->getIdDog());
    	$loadedMilou = Dog::load(self::$pdo, $milou->getIdDog());
    	
    	// Assert equals
    	$this->assertTrue($bill->equals($loadedBill));
    	$this->assertTrue($droopy->equals($loadedDroopy));
    	$this->assertTrue($milou->equals($loadedMilou));
    	
    	// Assert not equals
    	$this->assertFalse($milou->equals($loadedBill));
    	$this->assertFalse($bill->equals($loadedDroopy));
    	$this->assertFalse($droopy->equals($loadedMilou));
    }
    
    /**
     * Count test
     * @depends testCreate
     * @depends testDelete
     */
    public function testCount()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert count
    	$this->assertEquals(3, Dog::count(self::$pdo));
		
    	// Delete entry
    	$bill->delete();
    	
    	// Assert count
    	$this->assertEquals(2, Dog::count(self::$pdo));

    	// Delete entry
    	$droopy->delete();
    	
    	// Assert count
    	$this->assertEquals(1, Dog::count(self::$pdo));
    	
    	// Delete entry
    	$milou->delete();
    	
    	// Assert count
    	$this->assertEquals(0, Dog::count(self::$pdo));
    }
    
    /**
     * Reload test
     * @depends testCreate
     * @depends testGetIdDog
     * @depends testGetName
     */
    public function testReload()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');

    	// Update entries
    	$statement = self::$pdo->prepare('UPDATE '.Dog::TABLENAME.' SET '.Dog::FIELDNAME_NAME.' = ? WHERE '.Dog::FIELDNAME_IDDOG.' = ?');
    	$statement->execute(array('Bill2',$bill->getIdDog()));
    	$statement->execute(array('Droopy2',$droopy->getIdDog()));
    	$statement->execute(array('Milou2',$milou->getIdDog()));
    	
    	// Assert name
    	$this->assertSame('Bill', $bill->getName());
    	$this->assertSame('Droopy', $droopy->getName());
    	$this->assertSame('Milou', $milou->getName());
    	
    	// Reload
    	$bill->reload();
    	$droopy->reload();
    	$milou->reload();
    	
    	// Assert name
    	$this->assertSame('Bill2', $bill->getName());
    	$this->assertSame('Droopy2', $droopy->getName());
    	$this->assertSame('Milou2', $milou->getName());
    }
    
    /**
     * ToString test
     * @depends testCreate
     * @depends testGetIdDog
     */
    public function testToString()
    {
    	// Create entries
    	$bill = Dog::create(self::$pdo, 'Bill');
    	$droopy = Dog::create(self::$pdo, 'Droopy');
    	$milou = Dog::create(self::$pdo, 'Milou');
    	
    	// Assert toString
    	$this->assertSame('[Dog idDog="'.$bill->getIdDog().'" name="Bill"]', $bill->__toString());
    	$this->assertSame('[Dog idDog="'.$droopy->getIdDog().'" name="Droopy"]', $droopy->__toString());
    	$this->assertSame('[Dog idDog="'.$milou->getIdDog().'" name="Milou"]', $milou->__toString());
    }
    
    /**
     * Delete test
     * @depends testCreate
     * @depends testDelete
     * @depends testLoad
     * @depends testGetIdDog
     */
    public function testDeleteLazyLoad()
    {
    	// Create entry without lazyload and load with
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$bill->delete();
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog(), true);
    	$this->assertNull($loadedBill);
    	
    	// Create entry with lazyload and load without
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$bill->delete();
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog(), false);
    	$this->assertNull($loadedBill);
    	
    	// Create entry without lazyload and load without
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$bill->delete();
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog(), false);
    	$this->assertNull($loadedBill);
    	
    	// Create entry with lazyload and load with
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$bill->delete();
    	$loadedBill = Dog::load(self::$pdo, $bill->getIdDog(), true);
    	$this->assertNull($loadedBill);
    }
    
    /**
     * LazyLoad test for load()
     * @depends testCreate
     * @depends testDeleteLazyLoad
     * @depends testLoad
     * @depends testGetIdDog
     */
    public function testLoadLazyLoad()
    {
    	// Create without lazyload and load with
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$this->assertNotSame($bill, Dog::load(self::$pdo, $bill->getIdDog(), true));
    	$bill->delete();
    	
    	// Create with lazyload and load without
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$this->assertNotSame($bill, Dog::load(self::$pdo, $bill->getIdDog(), false));
    	$bill->delete();
    	
    	// Create without lazyload and load without
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$this->assertNotSame($bill, Dog::load(self::$pdo, $bill->getIdDog(), false));
    	$bill->delete();
    	
    	// Create with lazyload and load with
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$this->assertSame($bill, Dog::load(self::$pdo, $bill->getIdDog(), true));
    	$bill->delete();
    }

    /*
     * LazyLoad test for load()
     * @depends testCreate
     * @depends testDeleteLazyLoad
     * @depends testLoadAll
     */
    public function testLoadAllLazyLoad()
    {
    	// Create without lazyload and load all with
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$dogs = Dog::loadAll(self::$pdo,true);
    	$this->assertNotSame($bill, $dogs[0]);
    	$bill->delete();
    	
    	// Create with lazyload and load all without
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$dogs = Dog::loadAll(self::$pdo,false);
    	$this->assertNotSame($bill, $dogs[0]);
    	$bill->delete();
    	
    	// Create without lazyload and load all without
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$dogs = Dog::loadAll(self::$pdo,false);
    	$this->assertNotSame($bill, $dogs[0]);
    	$bill->delete();
    	
    	// Create with lazyload and load all with
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$dogs = Dog::loadAll(self::$pdo,true);
    	$this->assertSame($bill, $dogs[0]);
    	$bill->delete();
    }
    
    /**
     * LazyLoad test for fetch()
     * @depends testCreate
     * @depends testDeleteLazyLoad
     * @depends testSelectAllAndFetch
     */
    public function testFetchLazyLoad()
    {
    	// Create with lazyload and fetch without
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$statement = Dog::selectAll(self::$pdo);
    	$this->assertNotSame($bill, Dog::fetch(self::$pdo, $statement, false));
    	$bill->delete();
    	
    	// Create without lazyload and fetch with
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$statement = Dog::selectAll(self::$pdo);
    	$this->assertNotSame($bill, Dog::fetch(self::$pdo, $statement, true));
    	$bill->delete();
    	
    	// Create without lazyload and fetch without
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$statement = Dog::selectAll(self::$pdo);
    	$this->assertNotSame($bill, Dog::fetch(self::$pdo, $statement, false));
    	$bill->delete();
    	
    	// Create with lazyload and fetch with
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$statement = Dog::selectAll(self::$pdo);
    	$this->assertSame($bill, Dog::fetch(self::$pdo, $statement, true));
    	$bill->delete();
    }
    
    /**
     * LazyLoad test for fetchAll()
     * @depends testCreate
     * @depends testDeleteLazyLoad
     * @depends testSelectAllAndFetchAll
     */
    public function testFetchAllLazyLoad()
    {
    	// Create with lazyload and fetch all without
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$statement = Dog::selectAll(self::$pdo);
    	$dogs = Dog::fetchAll(self::$pdo, $statement, false);
    	$this->assertNotSame($bill, $dogs[0]);
    	$bill->delete();
    	
    	// Create without lazyload and fetch all with
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$statement = Dog::selectAll(self::$pdo);
    	$dogs = Dog::fetchAll(self::$pdo, $statement, true);
    	$this->assertNotSame($bill, $dogs[0]);
    	$bill->delete();
    	
    	// Create without lazyload and fetch all without
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$statement = Dog::selectAll(self::$pdo);
    	$dogs = Dog::fetchAll(self::$pdo, $statement, false);
    	$this->assertNotSame($bill, $dogs[0]);
    	$bill->delete();
    	
    	// Create with lazyload and fetch all with
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$statement = Dog::selectAll(self::$pdo);
    	$dogs = Dog::fetchAll(self::$pdo, $statement, true);
    	$this->assertSame($bill, $dogs[0]);
    	$bill->delete();
    }
    
    /**
     * LazyLoad test for unserialize()
     * @depends testCreate
     * @depends testDeleteLazyLoad
     * @depends testSerializeAndUnserialize
     */
    public function testUnserializeLazyLoad()
    {
    	// Create without lazyload and unserialize with
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$serializedBill = $bill->serialize();
    	$unserializedBill = Dog::unserialize(self::$pdo, $serializedBill, true);
    	$this->assertNotSame($bill, $unserializedBill);
    	$bill->delete();
    	
    	// Create with lazyload and unserialize without
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$serializedBill = $bill->serialize();
    	$unserializedBill = Dog::unserialize(self::$pdo, $serializedBill, false);
    	$this->assertNotSame($bill, $unserializedBill);
    	$bill->delete();
    	
    	// Create without lazyload and unserialize without
    	$bill = Dog::create(self::$pdo, 'Bill', false);
    	$serializedBill = $bill->serialize();
    	$unserializedBill = Dog::unserialize(self::$pdo, $serializedBill, false);
    	$this->assertNotSame($bill, $unserializedBill);
    	$bill->delete();
    	
    	// Create with lazyload and unserialize with
    	$bill = Dog::create(self::$pdo, 'Bill', true);
    	$serializedBill = $bill->serialize();
    	$unserializedBill = Dog::unserialize(self::$pdo, $serializedBill, true);
    	$this->assertSame($bill, $unserializedBill);
    	$bill->delete();
    }
}