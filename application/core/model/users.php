<?php

class Users_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = MODULE_NAME;
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$condition = NULL;

		$fields_list = array('user_login', 'user_name', 'user_surname', 'email');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name . ' WHERE 1' . $condition . $filter .
						' ORDER BY ' . $this->list_params['sort_field'] . ' ' . $this->list_params['sort_order'] . 
						' LIMIT ' . $this->list_params['start_from'] . ', ' . $this->list_params['show_rows'];

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

			$this->GetCount($query);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}

	public function GetOne($id)
	{
		$this->row_item = array();

		try
		{
			$query =	'SELECT * FROM ' . $this->table_name .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function Add($record)
	{
		$inserted_id = 0;

		if (!parent::check_required($record)) return NULL;

		try
		{
			// sprawdza, czy istnieje już taki user:

			$query =	'SELECT COUNT(*) AS licznik FROM ' . $this->table_name .
						' WHERE user_login = :user_login OR email = :email';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':user_login', $record['user_login'], PDO::PARAM_STR); 
			$statement->bindValue(':email', $record['email'], PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik'] == 0) // user nie istnieje
			{
				$query =	'INSERT INTO ' . $this->table_name .
							' (user_login, user_password, user_name, user_surname, email, status, registered, active) VALUES' .
							' (:user_login, :user_password, :user_name, :user_surname, :email, :status, :registered, :active)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':user_login', $record['user_login'], PDO::PARAM_STR); 
				$statement->bindValue(':user_password', $record['user_password'], PDO::PARAM_STR); 
				$statement->bindValue(':user_name', $record['user_name'], PDO::PARAM_STR); 
				$statement->bindValue(':user_surname', $record['user_surname'], PDO::PARAM_STR); 
				$statement->bindValue(':email', $record['email'], PDO::PARAM_STR); 
				$statement->bindValue(':status', $record['status'], PDO::PARAM_INT); 
				$statement->bindValue(':registered', $record['registered'], PDO::PARAM_STR); 
				$statement->bindValue(':active', $record['active'], PDO::PARAM_INT); 
				
				$statement->execute();

				$inserted_id = $this->db->lastInsertId();
	
				// dopisuje role usera:

				$granted_roles = array(1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); // funkcje przydzielone nowemu userowi

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
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $inserted_id;
	}

	public function Save($id, $record)
	{
		$affected_rows = 0;

		if (!parent::check_required($record)) return NULL;

		try
		{
			// sprawdza, czy istnieje już taki user:

			$query =	'SELECT COUNT(*) AS licznik FROM ' . $this->table_name .
						' WHERE (user_login = :user_login OR email = :email)' .
						' AND id <> :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':user_login', $record['user_login'], PDO::PARAM_STR); 
			$statement->bindValue(':email', $record['email'], PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik'] == 0) // user nie istnieje
			{
				$query =	'UPDATE ' . $this->table_name .
							' SET user_login = :user_login, user_name = :user_name, user_surname = :user_surname,' .
							' email = :email, status = :status, modified = :modified, active = :active' .
							' WHERE id = :id';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':id', $id, PDO::PARAM_INT); 
				$statement->bindValue(':user_login', $record['user_login'], PDO::PARAM_STR); 
				$statement->bindValue(':user_name', $record['user_name'], PDO::PARAM_STR); 
				$statement->bindValue(':user_surname', $record['user_surname'], PDO::PARAM_STR); 
				$statement->bindValue(':email', $record['email'], PDO::PARAM_STR); 
				$statement->bindValue(':status', $record['status'], PDO::PARAM_INT); 
				$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
				$statement->bindValue(':active', $record['active'], PDO::PARAM_INT); 
				
				$statement->execute();
				
				$affected_rows = $statement->rowCount();
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $affected_rows;
	}

	public function Delete($id)
	{
		$affected_rows = 0;

		try
		{
			// sprawdza, czy istnieją archiwa usera:

			$query =	'SELECT COUNT(*) AS licznik FROM archives' .
						' WHERE author_id = :author_id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':author_id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik']) return NULL;

			// sprawdza, czy istnieją strony usera:

			$query =	'SELECT COUNT(*) AS licznik FROM pages' .
						' WHERE author_id = :author_id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':author_id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik']) return NULL;

			// sprawdza, czy istnieją kategorie usera:

			$query =	'SELECT COUNT(*) AS licznik FROM categories' .
						' WHERE author_id = :author_id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':author_id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik']) return NULL;

			// usuwa galerię usera:

			$query = 	'SELECT id FROM images' .
						' WHERE owner_id = :owner_id' .
						' ORDER BY id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':owner_id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$rows_result = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($rows_result as $row) // usuwa pliki z dysku serwera
			{
				$delete_result = unlink(GALLERY_DIR . IMG_DIR . $row['id']);
			}

			$query =	'DELETE FROM images' .
						' WHERE owner_id = :owner_id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':owner_id', $id, PDO::PARAM_INT); 
			
			$statement->execute();

			// usuwa role usera:

			$query =	'DELETE FROM user_roles' .
						' WHERE user_id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			
			$statement->execute();

			// usuwa usera:
			
			$query =	'DELETE FROM ' . $this->table_name .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			
			$statement->execute();
			
			$affected_rows = $statement->rowCount();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $affected_rows;
	}

	public function SetPassword($id, $record)
	{
		$affected_rows = 0;

		if ($record['user_password'] == $record['user_password_repeat'])
		{
			try
			{
				$query =	'UPDATE ' . $this->table_name .
							' SET user_password = :user_password, modified = :modified' .
							' WHERE id = :id';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':id', $id, PDO::PARAM_INT); 
				$statement->bindValue(':user_password', $record['user_password'], PDO::PARAM_STR); 
				$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
				
				$statement->execute();
				
				$affected_rows = $statement->rowCount();
			}
			catch (PDOException $e)
			{
				die ($e->getMessage());
			}
		}

		return $affected_rows;
	}
}

?>