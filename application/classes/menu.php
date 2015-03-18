<?php

/*
 * Klasa odpowiedzialna za pobieranie menu nawigacji strony
 */

class Menu
{
	private $app;

	private $db;

	private $rows_list;

	private $categories;
	private $skip_bar = array();
	
	public function __construct($obj)
	{
		$this->app = $obj;

		$this->db = $this->app->get_dbc();
	}
	
	public function GetItems($section)
	{
		$visible = 1;

		if (isset($_SESSION['install_mode'])) return NULL;

		try
		{
			$query =	'SELECT id, parent_id, section, caption, link, target, permission' .
						' FROM categories' .
						' WHERE section = :section AND visible = :visible' .
						' ORDER BY item_order';
			
			$statement = $this->db->prepare($query);

			$statement->bindParam(':section', $section, PDO::PARAM_INT);
			$statement->bindParam(':visible', $visible, PDO::PARAM_INT);
			
			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
		
		return $this->rows_list;
	}

	public function GetSection($id)
	{
		$section = 0;

		try
		{
			$query =	'SELECT section FROM categories WHERE id = :id';
			
			$statement = $this->db->prepare($query);

			$statement->bindParam(':id', $id, PDO::PARAM_INT);
			
			$statement->execute();
			
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			$section = $row['section'];
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $section;
	}

	public function SetSkipNode($id, $elements)
	{
		$this->skip_bar[] = array(
				'id' => $id,
				'link' => $elements['link'],
				'caption' => $elements['caption'],
			);
	}

	public function GetSkipBar($id)
	{
		$prev = NULL;
		$next = NULL;

		$section = $this->GetSection($id);

		$this->categories = $this->GetItems($section);

		$this->GetChildren(0); // wywołanie rekurencyjnego budowania struktury od root-a (node = 0)

		foreach ($this->skip_bar as $k => $v)
		{
			if ($v['id'] == $id)
			{
				if (array_key_exists($k - 1, $this->skip_bar)) 
					$prev = $this->skip_bar[$k - 1];
				if (array_key_exists($k + 1, $this->skip_bar)) 
					$next = $this->skip_bar[$k + 1];
				break;
			}
		}

		return array('id' => $id, 'prev' => $prev, 'next' => $next);
	}

	private function GetChildren($node_id)
	{
		if (count($this->categories))
		{
			foreach ($this->categories as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					if ($k == 'id') $id = $v;
					if ($k == 'parent_id') $parent_id = $v;
					if ($k == 'caption') $caption = $v;
					if ($k == 'link') $link = $v;
				}
				
				if ($parent_id == $node_id)
				{
					$this->SetSkipNode($id, array('link' => $link, 'caption' => $caption,)); // buduje tablicę Prev/Next
						
					$this->GetChildren($id); // rekurencyjne zagłębianie w strukturę
				}
			}
		}
	}
}

?>
