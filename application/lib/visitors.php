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
	}
}

?>
