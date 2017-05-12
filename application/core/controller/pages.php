<?php

class Pages_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Strony',
			));	
		
		$columns = array(
			array('db_name' => 'id',          'column_name' => 'Id',          'sorting' => 1),
			array('db_name' => 'main_page',   'column_name' => 'Główna',      'sorting' => 1),
			array('db_name' => 'system_page', 'column_name' => 'Systemowa',   'sorting' => 1),
			array('db_name' => 'caption',     'column_name' => 'Kategoria',   'sorting' => 1),
			array('db_name' => 'title',       'column_name' => 'Tytuł',       'sorting' => 1),
			array('db_name' => 'contents',    'column_name' => 'Treść',       'sorting' => 1),
			array('db_name' => 'description', 'column_name' => 'Opis',        'sorting' => 1),
			array('db_name' => 'previews',    'column_name' => 'Odsłon',      'sorting' => 1),
			array('db_name' => 'user_login',  'column_name' => 'Autor',       'sorting' => 1),
			array('db_name' => 'visible',     'column_name' => 'Aktywna',     'sorting' => 1),
			array('db_name' => 'modified',    'column_name' => 'Modyfikacja', 'sorting' => 1),
		);

		$this->required = array('title', 'contents', 'description');

		parent::init($columns);

		$layout = $this->app->get_settings()->get_config_key('page_template_admin');

		$this->app->get_page()->set_layout($layout);
	}

	public function Index_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Index_Action();

			$options = array(
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&action=add',
					'caption' => 'Nowa strona',
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
					'main_page' => 0,
					'system_page' => 0,
					'category_id' => $_POST['category_id'],
					'title' => $_POST['title'],
					'contents' => $_POST['contents'],
					'description' => $_POST['description'],
					'author_id' => $this->app->get_user()->get_value('user_id'),
					'visible' => $_POST['visible'],
					'modified' => date("Y-m-d H:i:s"),
					);

				if (isset($_POST['save_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Strona została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Strona nie została utworzona.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id.'&check=true');
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Strona została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Strona nie została utworzona.');

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
						'link' => 'index.php?route=images&action=gallery',
						'caption' => 'Wstaw obrazek',
						'icon' => 'img/link.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$_SESSION['last_url'] = parent::clean_url($_SERVER['REQUEST_URI']);

				$data = NULL;

				$import = $this->app->get_model_object()->GetCategories();

				$image = isset($_GET['image']) ? $_GET['image'] : NULL;

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, $import, $image));
			}			
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Edit_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Edit_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			if (!empty($_POST))
			{
				$record = array(
					'main_page' => 0,
					'system_page' => 0,
					'category_id' => $_POST['category_id'],
					'title' => $_POST['title'],
					'contents' => $_POST['contents'],
					'description' => $_POST['description'],
					'author_id' => $this->app->get_user()->get_value('user_id'),
					'visible' => $_POST['visible'],
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
				$category_id = $this->app->get_model_object()->GetCategoryId($id);

				$options = array(
					array(
						'link' => 'index.php?route=images&action=gallery',
						'caption' => 'Wstaw obrazek',
						'icon' => 'img/link.png',
						),
					array(
						'link' => 'index.php?route=categories&action=edit&id='.$category_id,
						'caption' => 'Edytuj kategorię',
						'icon' => 'img/category.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=view&id='.$id,
						'caption' => 'Szczegóły strony',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=archive&id='.$id,
						'caption' => 'Archiwizuj stronę',
						'icon' => 'img/archive.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=restore&id='.$id,
						'caption' => 'Przywróć stronę',
						'icon' => 'img/archives.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń stronę',
						'icon' => 'img/trash.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$_SESSION['last_url'] = parent::clean_url($_SERVER['REQUEST_URI']);

				if (!$category_id) unset($options[0]);

				$data = $this->app->get_model_object()->GetOne($id);

				$import = $this->app->get_model_object()->GetCategories();

				$image = isset($_GET['image']) ? $_GET['image'] : NULL;

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, $import, $image));
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

			if (isset($_GET['confirm']))
			{
				$result = $this->app->get_model_object()->Delete($id);

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Strona została pomyślnie usunięta.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Strona nie została usunięta.');

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
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
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
						'caption' => 'Edytuj stronę',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=archive&id='.$id,
						'caption' => 'Archiwizuj stronę',
						'icon' => 'img/archive.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=restore&id='.$id,
						'caption' => 'Przywróć stronę',
						'icon' => 'img/archives.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń stronę',
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

	public function Archive_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$result = $this->app->get_model_object()->Archive($id);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Strona została pomyślnie zapisana w archiwum.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Strona nie została zapisana w archiwum.');

			header('Location: index.php?route='.MODULE_NAME);
			exit;
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Restore_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			if (!empty($_POST))
			{
				$record = array(
					'archive_id' => isset($_POST['archives']) ? $_POST['archives'] : NULL,
					);

				if (isset($_POST['restore_button']))
				{
					$result = $this->app->get_model_object()->Restore($id, $record);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Strona została pomyślnie przywrócona z archiwum.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Strona nie została przywrócona z archiwum.');

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
						'caption' => 'Szczegóły strony',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=edit&id='.$id,
						'caption' => 'Edytuj stronę',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń stronę',
						'icon' => 'img/trash.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$data = $this->app->get_model_object()->GetOne($id);
				
				$title = $data['title'];
				
				$data = $this->app->get_model_object()->GetArchives($id);

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowArchives($id, $title, $data));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Preview_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$data = $this->app->get_model_object()->Preview($id);

			$data['skip_bar_visible'] = $this->app->get_settings()->get_config_key('skip_bar_visible') == 'true';
			$data['skip_bar'] = $this->app->get_menu()->GetSkipBar($data['category_id']);
			$data['social_buttons_visible'] = $this->app->get_settings()->get_config_key('social_buttons_visible') == 'true';
			$data['social_buttons'] = $this->app->get_settings()->get_config_key('social_buttons');

			$this->app->get_page()->set_content($this->app->get_view_object()->ShowPage($data));
			
			$layout = $this->app->get_settings()->get_config_key('page_template_extended');
			$this->app->get_page()->set_layout($layout);
			$this->app->get_page()->set_template('index');
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}
}

?>
