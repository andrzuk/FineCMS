<?php

header('Content-Type: text/xml');

session_start();

$rows = array();
$xml_data = NULL;

if (isset($_SESSION[user_status]) && $_SESSION[user_status] > 0 || TRUE)
{
	include '../config/config.php';

	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	mysqli_query ($connection, 'SET NAMES utf8');

	$excludes = "'localhost' ";

	$query = 	'SELECT visitor_ip FROM excludes WHERE active = 1 ORDER BY id';

	$result = mysqli_query($connection, $query);
	
	if ($result)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$excludes .= "'" . $row['visitor_ip'] . "' ";
		}
		$excludes = ' AND visitor_ip NOT IN (' . str_replace(' ', ',', trim($excludes)) . ')';
				
		mysqli_free_result($result);
	}
	
	$query = 	"SELECT visitor_ip AS ip_label, COUNT(*) AS ip_counter" .
				" FROM visitors" .
				" WHERE 1" . $excludes .
				" GROUP BY visitor_ip" .
				" ORDER BY ip_counter DESC LIMIT 15";
				
	$result = mysqli_query($connection, $query);
	
	if ($result)
	{
		while ($row = mysqli_fetch_array($result)) $rows[] = $row;
		
		$xml_data .= '<?xml version="1.0" encoding="UTF-8"?>		
			<response>
		';
		
		foreach ($rows as $row)
		{
			$xml_data .= '
				<item>
				<ip_label>'.$row['ip_label'].'</ip_label>
				<ip_counter>'.$row['ip_counter'].'</ip_counter>
				</item>
			';
		}
		
		$xml_data .= '
			</response>
		';
		
		echo $xml_data; // zwraca dane w formacie XML
		
		mysqli_free_result($result);
	}

	mysqli_close($connection);
}

?>
