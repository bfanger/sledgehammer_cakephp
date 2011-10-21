<?php
/**
 * CakePHP Datasource wrapper for SledgeHammer's Database object
 *
 */
App::import('Datasource', 'DboSource');
App::import('Datasource', 'DboMysqli');
class DboSledgehammer extends DboMysqli {

	/**
	 * @var PDO
	 */
	var $connection;

	/**
	 * @var int|null
	 */
	var $affected_rows;
	/**
	 * @var PDOStatement
	 */
	var $results;

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
		if (empty($config['encoding'])) {
			$config['encoding'] = null;
		}
		$url = new \SledgeHammer\URL('');
		$url->scheme = 'mysql';
		$url->host = $config['host'];
		$url->user = $config['login'];
		$url->pass = $config['password'];
		$url->path = '/'.$config['database'];
		$url->port = $config['port'];
		if ($config['socket'] !== null) {
			$url->query['unix_socket'] = $config['socket'];
		}
		if (isset ($config['encoding'])) {
			$url->query['charset'] = $config['encoding'];
		}
		$this->connection = new \SledgeHammer\Database($url);
		$this->connected = true;

		$this->_useAlias = true;//(bool)version_compare(mysqli_get_server_info($this->connection), "4.1", ">=");

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
		if (preg_match('/^(INSERT|UPDATE|SET) ([^ ]+)[ ]*(.*)/', $sql, $match)) {
			$statement = $this->connection->exec($sql);
			$this->affected_rows = $statement;
		} else {
			$statement = $this->connection->query($sql);
			$this->affected_rows = null;
		}
		return $statement;
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
	 * @param PDOStatement $results
	 */
	function resultSet(&$results) {
		$this->results =& $results;
		$this->map = array();
		$numFields = $results->columnCount();
		$index = 0;

		while ($numFields-- > 0) {
			$column = $results->getColumnMeta($index);
			if (empty($column['native_type'])) {
				$type = ($column['len'] == 1) ? 'boolean' : 'string';
			} else {
				$type = $column['native_type'];
			}
			if (!empty($column['table']) && strpos($column['name'], $this->virtualFieldSeparator) === false) {
				$this->map[$index++] = array($column['table'], $column['name']);
			} else {
				$this->map[$index++] = array(0, $column['name']);
			}
		}
		/*
		$column = mysqli_fetch_field_direct($results, $j);
			if (!empty($column->table) && strpos($column->name, $this->virtualFieldSeparator) === false) {
				$this->map[$index++] = array($column->table, $column->name);
			} else {
				$this->map[$index++] = array(0, $column->name);
			}
		 */
	}

	/**
	 * Fetches the next row from the current result set
	 *
	 * @return unknown
	 */
	function fetchResult() {
		if ($row = $this->results->fetch(PDO::FETCH_NUM)) {
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

	public function lastAffected() {
		if ($this->_result) {
			return $this->affected_rows;
		}
		return null;
	}
	function lastError() {
		$error = $this->connection->errorInfo();
		if ($error[0] != '00000') { // An error occured?
			return $error[1].': '.$error[2];
		}
		return null;
	}

	function getCharsetName($name) {
		$cols = $this->query('SELECT CHARACTER_SET_NAME FROM INFORMATION_SCHEMA.COLLATIONS WHERE COLLATION_NAME= ' . $this->value($name) . ';');
		if (isset($cols[0]['COLLATIONS']['CHARACTER_SET_NAME'])) {
			return $cols[0]['COLLATIONS']['CHARACTER_SET_NAME'];
		}
		return false;
	}
	function value($data, $column = null, $safe = false) {
		$parent = DboSource::value($data, $column, $safe);

		if ($parent != null) {
			return $parent;
		}
		if ($data === null || (is_array($data) && empty($data))) {
			return 'NULL';
		}
		if ($data === '' && $column !== 'integer' && $column !== 'float' && $column !== 'boolean') {
			return "''";
		}
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch ($column) {
			case 'boolean':
				return $this->boolean((bool)$data);
			break;
			case 'integer' :
			case 'float' :
			case null :
				if ($data === '') {
					return 'NULL';
				}
				if (is_float($data)) {
					return str_replace(',', '.', strval($data));
				}
				if ((is_int($data) || is_float($data) || $data === '0') || (
					is_numeric($data) && strpos($data, ',') === false &&
					$data[0] != '0' && strpos($data, 'e') === false)) {
						return $data;
					}
			default:
				$data = $this->connection->quote($data);
				break;
		}

		return $data;
	}

}
?>
