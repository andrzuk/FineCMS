<?php

class Users_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Użytkownicy',
			));	

		$columns = array(
			array('db_name' => 'id',            'column_name' => 'Id',          'sorting' => 1),
			array('db_name' => 'user_login',    'column_name' => 'Login',       'sorting' => 1),
			array('db_name' => 'user_password', 'column_name' => 'Hasło',       'sorting' => 1),
			array('db_name' => 'user_name',     'column_name' => 'Imię',        'sorting' => 1),
			array('db_name' => 'user_surname',  'column_name' => 'Nazwisko',    'sorting' => 1),
			array('db_name' => 'email',         'column_name' => 'E-mail',      'sorting' => 1),
			array('db_name' => 'status',        'column_name' => 'Status',      'sorting' => 1),
			array('db_name' => 'registered',    'column_name' => 'Rejestracja', 'sorting' => 1),
			array('db_name' => 'logged_in',     'column_name' => 'Logowanie',   'sorting' => 1),
			array('db_name' => 'modified',      'column_name' => 'Modyfikacja', 'sorting' => 1),
			array('db_name' => 'logged_out',    'column_name' => 'Wylogowanie', 'sorting' => 1),
			array('db_name' => 'active',        'column_name' => 'Aktywny',     'sorting' => 1),
		);

		$this->required = array('user_login', 'user_name', 'user_surname', 'email');

		parent::init($columns);

		$layout = $this->app->get_settings()->get_config_key('page_template_admin');

		$this->app->get_page()->set_layout($layout);
	}

	public function Index_Action()
	{
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::Index_Action();

			$options = array(
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&action=add',
					'caption' => 'Nowy użytkownik',
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
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Add_Action();

			if (!empty($_POST))
			{
				$record = array(
					'user_login' => $_POST['user_login'],
					'user_password' => sha1($_POST['user_password']),
					'user_name' => $_POST['user_name'],
					'user_surname' => $_POST['user_surname'],
					'email' => $_POST['email'],
					'status' => $_POST['status'],
					'registered' => date("Y-m-d H:i:s"),
					'active' => $_POST['active'],
					);

				if (isset($_POST['save_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Konto użytkownika zostało pomyślnie utworzone.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Konto użytkownika nie zostało utworzone.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id.'&check=true');
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Konto użytkownika zostało pomyślnie utworzone.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Konto użytkownika nie zostało utworzone.');

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
				
				$user = $this->app->get_user();

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, $user));
			}			
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Edit_Action()
	{
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::Edit_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			if ($this->app->get_user()->super_admin() == FALSE) // jeśli nie super-admin
			{
				if ($this->app->get_user()->get_value('user_id') != $id) // obce konto
				{
					parent::AccessDenied();
					return;
				}
			}
			
			if (!empty($_POST))
			{
				$record = array(
					'user_login' => $_POST['user_login'],
					'user_name' => $_POST['user_name'],
					'user_surname' => $_POST['user_surname'],
					'email' => $_POST['email'],
					'status' => $_POST['status'],
					'modified' => date("Y-m-d H:i:s"),
					'active' => $_POST['active'],
					);

				if (isset($_POST['save_button']))
				{
					$result = $this->app->get_model_object()->Save($id, $record);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Zmiany zostały pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Zmiany nie zostały zapisane.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id.'&check=true');
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$result = $this->app->get_model_object()->Save($id, $record);

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
						'link' => 'index.php?route='.MODULE_NAME.'&action=setpass&id='.$id,
						'caption' => 'Zmień hasło',
						'icon' => 'img/access.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=view&id='.$id,
						'caption' => 'Szczegóły użytkownika',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń użytkownika',
						'icon' => 'img/trash.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$data = $this->app->get_model_object()->GetOne($id);

				$user = $this->app->get_user();

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, $user));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Delete_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Delete_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			if ($this->app->get_user()->super_admin() == FALSE) // jeśli nie super-admin
			{
				if ($id == 1) // konto superadmina - nie usuwamy
				{
					parent::AccessDenied();
					return;
				}
			}

			if (isset($_GET['confirm']))
			{
				$result = $this->app->get_model_object()->Delete($id);

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Konto użytkownika zostało pomyślnie usunięte.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Konto użytkownika nie zostało usunięte.');

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
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::View_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			if ($this->app->get_user()->get_value('user_status') == USER)
			{
				if ($this->app->get_user()->get_value('user_id') != $id) // obce konto
				{
					parent::AccessDenied();
					return;
				}
			}
			
			if (isset($_POST['cancel_button']))
			{
				header('Location: index.php?route='.MODULE_NAME);
				exit;
			}
			else // wczytany formularz
			{
				$options = array(
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=setpass&id='.$id,
						'caption' => 'Zmień hasło',
						'icon' => 'img/access.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=edit&id='.$id,
						'caption' => 'Edytuj użytkownika',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń użytkownika',
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

	public function Setpass_Action()
	{
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			if ($this->app->get_user()->super_admin() == FALSE) // jeśli nie super-admin
			{
				if ($this->app->get_user()->get_value('user_id') != $id) // obce konto
				{
					parent::AccessDenied();
					return;
				}
			}
			
			if (!empty($_POST))
			{
				$record = array(
					'user_password' => sha1($_POST['user_password']),
					'user_password_repeat' => sha1($_POST['user_password_repeat']),
					'modified' => date("Y-m-d H:i:s"),
					);

				if (isset($_POST['save_button']))
				{
					$result = $this->app->get_model_object()->SetPassword($id, $record);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Hasło zostało pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Hasło nie zostało zapisane.');

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
						'caption' => 'Szczegóły użytkownika',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=edit&id='.$id,
						'caption' => 'Edytuj użytkownika',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń użytkownika',
						'icon' => 'img/trash.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowPassword($id));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}
}

?>
