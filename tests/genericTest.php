<?php

/**
 * Generic test
 */
abstract class GenericTest extends PHPUnit_Extensions_Database_TestCase
{
	/** @var $pdo PDO pdo */
	protected static $pdo = null;

	/** @var $connection PHPUnit_Extensions_Database_DB_IDatabaseConnection connection */
	private $connection = null;
	

	/**
	 * @see PHPUnit_Extensions_Database_TestCase::getConnection()
	 */
	final public function getConnection()
	{
		// Build connection
		if ($this->connection == null) {
			$this->connection = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
		}
		
		// Return connection
		return $this->connection;
	}
	
	/**
	 * Build PDO variable
	 */
	protected static function buildPdo()
	{
		if (self::$pdo == null) {
			self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD'] );
		}
	}
	
	/**
	 * Execute MySQL script
	 * @param $mysqlFilename string MySQL script filename
	 */
	protected static function executeMysqlScript($mysqlFilename)
	{
		$sql = file_get_contents($mysqlFilename);
		foreach(array_filter(explode(";",$sql),'trim') as $request) {
			self::$pdo->exec($request);
		}
	}
	
	/**
	 * Drop all MySQL tables from a XML dataset 
	 * @param $xmlDataSetFilename string XML dataset filename
	 */
	protected static function dropAllMysqlTablesFromXmlDataSet($xmlDataSetFilename)
	{
		// Initialize tables array
		$tables = array();
		
		// Find tables
		$document = new DOMDocument();
		$document->load($xmlDataSetFilename);
		$xpath = new DOMXPath($document);
		$entries = $xpath->query('//dataset/table');
		foreach($entries as $entry) {
			$tables[] = $entry->getAttribute('name');
		}
		
		// Drop all tables
		foreach($tables as $table) {
			self::$pdo->exec('DROP TABLE IF EXISTS '.$table);
		}
	}
	
}