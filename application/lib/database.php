<?php

/*
 * Klasa odpowiedzialna za połączenie z bazą danych
 */

class Database
{
	private $host;
	private $database;
	private $user;
	private $password;

	private $connection;
	
	public function __construct()
	{
	}
	
	public function init($host, $db, $usr, $pwd)
	{
		$this->host = $host;
		$this->database = $db;
		$this->user = $usr;
		$this->password = $pwd;
	}

	public function connect()
	{
		try
		{
			$this->connection = new PDO(
				'mysql:host='.$this->host.';dbname='.$this->database.';', 
				$this->user, 
				$this->password
				);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->exec('set names utf8');
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
	}

	public function get_connection()
	{
		return $this->connection;
	}
}

?>
