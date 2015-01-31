<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Post class test
 */
class PostTest extends GenericTest
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
     * GetMember test
     */
    public function testGetMember()
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
		
		// Assert getMember
		$this->assertSame($luke, $question->getMember());
		$this->assertSame($vador, $answerA->getMember());
		$this->assertSame($luke, $answerB->getMember());
		$this->assertSame($vador, $answerC->getMember());
		
		// Load posts
		$loadedQuestion = Question::load(self::$pdo, $question->getIdPost());
		$loadedAnswerA = Answer::load(self::$pdo, $answerA->getIdPost());
		$loadedAnswerB = Answer::load(self::$pdo, $answerB->getIdPost());
		$loadedAnswerC = Answer::load(self::$pdo, $answerC->getIdPost());
		
		// Assert getMember
		$this->assertSame($luke, $loadedQuestion->getMember());
		$this->assertSame($vador, $loadedAnswerA->getMember());
		$this->assertSame($luke, $loadedAnswerB->getMember());
		$this->assertSame($vador, $loadedAnswerC->getMember());
    }
    
    /**
     * GetMemberId test
     */
    public function testGetMemberId()
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
		
		// Assert getMember
		$this->assertSame($luke->getIdMember(), $question->getMemberId());
		$this->assertSame($vador->getIdMember(), $answerA->getMemberId());
		$this->assertSame($luke->getIdMember(), $answerB->getMemberId());
		$this->assertSame($vador->getIdMember(), $answerC->getMemberId());
		
		// Load posts
		$loadedQuestion = Question::load(self::$pdo, $question->getIdPost());
		$loadedAnswerA = Answer::load(self::$pdo, $answerA->getIdPost());
		$loadedAnswerB = Answer::load(self::$pdo, $answerB->getIdPost());
		$loadedAnswerC = Answer::load(self::$pdo, $answerC->getIdPost());
		
		// Assert getMember
		$this->assertSame($luke->getIdMember(), $loadedQuestion->getMemberId());
		$this->assertSame($vador->getIdMember(), $loadedAnswerA->getMemberId());
		$this->assertSame($luke->getIdMember(), $loadedAnswerB->getMemberId());
		$this->assertSame($vador->getIdMember(), $loadedAnswerC->getMemberId());
    }
    
    /**
     * SetMember test
     * @depends testGetMember
     */
    public function testSetMember()
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
		
		// SetMember
		$this->assertTrue($question->setMember($vador));
		$this->assertTrue($answerA->setMember($luke));
		$this->assertTrue($answerB->setMember($vador));
		$this->assertTrue($answerC->setMember($luke));
		
		// Assert getMember
		$this->assertSame($vador, $question->getMember());
		$this->assertSame($luke, $answerA->getMember());
		$this->assertSame($vador, $answerB->getMember());
		$this->assertSame($luke, $answerC->getMember());
		
		// Load posts
		$loadedQuestion = Question::load(self::$pdo, $question->getIdPost());
		$loadedAnswerA = Answer::load(self::$pdo, $answerA->getIdPost());
		$loadedAnswerB = Answer::load(self::$pdo, $answerB->getIdPost());
		$loadedAnswerC = Answer::load(self::$pdo, $answerC->getIdPost());
		
		// Assert getMember
		$this->assertSame($vador, $loadedQuestion->getMember());
		$this->assertSame($luke, $loadedAnswerA->getMember());
		$this->assertSame($vador, $loadedAnswerB->getMember());
		$this->assertSame($luke, $loadedAnswerC->getMember());
    }
    
    /**
     * SetMemberById test
     * @depends testGetMember
     */
    public function testSetMemberById()
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
		
		// SetMember
		$this->assertTrue($question->setMemberById($vador->getIdMember()));
		$this->assertTrue($answerA->setMemberById($luke->getIdMember()));
		$this->assertTrue($answerB->setMemberById($vador->getIdMember()));
		$this->assertTrue($answerC->setMemberById($luke->getIdMember()));
		
		// Assert getMember
		$this->assertSame($vador, $question->getMember());
		$this->assertSame($luke, $answerA->getMember());
		$this->assertSame($vador, $answerB->getMember());
		$this->assertSame($luke, $answerC->getMember());
		
		// Load posts
		$loadedQuestion = Question::load(self::$pdo, $question->getIdPost());
		$loadedAnswerA = Answer::load(self::$pdo, $answerA->getIdPost());
		$loadedAnswerB = Answer::load(self::$pdo, $answerB->getIdPost());
		$loadedAnswerC = Answer::load(self::$pdo, $answerC->getIdPost());
		
		// Assert getMember
		$this->assertSame($vador, $loadedQuestion->getMember());
		$this->assertSame($luke, $loadedAnswerA->getMember());
		$this->assertSame($vador, $loadedAnswerB->getMember());
		$this->assertSame($luke, $loadedAnswerC->getMember());
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
		$dateC = $dateA+2;
		$dateD = $dateA+3;
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',$dateA,'...');
		
		// Create answers
		$answerA = Answer::create(self::$pdo,$vador,'Because',$dateB,'...',$question);
		$answerB = Answer::create(self::$pdo,$luke,'Sure?',$dateC,'...',$question);
		$answerC = Answer::create(self::$pdo,$vador,'Yeah!',$dateD,'...',$question);
		
		// Assert getDate
		$this->assertSame($dateA, $question->getDate());
		$this->assertSame($dateB, $answerA->getDate());
		$this->assertSame($dateC, $answerB->getDate());
		$this->assertSame($dateD, $answerC->getDate());
		
		// Load posts
		$loadedQuestion = Question::load(self::$pdo, $question->getIdPost());
		$loadedAnswerA = Answer::load(self::$pdo, $answerA->getIdPost());
		$loadedAnswerB = Answer::load(self::$pdo, $answerB->getIdPost());
		$loadedAnswerC = Answer::load(self::$pdo, $answerC->getIdPost());
		
		// Assert getDate
		$this->assertSame($dateA, $loadedQuestion->getDate());
		$this->assertSame($dateB, $loadedAnswerA->getDate());
		$this->assertSame($dateC, $loadedAnswerB->getDate());
		$this->assertSame($dateD, $loadedAnswerC->getDate());
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
		$dateC = $dateA+2;
		$dateD = $dateA+3;
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',$dateA,'...');
		
		// Create answers
		$answerA = Answer::create(self::$pdo,$vador,'Because',$dateB,'...',$question);
		$answerB = Answer::create(self::$pdo,$luke,'Sure?',$dateC,'...',$question);
		$answerC = Answer::create(self::$pdo,$vador,'Yeah!',$dateD,'...',$question);
		
		// Increment dates
		$dateA++;
		$dateB++;
		$dateC++;
		$dateD++;
		
		// SetDate
		$this->assertTrue($question->setDate($dateA));
		$this->assertTrue($answerA->setDate($dateB));
		$this->assertTrue($answerB->setDate($dateC));
		$this->assertTrue($answerC->setDate($dateD));
		
		// Assert getDate
		$this->assertSame($dateA, $question->getDate());
		$this->assertSame($dateB, $answerA->getDate());
		$this->assertSame($dateC, $answerB->getDate());
		$this->assertSame($dateD, $answerC->getDate());
		
		// Load posts
		$loadedQuestion = Question::load(self::$pdo, $question->getIdPost());
		$loadedAnswerA = Answer::load(self::$pdo, $answerA->getIdPost());
		$loadedAnswerB = Answer::load(self::$pdo, $answerB->getIdPost());
		$loadedAnswerC = Answer::load(self::$pdo, $answerC->getIdPost());
		
		// Assert getDate
		$this->assertSame($dateA, $loadedQuestion->getDate());
		$this->assertSame($dateB, $loadedAnswerA->getDate());
		$this->assertSame($dateC, $loadedAnswerB->getDate());
		$this->assertSame($dateD, $loadedAnswerC->getDate());
    }
}