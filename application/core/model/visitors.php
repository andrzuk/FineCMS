<?php

class Visitors_Model extends Model
{
	private $table_name;

	private $host_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = MODULE_NAME;

		include LIB_DIR . 'hosts.php';
		$this->host_name = new Hosts($db);
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$excludes = "'localhost' ";

		try
		{
			$query = 	'SELECT visitor_ip FROM excludes WHERE active = 1 ORDER BY id';

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($rows as $row)
			{
				$excludes .= "'" . $row['visitor_ip'] . "' ";
			}

			$excludes = ' AND visitor_ip NOT IN (' . str_replace(' ', ',', trim($excludes)) . ')';
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		$condition = NULL;

		$fields_list = array('visitor_ip', 'http_referer', 'request_uri');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		$date_range = isset($_SESSION['date_from']) && isset($_SESSION['date_to']) ? " AND visited >= '" . $_SESSION['date_from'] . " 00:00:00' AND visited <= '" . $_SESSION['date_to'] . " 23:59:59'" : NULL;

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name . ' WHERE 1' . $condition . $filter . $date_range . $excludes .
						' ORDER BY ' . $this->list_params['sort_field'] . ' ' . $this->list_params['sort_order'] . 
						' LIMIT ' . $this->list_params['start_from'] . ', ' . $this->list_params['show_rows'];

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach ($this->rows_list as $k => $v)
			{
				foreach ($v as $key => $value)
				{
					if ($key == 'visitor_ip')
					{
						$this->rows_list[$k][$key] = array(
							'ip' => $this->rows_list[$k][$key],
							'name' => $this->host_name->find_host_name($this->rows_list[$k][$key]),
							);
					}
					if ($key == 'http_referer' || $key == 'request_uri')
					{
						$this->rows_list[$k][$key] = str_replace(array("?", "&", "=", "%"), array(" ? ", " & ", " = ", " % "), $this->rows_list[$k][$key]);
					}
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

			foreach ($this->row_item as $key => $value)
			{
				if ($key == 'visitor_ip')
				{
					$this->row_item[$key] = array(
						'ip' => $this->row_item[$key],
						'name' => $this->host_name->find_host_name($this->row_item[$key]),
						);
				}
				if ($key == 'http_referer' || $key == 'request_uri')
				{
					$this->row_item[$key] = str_replace(array("?", "&", "=", "%"), array(" ? ", " & ", " = ", " % "), $this->row_item[$key]);
				}
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function Exclude($id)
	{
		$inserted_id = 0;

		try
		{
			// odczytuje adres IP:

			$query =	'SELECT visitor_ip FROM ' . $this->table_name . ' WHERE id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$visitor_ip = $this->row_item['visitor_ip'];

			// sprawdza, czy istnieje już taki adres:

			$query =	'SELECT COUNT(*) AS licznik FROM excludes' .
						' WHERE visitor_ip = :visitor_ip';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':visitor_ip', $visitor_ip, PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik'] == 0) // adres nie istnieje
			{
				$active = 1;

				$query =	'INSERT INTO excludes' .
							' (visitor_ip, active) VALUES' .
							' (:visitor_ip, :active)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':visitor_ip', $visitor_ip, PDO::PARAM_STR); 
				$statement->bindValue(':active', $active, PDO::PARAM_INT); 
				
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
}

?>
