<?php

/*
 * Klasa odpowiedzialna za sprawdzanie praw dostępu dla użytkownika na podst. tzw. Access Control List
 */

class AccessControlList
{
	private $app;

	private $db;
	private $user_id;
	private $user_status;
	private $module;
	private $access;

	private $row_item;
	
	public function __construct($obj)
	{
		$this->app = $obj;

		$this->db = $this->app->get_dbc();

		$this->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
		$this->user_status = isset($_SESSION['user_status']) ? $_SESSION['user_status'] : NULL;
		
		$this->module = MODULE_NAME;
	}
	
	public function allowed($profile)
	{
		if ($this->user_status == NULL) return FALSE;

		if ($this->user_status > $profile) return FALSE;

		try
		{
			$query =	'SELECT access FROM user_roles' .
						' INNER JOIN admin_functions ON admin_functions.id = user_roles.function_id' .
						' WHERE user_id = :user_id' .
						' AND module = :module';
			
			$statement = $this->db->prepare($query);

			$statement->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
			$statement->bindParam(':module', $this->module, PDO::PARAM_STR);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		if ($this->row_item)
		{
			$this->access = $this->row_item['access'];
		}
		
		return $this->access;
	}
}

?>