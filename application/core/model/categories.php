<?php

class Categories_Model extends Model
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

		$condition = isset($_SESSION['categories_list_mode']) ? ' AND section = ' . $_SESSION['categories_list_mode'] : NULL;

		$fields_list = array('caption', 'link');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT ' . $this->table_name . '.id, parent_id, section, permission, item_order,' .
						' caption, link, page_id, visible, target, user_login, ' . $this->table_name . '.modified' .
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

		try
		{
			$query =	'SELECT ' . $this->table_name . '.*, user_login FROM ' . $this->table_name .
						' INNER JOIN users ON users.id = ' . $this->table_name . '.author_id' .
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
		
		try
		{
			$query =	'INSERT INTO ' . $this->table_name .
						' (parent_id, section, permission, item_order, caption, link, page_id, visible, target, author_id, modified) VALUES' .
						' (:parent_id, :section, :permission, :item_order, :caption, :link, :page_id, :visible, :target, :author_id, :modified)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':parent_id', $record['parent_id'], PDO::PARAM_INT); 
			$statement->bindValue(':section', $record['section'], PDO::PARAM_INT); 
			$statement->bindValue(':permission', $record['permission'], PDO::PARAM_INT); 
			$statement->bindValue(':item_order', $record['item_order'], PDO::PARAM_INT); 
			$statement->bindValue(':caption', $record['caption'], PDO::PARAM_STR); 
			$statement->bindValue(':link', $record['link'], PDO::PARAM_STR); 
			$statement->bindValue(':page_id', $record['page_id'], PDO::PARAM_INT); 
			$statement->bindValue(':visible', $record['visible'], PDO::PARAM_INT); 
			$statement->bindValue(':target', $record['target'], PDO::PARAM_INT); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
			
			$statement->execute();
			
			$inserted_id = $this->db->lastInsertId();

			$new_link = $record['link'] == DEFAULT_LINK ? 'index.php?route=category&id='.$inserted_id : $record['link'];

			// tworzy powiązaną stronę:

			$main_page = 0;
			$system_page = 0;
			$contents = NULL;
			$description = NULL;

			$query =	'INSERT INTO pages' .
						' (main_page, system_page, category_id, title, contents, description, author_id, visible, modified) VALUES' .
						' (:main_page, :system_page, :category_id, :title, :contents, :description, :author_id, :visible, :modified)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':main_page', $main_page, PDO::PARAM_INT); 
			$statement->bindValue(':system_page', $system_page, PDO::PARAM_INT); 
			$statement->bindValue(':category_id', $inserted_id, PDO::PARAM_INT); 
			$statement->bindValue(':title', $record['caption'], PDO::PARAM_STR); 
			$statement->bindValue(':contents', $contents, PDO::PARAM_STR); 
			$statement->bindValue(':description', $description, PDO::PARAM_STR); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
			$statement->bindValue(':visible', $record['visible'], PDO::PARAM_INT); 
			$statement->bindValue(':modified', $record['modified'], PDO::PARAM_STR); 
			
			$statement->execute();
			
			$page_id = $this->db->lastInsertId();
			
			// ustawia kolejność, page_id oraz link:

			$query =	'UPDATE ' . $this->table_name .
						' SET item_order = :item_order, link = :link, page_id = :page_id WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $inserted_id, PDO::PARAM_INT); 
			$statement->bindValue(':item_order', $inserted_id, PDO::PARAM_INT); 
			$statement->bindValue(':link', $new_link, PDO::PARAM_STR); 
			$statement->bindValue(':page_id', $page_id, PDO::PARAM_INT); 

			$statement->execute();
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
						' SET parent_id = :parent_id, section = :section, permission = :permission, caption = :caption, link = :link,' .
						' visible = :visible, target = :target, author_id = :author_id, modified = :modified' .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':parent_id', $record['parent_id'], PDO::PARAM_INT); 
			$statement->bindValue(':section', $record['section'], PDO::PARAM_INT); 
			$statement->bindValue(':permission', $record['permission'], PDO::PARAM_INT); 
			$statement->bindValue(':caption', $record['caption'], PDO::PARAM_STR); 
			$statement->bindValue(':link', $record['link'], PDO::PARAM_STR); 
			$statement->bindValue(':visible', $record['visible'], PDO::PARAM_INT); 
			$statement->bindValue(':target', $record['target'], PDO::PARAM_INT); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
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
			// usuwa archiwa powiązanej strony:

			$query =	'DELETE FROM archives' .
						' WHERE category_id = :category_id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':category_id', $id, PDO::PARAM_INT); 
			
			$statement->execute();

			// usuwa powiązaną stronę:

			$query =	'DELETE FROM pages'.
						' WHERE category_id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			
			$statement->execute();

			// usuwa kategorię:

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

	public function MoveUp($id, $record)
	{
		$affected_rows = 0;

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET item_order = item_order - 1, author_id = :author_id, modified = :modified' .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
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

	public function MoveDown($id, $record)
	{
		$affected_rows = 0;

		try
		{
			$query =	'UPDATE ' . $this->table_name .
						' SET item_order = item_order + 1, author_id = :author_id, modified = :modified' .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			$statement->bindValue(':author_id', $record['author_id'], PDO::PARAM_INT); 
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

	public function GetPageId($category_id)
	{
		$this->row_item = array();
		$result = 0;

		try
		{
			$query =	'SELECT id FROM pages WHERE category_id = :category_id' .
						' ORDER BY id DESC LIMIT 0, 1';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':category_id', $category_id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$result = $this->row_item['id'];
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $result;
	}

	public function GetCategories()
	{
		$rows_result = array();

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name .
						' ORDER BY id';

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
}

?>
