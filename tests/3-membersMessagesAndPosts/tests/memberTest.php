<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Member class test
 */
class MemberTest extends GenericTest
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
     * Delete test
     */
    public function testDelete()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		
		// Create answers
		$idAnswerA = Answer::create(self::$pdo,$vador,'Because',time(),'...',$question)->getIdPost();
		$idAnswerB = Answer::create(self::$pdo,$luke,'Sure?',time(),'...',$question)->getIdPost();
		$idAnswerC = Answer::create(self::$pdo,$vador,'Yeah!',time(),'...',$question)->getIdPost();
		
    	// Delete users
    	$this->assertTrue($vador->delete());
    	$this->assertTrue($luke->delete());
    	
    	// Assert table content
    	$expected = $this->createXMLDataSet(__DIR__.'/../datasets/initial.xml')->getTable('member');
    	$actual = $this->getConnection()->createQueryTable('member', 'SELECT * FROM '.Member::TABLENAME);
    	$this->assertTablesEqual($expected, $actual);
    }
    
    /**
     * SelectSents test
     */
    public function testSelectSents()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// SelectSents/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, $vador->selectSents());
		$lukeMessages = Message::fetchAll(self::$pdo, $luke->selectSents());
		
		// Assert arrays content
		$this->assertCount(1, $vadorMessages);
		$this->assertContains($messageA, $vadorMessages);
		$this->assertCount(1, $lukeMessages);
		$this->assertContains($messageB, $lukeMessages);
		
		// Delete message
		$messageA->delete();
		
		// SelectSents/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, $vador->selectSents());
		$lukeMessages = Message::fetchAll(self::$pdo, $luke->selectSents());
		
		// Assert arrays content
		$this->assertCount(0, $vadorMessages);
		$this->assertCount(1, $lukeMessages);
		$this->assertContains($messageB, $lukeMessages);
		
		// Delete message
		$messageB->delete();
		
		// SelectSents/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, $vador->selectSents());
		$lukeMessages = Message::fetchAll(self::$pdo, $luke->selectSents());
		
		// Assert arrays content
		$this->assertCount(0, $vadorMessages);
		$this->assertCount(0, $lukeMessages);
    }
    
    /**
     * SelectReceiveds test
     */
    public function testSelectReceiveds()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create messages
		$messageA = Message::create(self::$pdo, $vador, $luke, 'Son...', time(), '...');
		$messageB = Message::create(self::$pdo, $luke, $vador, 'Father...', time(), '...');
		
		// SelectReceiveds/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, $vador->selectReceiveds());
		$lukeMessages = Message::fetchAll(self::$pdo, $luke->selectReceiveds());
		
		// Assert arrays content
		$this->assertCount(1, $vadorMessages);
		$this->assertContains($messageB, $vadorMessages);
		$this->assertCount(1, $lukeMessages);
		$this->assertContains($messageA, $lukeMessages);
		
		// Delete message
		$messageA->delete();

		// SelectReceiveds/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, $vador->selectReceiveds());
		$lukeMessages = Message::fetchAll(self::$pdo, $luke->selectReceiveds());
		
		// Assert arrays content
		$this->assertCount(1, $vadorMessages);
		$this->assertContains($messageB, $vadorMessages);
		$this->assertCount(0, $lukeMessages);
		
		// Delete message
		$messageB->delete();

		// SelectReceiveds/FetchAll
		$vadorMessages = Message::fetchAll(self::$pdo, $vador->selectReceiveds());
		$lukeMessages = Message::fetchAll(self::$pdo, $luke->selectReceiveds());
		
		// Assert arrays content
		$this->assertCount(0, $vadorMessages);
		$this->assertCount(0, $lukeMessages);
    }
    
    /**
     * LoadPosts test
     */
    public function testLoadPosts()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		
		// Create answers
		$answerA = Answer::create(self::$pdo,$vador,'Because',time(),'...',$question);
		$answerB = Answer::create(self::$pdo,$luke,'Sure?',time(),'...',$question);
		$answerC = Answer::create(self::$pdo,$vador,'Yeah!',time(),'...',$question);
		
		// Load posts
		$vadorPosts = $vador->loadPosts();
		$lukePosts = $luke->loadPosts();
		
		// Assert type
		$this->assertEquals('array', gettype($vadorPosts));
		$this->assertEquals('array', gettype($lukePosts));
		
		// Assert arrays content
		$this->assertCount(2, $vadorPosts);
		$this->assertContains($answerA, $vadorPosts);
		$this->assertContains($answerC, $vadorPosts);
		$this->assertCount(2, $lukePosts);
		$this->assertContains($question, $lukePosts);
		$this->assertContains($answerB, $lukePosts);
		
		// Delete answer
		$answerC->delete();
		
		// Load posts
		$vadorPosts = $vador->loadPosts();
		$lukePosts = $luke->loadPosts();
		
		// Assert arrays content
		$this->assertCount(1, $vadorPosts);
		$this->assertContains($answerA, $vadorPosts);
		$this->assertCount(2, $lukePosts);
		$this->assertContains($question, $lukePosts);
		$this->assertContains($answerB, $lukePosts);
		
		// Delete answer
		$answerB->delete();
		
		// Load posts
		$vadorPosts = $vador->loadPosts();
		$lukePosts = $luke->loadPosts();
		
		// Assert arrays content
		$this->assertCount(1, $vadorPosts);
		$this->assertContains($answerA, $vadorPosts);
		$this->assertCount(1, $lukePosts);
		$this->assertContains($question, $lukePosts);
		
		// Delete answer
		$answerA->delete();
		
		// Load posts
		$vadorPosts = $vador->loadPosts();
		$lukePosts = $luke->loadPosts();
		
		// Assert arrays content
		$this->assertCount(0, $vadorPosts);
		$this->assertCount(1, $lukePosts);
		$this->assertContains($question, $lukePosts);
		
		// Delete question
		$question->delete();
		
		// Load posts
		$vadorPosts = $vador->loadPosts();
		$lukePosts = $luke->loadPosts();
		
		// Assert arrays content
		$this->assertCount(0, $vadorPosts);
		$this->assertCount(0, $lukePosts);
    }
    
    
	
}