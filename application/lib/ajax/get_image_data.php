<?php

header('Access-Control-Allow-Origin: *');

if (isset($_GET['id']))
{
	$image_data = file_get_contents($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/'.GALLERY_DIR.IMG_DIR.$_GET['id']);
	echo base64_encode($image_data);
}

?>
