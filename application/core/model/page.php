<?php

class Page_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'pages';
	}

	public function GetPage($id)
	{
		$this->row_item = array();

		$main_page = 0;
		$system_page = 0;
		$show_all_types = 1;
		$visible = 1;

		try
		{
			$query = 	'SELECT title, contents, description, category_id, user_login, ' . $this->table_name . '.modified' .
						' FROM ' . $this->table_name .
						' INNER JOIN users ON users.id = ' . $this->table_name . '.author_id' .
						' WHERE (:show_all_types OR main_page = :main_page AND system_page = :system_page)' .
						' AND ' . $this->table_name . '.id = :id AND visible = :visible';
			
			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':main_page', $main_page, PDO::PARAM_INT); 
			$statement->bindValue(':system_page', $system_page, PDO::PARAM_INT); 
			$statement->bindValue(':show_all_types', $show_all_types, PDO::PARAM_INT); 
			$statement->bindValue(':visible', $visible, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);			
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function GetCategory($id)
	{
		$this->row_item = array();

		try
		{
			$query = 	'SELECT category_id, permission, categories.visible' .
						' FROM ' . $this->table_name .
						' INNER JOIN categories ON categories.id = ' . $this->table_name . '.category_id' .
						' WHERE ' . $this->table_name . '.id = :id';
			
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

	public function GetChildren($id)
	{
		$rows_result = array();

		try
		{
			$query = 	'SELECT caption, link FROM categories' .
						' WHERE parent_id = ' .
						' (SELECT category_id FROM ' . $this->table_name . ' WHERE id = :id)' .
						' ORDER BY item_order';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$rows_result = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $rows_result;
	}
}

?>
