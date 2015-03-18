<?php

/*
 * Klasa odpowiedzialna za obsługę ustawień konfiguracyjnych w bazie
 */

class Settings
{
	private $db;

	public function __construct($obj)
	{
		$this->db = $obj->get_dbc();
	}

	public function get_config_key($key)
	{
		$config_value = NULL;

		if (isset($_SESSION['install_mode']))
		{			
			if ($key == 'base_domain') return $_SERVER['HTTP_HOST'];
			else if ($key == 'page_footer') return '&copy; MyMVC ' . date("Y");
			else return NULL;
		}

		try
		{
			$query = 'SELECT * FROM configuration WHERE key_name = :key_name';

			$statement = $this->db->prepare($query);

			$statement->bindParam(':key_name', $key, PDO::PARAM_STR);
			
			$statement->execute();
			
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			
			$config_value = $result['key_value'];
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $config_value;
	}
}

?>