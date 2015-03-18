<?php

class Config_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'configuration';
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$condition = NULL;

		$fields_list = array('key_name', 'key_value', 'meaning');

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
			// sprawdza, czy istnieje już taki klucz:

			$query =	'SELECT COUNT(*) AS licznik FROM ' . $this->table_name .
						' WHERE key_name = :key_name';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':key_name', $record['key_name'], PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik'] == 0) // klucz nie istnieje
			{
				$query =	'INSERT INTO ' . $this->table_name .
							' (key_name, key_value, meaning, field_type, active, modified) VALUES' .
							' (:key_name, :key_value, :meaning, :field_type, :active, :modified)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':key_name', $record['key_name'], PDO::PARAM_STR); 
				$statement->bindValue(':key_value', $record['key_value'], PDO::PARAM_STR); 
				$statement->bindValue(':meaning', $record['meaning'], PDO::PARAM_STR); 
				$statement->bindValue(':field_type', $record['field_type'], PDO::PARAM_INT); 
				$statement->bindValue(':active', $record['active'], PDO::PARAM_INT); 
				$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
				
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

	public function Save($id, $record)
	{
		$affected_rows = 0;

		if (!parent::check_required($record)) return NULL;
		
		try
		{
			// sprawdza, czy istnieje już taki klucz:

			$query =	'SELECT COUNT(*) AS licznik FROM ' . $this->table_name .
						' WHERE key_name = :key_name' .
						' AND id <> :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':key_name', $record['key_name'], PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item['licznik'] == 0) // klucz nie istnieje
			{
				$query =	'UPDATE ' . $this->table_name .
							' SET key_name = :key_name, key_value = :key_value, meaning = :meaning,' .
							' field_type = :field_type, active = :active, modified = :modified' .
							' WHERE id = :id';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':id', $id, PDO::PARAM_INT); 
				$statement->bindValue(':key_name', $record['key_name'], PDO::PARAM_STR); 
				$statement->bindValue(':key_value', $record['key_value'], PDO::PARAM_STR); 
				$statement->bindValue(':meaning', $record['meaning'], PDO::PARAM_STR); 
				$statement->bindValue(':field_type', $record['field_type'], PDO::PARAM_INT); 
				$statement->bindValue(':active', $record['active'], PDO::PARAM_INT); 
				$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
				
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
}

?>
