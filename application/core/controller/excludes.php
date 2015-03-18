<?php

class Excludes_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route=visitors' => 'Odwiedziny',
			'index.php?route='.MODULE_NAME => 'Wykluczenia',
			));	
		
		$columns = array(
			array('db_name' => 'id',         'column_name' => 'Id',                      'sorting' => 1),
			array('db_name' => 'visitor_ip', 'column_name' => 'Adres IP odwiedzającego', 'sorting' => 1),
			array('db_name' => 'active',     'column_name' => 'Aktywny',                 'sorting' => 1),
			array('db_name' => 'host_name',  'column_name' => 'Nazwa usługodawcy',       'sorting' => 1),
		);

		$this->required = array('visitor_ip');

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
					'link' => 'index.php?route='.MODULE_NAME.'&action=add',
					'caption' => 'Nowy adres',
					'icon' => 'img/files.png',
					),
				array(
					'link' => 'index.php?route=visitors',
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

	public function Add_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::Add_Action();

			if (!empty($_POST))
			{
				$record = array(
					'visitor_ip' => $_POST['visitor_ip'],
					'active' => 1,
					);

				if (isset($_POST['save_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Adres IP został pomyślnie dodany do wykluczeń.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Adres IP nie został dodany do wykluczeń.');

					header('Location: index.php?route='.MODULE_NAME);
					exit;
				}
				else // button Anuluj
				{
					header('Location: index.php?route='.MODULE_NAME);
					exit;
				}
			}
			else // pusty formularz
			{
				$options = array(
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$data = NULL;

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data));
			}			
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Delete_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::Delete_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			if (isset($_GET['confirm']))
			{
				$result = $this->app->get_model_object()->Delete($id);

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Adres IP został pomyślnie usunięty z wykluczeń.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Adres IP nie został usunięty z wykluczeń.');

				header('Location: index.php?route='.MODULE_NAME);
				exit;
			}
			else
			{
				parent::ConfirmDelete($id);
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Activate_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$result = $this->app->get_model_object()->Activate($id);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Status adresu IP został pomyślnie zmieniony.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Status adresu IP nie został zmieniony.');

			header('Location: index.php?route='.MODULE_NAME);
			exit;
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}
}

?>
