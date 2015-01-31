<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');
require_once(__DIR__.'/../../arrayDataSet.php');

/**
 * Person class test
 */
class PersonTest extends GenericTest
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
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Person', $james);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testCreate/1-afterCreateJamesBond.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Create entry
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Person', $starsky);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testCreate/2-afterCreateDavidStarsky.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Create entry
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert instance of
    	$this->assertInstanceOf('Person', $hutch);
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testCreate/3-afterCreateKennethHutchinson.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * Delete test
     * @depends testCreate
     */
    public function testDelete()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Delete entry
    	$this->assertTrue($james->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testDelete/1-afterDeleteJamesBond.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($starsky->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testDelete/2-afterDeleteDavidStarsky.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Delete entry
    	$this->assertTrue($hutch->delete());
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('person'));
    }
    
    /**
     * GetIdPerson test
     * @depends testCreate
     */
    public function testGetIdPerson()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert getIdPerson
    	$this->assertEquals('integer', gettype($james->getIdPerson()));
    	$this->assertGreaterThanOrEqual(0, $james->getIdPerson());
    	$this->assertEquals('integer', gettype($starsky->getIdPerson()));
    	$this->assertGreaterThanOrEqual(0, $starsky->getIdPerson());
    	$this->assertEquals('integer', gettype($hutch->getIdPerson()));
    	$this->assertGreaterThanOrEqual(0, $hutch->getIdPerson());
    	$this->assertGreaterThan($james->getIdPerson(), $starsky->getIdPerson());
    	$this->assertGreaterThan($starsky->getIdPerson(), $hutch->getIdPerson());
    }
    
    /**
     * GetFirstName test
     * @depends testCreate
     */
    public function testGetFirstName()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert getFirstName
    	$this->assertSame('James', $james->getFirstname());
    	$this->assertSame('David', $starsky->getFirstname());
    	$this->assertSame('Kenneth', $hutch->getFirstname());
    }
    
    /**
     * SetFirstName/Update test
     * @depends testCreate
     * @depends testGetFirstName
     */
    public function testSetFirstNameAndUpdate()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Set firstname with execute
    	$this->assertTrue($james->setFirstname('James2',true));
    	$this->assertTrue($starsky->setFirstname('David2',true));
    	$this->assertTrue($hutch->setFirstname('Kenneth2',true));
    	
    	// Assert getFirstName
    	$this->assertSame('James2', $james->getFirstname());
    	$this->assertSame('David2', $starsky->getFirstname());
    	$this->assertSame('Kenneth2', $hutch->getFirstname());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testSetFirstNameAndUpdate/1-afterSetFirstNameWithExecute.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Set firstname without execute
    	$this->assertTrue($james->setFirstname('James3',false));
    	$this->assertTrue($starsky->setFirstname('David3',false));
    	$this->assertTrue($hutch->setFirstname('Kenneth3',false));
    	
    	// Assert getFirstName
    	$this->assertSame('James3', $james->getFirstname());
    	$this->assertSame('David3', $starsky->getFirstname());
    	$this->assertSame('Kenneth3', $hutch->getFirstname());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testSetFirstNameAndUpdate/2-afterSetFirstNameWithoutExecute.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Update
    	$this->assertTrue($james->update());
    	$this->assertTrue($starsky->update());
    	$this->assertTrue($hutch->update());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testSetFirstNameAndUpdate/3-afterUpdate.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * GetLastName test
     * @depends testCreate
     */
    public function testGetLastName()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert getLastName
    	$this->assertSame('Bond', $james->getLastName());
    	$this->assertSame('Starsky', $starsky->getLastName());
    	$this->assertSame('Hutchinson', $hutch->getLastName());
    }
    
    /**
     * SetLastName/Update test
     * @depends testCreate
     * @depends testGetLastName
     */
    public function testSetLastNameAndUpdate()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Set LastName with execute
    	$this->assertTrue($james->setLastName('Bond2',true));
    	$this->assertTrue($starsky->setLastName('Starsky2',true));
    	$this->assertTrue($hutch->setLastName('Hutchinson2',true));
    	
    	// Assert getLastName
    	$this->assertSame('Bond2', $james->getLastName());
    	$this->assertSame('Starsky2', $starsky->getLastName());
    	$this->assertSame('Hutchinson2', $hutch->getLastName());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testSetLastNameAndUpdate/1-afterSetLastNameWithExecute.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Set LastName without execute
    	$this->assertTrue($james->setLastName('Bond3',false));
    	$this->assertTrue($starsky->setLastName('Starsky3',false));
    	$this->assertTrue($hutch->setLastName('Hutchinson3',false));
    	
    	// Assert getLastName
    	$this->assertSame('Bond3', $james->getLastName());
    	$this->assertSame('Starsky3', $starsky->getLastName());
    	$this->assertSame('Hutchinson3', $hutch->getLastName());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testSetLastNameAndUpdate/2-afterSetLastNameWithoutExecute.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Update
    	$this->assertTrue($james->update());
    	$this->assertTrue($starsky->update());
    	$this->assertTrue($hutch->update());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/personTest/testSetLastNameAndUpdate/3-afterUpdate.xml')->getTable('person');
    	$actual = $this->getConnection()->createQueryTable('person', 'SELECT '.Person::FIELDNAME_FIRSTNAME.', '.Person::FIELDNAME_LASTNAME.' FROM '.Person::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * Load test
     * @depends testCreate
     * @depends testDelete
     * @depends testGetIdPerson
     * @depends testGetFirstName
     * @depends testGetLastName
     */
    public function testLoad()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Load entries
    	$loadedJames = Person::load(self::$pdo, $james->getIdPerson());
    	$loadedStarsky = Person::load(self::$pdo, $starsky->getIdPerson());
    	$loadedHutch = Person::load(self::$pdo, $hutch->getIdPerson());
    	
    	// Assert instance of
    	$this->assertInstanceOf('Person', $loadedJames);
    	$this->assertInstanceOf('Person', $loadedStarsky);
    	$this->assertInstanceOf('Person', $loadedHutch);
    	
    	// Assert attributes
    	$this->assertSame($james->getIdPerson(), $loadedJames->getIdPerson());
    	$this->assertSame($starsky->getIdPerson(), $loadedStarsky->getIdPerson());
    	$this->assertSame($hutch->getIdPerson(), $loadedHutch->getIdPerson());
    	$this->assertSame($james->getFirstname(), $loadedJames->getFirstname());
    	$this->assertSame($starsky->getFirstname(), $loadedStarsky->getFirstname());
    	$this->assertSame($hutch->getFirstname(), $loadedHutch->getFirstname());
    	$this->assertSame($james->getLastname(), $loadedJames->getLastname());
    	$this->assertSame($starsky->getLastname(), $loadedStarsky->getLastname());
    	$this->assertSame($hutch->getLastname(), $loadedHutch->getLastname());
    	
    	// Delete entries
    	$james->delete();
    	$starsky->delete();
    	$hutch->delete();
    	
    	// Load entries
    	$loadedJames = Person::load(self::$pdo, $james->getIdPerson());
    	$loadedStarsky = Person::load(self::$pdo, $starsky->getIdPerson());
    	$loadedHutch = Person::load(self::$pdo, $hutch->getIdPerson());
    	
    	// Assert null
    	$this->assertNull($loadedJames);
    	$this->assertNull($loadedStarsky);
    	$this->assertNull($loadedHutch);
    }
    
    /**
     * TestLoadAll test
     * @depends testCreate
     * @depends testDelete
     */
    public function testLoadAll()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Load all
    	$persons = Person::loadAll(self::$pdo);
    	
    	// Assert array content
    	$this->assertCount(3, $persons);
    	$this->assertContains($james, $persons);
    	$this->assertContains($starsky, $persons);
    	$this->assertContains($hutch, $persons);
    	
    	// Deletes entries
    	$james->delete();
    	$starsky->delete();
    	$hutch->delete();
    	
    	// Load all
    	$persons = Person::loadAll(self::$pdo);
    	
    	// Assert array content
    	$this->assertCount(0, $persons);
    }
    
    /**
     * SelectAll/Fetch test
     * @depends testCreate
     * @depends testDelete
     */
    public function testSelectAllAndFetch()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Select all and fetch
    	$persons = array();
    	$statement = Person::selectAll(self::$pdo);
    	while($person = Person::fetch(self::$pdo, $statement)) {
    		$persons[] = $person;
    	}
    	
    	// Assert array content
    	$this->assertCount(3, $persons);
    	$this->assertContains($james, $persons);
    	$this->assertContains($starsky, $persons);
    	$this->assertContains($hutch, $persons);
    	
    	// Deletes entries
    	$james->delete();
    	$starsky->delete();
    	$hutch->delete();
    	
    	// Select all and fetch
    	$persons = array();
    	$statement = Person::selectAll(self::$pdo);
    	while($person = Person::fetch(self::$pdo, $statement)) {
    		$persons[] = $person;
    	}
    	
    	// Assert array content
    	$this->assertCount(0, $persons);
    }
    
    /**
     * SelectAll/FetchAll test
     * @depends testCreate
     * @depends testDelete
     */
    public function testSelectAllAndFetchAll()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Select all and fetch all
    	$statement = Person::selectAll(self::$pdo);
    	$persons = Person::fetchAll(self::$pdo, $statement);
    	
    	// Assert type
    	$this->assertEquals('array', gettype($persons));
    	
    	// Assert array content
    	$this->assertCount(3, $persons);
    	$this->assertContains($james, $persons);
    	$this->assertContains($starsky, $persons);
    	$this->assertContains($hutch, $persons);
    	
    	// Deletes entries
    	$james->delete();
    	$starsky->delete();
    	$hutch->delete();
    	
    	// Select all and fetch all
    	$statement = Person::selectAll(self::$pdo);
    	$persons = Person::fetchAll(self::$pdo, $statement);
    	
    	// Assert array content
    	$this->assertCount(0, $persons);
    }
    
    /**
     * Serialize/Unserialize test
     * @depends testCreate
     * @depends testGetIdPerson
     * @depends testGetFirstName
     * @depends testGetLastName
     */
    public function testSerializeAndUnserialize()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Serialize
    	$serializedJames = $james->serialize();
    	$serializedStarsky = $starsky->serialize();
    	$serializedHutch = $hutch->serialize();
    	
    	// Assert type
    	$this->assertEquals('string',gettype($serializedJames));
    	$this->assertEquals('string',gettype($serializedStarsky));
    	$this->assertEquals('string',gettype($serializedHutch));
    	
    	// Unserialize
    	$unserializedJames = Person::unserialize(self::$pdo, $serializedJames);
    	$unserializedStarsky = Person::unserialize(self::$pdo, $serializedStarsky);
    	$unserializedHutch = Person::unserialize(self::$pdo, $serializedHutch);
		
    	// Assert instance of
    	$this->assertInstanceOf('Person', $unserializedJames);
    	$this->assertInstanceOf('Person', $unserializedStarsky);
    	$this->assertInstanceOf('Person', $unserializedHutch);
    	
    	// Assert attributes
    	$this->assertSame($james->getIdPerson(), $unserializedJames->getIdPerson());
    	$this->assertSame($starsky->getIdPerson(), $unserializedStarsky->getIdPerson());
    	$this->assertSame($hutch->getIdPerson(), $unserializedHutch->getIdPerson());
    	$this->assertSame($james->getFirstname(), $unserializedJames->getFirstname());
    	$this->assertSame($starsky->getFirstname(), $unserializedStarsky->getFirstname());
    	$this->assertSame($hutch->getFirstname(), $unserializedHutch->getFirstname());
    	$this->assertSame($james->getLastname(), $unserializedJames->getLastname());
    	$this->assertSame($starsky->getLastname(), $unserializedStarsky->getLastname());
    	$this->assertSame($hutch->getLastname(), $unserializedHutch->getLastname());
    }
    
    /**
     * Exists test
     * @depends testCreate
     * @depends testDelete
     */
    public function testExists()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert exists
    	$this->assertTrue($james->exists());
    	$this->assertTrue($starsky->exists());
    	$this->assertTrue($hutch->exists());
    	
    	// Delete entry
    	$james->delete();
    	
    	// Assert exists
    	$this->assertFalse($james->exists());
    	$this->assertTrue($starsky->exists());
    	$this->assertTrue($hutch->exists());
    	
    	// Delete entry
    	$starsky->delete();
    	
    	// Assert exists
    	$this->assertFalse($james->exists());
    	$this->assertFalse($starsky->exists());
    	$this->assertTrue($hutch->exists());
    	
    	// Delete entry
    	$hutch->delete();
    	
    	// Assert exists
    	$this->assertFalse($james->exists());
    	$this->assertFalse($starsky->exists());
    	$this->assertFalse($hutch->exists());
    }
    
    /**
     * Equals test
     * @depends testCreate
     * @depends testLoad
     * @depends testGetIdPerson
     */
    public function testEquals()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert equals
    	$this->assertTrue($james->equals($james));
    	$this->assertTrue($starsky->equals($starsky));
    	$this->assertTrue($hutch->equals($hutch));
    	
    	// Assert not equals
    	$this->assertFalse($james->equals($starsky));
    	$this->assertFalse($starsky->equals($hutch));
    	$this->assertFalse($hutch->equals($james));
    	$this->assertFalse($james->equals('James'));
    	$this->assertFalse($starsky->equals(2));
    	$this->assertFalse($hutch->equals(null));
    	
    	// Load entries
    	$loadedJames = Person::load(self::$pdo, $james->getIdPerson());
    	$loadedStarsky = Person::load(self::$pdo, $starsky->getIdPerson());
    	$loadedHutch = Person::load(self::$pdo, $hutch->getIdPerson());
    	
    	// Assert equals
    	$this->assertTrue($james->equals($loadedJames));
    	$this->assertTrue($starsky->equals($loadedStarsky));
    	$this->assertTrue($hutch->equals($loadedHutch));
    	
    	// Assert not equals
    	$this->assertFalse($james->equals($loadedStarsky));
    	$this->assertFalse($starsky->equals($loadedHutch));
    	$this->assertFalse($hutch->equals($loadedJames));
    }
    
    /**
     * Count test
     * @depends testCreate
     * @depends testDelete
     */
    public function testCount()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert count
    	$this->assertEquals(3, Person::count(self::$pdo));
    	
    	// Delete entry
    	$james->delete();
    	
    	// Assert count
    	$this->assertEquals(2, Person::count(self::$pdo));
    	
    	// Delete entry
    	$starsky->delete();
    	
    	// Assert count
    	$this->assertEquals(1, Person::count(self::$pdo));
    	
    	// Delete entry
    	$hutch->delete();
    	
    	// Assert count
    	$this->assertEquals(0, Person::count(self::$pdo));
    }
    
    /**
     * Reload test
     * @depends testCreate
     * @depends testGetIdPerson
     * @depends testGetFirstName
     * @depends testGetLastName
     */
    public function testReload()
    {
    	// Create entries
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Update entries
    	$statement = self::$pdo->prepare('UPDATE '.Person::TABLENAME.' SET '.Person::FIELDNAME_FIRSTNAME.' = ?, '.Person::FIELDNAME_LASTNAME.' = ? WHERE '.Person::FIELDNAME_IDPERSON.' = ?');
    	$statement->execute(array('James2', 'Bond2', $james->getIdPerson()));
    	$statement->execute(array('David2', 'Starsky2', $starsky->getIdPerson()));
    	$statement->execute(array('Kenneth2', 'Hutchinson2', $hutch->getIdPerson()));
    	
    	// Assert firstname and lastname
    	$this->assertSame('James', $james->getFirstname());
    	$this->assertSame('David', $starsky->getFirstname());
    	$this->assertSame('Kenneth', $hutch->getFirstname());
    	$this->assertSame('Bond', $james->getLastname());
    	$this->assertSame('Starsky', $starsky->getLastname());
    	$this->assertSame('Hutchinson', $hutch->getLastname());
    	
    	// Reload
    	$james->reload();
    	$starsky->reload();
    	$hutch->reload();
    	
    	// Assert firstname and lastname
    	$this->assertSame('James2', $james->getFirstname());
    	$this->assertSame('David2', $starsky->getFirstname());
    	$this->assertSame('Kenneth2', $hutch->getFirstname());
    	$this->assertSame('Bond2', $james->getLastname());
    	$this->assertSame('Starsky2', $starsky->getLastname());
    	$this->assertSame('Hutchinson2', $hutch->getLastname());
    }
    
    /**
     * AddCar test
     * @depends testCreate
     * @depends testGetIdPerson
     */
    public function testAddCar()
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
    	$james->addCar($vanquish);
    	$james->addCar($esprit);
    	$james->addCar($fastback);
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
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
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * AddListOfCars test
     * @depends testCreate
     * @depends testGetIdPerson
     */
    public function testAddListOfCars()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addListOfCars(array($torino));
    	$hutch->addListOfCars(array($torino));
    	
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
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * AddCarById test
     * @depends testCreate
     * @depends testGetIdPerson
     */
    public function testAddCarById()
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
    	$james->addCarById($vanquish->getIdCar());
    	$james->addCarById($esprit->getIdCar());
    	$james->addCarById($fastback->getIdCar());
    	$starsky->addCarById($torino->getIdCar());
    	$hutch->addCarById($torino->getIdCar());
    	
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
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * RemoveCar test
     * @depends testCreate
     * @depends testAddCar
     * @depends testAddListOfCars
     * @depends testGetIdPerson
     */
    public function testRemoveCar()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
    	// Remove a car of a person
    	$james->removeCar($vanquish);
    	
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
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person
    	$james->removeCar($esprit);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person
    	$james->removeCar($fastback);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person
    	$starsky->removeCar($torino);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person
    	$hutch->removeCar($torino);
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * RemoveListOfCars test
     * @depends testCreate
     * @depends testAddCar
     * @depends testAddListOfCars
     * @depends testGetIdPerson
     */
    public function testRemoveListOfCars()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);

    	// Remove a list of cars of a person
    	$james->removeListOfCars(array($vanquish,$esprit));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Remove a list of cars of a person
    	$james->removeListOfCars(array($fastback));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Remove a list of cars of a person
    	$starsky->removeListOfCars(array($torino));
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a list of cars of a person
    	$hutch->removeListOfCars(array($torino));
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * RemoveCarById test
     * @depends testCreate
     * @depends testAddCar
     * @depends testAddListOfCars
     * @depends testGetIdPerson
     */
    public function testRemoveCarById()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
    	// Remove a car of a person by id
    	$james->removeCarById($vanquish->getIdCar());
    	
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
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person by id
    	$james->removeCarById($esprit->getIdCar());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $fastback->getIdCar(), 'fk_idperson' => $james->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person by id
    	$james->removeCarById($fastback->getIdCar());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person by id
    	$starsky->removeCarById($torino->getIdCar());
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    	
    	// Remove a car of a person by id
    	$hutch->removeCarById($torino->getIdCar());
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * RemoveAllCars test
     * @depends testCreate
     * @depends testAddCar
     * @depends testAddListOfCars
     * @depends testGetIdPerson
     */
    public function testRemoveAllCars()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
    	// Remove all cars of a person
    	$affectedRows = $james->removeAllCars();
    	
    	// Assert number affected rows
    	$this->assertSame(3,$affectedRows);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $starsky->getIdPerson()),
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Remove all cars of a person
    	$affectedRows = $starsky->removeAllCars();
    	
    	// Assert number affected rows
    	$this->assertSame(1,$affectedRows);
    	
    	// Assert table content
    	$dataset = new ArrayDataSet(array(
    			'car_person' => array(
    					array('fk_idcar' => $torino->getIdCar(), 'fk_idperson' => $hutch->getIdPerson())
    			)
    	));
    	$expected = $dataset->getTable('car_person');
    	$actual = $this->getConnection()->createQueryTable('car_person', 'SELECT '.Association_CarPerson::FIELDNAME_CAR_IDCAR.', '.Association_CarPerson::FIELDNAME_PERSON_IDPERSON.' FROM '.Association_CarPerson::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);

    	// Remove all cars of a person
    	$affectedRows = $hutch->removeAllCars();
    	
    	// Assert number affected rows
    	$this->assertSame(1,$affectedRows);
    	
    	// Assert table content
    	$this->assertEquals(0, $this->getConnection()->getRowCount('car_person'));
    }
    
    /**
     * SelectCars test
     * @depends testCreate
     * @depends testAddCar
     * @depends testAddListOfCars
     * @depends testRemoveCar
     */
    public function testSelectCars()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
    	// Select/FetchAll persons's car
    	$jamesCars = Car::fetchAll(self::$pdo, $james->selectCars());
    	$starskyCars = Car::fetchAll(self::$pdo, $starsky->selectCars());
    	$hutchCars = Car::fetchAll(self::$pdo, $hutch->selectCars());

    	// Assert arrays content
    	$this->assertCount(3, $jamesCars);
    	$this->assertContains($vanquish, $jamesCars);
    	$this->assertContains($esprit, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);
    	
    	// Remove car of a person
    	$james->removeCar($vanquish);
		
    	// Select/FetchAll persons's car
    	$jamesCars = Car::fetchAll(self::$pdo, $james->selectCars());
    	$starskyCars = Car::fetchAll(self::$pdo, $starsky->selectCars());
    	$hutchCars = Car::fetchAll(self::$pdo, $hutch->selectCars());
    	
    	// Assert arrays content
    	$this->assertCount(2, $jamesCars);
    	$this->assertContains($esprit, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);
    	
    	// Remove car of a person
    	$james->removeCar($esprit);
		
    	// Select/FetchAll persons's car
    	$jamesCars = Car::fetchAll(self::$pdo, $james->selectCars());
    	$starskyCars = Car::fetchAll(self::$pdo, $starsky->selectCars());
    	$hutchCars = Car::fetchAll(self::$pdo, $hutch->selectCars());
    	
    	// Assert arrays content
    	$this->assertCount(1, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);
    	
    	// Remove car of a person
    	$james->removeCar($fastback);
		
    	// Select/FetchAll persons's car
    	$jamesCars = Car::fetchAll(self::$pdo, $james->selectCars());
    	$starskyCars = Car::fetchAll(self::$pdo, $starsky->selectCars());
    	$hutchCars = Car::fetchAll(self::$pdo, $hutch->selectCars());
    	
    	// Assert arrays content
    	$this->assertCount(0, $jamesCars);
    	$this->assertCount(1, $starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);
    	
    	// Remove car of a person
    	$starsky->removeCar($torino);
		
    	// Select/FetchAll persons's car
    	$jamesCars = Car::fetchAll(self::$pdo, $james->selectCars());
    	$starskyCars = Car::fetchAll(self::$pdo, $starsky->selectCars());
    	$hutchCars = Car::fetchAll(self::$pdo, $hutch->selectCars());
    	
    	// Assert arrays content
    	$this->assertCount(0, $jamesCars);
    	$this->assertCount(0, $starskyCars);
    	$this->assertCount(1, $hutchCars);
    	$this->assertContains($torino, $hutchCars);
    	
    	// Remove car of a person
    	$hutch->removeCar($torino);
		
    	// Select/FetchAll persons's car
    	$jamesCars = Car::fetchAll(self::$pdo, $james->selectCars());
    	$starskyCars = Car::fetchAll(self::$pdo, $starsky->selectCars());
    	$hutchCars = Car::fetchAll(self::$pdo, $hutch->selectCars());
    	
    	// Assert arrays content
    	$this->assertCount(0, $jamesCars);
    	$this->assertCount(0, $starskyCars);
    	$this->assertCount(0, $hutchCars);
    }
    
    /**
     * SelectByCar test
     * @depends testCreate
     * @depends testAddCar
     * @depends testAddListOfCars
     * @depends testRemoveCar
     * @depends testSelectAllAndFetchAll
     */
    public function testSelectByCar()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
    	// SelectByCar/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $vanquish));
    	$espritPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $esprit));
    	$fastbackPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $fastback));
    	$torinoPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $torino));

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
    	
    	// Remove car of a person
    	$james->removeCar($vanquish);
    	
    	// SelectByCar/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $vanquish));
    	$espritPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $esprit));
    	$fastbackPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $fastback));
    	$torinoPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $torino));

    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(1, $espritPersons);
    	$this->assertContains($james, $espritPersons);
    	$this->assertCount(1, $fastbackPersons);
    	$this->assertContains($james, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);
    	
    	// Remove car of a person
    	$james->removeCar($esprit);
    	
    	// SelectByCar/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $vanquish));
    	$espritPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $esprit));
    	$fastbackPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $fastback));
    	$torinoPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $torino));

    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(1, $fastbackPersons);
    	$this->assertContains($james, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);
    	
    	// Remove car of a person
    	$james->removeCar($fastback);
    	
    	// SelectByCar/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $vanquish));
    	$espritPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $esprit));
    	$fastbackPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $fastback));
    	$torinoPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $torino));

    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(2, $torinoPersons);
    	$this->assertContains($starsky, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);
    	
    	// Remove car of a person
    	$starsky->removeCar($torino);
    	
    	// SelectByCar/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $vanquish));
    	$espritPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $esprit));
    	$fastbackPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $fastback));
    	$torinoPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $torino));

    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(1, $torinoPersons);
    	$this->assertContains($hutch, $torinoPersons);
    	
    	// Remove car of a person
    	$hutch->removeCar($torino);
    	
    	// SelectByCar/FetchAll cars's persons
    	$vanquishPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $vanquish));
    	$espritPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $esprit));
    	$fastbackPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $fastback));
    	$torinoPersons = Person::fetchAll(self::$pdo, Person::selectByCar(self::$pdo, $torino));

    	// Assert arrays content
    	$this->assertCount(0, $vanquishPersons);
    	$this->assertCount(0, $espritPersons);
    	$this->assertCount(0, $fastbackPersons);
    	$this->assertCount(0, $torinoPersons);
    }
    
    /**
     * Iterator test
     * @depends testCreate
     * @depends testAddListOfCars
     * @depends testAddCar
     */
    public function testIterator()
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
    	$james->addListOfCars(array($vanquish,$esprit,$fastback));
    	$starsky->addCar($torino);
    	$hutch->addCar($torino);
    	
    	// Use persons as iterators
    	$jamesCars = array();
    	foreach($james as $car) {
    		$jamesCars[] = $car;
    	}
    	$starskyCars = array();
    	foreach($starsky as $car) {
    		$starskyCars[] = $car;
    	}
    	$hutchCars = array();
    	foreach($hutch as $car) {
    		$hutchCars[] = $car;
    	}
    	
    	// Assert arrays content
    	$this->assertCount(3,$jamesCars);
    	$this->assertContains($vanquish, $jamesCars);
    	$this->assertContains($esprit, $jamesCars);
    	$this->assertContains($fastback, $jamesCars);
    	$this->assertCount(1,$starskyCars);
    	$this->assertContains($torino, $starskyCars);
    	$this->assertCount(1,$hutchCars);
    	$this->assertContains($torino, $hutchCars);
    	
    	// Delete cars
    	$vanquish->delete();
    	$esprit->delete();
    	$fastback->delete();
    	$torino->delete();
    	
    	// Use persons as iterators
    	$jamesCars = array();
    	foreach($james as $car) {
    		$jamesCars[] = $car;
    	}
    	$starskyCars = array();
    	foreach($starsky as $car) {
    		$starskyCars[] = $car;
    	}
    	$hutchCars = array();
    	foreach($hutch as $car) {
    		$hutchCars[] = $car;
    	}
    	
    	// Assert arrays content
    	$this->assertCount(0,$jamesCars);
    	$this->assertCount(0,$starskyCars);
    	$this->assertCount(0,$hutchCars);
    }
    
    /**
     * ToString test
     * @depends testCreate
     * @depends testGetIdPerson
     */
    public function testToString()
    {
    	// Create persons
    	$james = Person::create(self::$pdo, 'James', 'Bond');
    	$starsky = Person::create(self::$pdo, 'David', 'Starsky');
    	$hutch = Person::create(self::$pdo, 'Kenneth', 'Hutchinson');
    	
    	// Assert toString
    	$this->assertSame('[Person idPerson="'.$james->getIdPerson().'" firstname="James" lastname="Bond"]', $james->__toString());
    	$this->assertSame('[Person idPerson="'.$starsky->getIdPerson().'" firstname="David" lastname="Starsky"]', $starsky->__toString());
    	$this->assertSame('[Person idPerson="'.$hutch->getIdPerson().'" firstname="Kenneth" lastname="Hutchinson"]', $hutch->__toString());
    }    
}