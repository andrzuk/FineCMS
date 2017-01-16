<?php

header('Access-Control-Allow-Origin: *');

if (isset($_GET['id']) && isset($_GET['folder']))
{
	$image_data = file_get_contents($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/'.$_GET['folder'].$_GET['id']);
	echo base64_encode($image_data);
}

?>
