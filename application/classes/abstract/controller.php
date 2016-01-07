<?php

class Controller
{
	protected $app;

	protected $columns;
	protected $required;

	protected $navigator;

	private $path;
	private $categories;

	public function __construct($app)
	{
		$this->app = $app;
		
		$this->app->get_page()->set_template('index');
	}

	public function init($columns)
	{
		$this->columns = $columns;
	}

	public function AccessDenied()
	{
		$layout = $this->app->get_settings()->get_config_key('page_template_default');

		$this->app->get_page()->set_layout($layout);

		$this->app->get_page()->set_dialog(
			MSG_ERROR, 
			'Brak uprawnień', 
			'Dostęp zabroniony. Proszę się zalogować na konto z odpowiednimi uprawnieniami.',
			array(
				array('link' => 'index.php?route=login', 'caption' => 'Zaloguj', 'onclick' => NULL),
				array('link' => 'index.php', 'caption' => 'Anuluj', 'onclick' => NULL),
				)
			);

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowDialog());
	}

	public function Index_Action()
	{
		$date_from = isset($_SESSION['date_from']) ? $_SESSION['date_from'] : date("Y-m-d");
		$date_to = isset($_SESSION['date_to']) ? $_SESSION['date_to'] : date("Y-m-d");

		if (isset($_POST['date_from']))
		{
			$date_from = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['date_from']) ? trim($_POST['date_from']) : date("Y-m-d");
		}
		$_SESSION['date_from'] = $date_from;

		if (isset($_POST['date_to']))
		{
			$date_to = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['date_to']) ? trim($_POST['date_to']) : date("Y-m-d");
		}
		$_SESSION['date_to'] = $date_to;
	
		include CLASS_DIR . 'navigator.php';

		$this->navigator = new ListNavigator($this->app);

		$this->navigator->init($this->columns);

		$list_params = $this->navigator->get_params('db_params');

		$this->app->get_model_object()->set_list_params($list_params);
	}

	public function Update_Paginator()
	{
		$this->navigator->set_paginator($this->app->get_model_object()->get_rows_count());

		$list_params = $this->navigator->get_params('list_params');

		$this->app->get_view_object()->set_list_params($list_params);
	}

	public function Add_Action()
	{
		$this->app->get_model_object()->set_required($this->required);
	}

	public function Edit_Action()
	{
		$this->app->get_model_object()->set_required($this->required);
	}

	public function Delete_Action()
	{

	}

	public function ConfirmDelete($id)
	{
		$options = array(
			array(
				'link' => 'index.php?route='.MODULE_NAME,
				'caption' => 'Zamknij',
				'icon' => 'img/stop.png',
				),
			);

		$this->app->get_page()->set_options($options);

		$this->app->get_page()->set_dialog(
			MSG_QUESTION, 
			'Usuwanie rekordu', 
			'Uwaga! Rekord zostanie bezpowrotnie usunięty. <br />Czy na pewno chcesz usunąć rekord?',
			array(
				array('link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id.'&confirm=yes', 'caption' => 'Tak', 'onclick' => NULL),
				array('link' => 'index.php?route='.MODULE_NAME, 'caption' => 'Nie', 'onclick' => NULL),
				)
			);

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowDialog());
	}

	public function ConfirmReset()
	{
		$options = array(
			array(
				'link' => 'index.php?route='.MODULE_NAME,
				'caption' => 'Zamknij',
				'icon' => 'img/stop.png',
				),
			);

		$this->app->get_page()->set_options($options);

		$this->app->get_page()->set_dialog(
			MSG_QUESTION, 
			'Reset pliku', 
			'Uwaga! Plik zostanie bezpowrotnie zresetowany. <br />Czy na pewno chcesz zresetować plik?',
			array(
				array('link' => 'index.php?route='.MODULE_NAME.'&action=reset&confirm=yes', 'caption' => 'Tak', 'onclick' => NULL),
				array('link' => 'index.php?route='.MODULE_NAME, 'caption' => 'Nie', 'onclick' => NULL),
				)
			);

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowDialog());
	}

	public function View_Action()
	{

	}

	public function Up_Action()
	{

	}

	public function Down_Action()
	{

	}

	public function PageNotFound()
	{
		$this->app->get_page()->set_dialog(
			MSG_ERROR, 
			'Strona nie znaleziona', 
			'Żądanie nie może zostać obsłużone. Proszę sprawdzić poprawność adresu wywołania.',
			array(
				array('link' => 'index.php', 'caption' => 'Zamknij', 'onclick' => NULL),
				)
			);

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowDialog());
	}

	public function CategoryNotFound()
	{
		$this->app->get_page()->set_dialog(
			MSG_ERROR, 
			'Kategoria nie znaleziona', 
			'Żądanie nie może zostać obsłużone. Proszę sprawdzić poprawność adresu wywołania.',
			array(
				array('link' => 'index.php', 'caption' => 'Zamknij', 'onclick' => NULL),
				)
			);

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowDialog());
	}

	public function clean_url($url)
	{
		$pos = strpos($url, '&image=');

		$result = $pos ? substr($url, 0, $pos) : $url;

		return $result;
	}

	public function make_path($id)
	{
		$this->path = array();

		$section = $this->app->get_menu()->GetSection($id);

		$this->categories = $this->app->get_menu()->GetItems($section);

		$this->GetParent($id); // wywołanie rekurencyjnego budowania struktury od bieżącego node-a

		$this->path['index.php'] = 'Strona główna';

		return $this->path;
	}

	private function GetParent($node_id)
	{
		if (count($this->categories))
		{
			foreach ($this->categories as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					if ($k == 'id') $id = $v;
					if ($k == 'parent_id') $parent_id = $v;
					if ($k == 'caption') $caption = $v;
					if ($k == 'link') $link = $v;
				}

				if ($id == $node_id)
				{
					$this->path[$link] = $caption;

					$this->GetParent($parent_id); // rekurencyjne zagłębianie w strukturę
				}
			}
		}
	}
}

?>
