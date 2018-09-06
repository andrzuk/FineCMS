<?php

class Notes_Model extends Model
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

		$condition = ' AND ' . $this->table_name . '.author_id = ' . $_SESSION['user_id'];

		$fields_list = array('title', 'contents');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT ' . $this->table_name . '.id, title, contents,' .
						' user_login, ' . $this->table_name . '.modified' .
						' FROM ' . $this->table_name . 
						' INNER JOIN users ON users.id = ' . $this->table_name . '.author_id' .
						' WHERE 1' . $condition . $filter .
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
		
		$author_id = $_SESSION['user_id'];

		try
		{
			$query =	'SELECT ' . $this->table_name . '.*, user_login FROM ' . $this->table_name .
						' INNER JOIN users ON users.id = ' . $this->table_name . '.author_id' .
						' WHERE author_id = :author_id AND ' . $this->table_name . '.id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':author_id', $author_id, PDO::PARAM_INT); 
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
			$query =	'INSERT INTO ' . $this->table_name .
						' (title, contents, author_id, modified) VALUES' .
						' (:title, :contents, :author_id, :modified)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':title', $record['title'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $record['contents'], PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
			
			$statement->execute();
			
			$inserted_id = $this->db->lastInsertId();
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

		$author_id = $_SESSION['user_id'];

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET title = :title, contents = :contents, author_id = :author_id, modified = :modified' .
						' WHERE author_id = :author_id AND id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':title', $record['title'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $record['contents'], PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $author_id, PDO::PARAM_INT); 
			$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
			
			$statement->execute();
			
			$affected_rows = $statement->rowCount();
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

		$author_id = $_SESSION['user_id'];

		try
		{
			$query =	'DELETE FROM ' . $this->table_name .
						' WHERE author_id = :author_id AND id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':author_id', $author_id, PDO::PARAM_INT); 
			
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
