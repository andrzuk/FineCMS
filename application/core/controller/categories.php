<?php

class Categories_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Kategorie',
			));	
		
		$columns = array(
			array('db_name' => 'id',         'column_name' => 'Id',          'sorting' => 1),
			array('db_name' => 'parent_id',  'column_name' => 'Rodzic',      'sorting' => 1),
			array('db_name' => 'section',    'column_name' => 'Sekcja',      'sorting' => 1),
			array('db_name' => 'permission', 'column_name' => 'Dostęp',      'sorting' => 1),
			array('db_name' => 'item_order', 'column_name' => 'Kolejność',   'sorting' => 1),
			array('db_name' => 'caption',    'column_name' => 'Tytuł',       'sorting' => 1),
			array('db_name' => 'link',       'column_name' => 'Adres',       'sorting' => 1),
			array('db_name' => 'page_id',    'column_name' => 'Strona',      'sorting' => 1),
			array('db_name' => 'visible',    'column_name' => 'Aktywna',     'sorting' => 1),
			array('db_name' => 'target',     'column_name' => 'Target',      'sorting' => 1),
			array('db_name' => 'user_login', 'column_name' => 'Autor',       'sorting' => 1),
			array('db_name' => 'modified',   'column_name' => 'Modyfikacja', 'sorting' => 1),
		);

		$this->required = array('caption', 'link');

		parent::init($columns);

		$layout = $this->app->get_settings()->get_config_key('page_template_admin');

		$this->app->get_page()->set_layout($layout);
	}

	public function Index_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Index_Action();

			if (isset($_GET['mode'])) $_SESSION['categories_list_mode'] = $_GET['mode'];

			$options = array(
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&action=add',
					'caption' => 'Nowa kategoria',
					'icon' => 'img/files.png',
					),
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&mode=1',
					'caption' => 'Nawigacja górna',
					'icon' => 'img/top_menu.png',
					),
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&mode=2',
					'caption' => 'Nawigacja boczna',
					'icon' => 'img/left_menu.png',
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
					'parent_id' => $_POST['parent_id'],
					'section' => $_POST['section'],
					'page_id' => 0,
					'item_order' => 0,
					'caption' => $_POST['caption'],
					'link' => $_POST['link'],
					'permission' => $_POST['permission'],
					'visible' => $_POST['active'],
					'target' => isset($_POST['target']) ? 1 : 0,
					'author_id' => $this->app->get_user()->get_value('user_id'),
					'modified' => date("Y-m-d H:i:s"),
					);

				if (isset($_POST['save_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Kategoria została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Kategoria nie została utworzona.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id.'&check=true');
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$id = $this->app->get_model_object()->Add($record);

					if ($id) $this->app->get_page()->set_message(MSG_INFORMATION, 'Kategoria została pomyślnie utworzona.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Kategoria nie została utworzona.');

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

				$import = $this->app->get_model_object()->GetCategories();

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, $import));
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
					'parent_id' => $_POST['parent_id'],
					'section' => $_POST['section'],
					'page_id' => 0,
					'item_order' => 0,
					'caption' => $_POST['caption'],
					'link' => $_POST['link'],
					'permission' => $_POST['permission'],
					'visible' => $_POST['active'],
					'target' => isset($_POST['target']) ? 1 : 0,
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
				$page_id = $this->app->get_model_object()->GetPageId($id);

				$options = array(
					array(
						'link' => 'index.php?route=pages&action=edit&id='.$page_id,
						'caption' => 'Edytuj stronę',
						'icon' => 'img/category.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=view&id='.$id,
						'caption' => 'Szczegóły kategorii',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń kategorię',
						'icon' => 'img/trash.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME,
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				if (!$page_id) unset($options[0]);

				$data = $this->app->get_model_object()->GetOne($id);

				$import = $this->app->get_model_object()->GetCategories();

				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data, $import));
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

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Kategoria została pomyślnie usunięta.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Kategoria nie została usunięta.');

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
						'caption' => 'Edytuj kategorię',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń kategorię',
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

	public function Up_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Up_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$record = array(
				'author_id' => $this->app->get_user()->get_value('user_id'),
				'modified' => date("Y-m-d H:i:s"),
				);

			$result = $this->app->get_model_object()->MoveUp($id, $record);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Kolejność została pomyślnie zmieniona.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Kolejność nie została zmieniona.');

			header('Location: index.php?route='.MODULE_NAME);
			exit;
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Down_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			parent::Down_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

			$record = array(
				'author_id' => $this->app->get_user()->get_value('user_id'),
				'modified' => date("Y-m-d H:i:s"),
				);

			$result = $this->app->get_model_object()->MoveDown($id, $record);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Kolejność została pomyślnie zmieniona.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Kolejność nie została zmieniona.');

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
