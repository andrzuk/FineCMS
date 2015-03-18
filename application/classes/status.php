<?php

/*
 * Klasa odpowiedzialna za odczyt statusu użytkownika, jego id, nazwy itd.
 */

class Status
{
	private $app;

	private $user_id;
	private $user_status;
	private $user_name;
	private $user_surname;
	
	public function __construct($obj)
	{
		$this->app = $obj;
		
		$this->init();
	}
	
	public function init()
	{
		$this->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
		$this->user_status = isset($_SESSION['user_status']) ? $_SESSION['user_status'] : NULL;
		$this->user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : NULL;
		$this->user_surname = isset($_SESSION['user_surname']) ? $_SESSION['user_surname'] : NULL;
	}
	
	public function get_status()
	{
		$status = array(
			'user_id' => $this->user_id,
			'user_status' => $this->user_status,
			'user_name' => $this->user_name,
			'user_surname' => $this->user_surname,
		);
		
		return $status;
	}

	public function get_value($key)
	{
		$status = array(
			'user_id' => $this->user_id,
			'user_status' => $this->user_status,
			'user_name' => $this->user_name,
			'user_surname' => $this->user_surname,
		);
		
		return $status[$key];
	}

	public function super_admin()
	{
		return $this->user_id == 1;
	}
}

?>