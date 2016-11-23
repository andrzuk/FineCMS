<?php

class Admin_Model extends Model
{
	private $table_params;
	private $table_counter;

	public function __construct($db)
	{
		parent::__construct($db);

		$this->table_params = array(
			array(
				'module' => 'Funkcje',
				'table' => 'admin_functions',
				'condition' => '1',
			),
			array(
				'module' => 'Role',
				'table' => 'user_roles',
				'condition' => 'access = 1',
			),
			array(
				'module' => 'Konfiguracja',
				'table' => 'configuration',
				'condition' => '1',
			),
			array(
				'module' => 'Użytkownicy',
				'table' => 'users',
				'condition' => '1',
			),
			array(
				'module' => 'Galeria',
				'table' => 'images',
				'condition' => '1',
			),
			array(
				'module' => 'Kategorie',
				'table' => 'categories',
				'condition' => '1',
			),
			array(
				'module' => 'Strony',
				'table' => 'pages',
				'condition' => 'category_id > 0',
			),
			array(
				'module' => 'Opisy',
				'table' => 'pages',
				'condition' => 'category_id = 0',
			),
			array(
				'module' => 'Odwiedziny',
				'table' => 'visitors',
				'condition' => '1',
			),
			array(
				'module' => 'Wiadomości',
				'table' => 'user_messages',
				'condition' => 'requested = 1',
			),
			array(
				'module' => 'Wyszukiwania',
				'table' => 'searches',
				'condition' => '1',
			),
			array(
				'module' => 'Rejestracje',
				'table' => 'registers',
				'condition' => 'result = 1',
			),
			array(
				'module' => 'Logowania',
				'table' => 'logins',
				'condition' => 'user_id > 0',
			),
			array(
				'module' => 'Hasła',
				'table' => 'reminds',
				'condition' => '1',
			),
		);
	}

	public function GetTableCount($module_name)
	{
		foreach ($this->table_params as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'module') $module = $value;
				if ($key == 'table') $table = $value;
				if ($key == 'condition') $condition = $value;
			}

			if ($module == $module_name)
			{
				try
				{
					$query = "SELECT COUNT(*) AS licznik FROM " . $table . " WHERE " . $condition;

					$statement = $this->db->prepare($query);

					$statement->execute();
					
					$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

					$this->table_counter = $this->row_item['licznik'];
				}
				catch (PDOException $e)
				{
					die ($e->getMessage());
				}
				break;
			}
		}
		
		return $this->table_counter;
	}

	public function GetFileLines($module_name, $layout)
	{
		$result = 0;
		$file = NULL;

		switch ($module_name)
		{
			case 'Szablon':
				$file = TEMPL_DIR . 'pages/' . $layout . '.php';
				break;
			case 'Styl':
				$file = 'css/' . $layout . '.css';
				break;
			case 'Skrypt':
				$file = 'js/' . $layout . '.js';
				break;
		}

		if (file_exists($file))
		{
			$handle = fopen($file, "r");
			while (!feof($handle)) { $line = fgets($handle); $result++; }
			fclose($handle);
		}

		return $result;
	}
}

?>

