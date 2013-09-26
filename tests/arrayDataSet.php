<?php

/**
 * Array dataset
 */
class ArrayDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
	/** @var $tables array tables */
	protected $tables = array();

	/**
	 * Constructor
	 * @param $data array data
	*/
	public function __construct(array $data)
	{
		foreach ($data as $tableName => $rows) {
			$columns = array();
			if (isset($rows[0])) {
				$columns = array_keys($rows[0]);
			}

			$metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
			$table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

			foreach ($rows as $row) {
				$table->addRow($row);
			}
			$this->tables[$tableName] = $table;
		}
	}

	/**
	 * @see PHPUnit_Extensions_Database_DataSet_AbstractDataSet::createIterator()
	 */
	protected function createIterator($reverse=false)
	{
		return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
	}

	/**
	 * @see PHPUnit_Extensions_Database_DataSet_AbstractDataSet::getTable()
	 */
	public function getTable($tableName)
	{
		if (!isset($this->tables[$tableName])) {
			throw new InvalidArgumentException($tableName.' is not a table in the current database.');
		}
		return $this->tables[$tableName];
	}
}