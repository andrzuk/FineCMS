<?php

class Pages_Model extends Model
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

		$condition = ' AND category_id > 0 ';

		$fields_list = array('caption', 'title', 'contents', 'description');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT ' . $this->table_name . '.id, main_page, system_page, caption, title, contents, description,' .
						' user_login, ' . $this->table_name . '.visible, ' . $this->table_name . '.modified' .
						' FROM ' . $this->table_name . 
						' INNER JOIN users ON users.id = ' . $this->table_name . '.author_id' .
						' INNER JOIN categories ON categories.id = ' . $this->table_name . '.category_id' .
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

		try
		{
			$query =	'SELECT ' . $this->table_name . '.*, caption, user_login FROM ' . $this->table_name .
						' INNER JOIN users ON users.id = ' . $this->table_name . '.author_id' .
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

	public function Add($record)
	{
		$inserted_id = 0;

		if (!parent::check_required($record)) return NULL;

		if (!$record['category_id']) return NULL;

		try
		{
			$query =	'INSERT INTO ' . $this->table_name .
						' (main_page, system_page, category_id, title, contents, description, author_id, visible, modified) VALUES' .
						' (:main_page, :system_page, :category_id, :title, :contents, :description, :author_id, :visible, :modified)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':main_page', $record['main_page'], PDO::PARAM_INT); 
			$statement->bindValue(':system_page', $record['system_page'], PDO::PARAM_INT); 
			$statement->bindValue(':category_id', $record['category_id'], PDO::PARAM_INT); 
			$statement->bindValue(':title', $record['title'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $record['contents'], PDO::PARAM_STR); 
			$statement->bindValue(':description', $record['description'], PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':visible', $record['visible'], PDO::PARAM_INT); 
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

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET category_id = :category_id, title = :title, contents = :contents, description = :description,' .
						' author_id = :author_id, visible = :visible, modified = :modified' .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':category_id', $record['category_id'], PDO::PARAM_INT); 
			$statement->bindValue(':title', $record['title'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $record['contents'], PDO::PARAM_STR); 
			$statement->bindValue(':description', $record['description'], PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':visible', $record['visible'], PDO::PARAM_INT); 
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

		try
		{
			// usuwa archiwa strony:

			$query =	'DELETE FROM archives' .
						' WHERE page_id = :page_id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':page_id', $id, PDO::PARAM_INT); 
			
			$statement->execute();

			// usuwa stronę:

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

	public function Archive($id)
	{
		$inserted_id = 0;

		$original_row_item = array();

		if (!$id) return NULL;

		try
		{
			// wczytuje oryginalny rekord:

			$query =	'SELECT * FROM ' . $this->table_name . ' WHERE id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$original_row_item = $statement->fetch(PDO::FETCH_ASSOC);

			// sprawdza, czy istnieje już kopia rekordu:

			$modified = $original_row_item['modified'];

			$query =	'SELECT COUNT(*) AS licznik FROM archives WHERE page_id = :id AND modified = :modified ';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':modified', $modified, PDO::PARAM_INT); 

			$statement->execute();
			
			$result = $statement->fetch(PDO::FETCH_ASSOC);

			if ($result['licznik'] == 0) // nie ma jeszcze kopii rekordu
			{
				$query =	'INSERT INTO archives' .
							' (page_id, main_page, system_page, category_id, title, contents, description, author_id, visible, modified) VALUES' .
							' (:page_id, :main_page, :system_page, :category_id, :title, :contents, :description, :author_id, :visible, :modified)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':page_id', $original_row_item['id'], PDO::PARAM_INT); 
				$statement->bindValue(':main_page', $original_row_item['main_page'], PDO::PARAM_INT); 
				$statement->bindValue(':system_page', $original_row_item['system_page'], PDO::PARAM_INT); 
				$statement->bindValue(':category_id', $original_row_item['category_id'], PDO::PARAM_INT); 
				$statement->bindValue(':title', $original_row_item['title'], PDO::PARAM_STR); 
				$statement->bindValue(':contents', $original_row_item['contents'], PDO::PARAM_STR); 
				$statement->bindValue(':description', $original_row_item['description'], PDO::PARAM_STR); 
				$statement->bindValue(':author_id', $original_row_item['author_id'], PDO::PARAM_INT); 
				$statement->bindValue(':visible', $original_row_item['visible'], PDO::PARAM_INT); 
				$statement->bindValue(':modified', $original_row_item['modified'], PDO::PARAM_STR); 
				
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

	public function GetArchives($id)
	{
		$this->rows_list = array();

		try
		{
			$query = 	'SELECT * FROM archives WHERE page_id = :page_id ORDER BY id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':page_id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}

	public function Restore($id, $record)
	{
		$affected_rows = 0;

		$archive_row_item = array();

		try
		{
			// wczytuje archiwalny rekord:

			$archive_id = $record['archive_id'];

			$query =	'SELECT * FROM archives WHERE id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $archive_id, PDO::PARAM_INT); 

			$statement->execute();
			
			$archive_row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($archive_row_item)
			{
				// przywraca rekord z archiwum:

				$query =	'UPDATE ' . $this->table_name .
							' SET category_id = :category_id, title = :title, contents = :contents, description = :description,' .
							' author_id = :author_id, visible = :visible, modified = :modified' .
							' WHERE id = :id';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':id', $id, PDO::PARAM_INT); 
				$statement->bindValue(':category_id', $archive_row_item['category_id'], PDO::PARAM_INT); 
				$statement->bindValue(':title', $archive_row_item['title'], PDO::PARAM_STR); 
				$statement->bindValue(':contents', $archive_row_item['contents'], PDO::PARAM_STR); 
				$statement->bindValue(':description', $archive_row_item['description'], PDO::PARAM_STR); 
				$statement->bindValue(':author_id', $archive_row_item['author_id'], PDO::PARAM_INT); 
				$statement->bindValue(':visible', $archive_row_item['visible'], PDO::PARAM_INT); 
				$statement->bindValue(':modified', $archive_row_item['modified'], PDO::PARAM_STR); 
				
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

	public function GetCategories()
	{
		$rows_result = array();

		try
		{
			$query = 	'SELECT * FROM categories ORDER BY id';

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$rows_result = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $rows_result;
	}

	public function GetCategoryId($page_id)
	{
		$this->row_item = array();
		$result = 0;

		try
		{
			$query =	'SELECT category_id FROM pages WHERE id = :page_id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':page_id', $page_id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$result = $this->row_item['category_id'];
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $result;
	}
}

?>
