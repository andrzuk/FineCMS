<?php

class Logins_Model extends Model
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

		$condition = NULL;

		if (isset($_SESSION['logins_list_mode']))
		{
			$condition = $_SESSION['logins_list_mode'] == 1 ? ' AND user_id > 0' : ' AND user_id = 0';
		}

		$fields_list = array('agent', 'user_ip', 'login');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		$date_range = isset($_SESSION['date_from']) && isset($_SESSION['date_to']) ? " AND login_time >= '" . $_SESSION['date_from'] . " 00:00:00' AND login_time <= '" . $_SESSION['date_to'] . " 23:59:59'" : NULL;

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name . ' WHERE 1' . $condition . $filter . $date_range .
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
}

?>
