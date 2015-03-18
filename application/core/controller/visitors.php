<?php

class Visitors_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Odwiedziny',
			));	
		
		$columns = array(
			array('db_name' => 'id',           'column_name' => 'Id',               'sorting' => 1),
			array('db_name' => 'visitor_ip',   'column_name' => 'Adres IP',         'sorting' => 1),
			array('db_name' => 'http_referer', 'column_name' => 'Adres odwoławczy', 'sorting' => 1),
			array('db_name' => 'request_uri',  'column_name' => 'Adres wywołany',   'sorting' => 1),
			array('db_name' => 'visited',      'column_name' => 'Data',             'sorting' => 1),
		);

		parent::init($columns);

		$layout = $this->app->get_settings()->get_config_key('page_template_admin');

		$this->app->get_page()->set_layout($layout);
	}

	public function Index_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::Index_Action();

			$options = array(
				array(
					'link' => 'index.php?route=excludes',
					'caption' => 'Wykluczenia',
					'icon' => 'img/rejected.png',
					),
				array(
					'link' => 'index.php?route=admin',
					'caption' => 'Zamknij',
					'icon' => 'img/stop.png',
					),
				);

			$data = $this->app->get_model_object()->GetAll();

			parent::Update_Paginator();

			$this->app->get_page()->set_options($options);

			$this->app->get_page()->set_content($this->app->get_view_object()->ShowList($this->columns, $data));
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function View_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::View_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			if (isset($_POST['cancel_button']))
			{
				header('Location: index.php?route='.MODULE_NAME);
				exit;
			}
			else // wczytany formularz
			{
				$options = array(
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=exclude&id='.$id,
						'caption' => 'Wyklucz adres',
						'icon' => 'img/table_export.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$data = $this->app->get_model_object()->GetOne($id);

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowDetails($data));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Exclude_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			$result = $this->app->get_model_object()->Exclude($id);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Adres IP został pomyślnie dodany do wykluczeń.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Adres IP nie został dodany do wykluczeń.');

			header('Location: index.php?route='.MODULE_NAME.'&paginator_reset=true');
			exit;
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}
}

?>
