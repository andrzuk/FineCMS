<?php

/*
 * Klasa odpowiedzialna za rejestrację wejść na stronę
 */

class Visitors
{
	private $db;

	public function __construct($obj)
	{
		$this->db = $obj->get_dbc();
	}
	
	public function Store()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
		$uri = $_SERVER["REQUEST_URI"];
		$visit_time = date("Y-m-d H:i:s");

		if (isset($_SESSION['install_mode'])) return;

		try
		{
			$query = 	'INSERT INTO visitors' .
						' (visitor_ip, http_referer, request_uri, visited) VALUES' .
						' (:visitor_ip, :http_referer, :request_uri, :visited)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':visitor_ip', $ip, PDO::PARAM_STR); 
			$statement->bindValue(':http_referer', $ref, PDO::PARAM_STR); 
			$statement->bindValue(':request_uri', $uri, PDO::PARAM_STR); 
			$statement->bindValue(':visited', $visit_time, PDO::PARAM_STR); 
			
			$statement->execute();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
		
		// uaktualnia statystykę (tylko raz dziennie):
		$this->UpdateStatistics();
	}
	
	private function UpdateStatistics()
	{
		$date_from = date("Y-m-d", strtotime('-1 days'));
		$date_to = date("Y-m-d", time());

		try
		{
			// sprawdza, czy jest wpis w 'stat_main' na wczorajszy dzień:
			
			$query = "SELECT COUNT(*) AS licznik FROM stat_main WHERE date='".$date_from."'";

			$statement = $this->db->prepare($query);
			
			$statement->execute();
			
			$row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$data_found = intval($row_item['licznik']);
			
			if ($data_found == 0) // nie ma wpisu - dodajemy dla wszystkich trzech tabel
			{
				// tabela 'stat_main':

				$query = "INSERT INTO stat_main (id, date, start, contact, admin, login, reset, statistics) 
							VALUES 
							(NULL, '".$date_from."', 
							(SELECT COUNT(*) from visitors WHERE request_uri IN ('/', '/index.php') AND visited BETWEEN '".$date_from."' AND '".$date_to."'), 
							(SELECT COUNT(*) from visitors WHERE request_uri LIKE '%?route=contact' AND visited BETWEEN '".$date_from."' AND '".$date_to."'), 
							(SELECT COUNT(*) from visitors WHERE request_uri LIKE '%?route=admin' AND visited BETWEEN '".$date_from."' AND '".$date_to."'), 
							(SELECT COUNT(*) from visitors WHERE request_uri LIKE '%?route=login' AND visited BETWEEN '".$date_from."' AND '".$date_to."'), 
							(SELECT COUNT(*) from visitors WHERE request_uri LIKE '%?route=password' AND visited BETWEEN '".$date_from."' AND '".$date_to."'),
							(SELECT COUNT(*) from visitors WHERE request_uri LIKE '%?route=statistics' AND visited BETWEEN '".$date_from."' AND '".$date_to."')
							)";

				$statement = $this->db->prepare($query);

				$statement->execute();
				
				// tabela 'stat_cat':
				
				$query = "SELECT DISTINCT request_uri, COUNT(*) FROM visitors 
							WHERE request_uri LIKE '%?route=category%' AND visited BETWEEN '".$date_from."' AND '".$date_to."' 
							GROUP BY request_uri ORDER BY request_uri";

				$statement = $this->db->prepare($query);

				$statement->execute();
				
				$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($rows as $row)
				{
					$category_id = intval(substr($row['request_uri'], strpos($row['request_uri'], 'id=') + 3));
					
					$query = "INSERT INTO stat_cat (id, date, category_id, counter) VALUES (NULL, '".$date_from."', ".$category_id.", 
								(SELECT COUNT(*) FROM visitors WHERE request_uri LIKE '%?route=category&id=".$category_id."' AND visited BETWEEN '".$date_from."' AND '".$date_to."'))";

					$statement = $this->db->prepare($query);

					$statement->execute();
				}
				
				// tabela 'stat_ip':

				$query = "SELECT DISTINCT visitor_ip, COUNT(*) FROM visitors 
							WHERE visited BETWEEN '".$date_from."' AND '".$date_to."' 
							GROUP BY visitor_ip ORDER BY visitor_ip";

				$statement = $this->db->prepare($query);

				$statement->execute();
				
				$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($rows as $row)
				{
					$query = "INSERT INTO stat_ip (id, date, ip, counter) VALUES (NULL, '".$date_from."', '".$row['visitor_ip']."', 
								(SELECT COUNT(*) FROM visitors WHERE visitor_ip='".$row['visitor_ip']."' AND visited BETWEEN '".$date_from."' AND '".$date_to."'))";

					$statement = $this->db->prepare($query);

					$statement->execute();
				}
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
	}
}

?>
