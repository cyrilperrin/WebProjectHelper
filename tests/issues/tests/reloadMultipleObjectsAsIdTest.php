<?php

// Requires
require_once(__DIR__.'/../../genericTest.php');

/**
 * Crash in generating reload method in case of multiple objects used as id
 */
class reloadMultipleObjectsAsIdTest extends GenericTest
{
	
    /**
     * Set up before class
     */
    public static function setUpBeforeClass()
    {
		// Generate PHP classes and MySQL script
    	generatePhpClassesAndMysqlScript(
	    	__DIR__.'/../definitions/reloadMultipleObjectsAsId.txt',
	    	__DIR__.'/../scripts/reloadMultipleObjectsAsId.sql',
	    	__DIR__.'/../classes/reloadMultipleObjectsAsId.php'
    	);
    	
    	// Require classes
    	require_once(__DIR__.'/../classes/reloadMultipleObjectsAsId.php');
    	
    	// Build PDO variable
    	self::buildPdo();
    	
    	// Drop all MySQL tables
    	self::dropAllMysqlTablesFromXmlDataSet(__DIR__.'/../datasets/reloadMultipleObjectsAsId.xml');
    	
    	// Execute MySQL script
    	self::executeMysqlScript(__DIR__.'/../scripts/reloadMultipleObjectsAsId.sql');	
    }
    
    /**
     * Tear down after class
     */
    public static function tearDownAfterClass()
    {
    	// Drop all MySQL tables
    	self::dropAllMysqlTablesFromXmlDataSet(__DIR__.'/../datasets/reloadMultipleObjectsAsId.xml');
    }
    
    /**
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__.'/../datasets/reloadMultipleObjectsAsId.xml');
    }
    
    /**
     * Reload test
     */
    public function testReload()
    {
    	// Create A1/B1/C1
    	$a1 = A_Issue1::create(self::$pdo);
    	$b1 = B_Issue1::create(self::$pdo);
    	$c1 = C_Issue1::create(self::$pdo,$a1,$b1);
    	
    	// Create D
    	$d = D_Issue1::create(self::$pdo,'e',$c1);
		
    	// Create A2/B2/C2
    	$a2 = A_Issue1::create(self::$pdo);
    	$b2 = B_Issue1::create(self::$pdo);
    	$c2 = C_Issue1::create(self::$pdo,$a2,$b2);

    	// Update D
    	$statement = self::$pdo->prepare('UPDATE '.D_Issue1::TABLENAME.' SET '.D_Issue1::FIELDNAME_C_ISSUE1_IDA_ISSUE1.' = ?, '.D_Issue1::FIELDNAME_C_ISSUE1_IDB_ISSUE1.' = ? WHERE '.D_Issue1::FIELDNAME_IDD_ISSUE1.' = ?');
    	$statement->execute(array($a2->getIdA_issue1(),$b2->getIdB_issue1(),$d->getIdD_issue1()));
    	
    	// Reload D
    	$d->reload();
    	
    	// Assert C
		$this->assertSame($c2, $d->getC_issue1());
    	
    }
}