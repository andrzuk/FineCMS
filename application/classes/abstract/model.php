<?php

class Model
{
	protected $db;

	protected $rows_list;
	protected $row_item;

	protected $rows_count;

	protected $list_params;

	protected $required;
	protected $failed;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function GetCount($query)
	{
		$counter = 0;

		$from = strpos($query, ' FROM ');
		$to = strpos($query, ' ORDER BY ');

		try
		{
			$query = 'SELECT COUNT(*) AS counter' . substr($query, $from, $to - $from);

			$statement = $this->db->prepare($query);
			$statement->execute();
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item)
			{
				$this->rows_count = $this->row_item['counter'];
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_count;
	}

	public function get_rows_count()
	{
		return $this->rows_count;
	}

	public function set_list_params($params)
	{
		$this->list_params = $params;
	}

	public function get_list_params()
	{
		return $this->list_params;
	}

	public function make_filter($fields_list)
	{
		$fields_string = NULL;

		$fields_string .= ' AND (0';

		foreach ($fields_list as $field) 
		{
			$fields_string .= " OR " . $field . " LIKE '%" . $_SESSION['list_filter'] . "%'";
		}

		$fields_string .= ')';

		return $fields_string;
	}

	public function set_required($required)
	{
		$this->required = $required;
	}

	public function check_required($record)
	{
		$result = TRUE;
		$this->failed = array();

		foreach ($record as $key => $value)
		{
			$_SESSION['form_fields'][$key] = $value;
		}

		foreach ($this->required as $key => $value) 
		{
			if (!strlen(strval($record[$value])))
			{
				$this->failed[] = $value;
				$result = FALSE;
			}
		}

		$_SESSION['form_failed'] = $this->failed;

		return $result;
	}

	public function convert_to_html($subject, $content)
	{
		$main_text = "<html><head><title>" . $subject . "</title></head><body><p>" . $content . "</p></body></html>";
		$main_text = str_replace("\n", "<br />", $main_text);

		return $main_text;
	}
}

?>
