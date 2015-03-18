<?php

class Roles_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Access Control List',
			));	
		
		$columns = array(
			array('db_name' => 'id',          'column_name' => 'Id',               'sorting' => 1),
			array('db_name' => 'user_login',  'column_name' => 'Login',            'sorting' => 1),
			array('db_name' => 'user_name',   'column_name' => 'Imię i nazwisko',  'sorting' => 1),
			array('db_name' => 'status',      'column_name' => 'Profil',           'sorting' => 1),
			array('db_name' => 'function',    'column_name' => 'Dostępne funkcje', 'sorting' => 1),
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
					'link' => 'index.php?route='.MODULE_NAME.'&action=add',
					'caption' => 'Nowa rola',
					'icon' => 'img/files.png',
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

	public function Add_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::Add_Action();

			if (!empty($_POST))
			{
				$records = array();

				$functions = $this->app->get_model_object()->GetFunctions(NULL);

				$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : NULL;

				foreach ($functions as $i => $j)
				{
					foreach ($j as $key => $value)
					{
						if ($key == 'function_id') $function_id = $value;
					}
					$record = array(
						'user_id' => $user_id,
						'function_id' => $function_id,
						'access' => isset($_POST['function_'.$function_id]) ? 1 : 0,
						);
					$records[] = $record;
				}

				if (isset($_POST['save_button']))
				{
					$id = $this->app->get_model_object()->Add($records);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Rola użytkownika została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Rola użytkownika nie została utworzona.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$user_id);
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$id = $this->app->get_model_object()->Add($records);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Rola użytkownika została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Rola użytkownika nie została utworzona.');

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

				$users = $this->app->get_model_object()->GetNewUsers();
				$functions = $this->app->get_model_object()->GetFunctions(NULL);

				$data = array('users' => $users, 'functions' => $functions);

				if (count($users)) // są userzy bez przypisanej roli
				{
					$this->app->get_page()->set_options($options);

					$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm(NULL, $data));
				}
				else // nie ma userów bez przypisanej roli
				{
					header('Location: index.php?route='.MODULE_NAME);
					exit;
				}
			}			
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Edit_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::Edit_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			if (!empty($_POST))
			{
				$records = array();

				$functions = $this->app->get_model_object()->GetFunctions(NULL);

				$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : NULL;

				foreach ($functions as $i => $j)
				{
					foreach ($j as $key => $value)
					{
						if ($key == 'function_id') $function_id = $value;
					}
					$record = array(
						'user_id' => $user_id,
						'function_id' => $function_id,
						'access' => isset($_POST['function_'.$function_id]) ? 1 : 0,
						);
					$records[] = $record;
				}

				if (isset($_POST['save_button']))
				{
					$result = $this->app->get_model_object()->Save($id, $records);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Zmiany zostały pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Zmiany nie zostały zapisane.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id);
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$result = $this->app->get_model_object()->Save($id, $records);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Zmiany zostały pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Zmiany nie zostały zapisane.');

					header('Location: index.php?route='.MODULE_NAME);
					exit;
				}
				else // button Anuluj
				{
					header('Location: index.php?route='.MODULE_NAME);
					exit;
				}
			}
			else // wczytany formularz
			{
				$options = array(
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=view&id='.$id,
						'caption' => 'Szczegóły roli',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń rolę',
						'icon' => 'img/trash.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$users = $this->app->get_model_object()->GetAllUsers();
				$functions = $this->app->get_model_object()->GetFunctions($id);

				$data = array('users' => $users, 'functions' => $functions);

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($id, $data));
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

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Rola użytkownika została pomyślnie usunięta.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Rola użytkownika nie została usunięta.');

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
						'link' => 'index.php?route='.MODULE_NAME.'&action=edit&id='.$id,
						'caption' => 'Edytuj rolę',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń rolę',
						'icon' => 'img/trash.png',
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
}

?>
