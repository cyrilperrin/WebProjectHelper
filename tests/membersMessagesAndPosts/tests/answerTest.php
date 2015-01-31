<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Answer class test
 */
class AnswerTest extends GenericTest
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
     * GetQuestion test
     */
    public function testGetQuestion()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		
		// Create answer
		$answer = Answer::create(self::$pdo,$vador,'Because',time(),'...',$question);
		
		// Assert getQuestion
		$this->assertSame($question, $answer->getQuestion());
		
		// Load answer
		$loadedAnswer = Answer::load(self::$pdo, $answer->getIdPost());
		
		// Assert getQuestion
		$this->assertSame($question, $loadedAnswer->getQuestion());
    }
    
    /**
     * GetQuestionId test
     */
    public function testGetQuestionId()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create question
		$question = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		
		// Create answer
		$answer = Answer::create(self::$pdo,$vador,'Because',time(),'...',$question);
		
		// Assert getQuestion
		$this->assertSame($question->getIdPost(), $answer->getQuestionId());
		
		// Load answer
		$loadedAnswer = Answer::load(self::$pdo, $answer->getIdPost());
		
		// Assert getQuestion
		$this->assertSame($question->getIdPost(), $loadedAnswer->getQuestionId());
    }
    
    /**
     * SetQuestion test
     * @depends testGetQuestion
     */
    public function testSetQuestion()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create questions
		$questionA = Question::create(self::$pdo,$luke,'Why are you my father?',time(),'...');
		$questionB = Question::create(self::$pdo,$luke,'Why did you choose the dark side of the force?',time(),'...');
		
		// Create answer
		$answer = Answer::create(self::$pdo,$vador,'Because',time(),'...',$questionA);
		
		// Set question
		$this->assertTrue($answer->setQuestion($questionB));
		
		// Assert getQuestion
		$this->assertSame($questionB, $answer->getQuestion());
		
		// Load answer
		$loadedAnswer = Answer::load(self::$pdo, $answer->getIdPost());
		
		// Assert getQuestion
		$this->assertSame($questionB, $loadedAnswer->getQuestion());
    }
    
    /**
     * SetQuestionById test
     * @depends testGetQuestion
     */
    public function testSetQuestionById()
    {
    	// Create members
		$vador = Member::create(self::$pdo,'Dark Vador',sha1('shmi'),true);
		$luke = Member::create(self::$pdo,'Luke Skywalker',sha1('force'));
		
		// Create questions
		$questionA = Question::create(self::$pdo,$luke,'Why are you my father?',time(),'...');
		$questionB = Question::create(self::$pdo,$luke,'Why did you choose the dark side of the force?',time(),'...');
		
		// Create answer
		$answer = Answer::create(self::$pdo,$vador,'Because',time(),'...',$questionA);
		
		// Set question
		$this->assertTrue($answer->setQuestionById($questionB->getIdPost()));
		
		// Assert getQuestion
		$this->assertSame($questionB, $answer->getQuestion());
		
		// Load answer
		$loadedAnswer = Answer::load(self::$pdo, $answer->getIdPost());
		
		// Assert getQuestion
		$this->assertSame($questionB, $loadedAnswer->getQuestion());
    }
    
    /**
     * SelectByQuestion test
     */
    public function testSelectByQuestion()
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
		
		// SelectByQuestion/FetchAll
		$answers = Answer::fetchAll(self::$pdo, Answer::selectByQuestion(self::$pdo, $question));
		
		// Assert array content
		$this->assertCount(3, $answers);
		$this->assertContains($answerA, $answers);
		$this->assertContains($answerB, $answers);
		$this->assertContains($answerC, $answers);
		
		// Delete answer
		$answerA->delete();
		
		// SelectByQuestion/FetchAll
		$answers = Answer::fetchAll(self::$pdo, Answer::selectByQuestion(self::$pdo, $question));
		
		// Assert array content
		$this->assertCount(2, $answers);
		$this->assertContains($answerB, $answers);
		$this->assertContains($answerC, $answers);
		
		// Delete answer
		$answerB->delete();
		
		// SelectByQuestion/FetchAll
		$answers = Answer::fetchAll(self::$pdo, Answer::selectByQuestion(self::$pdo, $question));
		
		// Assert array content
		$this->assertCount(1, $answers);
		$this->assertContains($answerC, $answers);
		
		// Delete answer
		$answerC->delete();
		
		// SelectByQuestion/FetchAll
		$answers = Answer::fetchAll(self::$pdo, Answer::selectByQuestion(self::$pdo, $question));
		
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
		$question = Question::create(self::$pdo,$luke,'Why?',time(),'...');
		
		// Create answers
		$answerA = Answer::create(self::$pdo,$vador,'Because',time(),'...',$question);
		$answerB = Answer::create(self::$pdo,$luke,'Sure?',time(),'...',$question);
		$answerC = Answer::create(self::$pdo,$vador,'Yeah!',time(),'...',$question);
		
		// SelectByQuestion/FetchAll
		$lukeAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $luke));
		$vadorAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $vador));
		
		// Assert arrays content
		$this->assertCount(1, $lukeAnswers);
		$this->assertContains($answerB, $lukeAnswers);
		$this->assertCount(2, $vadorAnswers);
		$this->assertContains($answerA, $vadorAnswers);
		$this->assertContains($answerC, $vadorAnswers);
		
		// Delete answer
		$answerA->delete();
		
		// SelectByQuestion/FetchAll
		$lukeAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $luke));
		$vadorAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $vador));
		
		// Assert arrays content
		$this->assertCount(1, $lukeAnswers);
		$this->assertContains($answerB, $lukeAnswers);
		$this->assertCount(1, $vadorAnswers);
		$this->assertContains($answerC, $vadorAnswers);
		
		// Delete answer
		$answerB->delete();
		
		// SelectByQuestion/FetchAll
		$lukeAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $luke));
		$vadorAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $vador));
		
		// Assert arrays content
		$this->assertCount(0, $lukeAnswers);
		$this->assertCount(1, $vadorAnswers);
		$this->assertContains($answerC, $vadorAnswers);
		
		// Delete answer
		$answerC->delete();
		
		// SelectByQuestion/FetchAll
		$lukeAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $luke));
		$vadorAnswers = Answer::fetchAll(self::$pdo, Answer::selectByMember(self::$pdo, $vador));
		
		// Assert arrays content
		$this->assertCount(0, $lukeAnswers);
		$this->assertCount(0, $vadorAnswers);
    }
}