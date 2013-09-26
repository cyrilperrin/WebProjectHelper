<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Message class test
 */
class MessageTest extends GenericTest
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
     * GetSender test
     */
    public function testGetSender()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Assert getSender
		$this->assertSame($vador,$messageA->getSender());
		$this->assertSame($luke,$messageB->getSender());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($vador,$loadedMessageA->getSender());
		$this->assertSame($luke,$loadedMessageB->getSender());
    }
    
    /**
     * GetSenderId test
     */
    public function testGetSenderId()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Assert getSender
		$this->assertSame($vador->getIdMember(),$messageA->getSenderId());
		$this->assertSame($luke->getIdMember(),$messageB->getSenderId());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($vador->getIdMember(),$loadedMessageA->getSenderId());
		$this->assertSame($luke->getIdMember(),$loadedMessageB->getSenderId());
    }
    
    /**
     * SetSender test
     * @depends testGetSender
     */
    public function testSetSender()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Set sender
		$this->assertTrue($messageA->setSender($luke));
		$this->assertTrue($messageB->setSender($vador));
		
		// Assert getSender
		$this->assertSame($luke,$messageA->getSender());
		$this->assertSame($vador,$messageB->getSender());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($luke,$loadedMessageA->getSender());
		$this->assertSame($vador,$loadedMessageB->getSender());
    }
    
    /**
     * SetSenderById test
     * @depends testGetSender
     */
    public function testSetSenderById()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Set sender
		$this->assertTrue($messageA->setSenderById($luke->getIdMember()));
		$this->assertTrue($messageB->setSenderById($vador->getIdMember()));
		
		// Assert getSender
		$this->assertSame($luke,$messageA->getSender());
		$this->assertSame($vador,$messageB->getSender());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($luke,$loadedMessageA->getSender());
		$this->assertSame($vador,$loadedMessageB->getSender());
    }
    
    /**
     * SelectBySender test
     */
    public function testSelectBySender()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// SelectBySender/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, Message::selectBySender(self::$pdo, $vador));
		$lukeMessages = Message::fetchAll(self::$pdo, Message::selectBySender(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(1, $vadorMessages);
		$this->assertContains($messageA, $vadorMessages);
		$this->assertCount(1, $lukeMessages);
		$this->assertContains($messageB, $lukeMessages);
		
		// Delete message
		$messageA->delete();
		
		// SelectBySender/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, Message::selectBySender(self::$pdo, $vador));
		$lukeMessages = Message::fetchAll(self::$pdo, Message::selectBySender(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(0, $vadorMessages);
		$this->assertCount(1, $lukeMessages);
		$this->assertContains($messageB, $lukeMessages);
		
		// Delete message
		$messageB->delete();
		
		// SelectBySender/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, Message::selectBySender(self::$pdo, $vador));
		$lukeMessages = Message::fetchAll(self::$pdo, Message::selectBySender(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(0, $vadorMessages);
		$this->assertCount(0, $lukeMessages);
    }
    
    /**
     * GetRecipient test
     */
    public function testGetRecipient()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Assert getSender
		$this->assertSame($luke,$messageA->getRecipient());
		$this->assertSame($vador,$messageB->getRecipient());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($luke,$loadedMessageA->getRecipient());
		$this->assertSame($vador,$loadedMessageB->getRecipient());
    }
    
    /**
     * GetRecipientId test
     */
    public function testGetRecipientId()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Assert getSender
		$this->assertSame($luke->getIdMember(),$messageA->getRecipientId());
		$this->assertSame($vador->getIdMember(),$messageB->getRecipientId());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($luke->getIdMember(),$loadedMessageA->getRecipientId());
		$this->assertSame($vador->getIdMember(),$loadedMessageB->getRecipientId());
    }
    
    /**
     * SetRecipient test
     * @depends testGetRecipient
     */
    public function testSetRecipient()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Set sender
		$this->assertTrue($messageA->setRecipient($vador));
		$this->assertTrue($messageB->setRecipient($luke));
		
		// Assert getSender
		$this->assertSame($vador,$messageA->getRecipient());
		$this->assertSame($luke,$messageB->getRecipient());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($vador,$loadedMessageA->getRecipient());
		$this->assertSame($luke,$loadedMessageB->getRecipient());
    }
    
    /**
     * SetRecipientById test
     * @depends testGetRecipient
     */
    public function testSetRecipientById()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Set sender
		$this->assertTrue($messageA->setRecipientById($vador->getIdMember()));
		$this->assertTrue($messageB->setRecipientById($luke->getIdMember()));
		
		// Assert getSender
		$this->assertSame($vador,$messageA->getRecipient());
		$this->assertSame($luke,$messageB->getRecipient());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getSender
		$this->assertSame($vador,$loadedMessageA->getRecipient());
		$this->assertSame($luke,$loadedMessageB->getRecipient());
    }
    
    /**
     * SelectByRecipient test
     */
    public function testSelectByRecipient()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// SelectBySender/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, Message::selectByRecipient(self::$pdo, $vador));
		$lukeMessages = Message::fetchAll(self::$pdo, Message::selectByRecipient(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(1, $vadorMessages);
		$this->assertContains($messageB, $vadorMessages);
		$this->assertCount(1, $lukeMessages);
		$this->assertContains($messageA, $lukeMessages);
		
		// Delete message
		$messageA->delete();
		
		// SelectBySender/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, Message::selectByRecipient(self::$pdo, $vador));
		$lukeMessages = Message::fetchAll(self::$pdo, Message::selectByRecipient(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(1, $vadorMessages);
		$this->assertContains($messageB, $vadorMessages);
		$this->assertCount(0, $lukeMessages);
		
		// Delete message
		$messageB->delete();
		
		// SelectBySender/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, Message::selectByRecipient(self::$pdo, $vador));
		$lukeMessages = Message::fetchAll(self::$pdo, Message::selectByRecipient(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(0, $vadorMessages);
		$this->assertCount(0, $lukeMessages);
    }
    
    /**
     * GetDate test
     */
    public function testGetDate()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Set dates
		$dateA = time();
		$dateB = $dateA+1;
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', $dateA, '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', $dateB, '...');
		
		// Assert getDate
		$this->assertSame($dateA,$messageA->getDate());
		$this->assertSame($dateB,$messageB->getDate());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getDate
		$this->assertSame($dateA,$loadedMessageA->getDate());
		$this->assertSame($dateB,$loadedMessageB->getDate());
    }
    
    /**
     * SetDate test
     * @depends testGetDate
     */
    public function testSetDate()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Set dates
		$dateA = time();
		$dateB = $dateA+1;
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', $dateA, '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', $dateB, '...');
		
		// Increment dates
		$dateA++;
		$dateB++;
		
		// SetDate
		$this->assertTrue($messageA->setDate($dateA));
		$this->assertTrue($messageB->setDate($dateB));
		
		// Assert getDate
		$this->assertSame($dateA,$messageA->getDate());
		$this->assertSame($dateB,$messageB->getDate());
		
		// Load messages
		$loadedMessageA = Message::load(self::$pdo, $messageA->getIdMessage());
		$loadedMessageB = Message::load(self::$pdo, $messageB->getIdMessage());
		
		// Assert getDate
		$this->assertSame($dateA,$loadedMessageA->getDate());
		$this->assertSame($dateB,$loadedMessageB->getDate());
    }	
}