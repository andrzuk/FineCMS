<?php

class Comments_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'comments';
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$condition = isset($_SESSION['comments_list_mode']) ? ' AND ' . $this->table_name . '.visible = ' . $_SESSION['comments_list_mode'] : NULL;
		$condition .= $_SESSION['user_status'] == USER ? ' AND user_id = ' . $_SESSION['user_id'] : NULL;

		$fields_list = array('ip', 'title', 'user_login', 'comment_content');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT ' . $this->table_name . '.id, user_login, ip, title, comment_content, ' . $this->table_name . '.visible, send_date' .
						' FROM ' . $this->table_name . 
						' INNER JOIN users ON users.id = ' . $this->table_name . '.user_id' .
						' INNER JOIN pages ON pages.id = ' . $this->table_name . '.page_id' .
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
		
		$condition = $_SESSION['user_status'] == USER ? ' AND user_id = ' . $_SESSION['user_id'] : NULL;

		try
		{
			$query =	'SELECT * FROM ' . $this->table_name .
						' WHERE id = :id' . $condition;

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

	public function Save($id, $record)
	{
		$affected_rows = 0;
		
		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET visible = :visible' .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':visible', $record['visible'], PDO::PARAM_INT); 
			
			$statement->execute();
		
			$affected_rows = $statement->rowCount();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $affected_rows;
	}

	public function Update($id, $record)
	{
		$affected_rows = 0;

		$condition = $_SESSION['user_status'] == USER ? ' AND user_id = ' . $_SESSION['user_id'] : NULL;

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET comment_content = :comment_content' .
						' WHERE id = :id' . $condition;

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':comment_content', $record['comment_content'], PDO::PARAM_STR); 
			
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

		$condition = $_SESSION['user_status'] == USER ? ' AND user_id = ' . $_SESSION['user_id'] : NULL;

		try
		{
			$query =	'DELETE FROM ' . $this->table_name .
						' WHERE id = :id' . $condition;

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

	public function GetAuthorId($id)
	{
		$this->row_item = array();

		try
		{
			$query =	'SELECT user_id FROM ' . $this->table_name .
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

		return $this->row_item['user_id'];
	}
}

?>
