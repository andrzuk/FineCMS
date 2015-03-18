<?php

/*
 * Klasa odpowiedzialna za obsługę nazw hostów na podst. adresów IP
 */

class Hosts
{
	private $db;

	private $table_name;

	public function __construct($obj)
	{
		$this->db = $obj;

		$this->table_name = 'hosts';
	}

	public function find_host_name($host_address)
	{
		$host_name = NULL;

		try
		{
			$query =	'SELECT server_name FROM ' . $this->table_name .
						' WHERE server_ip = :server_ip';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':server_ip', $host_address, PDO::PARAM_STR); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$host_name = $this->row_item['server_name'];

			if ($host_name == NULL) // nie znalazł w tablicy - trzeba dopisać
			{
				$host_name = gethostbyaddr($host_address);

				$query =	'INSERT INTO ' . $this->table_name .
							' (server_ip, server_name) VALUES' .
							' (:server_ip, :server_name)';

				$statement = $this->db->prepare($query);

				$statement->bindValue(':server_ip', $host_address, PDO::PARAM_STR); 
				$statement->bindValue(':server_name', $host_name, PDO::PARAM_STR); 
				
				$statement->execute();
			
				$inserted_id = $this->db->lastInsertId();
			}

			$host_name = str_replace(array("."), array(". "), $host_name);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $host_name;
	}
}

?>
