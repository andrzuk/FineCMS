<?php

session_start();

if (!isset($_SESSION['init']))
{
	session_regenerate_id();
	$_SESSION['init'] = TRUE;
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}
if ($_SERVER['REMOTE_ADDR'] != $_SESSION['ip'])
{
	session_destroy();
}

error_reporting(E_ALL);

ob_start();

include 'config/config.php';

include CLASS_DIR . 'application.php';

$app = new Application();

$app->start();

if (isset($_GET['route'])) 
{
	$routing = explode('&', $_GET['route']);
	$route_controller = APP_DIR . 'controller/' . trim($routing[0]) . '.php';
	
	if (!file_exists($route_controller)) 
	{
		$route_controller = APP_DIR . 'controller/index.php';
	}
}
else
{
	$route_controller = APP_DIR . 'controller/index.php';
}

if (isset($_SESSION['install_mode']))
{
	$route_controller = APP_DIR . 'controller/install.php';
}

$route_action = isset($_GET['action']) ? $_GET['action'] : 'index';

$app->set_controller($route_controller, $route_action);

$app->get_page()->render();

$app->stop();

ob_end_flush();

?>
