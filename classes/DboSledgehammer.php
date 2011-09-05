<?php
/**
 * CakePHP Datasource wrapper for SledgeHammer's MySQLiDatabase object
 *
 */

App::import('Datasource', 'DboSource');
App::import('Datasource', 'DboMysqli');
class DboSledgehammer extends DboMysqli {
	
	/**
	 * @var \SledgeHammer\MySQLiDatabase
	 */
	var $connection;
	
	public function __construct($config = null, $autoConnect = true) {
		unset($this->configKeyName);
		parent::__construct($config, $autoConnect);
	}

	function connect() {
		$config = $this->config;
		if (is_numeric($config['port'])) {
			$config['socket'] = null;
		} else {
			$config['socket'] = $config['port'];
			$config['port'] = null;
		}
		$this->connection = new \SledgeHammer\MySQLiDatabase();
		$this->connected = $this->connection->connect($config['host'], $config['login'], $config['password'], $config['database'], $config['port'], $config['socket']);

		$this->_useAlias = (bool)version_compare(mysqli_get_server_info($this->connection), "4.1", ">=");

		if (!empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
		return $this->connected;
	}

	public function __set($property, $value) {
		if ($property == 'configKeyName') {
			$GLOBALS['Databases'][$value] = $this->connection; // Import the datbase object into sledgehammer
		}
		$this->property = $value;
	}

	/**
	 * Executes given SQL statement.
	 *
	 * @param string $sql SQL statement
	 * @return resource Result resource identifier
	 * @access protected
	 */
	function _execute($sql) {
		if (preg_match('/^\s*call/i', $sql)) {
			return $this->_executeProcedure($sql);
		}
		return $this->connection->query($sql);
	}

	/**
	 * Returns an array of sources (tables) in the database.
	 *
	 * @return array Array of tablenames in the database
	 */
	function listSources() {
		$cache = DboMysqlBase::listSources();
		if ($cache !== null) {
			return $cache;
		}
		$result = $this->_execute('SHOW TABLES FROM ' . $this->name($this->config['database']) . ';');

		if (!$result) {
			return array();
		}

		$tables = array();

		foreach ($result as $row) {
			$tables[] = current($row);
		}
		DboMysqlBase::listSources($tables);
		return $tables;
	}
	
	/**
	 * Returns number of rows in previous resultset. If no previous resultset exists,
	 * this returns false.
	 *
	 * @return integer Number of rows in resultset
	 */
	function lastNumRows() {
		if ($this->hasResult()) {
			return count($this->_result);
		}
		return null;
	}
	
	/**
	 *
	 * @param \SledgeHammer\MySQLiResultIterator $results 
	 */
	function resultSet(&$results) {
		$this->results =& $results;
		$this->map = array();
		$numFields = mysqli_num_fields($results->Result);
		$index = 0;
		$j = 0;
		while ($j < $numFields) {
			$column = mysqli_fetch_field_direct($results->Result, $j);
			if (!empty($column->table)) {
				$this->map[$index++] = array($column->table, $column->name);
			} else {
				$this->map[$index++] = array(0, $column->name);
			}
			$j++;
		}
	}
	
	/**
	 * Fetches the next row from the current result set
	 *
	 * @return unknown
	 */
	function fetchResult() {
		if ($row = mysqli_fetch_row($this->results->Result)) {
			$resultRow = array();
			foreach ($row as $index => $field) {
				$table = $column = null;
				if (count($this->map[$index]) === 2) {
					list($table, $column) = $this->map[$index];
				}
				$resultRow[$table][$column] = $row[$index];
			}
			return $resultRow;
		}
		return false;
	}
}
?>
