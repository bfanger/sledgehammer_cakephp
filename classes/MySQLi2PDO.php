<?php
/**
 * 
 */

class MySQLi2PDO extends SledgeHammer\Object {
	
	/**
	 * @var PDO  The real database connection
	 */
	public $pdo;
	
	public $affected_rows;
	
	function connect($host, $login, $password, $database, $port, $socket, $encoding = null) {
		$url = new \SledgeHammer\URL('');
		$url->scheme = 'mysql';
		$url->host = $host;
		$url->user = $login;
		$url->pass = $password;
		$url->path = '/'.$database;
		$url->port = $port;
		if ($socket !== null) {
			$url->query['unix_socket'] = $socket;
		}
		if ($encoding !== null) {
			$url->query['charset'] = $encoding;
		}
		$this->pdo = new \SledgeHammer\Database($url);
		return true;
	}
	function query($sql, $resultmode = null) {
		if (preg_match('/^(INSERT|UPDATE|SET) ([^ ]+)[ ]*(.*)/', $sql, $match)) {
			$statement = $this->pdo->exec($sql);
			$this->affected_rows = $statement;
		} else {
			$statement = $this->pdo->query($sql);
			$this->affected_rows = null;
		}
		return $statement;
	}
}