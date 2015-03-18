<?php

class Index_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'pages';
	}

	public function GetPage()
	{
		$this->row_item = array();

		$main_page = 1;
		$system_page = 0;
		$visible = 1;

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name .
						' WHERE main_page = :main_page AND system_page = :system_page AND visible = :visible' .
						' ORDER BY id DESC LIMIT 0, 1';
			
			$statement = $this->db->prepare($query);

			$statement->bindValue(':main_page', $main_page, PDO::PARAM_INT); 
			$statement->bindValue(':system_page', $system_page, PDO::PARAM_INT); 
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
}

?>