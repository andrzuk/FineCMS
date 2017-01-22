<?php

class Comments_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Komentarze',
			));	
		
		$columns = array(
			array('db_name' => 'id',              'column_name' => 'Id',                'sorting' => 1),
			array('db_name' => 'user_login',      'column_name' => 'Autor',             'sorting' => 1),
			array('db_name' => 'ip',              'column_name' => 'Adres IP',          'sorting' => 1),
			array('db_name' => 'title',           'column_name' => 'Artykuł',           'sorting' => 1),
			array('db_name' => 'comment_content', 'column_name' => 'Treść komentarza',  'sorting' => 1),
			array('db_name' => 'visible',         'column_name' => 'Widoczna',          'sorting' => 1),
			array('db_name' => 'send_date',       'column_name' => 'Wysłano',           'sorting' => 1),
		);

		parent::init($columns);

		$layout = $this->app->get_settings()->get_config_key('page_template_admin');

		$this->app->get_page()->set_layout($layout);
	}

	public function Index_Action()
	{
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::Index_Action();

			if (isset($_GET['mode'])) $_SESSION['comments_list_mode'] = $_GET['mode'];

			$options = array(
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&mode=0',
					'caption' => 'Komentarze nadesłane',
					'icon' => 'img/category.png',
					),
				array(
					'link' => 'index.php?route='.MODULE_NAME.'&mode=1',
					'caption' => 'Komentarze zatwierdzone',
					'icon' => 'img/checked.png',
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

	public function Edit_Action()
	{
		if ($this->app->get_acl()->allowed(USER)) // są uprawnienia
		{
			parent::Edit_Action();

			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			if ($this->app->get_user()->super_admin() == FALSE) // jeśli nie super-admin
			{
				if ($this->app->get_user()->get_value('user_id') != $this->app->get_model_object()->GetAuthorId($id)) // obcy komentarz
				{
					parent::AccessDenied();
					return;
				}
			}

			if (!empty($_POST))
			{
				$record = array(
					'comment_content' => $_POST['comment_content'],
					);

				if (isset($_POST['save_button']))
				{
					$result = $this->app->get_model_object()->Update($id, $record);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Zmiany zostały pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Zmiany nie zostały zapisane.');

					header('Location: index.php?route='.MODULE_NAME.'&action=edit&id='.$id.'&check=true');
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$result = $this->app->get_model_object()->Update($id, $record);

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
						'link' => 'index.php?route='.MODULE_NAME.'&action=confirm&id='.$id,
						'caption' => 'Zatwierdź komentarz',
						'icon' => 'img/accept.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=disable&id='.$id,
						'caption' => 'Zablokuj komentarz',
						'icon' => 'img/remove.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=view&id='.$id,
						'caption' => 'Podgląd komentarza',
						'icon' => 'img/info.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń komentarz',
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

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Confirm_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			$record = array(
				'visible' => 1,
				);

			$result = $this->app->get_model_object()->Save($id, $record);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Status komentarza został pomyślnie zapisany.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Status komentarza nie został zapisany.');

			header('Location: index.php?route='.MODULE_NAME);
			exit;
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Disable_Action()
	{
		if ($this->app->get_acl()->allowed(OPERATOR)) // są uprawnienia
		{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			$record = array(
				'visible' => 0,
				);

			$result = $this->app->get_model_object()->Save($id, $record);

			if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Status komentarza został pomyślnie zapisany.');
			else $this->app->get_page()->set_message(MSG_ERROR, 'Status komentarza nie został zapisany.');

			header('Location: index.php?route='.MODULE_NAME);
			exit;
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

			if ($this->app->get_user()->super_admin() == FALSE) // jeśli nie super-admin
			{
				if ($this->app->get_user()->get_value('user_id') != $this->app->get_model_object()->GetAuthorId($id)) // obcy komentarz
				{
					parent::AccessDenied();
					return;
				}
			}

			if (isset($_GET['confirm']))
			{
				$result = $this->app->get_model_object()->Delete($id);

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Komentarz został pomyślnie usunięty.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Komentarz nie został usunięty.');

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
						'link' => 'index.php?route='.MODULE_NAME.'&action=confirm&id='.$id,
						'caption' => 'Zatwierdź komentarz',
						'icon' => 'img/accept.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=disable&id='.$id,
						'caption' => 'Zablokuj komentarz',
						'icon' => 'img/remove.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=edit&id='.$id,
						'caption' => 'Edytuj komentarz',
						'icon' => 'img/edit.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=delete&id='.$id,
						'caption' => 'Usuń komentarz',
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
