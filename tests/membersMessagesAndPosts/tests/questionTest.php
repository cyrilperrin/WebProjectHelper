<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Question class test
 */
class QuestionTest extends GenericTest
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
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		
		// Create answers
		$idAnswerA = Answer::create(self::$pdo,$vador,'Because',time(),'...',$question)->getIdPost();
		$idAnswerB = Answer::create(self::$pdo,$luke,'Sure?',time(),'...',$question)->getIdPost();
		$idAnswerC = Answer::create(self::$pdo,$vador,'Yeah!',time(),'...',$question)->getIdPost();
		
		// Delete question
		$this->assertTrue($question->delete());
		
		// Load answers
		$answerA = Answer::load(self::$pdo, $idAnswerA);
		$answerB = Answer::load(self::$pdo, $idAnswerB);
		$answerC = Answer::load(self::$pdo, $idAnswerC);
		
		// Assert null
		$this->assertNull($answerA);
		$this->assertNull($answerB);
		$this->assertNull($answerC);
    }
    
    /**
     * SelectAnswers test
     */
    public function testSelectAnswers()
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
		
		// SelectAnswers/FetchAll
		$answers = Answer::fetchAll(self::$pdo, $question->selectAnswers());
		
		// Assert array content
		$this->assertCount(3, $answers);
		$this->assertContains($answerA, $answers);
		$this->assertContains($answerB, $answers);
		$this->assertContains($answerC, $answers);
		
		// Delete answer
		$answerA->delete();
		
		// SelectAnswers/FetchAll
		$answers = Answer::fetchAll(self::$pdo, $question->selectAnswers());
		
		// Assert array content
		$this->assertCount(2, $answers);
		$this->assertContains($answerB, $answers);
		$this->assertContains($answerC, $answers);
		
		// Delete answer
		$answerB->delete();
		
		// SelectAnswers/FetchAll
		$answers = Answer::fetchAll(self::$pdo, $question->selectAnswers());
		
		// Assert array content
		$this->assertCount(1, $answers);
		$this->assertContains($answerC, $answers);
		
		// Delete answer
		$answerC->delete();
		
		// SelectAnswers/FetchAll
		$answers = Answer::fetchAll(self::$pdo, $question->selectAnswers());
		
		// Assert array content
		$this->assertCount(0, $answers);
    }
    
    /**
     * SelectByMember test
     */
    public function testSelectByMember()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create question
		$questionA = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		$questionB = Question::create(self::$pdo,$vador,'Son...',time(),'...');
		$questionC = Question::create(self::$pdo,$luke,'Father...',time(),'...');
		
		// SelectByMember/FetchAll
		$vadorQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $vador));
		$lukeQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(1, $vadorQuestions);
		$this->assertContains($questionB, $vadorQuestions);
		$this->assertCount(2, $lukeQuestions);
		$this->assertContains($questionA, $lukeQuestions);
		$this->assertContains($questionC, $lukeQuestions);
		
		// Delete question
		$questionA->delete();
		
		// SelectByMember/FetchAll
		$vadorQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $vador));
		$lukeQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(1, $vadorQuestions);
		$this->assertContains($questionB, $vadorQuestions);
		$this->assertCount(1, $lukeQuestions);
		$this->assertContains($questionC, $lukeQuestions);
		
		// Delete question
		$questionB->delete();
		
		// SelectByMember/FetchAll
		$vadorQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $vador));
		$lukeQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(0, $vadorQuestions);
		$this->assertCount(1, $lukeQuestions);
		$this->assertContains($questionC, $lukeQuestions);
		
		// Delete question
		$questionC->delete();
		
		// SelectByMember/FetchAll
		$vadorQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $vador));
		$lukeQuestions = Question::fetchAll(self::$pdo, Question::selectByMember(self::$pdo, $luke));
		
		// Assert arrays content
		$this->assertCount(0, $vadorQuestions);
		$this->assertCount(0, $lukeQuestions);
    }
	
}