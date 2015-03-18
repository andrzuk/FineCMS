<?php

class Logout_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);

		$this->table_name = 'users';
	}

	public function SaveLogout($id)
	{
		$date_time = date("Y-m-d H:i:s");

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET logged_out = :date_time' .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);
		
			$statement->bindParam(':date_time', $date_time, PDO::PARAM_STR);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);
		
			$statement->execute();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
	}
}

?>