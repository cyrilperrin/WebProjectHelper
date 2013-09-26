<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');
require_once(__DIR__.'/../../arrayDataSet.php');

/**
 * Car class test
 */
class CarTest extends GenericTest
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
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Car', $vanquish);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testCreate/1-afterCreateVanquishAstonMartin.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Create entry
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Car', $esprit);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testCreate/2-afterCreateEspritLotus.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Create entry
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Car', $fastback);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testCreate/3-afterCreateFastbackMustang.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Create entry
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Car', $torino);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testCreate/4-afterCreateFordTorinoFord.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * Delete test
     * @depends testCreate
     */
    public function testDelete()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Delete entry
    	$this->assertTrue($vanquish->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testDelete/1-afterDeleteVanquishAstonMartin.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($esprit->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testDelete/2-afterDeleteEspritLotus.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($fastback->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testDelete/3-afterDeleteFastbackMustang.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($torino->delete());
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car'));
    }
    
    /**
     * GetIdCar test
     * @depends testCreate
     */
    public function testGetIdCar()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert getIdCar
    	$this->assertEquals('integer', gettype($vanquish->getIdCar()));
    	$this->assertGreaterThanOrEqual(0, $vanquish->getIdCar());
    	$this->assertEquals('integer', gettype($esprit->getIdCar()));
    	$this->assertGreaterThanOrEqual(0, $esprit->getIdCar());
    	$this->assertEquals('integer', gettype($fastback->getIdCar()));
    	$this->assertGreaterThanOrEqual(0, $fastback->getIdCar());
    	$this->assertEquals('integer', gettype($torino->getIdCar()));
    	$this->assertGreaterThanOrEqual(0, $torino->getIdCar());
    	$this->assertGreaterThan($vanquish->getIdCar(), $esprit->getIdCar());
    	$this->assertGreaterThan($esprit->getIdCar(), $fastback->getIdCar());
    	$this->assertGreaterThan($fastback->getIdCar(), $torino->getIdCar());
    }
    
    /**
     * GetModel test
     * @depends testCreate
     */
    public function testGetModel()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert getModel
    	$this->assertSame('Vanquish', $vanquish->getModel());
    	$this->assertSame('Esprit', $esprit->getModel());
    	$this->assertSame('Fastback', $fastback->getModel());
    	$this->assertSame('Ford Torino', $torino->getModel());
    }
    
    /**
     * SetModel/Update test
     * @depends testCreate
     * @depends testGetModel
     */
    public function testSetModelAndUpdate()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Set model with execute
    	$this->assertTrue($vanquish->setModel('Vanquish2',true));
    	$this->assertTrue($esprit->setModel('Esprit2',true));
    	$this->assertTrue($fastback->setModel('Fastback2',true));
    	$this->assertTrue($torino->setModel('Ford Torino2',true));
    	
    	// Assert getModel
    	$this->assertSame('Vanquish2', $vanquish->getModel());
    	$this->assertSame('Esprit2', $esprit->getModel());
    	$this->assertSame('Fastback2', $fastback->getModel());
    	$this->assertSame('Ford Torino2', $torino->getModel());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testSetModelAndUpdate/1-afterSetModelWithExecute.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Set model without execute
    	$this->assertTrue($vanquish->setModel('Vanquish3',false));
    	$this->assertTrue($esprit->setModel('Esprit3',false));
    	$this->assertTrue($fastback->setModel('Fastback3',false));
    	$this->assertTrue($torino->setModel('Ford Torino3',false));
    	
    	// Assert getModel
    	$this->assertSame('Vanquish3', $vanquish->getModel());
    	$this->assertSame('Esprit3', $esprit->getModel());
    	$this->assertSame('Fastback3', $fastback->getModel());
    	$this->assertSame('Ford Torino3', $torino->getModel());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testSetModelAndUpdate/2-afterSetModelWithoutExecute.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Update
    	$this->assertTrue($vanquish->update());
    	$this->assertTrue($esprit->update());
    	$this->assertTrue($fastback->update());
    	$this->assertTrue($torino->update());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testSetModelAndUpdate/3-afterUpdate.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * GetBrand test
     * @depends testCreate
     */
    public function testGetBrand()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert getBrand
    	$this->assertSame('Aston Martin', $vanquish->getBrand());
    	$this->assertSame('Lotus', $esprit->getBrand());
    	$this->assertSame('Mustang', $fastback->getBrand());
    	$this->assertSame('Ford', $torino->getBrand());
    }
    
    /**
     * SetBrand/Update test
     * @depends testCreate
     * @depends testGetBrand
     */
    public function testSetBrandAndUpdate()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Set model with execute
    	$this->assertTrue($vanquish->setBrand('Aston Martin2',true));
    	$this->assertTrue($esprit->setBrand('Lotus2',true));
    	$this->assertTrue($fastback->setBrand('Mustang2',true));
    	$this->assertTrue($torino->setBrand('Ford2',true));
    	
    	// Assert getModel
    	$this->assertSame('Aston Martin2', $vanquish->getBrand());
    	$this->assertSame('Lotus2', $esprit->getBrand());
    	$this->assertSame('Mustang2', $fastback->getBrand());
    	$this->assertSame('Ford2', $torino->getBrand());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testSetBrandAndUpdate/1-afterSetBrandWithExecute.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Set model without execute
    	$this->assertTrue($vanquish->setBrand('Aston Martin3',false));
    	$this->assertTrue($esprit->setBrand('Lotus3',false));
    	$this->assertTrue($fastback->setBrand('Mustang3',false));
    	$this->assertTrue($torino->setBrand('Ford3',false));
    	
    	// Assert getModel
    	$this->assertSame('Aston Martin3', $vanquish->getBrand());
    	$this->assertSame('Lotus3', $esprit->getBrand());
    	$this->assertSame('Mustang3', $fastback->getBrand());
    	$this->assertSame('Ford3', $torino->getBrand());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testSetBrandAndUpdate/2-afterSetBrandWithoutExecute.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Update
    	$this->assertTrue($vanquish->update());
    	$this->assertTrue($esprit->update());
    	$this->assertTrue($fastback->update());
    	$this->assertTrue($torino->update());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/carTest/testSetBrandAndUpdate/3-afterUpdate.xml')->getTable('car');
    	$actual = $this->getConnection()->createQueryTable('car', 'SELECT '.Car::FIELDNAME_MODEL.', '.Car::FIELDNAME_BRAND.' FROM '.Car::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * Load test
     * @depends testCreate
     * @depends testDelete
     * @depends testGetIdCar
     * @depends testGetModel
     * @depends testGetBrand
     */
    public function testLoad()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Load entries
    	$loadedVanquish = Car::load(self::$pdo, $vanquish->getIdCar());
    	$loadedEsprit = Car::load(self::$pdo, $esprit->getIdCar());
    	$loadedFastback = Car::load(self::$pdo, $fastback->getIdCar());
    	$loadedTorino = Car::load(self::$pdo, $torino->getIdCar());
    	
    	// Assert instance of
    	$this->assertInstanceOf('Car', $loadedVanquish);
    	$this->assertInstanceOf('Car', $loadedEsprit);
    	$this->assertInstanceOf('Car', $loadedFastback);
    	$this->assertInstanceOf('Car', $loadedTorino);
    	
    	// Assert attributes
    	$this->assertSame($vanquish->getIdCar(), $loadedVanquish->getIdCar());
    	$this->assertSame($esprit->getIdCar(), $loadedEsprit->getIdCar());
    	$this->assertSame($fastback->getIdCar(), $loadedFastback->getIdCar());
    	$this->assertSame($torino->getIdCar(), $loadedTorino->getIdCar());
    	$this->assertSame($vanquish->getModel(), $loadedVanquish->getModel());
    	$this->assertSame($esprit->getModel(), $loadedEsprit->getModel());
    	$this->assertSame($fastback->getModel(), $loadedFastback->getModel());
    	$this->assertSame($torino->getModel(), $loadedTorino->getModel());
    	$this->assertSame($vanquish->getBrand(), $loadedVanquish->getBrand());
    	$this->assertSame($esprit->getBrand(), $loadedEsprit->getBrand());
    	$this->assertSame($fastback->getBrand(), $loadedFastback->getBrand());
    	$this->assertSame($torino->getBrand(), $loadedTorino->getBrand());
    	
    	// Delete entries
    	$vanquish->delete();
    	$esprit->delete();
    	$fastback->delete();
    	$torino->delete();
    	
    	// Load entries
    	$loadedVanquish = Car::load(self::$pdo, $vanquish->getIdCar());
    	$loadedEsprit = Car::load(self::$pdo, $esprit->getIdCar());
    	$loadedFastback = Car::load(self::$pdo, $fastback->getIdCar());
    	$loadedTorino = Car::load(self::$pdo, $torino->getIdCar());
    	
    	// Assert null
    	$this->assertNull($loadedVanquish);
    	$this->assertNull($loadedEsprit);
    	$this->assertNull($loadedFastback);
    	$this->assertNull($loadedTorino);
    }
    
    /**
     * TestLoadAll test
     * @depends testCreate
     * @depends testDelete
     */
    public function testLoadAll()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Load all
    	$cars = Car::loadAll(self::$pdo);
    	
    	// Assert array content
    	$this->assertCount(4, $cars);
    	$this->assertContains($vanquish, $cars);
    	$this->assertContains($esprit, $cars);
    	$this->assertContains($fastback, $cars);
    	$this->assertContains($torino, $cars);
    	
    	// Delete entries
    	$vanquish->delete();
    	$esprit->delete();
    	$fastback->delete();
    	$torino->delete();
    	
    	// Load all
    	$cars = Car::loadAll(self::$pdo);

    	// Assert array content
    	$this->assertCount(0, $cars);
    }
    
    /**
     * SelectAll/Fetch test
     * @depends testCreate
     * @depends testDelete
     */
    public function testSelectAllAndFetch()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Select all and fetch
    	$cars = array();
    	$statement = Car::selectAll(self::$pdo);
    	while($car = Car::fetch(self::$pdo, $statement)) {
    		$cars[] = $car;
    	}
    	
    	// Assert array content
    	$this->assertCount(4, $cars);
    	$this->assertContains($vanquish, $cars);
    	$this->assertContains($esprit, $cars);
    	$this->assertContains($fastback, $cars);
    	$this->assertContains($torino, $cars);
    	
    	// Delete entries
    	$vanquish->delete();
    	$esprit->delete();
    	$fastback->delete();
    	$torino->delete();
    	
    	// Select all and fetch
    	$cars = array();
    	$statement = Car::selectAll(self::$pdo);
    	while($car = Car::fetch(self::$pdo, $statement)) {
    		$cars[] = $car;
    	}

    	// Assert array content
    	$this->assertCount(0, $cars);
    }
    
    /**
     * SelectAll/FetchAll test
     * @depends testCreate
     * @depends testDelete
     */
    public function testSelectAllAndFetchAll()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Select all and fetch
    	$statement = Car::selectAll(self::$pdo);
    	$cars = Car::fetchAll(self::$pdo, $statement);
    	
    	// Assert type
    	$this->assertEquals('array', gettype($cars));
    	
    	// Assert array content
    	$this->assertCount(4, $cars);
    	$this->assertContains($vanquish, $cars);
    	$this->assertContains($esprit, $cars);
    	$this->assertContains($fastback, $cars);
    	$this->assertContains($torino, $cars);
    	
    	// Delete entries
    	$vanquish->delete();
    	$esprit->delete();
    	$fastback->delete();
    	$torino->delete();
    	
    	// Select all and fetch
    	$statement = Car::selectAll(self::$pdo);
    	$cars = Car::fetchAll(self::$pdo, $statement);

    	// Assert array content
    	$this->assertCount(0, $cars);
    }
    
    /**
     * Serialize/Unserialize test
     * @depends testCreate
     * @depends testGetIdCar
     * @depends testGetModel
     * @depends testGetBrand
     */
    public function testSerializeAndUnserialize()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Serialize entries
    	$serializedVanquish = $vanquish->serialize();
    	$serializedEsprit = $esprit->serialize();
    	$serializedFastback = $fastback->serialize();
    	$serializedTorino = $torino->serialize();
    	
    	// Assert type
    	$this->assertEquals('string',gettype($serializedVanquish));
    	$this->assertEquals('string',gettype($serializedEsprit));
    	$this->assertEquals('string',gettype($serializedFastback));
    	$this->assertEquals('string',gettype($serializedTorino));
    	
    	// Unserialize entries
    	$unserializedVanquish = Car::unserialize(self::$pdo, $serializedVanquish);
    	$unserializedEsprit = Car::unserialize(self::$pdo, $serializedEsprit);
    	$unserializedFastback = Car::unserialize(self::$pdo, $serializedFastback);
    	$unserializedTorino = Car::unserialize(self::$pdo, $serializedTorino);
    	
    	// Assert instance of
    	$this->assertInstanceOf('Car', $unserializedVanquish);
    	$this->assertInstanceOf('Car', $unserializedEsprit);
    	$this->assertInstanceOf('Car', $unserializedFastback);
    	$this->assertInstanceOf('Car', $unserializedTorino);

    	// Assert attributes
    	$this->assertSame($vanquish->getIdCar(), $unserializedVanquish->getIdCar());
    	$this->assertSame($esprit->getIdCar(), $unserializedEsprit->getIdCar());
    	$this->assertSame($fastback->getIdCar(), $unserializedFastback->getIdCar());
    	$this->assertSame($torino->getIdCar(), $unserializedTorino->getIdCar());
    	$this->assertSame($vanquish->getModel(), $unserializedVanquish->getModel());
    	$this->assertSame($esprit->getModel(), $unserializedEsprit->getModel());
    	$this->assertSame($fastback->getModel(), $unserializedFastback->getModel());
    	$this->assertSame($torino->getModel(), $unserializedTorino->getModel());
    	$this->assertSame($vanquish->getBrand(), $unserializedVanquish->getBrand());
    	$this->assertSame($esprit->getBrand(), $unserializedEsprit->getBrand());
    	$this->assertSame($fastback->getBrand(), $unserializedFastback->getBrand());
    	$this->assertSame($torino->getBrand(), $unserializedTorino->getBrand());
    }
    
    /**
     * Exists test
     * @depends testCreate
     * @depends testDelete
     */
    public function testExists()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert exists
    	$this->assertTrue($vanquish->exists());
    	$this->assertTrue($esprit->exists());
    	$this->assertTrue($fastback->exists());
    	$this->assertTrue($torino->exists());
    	
    	// Delete entry
    	$vanquish->delete();
    	
    	// Assert exists
    	$this->assertFalse($vanquish->exists());
    	$this->assertTrue($esprit->exists());
    	$this->assertTrue($fastback->exists());
    	$this->assertTrue($torino->exists());
    	
    	// Delete entry
    	$esprit->delete();
    	
    	// Assert exists
    	$this->assertFalse($vanquish->exists());
    	$this->assertFalse($esprit->exists());
    	$this->assertTrue($fastback->exists());
    	$this->assertTrue($torino->exists());
    	
    	// Delete entry
    	$fastback->delete();
    	
    	// Assert exists
    	$this->assertFalse($vanquish->exists());
    	$this->assertFalse($esprit->exists());
    	$this->assertFalse($fastback->exists());
    	$this->assertTrue($torino->exists());
    	
    	// Delete entry
    	$torino->delete();
    	
    	// Assert exists
    	$this->assertFalse($vanquish->exists());
    	$this->assertFalse($esprit->exists());
    	$this->assertFalse($fastback->exists());
    	$this->assertFalse($torino->exists());
    }
    
    /**
     * Equals test
     * @depends testCreate
     * @depends testLoad
     * @depends testGetIdCar
     */
    public function testEquals()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert equals
    	$this->assertTrue($vanquish->equals($vanquish));
    	$this->assertTrue($esprit->equals($esprit));
    	$this->assertTrue($fastback->equals($fastback));
    	$this->assertTrue($torino->equals($torino));
    	
    	// Assert not equals
    	$this->assertFalse($vanquish->equals($esprit));
    	$this->assertFalse($esprit->equals($fastback));
    	$this->assertFalse($fastback->equals($torino));
    	$this->assertFalse($torino->equals($vanquish));
    	$this->assertFalse($vanquish->equals('Vanquish'));
    	$this->assertFalse($esprit->equals(2));
    	$this->assertFalse($fastback->equals(true));
    	$this->assertFalse($torino->equals(null));
    	
    	// Load entries
    	$loadedVanquish = Car::load(self::$pdo, $vanquish->getIdCar());
    	$loadedEsprit = Car::load(self::$pdo, $esprit->getIdCar());
    	$loadedFastback = Car::load(self::$pdo, $fastback->getIdCar());
    	$loadedTorino = Car::load(self::$pdo, $torino->getIdCar());
    	
    	// Assert equals
    	$this->assertTrue($vanquish->equals($loadedVanquish));
    	$this->assertTrue($esprit->equals($loadedEsprit));
    	$this->assertTrue($fastback->equals($loadedFastback));
    	$this->assertTrue($torino->equals($loadedTorino));
    	
    	// Assert not equals
    	$this->assertFalse($vanquish->equals($loadedEsprit));
    	$this->assertFalse($esprit->equals($loadedFastback));
    	$this->assertFalse($fastback->equals($loadedTorino));
    	$this->assertFalse($torino->equals($loadedVanquish));
    }
    
    /**
     * Count test
     * @depends testCreate
     * @depends testDelete
     */
    public function testCount()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert count
    	$this->assertEquals(4, Car::count(self::$pdo));
    	
    	// Delete entry
    	$vanquish->delete();
    	
    	// Assert count
    	$this->assertEquals(3, Car::count(self::$pdo));
    	
    	// Delete entry
    	$esprit->delete();
    	
    	// Assert count
    	$this->assertEquals(2, Car::count(self::$pdo));
    	
    	// Delete entry
    	$fastback->delete();
    	
    	// Assert count
    	$this->assertEquals(1, Car::count(self::$pdo));
    	
    	// Delete entry
    	$torino->delete();
    	
    	// Assert count
    	$this->assertEquals(0, Car::count(self::$pdo));
    }
    
    /**
     * Reload test
     * @depends testCreate
     * @depends testGetIdCar
     * @depends testGetModel
     * @depends testGetBrand
     */
    public function testReload()
    {
    	// Create entries
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Update entries
    	$statement = self::$pdo->prepare('UPDATE '.Car::TABLENAME.' SET '.Car::FIELDNAME_MODEL.' = ?, '.Car::FIELDNAME_BRAND.' = ? WHERE '.Car::FIELDNAME_IDCAR.' = ?');
    	$statement->execute(array('Vanquish2','Aston Martin2',$vanquish->getIdCar()));
    	$statement->execute(array('Esprit2','Lotus2',$esprit->getIdCar()));
    	$statement->execute(array('Fastback2','Mustang2',$fastback->getIdCar()));
    	$statement->execute(array('Ford Torino2','Ford2',$torino->getIdCar()));
    	
    	// Assert model and brand
    	$this->assertSame('Vanquish', $vanquish->getModel());
    	$this->assertSame('Esprit', $esprit->getModel());
    	$this->assertSame('Fastback', $fastback->getModel());
    	$this->assertSame('Ford Torino', $torino->getModel());
    	$this->assertSame('Aston Martin', $vanquish->getBrand());
    	$this->assertSame('Lotus', $esprit->getBrand());
    	$this->assertSame('Mustang', $fastback->getBrand());
    	$this->assertSame('Ford', $torino->getBrand());
    	
    	// Reload
    	$vanquish->reload();
    	$esprit->reload();
    	$fastback->reload();
    	$torino->reload();
    	
    	// Assert model and brand
    	$this->assertSame('Vanquish2', $vanquish->getModel());
    	$this->assertSame('Esprit2', $esprit->getModel());
    	$this->assertSame('Fastback2', $fastback->getModel());
    	$this->assertSame('Ford Torino2', $torino->getModel());
    	$this->assertSame('Aston Martin2', $vanquish->getBrand());
    	$this->assertSame('Lotus2', $esprit->getBrand());
    	$this->assertSame('Mustang2', $fastback->getBrand());
    	$this->assertSame('Ford2', $torino->getBrand());
    }
    
    /**
     * AddPerson test
     * @depends testCreate
     * @depends testGetIdCar
     */
    public function testAddPerson()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addPerson($starsky);
    	$torino->addPerson($hutch);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $vanquish->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * AddListOfPersons test
     * @depends testCreate
     * @depends testGetIdCar
     */
    public function testAddListOfPersons()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addListOfPersons(array($james));
    	$esprit->addListOfPersons(array($james));
    	$fastback->addListOfPersons(array($james));
    	$torino->addListOfPersons(array($starsky,$hutch));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $vanquish->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * AddPersonById test
     * @depends testCreate
     * @depends testGetIdCar
     */
    public function testAddPersonById()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPersonById($james->getIdPerson());
    	$esprit->addPersonById($james->getIdPerson());
    	$fastback->addPersonById($james->getIdPerson());
    	$torino->addPersonById($starsky->getIdPerson());
    	$torino->addPersonById($hutch->getIdPerson());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $vanquish->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * RemovePerson test
     * @depends testCreate
     * @depends testAddPerson
     * @depends testAddListOfPersons
     * @depends testGetIdCar
     */
    public function testRemovePerson()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addListOfPersons(array($starsky,$hutch));
    	
    	// Remove person of a car
    	$vanquish->removePerson($james);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car
    	$esprit->removePerson($james);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car
    	$fastback->removePerson($james);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car
    	$torino->removePerson($starsky);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car
    	$torino->removePerson($hutch);
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * RemoveListOfPersons test
     * @depends testCreate
     * @depends testAddPerson
     * @depends testAddListOfPersons
     * @depends testGetIdCar
     */
    public function testRemoveListOfPersons()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addListOfPersons(array($starsky,$hutch));

    	// Remove a list of persons of a car
    	$vanquish->removeListOfPersons(array($james));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Remove a list of persons of a car
    	$esprit->removeListOfPersons(array($james));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a list of persons of a car
    	$fastback->removeListOfPersons(array($james));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Remove a list of persons of a car
    	$torino->removeListOfPersons(array($starsky,$hutch));
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * RemovePersonById test
     * @depends testCreate
     * @depends testAddPerson
     * @depends testAddListOfPersons
     * @depends testGetIdCar
     */
    public function testRemovePersonById()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addListOfPersons(array($starsky,$hutch));
    	
    	// Remove person of a car by id
    	$vanquish->removePersonById($james->getIdPerson());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car by id
    	$esprit->removePersonById($james->getIdPerson());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car by id
    	$fastback->removePersonById($james->getIdPerson());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car by id
    	$torino->removePersonById($starsky->getIdPerson());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove person of a car by id
    	$torino->removePersonById($hutch->getIdPerson());
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * RemoveAllPersons test
     * @depends testCreate
     * @depends testAddPerson
     * @depends testAddListOfPersons
     * @depends testGetIdCar
     */
    public function testRemoveAllPersons()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addListOfPersons(array($starsky,$hutch));
    	
    	// Remove all persons of a car
    	$affectedRows = $vanquish->removeAllPersons();
    	
    	// Assert number affected rows
    	$this->assertSame(1,$affectedRows);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $esprit->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove all persons of a car
    	$affectedRows = $esprit->removeAllPersons();
    	
    	// Assert number affected rows
    	$this->assertSame(1,$affectedRows);

    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove all persons of a car
    	$affectedRows = $fastback->removeAllPersons();
    	
    	// Assert number affected rows
    	$this->assertSame(1,$affectedRows);

    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Car_Person::FIELDNAME_CAR_IDCAR.', '.Car_Person::FIELDNAME_PERSON_IDPERSON.' FROM '.Car_Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove all persons of a car
    	$affectedRows = $torino->removeAllPersons();
    	
    	// Assert number affected rows
    	$this->assertSame(2,$affectedRows);
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * SelectPersons test
     * @depends testCreate
     * @depends testAddPerson
     * @depends testAddListOfPersons
     * @depends testRemovePerson
     */
    public function testSelectPersons()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addListOfPersons(array($starsky,$hutch));
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(1, $vanquishPersons);
    	$this->assertContains($james, $vanquishPersons);
    	$this->assertCount(1, $espritPersons);
    	$this->assertContains($james, $espritPersons);
    	$this->assertCount(1, $fastbackPersons);
    	$this->assertContains($james, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);

    	// Remove person of a car
    	$vanquish->removePerson($james);
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(1, $espritPersons);
    	$this->assertContains($james, $espritPersons);
    	$this->assertCount(1, $fastbackPersons);
    	$this->assertContains($james, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);

    	// Remove person of a car
    	$esprit->removePerson($james);
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(1, $fastbackPersons);
    	$this->assertContains($james, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);

    	// Remove person of a car
    	$fastback->removePerson($james);
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);

    	// Remove person of a car
    	$torino->removePerson($starsky);
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(1, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);

    	// Remove person of a car
    	$torino->removePerson($starsky);
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(1, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);

    	// Remove person of a car
    	$torino->removePerson($hutch);
    	
    	// Select/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, $vanquish->selectPersons());
    	$espritPersons = Person::fetchAll(self::$pdo, $esprit->selectPersons());
    	$fastbackPersons = Person::fetchAll(self::$pdo, $fastback->selectPersons());
    	$torinoPersons = Person::fetchAll(self::$pdo, $torino->selectPersons());
    	
    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(0, $torinoPersons);
    }
    
    /**
     * SelectByPerson test
     * @depends testCreate
     * @depends testAddPerson
     * @depends testAddListOfPersons
     * @depends testRemovePerson
     * @depends testSelectAllAndFetchAll
     */
    public function testSelectByPerson()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');

    	// Associate persons and cars
    	$vanquish->addPerson($james);
    	$esprit->addPerson($james);
    	$fastback->addPerson($james);
    	$torino->addListOfPersons(array($starsky,$hutch));
    	
    	// SelectByPerson/FetchAll persons's cars
    	$jamesCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $james));
    	$starskyCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $starsky));
    	$hutchCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $hutch));

    	// Assert arrays content
    	$this->assertCount(3, $jamesCars);
    	$this->assertContains($vanquish, $jamesCars);
    	$this->assertContains($esprit, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);

    	// Remove person of a car
    	$vanquish->removePerson($james);
    	
    	// SelectByPerson/FetchAll persons's cars
    	$jamesCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $james));
    	$starskyCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $starsky));
    	$hutchCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $hutch));

    	// Assert arrays content
    	$this->assertCount(2, $jamesCars);
    	$this->assertContains($esprit, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);

    	// Remove person of a car
    	$esprit->removePerson($james);
    	
    	// SelectByPerson/FetchAll persons's cars
    	$jamesCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $james));
    	$starskyCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $starsky));
    	$hutchCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $hutch));

    	// Assert arrays content
    	$this->assertCount(1, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);

    	// Remove person of a car
    	$fastback->removePerson($james);
    	
    	// SelectByPerson/FetchAll persons's cars
    	$jamesCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $james));
    	$starskyCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $starsky));
    	$hutchCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $hutch));

    	// Assert arrays content
    	$this->assertCount(0, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);

    	// Remove person of a car
    	$torino->removePerson($starsky);
    	
    	// SelectByPerson/FetchAll persons's cars
    	$jamesCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $james));
    	$starskyCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $starsky));
    	$hutchCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $hutch));

    	// Assert arrays content
    	$this->assertCount(0, $jamesCars);
    	$this->assertCount(0, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);

    	// Remove person of a car
    	$torino->removePerson($hutch);
    	
    	// SelectByPerson/FetchAll persons's cars
    	$jamesCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $james));
    	$starskyCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $starsky));
    	$hutchCars = Car::fetchAll(self::$pdo, Car::selectByPerson(self::$pdo, $hutch));

    	// Assert arrays content
    	$this->assertCount(0, $jamesCars);
    	$this->assertCount(0, $starskyCars);
    	$this->assertCount(0, $hutchCars);
    }
    
    /**
     * ToString test
     * @depends testCreate
     * @depends testGetIdCar
     */
    public function testToString()
    {
    	
    	// Create cars
    	$vanquish = Car::create(self::$pdo,'Vanquish','Aston Martin');
    	$esprit = Car::create(self::$pdo,'Esprit','Lotus');
    	$fastback = Car::create(self::$pdo,'Fastback','Mustang');
    	$torino = Car::create(self::$pdo,'Ford Torino','Ford');
    	
    	// Assert toString
    	$this->assertSame('[Car idCar="'.$vanquish->getIdCar().'" model="Vanquish" brand="Aston Martin"]', $vanquish->__toString());
    	$this->assertSame('[Car idCar="'.$esprit->getIdCar().'" model="Esprit" brand="Lotus"]', $esprit->__toString());
    	$this->assertSame('[Car idCar="'.$fastback->getIdCar().'" model="Fastback" brand="Mustang"]', $fastback->__toString());
    	$this->assertSame('[Car idCar="'.$torino->getIdCar().'" model="Ford Torino" brand="Ford"]', $torino->__toString());
    } 
    
}