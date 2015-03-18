<?php

class Excludes_Model extends Model
{
	private $table_name;

	private $host_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'excludes';

		include LIB_DIR . 'hosts.php';
		$this->host_name = new Hosts($db);
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$condition = NULL;

		$fields_list = array('visitor_ip');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT *, NULL AS host_name FROM ' . $this->table_name . ' WHERE 1' . $condition . $filter .
						' ORDER BY ' . $this->list_params['sort_field'] . ' ' . $this->list_params['sort_order'] . 
						' LIMIT ' . $this->list_params['start_from'] . ', ' . $this->list_params['show_rows'];

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($this->rows_list as $k => $v)
			{
				foreach ($v as $key => $value)
				{
					if ($key == 'visitor_ip') $visitor_ip = $value;
					if ($key == 'host_name') $this->rows_list[$k][$key] = $this->host_name->find_host_name($visitor_ip);
				}
			}

			$this->GetCount($query);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}

	public function Add($record)
	{
		$inserted_id = 0;

		if (!parent::check_required($record)) return NULL;
		
		try
		{
			// sprawdza, czy istnieje już taki adres:

			$query =	'SELECT COUNT(*) AS licznik FROM ' . $this->table_name .
						' WHERE visitor_ip = :visitor_ip';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':visitor_ip', $record['visitor_ip'], PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik'] == 0) // adres nie istnieje
			{
				$query =	'INSERT INTO ' . $this->table_name .
							' (visitor_ip, active) VALUES' .
							' (:visitor_ip, :active)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':visitor_ip', $record['visitor_ip'], PDO::PARAM_STR); 
				$statement->bindValue(':active', $record['active'], PDO::PARAM_INT); 
				
				$statement->execute();
			
				$inserted_id = $this->db->lastInsertId();
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $inserted_id;
	}

	public function Delete($id)
	{
		$affected_rows = 0;

		try
		{
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

	public function Activate($id)
	{
		$affected_rows = 0;

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET active = 1 - active' .
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
}

?>
