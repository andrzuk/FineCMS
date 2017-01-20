<?php

class Register_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'users';
	}

	public function Register($name, $surname, $login, $email, $password)
	{
		$this->row_item = NULL;

		$record = array('name' => $name, 'surname' => $surname, 'login' => $login, 'email' => $email, 'password' => $password);

		if (!parent::check_required($record)) return NULL;

		$user_name = substr($name, 0, 32);
		$user_surname = substr($surname, 0, 32);
		$user_login = substr($login, 0, 32);
		$email = substr($email, 0, 64);
		$user_password = sha1($password);
		$registered = date("Y-m-d H:i:s");
		$null_date = '2000-01-01 00:00:00';
		$status = 3;
		$active = 1;

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name . 
						' WHERE user_login = :login OR email = :email';

			$statement = $this->db->prepare($query);

			$statement->bindParam(':login', $login, PDO::PARAM_STR);
			$statement->bindParam(':email', $email, PDO::PARAM_STR);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item) // konto istnieje - nie można rejestrować
			{
				return NULL;
			}
			else // nie ma takiego konta - można rejestrować
			{
				$query =	'INSERT INTO ' . $this->table_name .
							' (user_login, user_password, user_name, user_surname, email, status, registered, logged_in, modified, logged_out, active) VALUES' .
							' (:user_login, :user_password, :user_name, :user_surname, :email, :status, :registered, :null_date, :null_date, :null_date, :active)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':user_login', $user_login, PDO::PARAM_STR); 
				$statement->bindValue(':user_password', $user_password, PDO::PARAM_STR); 
				$statement->bindValue(':user_name', $user_name, PDO::PARAM_STR); 
				$statement->bindValue(':user_surname', $user_surname, PDO::PARAM_STR); 
				$statement->bindValue(':email', $email, PDO::PARAM_STR); 
				$statement->bindValue(':status', $status, PDO::PARAM_INT); 
				$statement->bindValue(':registered', $registered, PDO::PARAM_STR); 
				$statement->bindValue(':null_date', $null_date, PDO::PARAM_STR); 
				$statement->bindValue(':active', $active, PDO::PARAM_INT); 
				
				$statement->execute();

				$inserted_id = $this->db->lastInsertId();

				// dopisuje role usera:

				$granted_roles = array(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); // funkcje przydzielone nowemu userowi

				$query =	'INSERT INTO user_roles' .
							' (user_id, function_id, access) VALUES' .
							' (:user_id, :function_id, :access)';

				$statement = $this->db->prepare($query);

				foreach ($granted_roles as $role => $access)
				{
					$function_id = $role + 1;
					$statement->bindParam(':user_id', $inserted_id, PDO::PARAM_INT); 
					$statement->bindParam(':function_id', $function_id, PDO::PARAM_INT); 
					$statement->bindParam(':access', $access, PDO::PARAM_INT); 
					
					$statement->execute();
				}

				// zwraca informacje o zarejestrowanym koncie:

				$query = 	'SELECT * FROM ' . $this->table_name . 
							' WHERE id = :id';

				$statement = $this->db->prepare($query);

				$statement->bindParam(':id', $inserted_id, PDO::PARAM_INT);
				
				$statement->execute();
				
				$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}
}

?>