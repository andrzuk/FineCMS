<?php

header('Content-Type: text/xml');

session_start();

$rows = array();
$xml_data = NULL;
$idx = 0;
$days = 0;
$offset = 0;

if (isset($_SESSION[user_status]) && $_SESSION[user_status] > 0 || TRUE)
{
	include '../config/config.php';

	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	mysqli_query ($connection, 'SET NAMES utf8');
	
	$days = isset($_SESSION['chart_range_days']) ? $_SESSION['chart_range_days'] : 7;
	$offset = isset($_SESSION['chart_range_offset']) ? $_SESSION['chart_range_offset'] : 0;

	if (in_array($_GET['type'], array('days_7', 'days_14', 'days_21')))
	{
		$days = intval(substr($_GET['type'], 5));
		$offset = 0;
		$_SESSION['chart_range_offset'] = 0;
	}
	if (in_array($_GET['type'], array('months_1', 'months_2', 'months_3', 'months_6', 'months_12')))
	{
		$days = intval(substr($_GET['type'], 7)) * 30;
		$offset = 0;
		$_SESSION['chart_range_offset'] = 0;
	}
	if ($_GET['type'] == 'offset_prev') 
	{
		$offset++;
	}
	if ($_GET['type'] == 'offset_next') 
	{
		if ($offset > 0) $offset--;
	}
	
	$_SESSION['chart_range_days'] = $days;
	$_SESSION['chart_range_offset'] = $offset;
	
	$str_days_from = '-' . strval($days + $offset) . ' days';
	$str_days_to = '-' . strval($offset) . ' days';
	
	$date_from = date("Y-m-d", strtotime($str_days_from));
	$date_to = date("Y-m-d", strtotime($str_days_to));
	
	$query = 	"SELECT date AS date_label, COUNT(*) AS date_counter" .
				" FROM stat_ip" .
				" WHERE date BETWEEN '".$date_from."' AND '".$date_to."'" . 
				" GROUP BY date" .
				" ORDER BY date LIMIT 365";
				
	$result = mysqli_query($connection, $query);

	if ($result)
	{
		while ($row = mysqli_fetch_array($result)) $rows[] = $row;
		
		$xml_data .= '<?xml version="1.0" encoding="UTF-8"?>		
			<response>
		';
		
		$xml_data .= '
			<period>
			<date_from>'.$date_from.'</date_from>
			<date_to>'.$date_to.'</date_to>
			</period>
		';

		foreach ($rows as $row)
		{
			$xml_data .= '
				<item>
				<date_label>'.($idx++ % intval(count($rows) / 21 + 1) ? ' ' : substr($row['date_label'], 5)).'</date_label>
				<date_counter>'.$row['date_counter'].'</date_counter>
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
