<?php

class Login_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'users';
	}

	public function Authenticate($login, $password)
	{
		$this->row_item = NULL;

		$id = 0;
		$login = substr($login, 0, 32);
		$password = sha1($password);
		$date_time = date("Y-m-d H:i:s");

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name . 
						' WHERE (user_login = :login' .
						' OR email = :login)' .
						' AND user_password = :password '.
						' AND active = 1';

			$statement = $this->db->prepare($query);

			$statement->bindParam(':login', $login, PDO::PARAM_STR);
			$statement->bindParam(':password', $password, PDO::PARAM_STR);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item)
			{
				$id = $this->row_item['id'];

				$query =	'UPDATE ' . $this->table_name .
							' SET logged_in = :date_time' .
							' WHERE id = :id';
	
				$statement = $this->db->prepare($query);
			
				$statement->bindParam(':date_time', $date_time, PDO::PARAM_STR);
				$statement->bindParam(':id', $id, PDO::PARAM_INT);
			
				$statement->execute();
			}

			$this->Store($login, $password, $id); // rejestruje logowanie
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	private function Store($login, $password, $user_id)
	{
		$login_time = date("Y-m-d H:i:s");

		try
		{
			$query = 	'INSERT INTO logins' .
						' (agent, user_ip, user_id, login, password, login_time) VALUES' .
						' (:agent, :user_ip, :user_id, :login, :password, :login_time)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':agent', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR); 
			$statement->bindValue(':user_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR); 
			$statement->bindValue(':user_id', $user_id, PDO::PARAM_INT); 
			$statement->bindValue(':login', $login, PDO::PARAM_STR); 
			$statement->bindValue(':password', $password, PDO::PARAM_STR); 
			$statement->bindValue(':login_time', $login_time, PDO::PARAM_STR); 
			
			$statement->execute();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
	}
}

?>