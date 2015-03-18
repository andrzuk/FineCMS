<?php

class Roles_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'user_roles';
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$condition = NULL;

		$fields_list = array('user_login', 'user_name', 'user_surname');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query =	"SELECT users.id, users.user_login," .
						" CONCAT(users.user_name, ' ', users.user_surname) AS user_name," .
						" users.status, NULL AS function" .
						" FROM " . $this->table_name . 
						" INNER JOIN users ON users.id = " . $this->table_name . ".user_id" .
						" INNER JOIN admin_functions ON admin_functions.id = " . $this->table_name . ".function_id" .
						" WHERE " . $this->table_name . ".access <= 1" . $condition . $filter .
						" GROUP BY users.id" .
						" ORDER BY " . $this->list_params['sort_field'] . " " . $this->list_params['sort_order'] . 
						" LIMIT " . $this->list_params['start_from'] . ", " . $this->list_params['show_rows'];

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($this->rows_list as $key => $row)
			{
				$query =	"SELECT admin_functions.function" .
								" FROM " . $this->table_name .
								" INNER JOIN admin_functions ON admin_functions.id = " . $this->table_name . ".function_id" .
								" WHERE " . $this->table_name . ".user_id = " . $row['id'] .
								" AND " . $this->table_name . ".access = 1" . 
								" ORDER BY " . $this->table_name . ".function_id";

				$statement = $this->db->prepare($query);

				$statement->execute();
				
				$sub_rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

				$group_names = array('Guest', 'Admin', 'Operator', 'User');

				$this->rows_list[$key]['status'] = $group_names[$row['status']];

				foreach ($sub_rows_list as $sub_row)
				{
					$this->rows_list[$key]['function'] .= $sub_row['function'] . '; ';
				}
			}

			$query =	"SELECT COUNT(DISTINCT user_id) AS licznik FROM " . $this->table_name .
						" INNER JOIN users ON users.id = " . $this->table_name . ".user_id" .
						" WHERE access <= 1" . $condition . $filter;

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$this->rows_count = $this->row_item['licznik'];
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

		$this->rows_list = array();

		try
		{
			$query =	'SELECT user_login, user_name, user_surname, module, meaning, access' .
						' FROM users' .
						' INNER JOIN ' . $this->table_name . ' ON ' . $this->table_name . '.user_id = users.id' .
						' INNER JOIN admin_functions ON admin_functions.id = ' . $this->table_name . '.function_id' .
						' WHERE user_id = :user_id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':user_id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

			$functions = NULL;

			$functions .= '<ol>';

			foreach ($this->rows_list as $k => $v) 
			{
				foreach ($v as $key => $value) 
				{
					if ($key == 'user_login') $user_login = $value;
					if ($key == 'user_name') $user_name = $value;
					if ($key == 'user_surname') $user_surname = $value;
					if ($key == 'module') $module = $value;
					if ($key == 'meaning') $meaning = $value;
					if ($key == 'access') $access = $value;
				}
				if ($access)
				{
					$functions .= '<li>';
					$functions .= $meaning . ' (' . $module . ')';
					$functions .= '</li>';
				}
			}

			$functions .= '</ol>';

			$this->row_item = array(
				'Imię i nazwisko' => $user_name . ' ' . $user_surname,
				'Login' => $user_login,
				'Funkcje' => $functions,
				);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function Add($records)
	{
		$inserted_id = 0;

		try
		{
			$query =	'INSERT INTO ' . $this->table_name .
						' (user_id, function_id, access) VALUES' .
						' (:user_id, :function_id, :access)';

			$statement = $this->db->prepare($query);

			foreach ($records as $k => $v)
			{
				foreach ($v as $key => $value)
				{
					if ($key == 'user_id') $user_id = $value;
					if ($key == 'function_id') $function_id = $value;
					if ($key == 'access') $access = $value;
				}
				$statement->bindParam(':user_id', $user_id, PDO::PARAM_INT); 
				$statement->bindParam(':function_id', $function_id, PDO::PARAM_INT); 
				$statement->bindParam(':access', $access, PDO::PARAM_INT); 

				$statement->execute();
			}
		
			$inserted_id = $this->db->lastInsertId();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $inserted_id;
	}

	public function Save($id, $records)
	{
		$this->Delete($id);

		$inserted_id = $this->Add($records);

		return $inserted_id;
	}

	public function Delete($id)
	{
		$affected_rows = 0;

		try
		{
			$query =	'DELETE FROM ' . $this->table_name .
						' WHERE user_id = :user_id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':user_id', $id, PDO::PARAM_INT); 
			
			$statement->execute();
			
			$affected_rows = $statement->rowCount();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $affected_rows;
	}

	public function GetAllUsers()
	{
		$this->rows_list = array();

		try
		{
			$query =	"SELECT users.id, users.user_login, users.user_name, users.user_surname, users.status" .
						" FROM users" .
						" LEFT JOIN user_roles ON user_roles.user_id = users.id" .
						" GROUP BY users.id" .
						" ORDER BY users.id";

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
		
		return $this->rows_list;
	}	

	public function GetNewUsers()
	{
		$this->rows_list = array();

		try
		{
			$query =	"SELECT users.id, users.user_login, users.user_name, users.user_surname, users.status" .
						" FROM users" .
						" LEFT JOIN user_roles ON user_roles.user_id = users.id" .
						" WHERE users.id NOT IN (SELECT user_id FROM user_roles)" .
						" GROUP BY users.id" .
						" ORDER BY users.id";

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
		
		return $this->rows_list;
	}	

	public function GetFunctions($user_id)
	{
		$this->rows_list = array();

		if ($user_id) // edit user roles
		{
			$query =	"SELECT admin_functions.id, admin_functions.function, admin_functions.meaning, admin_functions.module," .
						" user_roles.user_id, user_roles.function_id, user_roles.access" .
						" FROM admin_functions" .
						" INNER JOIN user_roles ON user_roles.function_id = admin_functions.id" .
						" WHERE user_roles.user_id = " . intval($user_id) .
						" ORDER BY admin_functions.id";
		}
		else // new user roles
		{
			$query =	"SELECT admin_functions.id, admin_functions.function, admin_functions.meaning, admin_functions.module," .
						" NULL AS user_id, admin_functions.id AS function_id, NULL AS access" .
						" FROM admin_functions" .
						" ORDER BY admin_functions.id";
		}

		try
		{
			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
		
		return $this->rows_list;
	}
}

?>
