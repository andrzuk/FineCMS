<?php

class Notes_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Notatki',
			));	
		
		$columns = array(
			array('db_name' => 'id',         'column_name' => 'Id',          'sorting' => 1),
			array('db_name' => 'title',      'column_name' => 'Tytuł',       'sorting' => 1),
			array('db_name' => 'contents',   'column_name' => 'Treść',       'sorting' => 1),
			array('db_name' => 'user_login', 'column_name' => 'Autor',       'sorting' => 1),
			array('db_name' => 'modified',   'column_name' => 'Modyfikacja', 'sorting' => 1),
		);

		$this->required = array('title', 'contents');

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
					'caption' => 'Nowa notatka',
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
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::Add_Action();

			if (!empty($_POST))
			{
				$record = array(
					'title' => $_POST['title'],
					'contents' => $_POST['contents'],
					'author_id' => $this->app->get_user()->get_value('user_id'),
					'modified' => date("Y-m-d H:i:s"),
					);

				if (isset($_POST['save_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Notatka została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Notatka nie została utworzona.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id.'&check=true');
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Notatka została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Notatka nie została utworzona.');

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

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm(NULL, NULL, NULL));
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
			
			if (!empty($_POST))
			{
				$record = array(
					'title' => $_POST['title'],
					'contents' => $_POST['contents'],
					'author_id' => $this->app->get_user()->get_value('user_id'),
					'modified' => date("Y-m-d H:i:s"),
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
						'link' => 'index.php?route='.MODULE_NAME.'&action=view&id='.$id,
						'caption' => 'Szczegóły notatki',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń notatkę',
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

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, NULL, NULL));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Delete_Action()
	{
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::Delete_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			if (isset($_GET['confirm']))
			{
				$result = $this->app->get_model_object()->Delete($id);

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Notatka została pomyślnie usunięta.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Notatka nie została usunięta.');

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
						'caption' => 'Edytuj notatkę',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń notatkę',
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
