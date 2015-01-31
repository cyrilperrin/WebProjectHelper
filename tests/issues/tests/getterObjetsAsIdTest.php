<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Crash in loading object in getter method in case of objects used as id
 */
class GetterObjectsAsIdTest extends GenericTest
{
	
    /**
     * Set up before class
     */
    public static function setUpBeforeClass()
    {
		// Generate PHP classes and MySQL script
    	generatePhpClassesAndMysqlScript(
	    	__DIR__.'/../definitions/getterObjectsAsId.txt',
	    	__DIR__.'/../scripts/getterObjectsAsId.sql',
	    	__DIR__.'/../classes/getterObjectsAsId.php'
    	);
    	
    	// Require classes
    	require_once(__DIR__.'/../classes/getterObjectsAsId.php');
    	
    	// Build PDO variable
    	self::buildPdo();
    	
    	// Drop all MySQL tables
    	self::dropAllMysqlTablesFromXmlDataSet(__DIR__.'/../datasets/getterObjectsAsId.xml');
    	
    	// Execute MySQL script
    	self::executeMysqlScript(__DIR__.'/../scripts/getterObjectsAsId.sql');	
    }
    
    /**
     * Tear down after class
     */
    public static function tearDownAfterClass()
    {
    	// Drop all MySQL tables
    	self::dropAllMysqlTablesFromXmlDataSet(__DIR__.'/../datasets/getterObjectsAsId.xml');
    }
    
    /**
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__.'/../datasets/getterObjectsAsId.xml');
    }
    
    /**
     * Getter test
     */
    public function testGetter()
    {
    	// Create A/B/C/D/E/F
    	$a = A_Issue2::create(self::$pdo);
    	$b = B_Issue2::create(self::$pdo,$a);
    	$c = C_Issue2::create(self::$pdo);
    	$d = D_Issue2::create(self::$pdo);
    	$e = E_Issue2::create(self::$pdo, $c, $d);
    	$f = F_Issue2::create(self::$pdo, $b, $e);
    	
    	// Assert getB
    	$this->assertSame($b,$f->getB_issue2());
    	
    	// Assert getE
    	$this->assertSame($e,$f->getE_issue2());
    }
}