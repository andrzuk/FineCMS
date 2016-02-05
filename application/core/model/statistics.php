<?php

class Statistics_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'visitors';
	}
	
	public function GetStatistics()
	{
		$this->rows_list = array();
		$this->row_item = array();

		try
		{
			// całkowita ilość wejść:
			
			$query = 	"SELECT COUNT(*) AS period_counter" .
						" FROM stat_ip";

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
			
			$this->rows_list['all'] = $this->row_item;
			
			// ilość wejść w miesiącu:
			
			$str_days_from = '-31 days';
			$str_days_to = '+1 days';
			
			$date_from = date("Y-m-d", strtotime($str_days_from));
			$date_to = date("Y-m-d", strtotime($str_days_to));
			
			$query = 	"SELECT COUNT(*) AS period_counter" .
						" FROM stat_ip" .
						" WHERE date BETWEEN '".$date_from."' AND '".$date_to."'";

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
			
			$this->rows_list['month'] = $this->row_item;
			
			// ilość wejść w tygodniu:
			
			$str_days_from = '-7 days';
			$str_days_to = '+1 days';
			
			$date_from = date("Y-m-d", strtotime($str_days_from));
			$date_to = date("Y-m-d", strtotime($str_days_to));
			
			$query = 	"SELECT COUNT(*) AS period_counter" .
						" FROM stat_ip" .
						" WHERE date BETWEEN '".$date_from."' AND '".$date_to."'";

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
			
			$this->rows_list['week'] = $this->row_item;
			
			// ilość wejść dzisiejszych:
			
			$str_days_from = '-1 days';
			$str_days_to = '+1 days';
			
			$date_from = date("Y-m-d", strtotime($str_days_from));
			$date_to = date("Y-m-d", strtotime($str_days_to));
			
			$query = 	"SELECT COUNT(*) AS period_counter" .
						" FROM stat_ip" .
						" WHERE date BETWEEN '".$date_from."' AND '".$date_to."'";

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
			
			$this->rows_list['day'] = $this->row_item;
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}
	
	public function GetIpStats()
	{
		$this->rows_list = array();
		$this->row_item = array();

		try
		{
			// całkowita ilość adresów IP:
			
			$query = 	"SELECT COUNT(DISTINCT visitor_ip) AS ip_counter" .
						" FROM visitors";

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
			
			$this->rows_list['all'] = $this->row_item;			
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}
}

?>