<?php

class Template_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=admin' => 'Admin Panel',
			'index.php?route='.MODULE_NAME => 'Szablon',
			));	
		
		$this->required = array('contents');

		$layout = $this->app->get_settings()->get_config_key('page_template_admin');

		$this->app->get_page()->set_layout($layout);
	}

	public function Index_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			parent::Edit_Action();

			$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_SESSION['template_mode']) ? $_SESSION['template_mode'] : 1);

			$_SESSION['template_mode'] = $mode;

			switch ($mode)
			{
				case 1: 
					$layout_name = 'page_template_default'; break;
				case 2: 
					$layout_name = 'page_template_extended'; break;
				case 3: 
					$layout_name = 'page_template_admin'; break;
				default: 
					$layout_name = 'page_template_default'; break;
			}

			$layout = $this->app->get_settings()->get_config_key($layout_name);

			$file = TEMPL_DIR . 'pages/' . $layout . '.php';

			if (!empty($_POST))
			{
				$record = array(
					'filename' => $_POST['filename'],
					'contents' => $_POST['contents'],
					);

				if (isset($_POST['save_button']))
				{
					$result = $this->app->get_model_object()->Save($record);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Zmiany zostały pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Zmiany nie zostały zapisane.');

					header('Location: index.php?route='.MODULE_NAME);
					exit;
				}
				else if (isset($_POST['update_button']))
				{
					$result = $this->app->get_model_object()->Save($record);

					if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Zmiany zostały pomyślnie zapisane.');
					else $this->app->get_page()->set_message(MSG_ERROR, 'Zmiany nie zostały zapisane.');

					header('Location: index.php?route=admin');
					exit;
				}
				else // button Anuluj
				{
					header('Location: index.php?route=admin');
					exit;
				}
			}
			else // wczytany formularz
			{
				$options = array(
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&mode=1',
						'caption' => 'Domyślny',
						'icon' => 'img/checked.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&mode=2',
						'caption' => 'Rozszerzony',
						'icon' => 'img/archive.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&mode=3',
						'caption' => 'Administracyjny',
						'icon' => 'img/control_panel.png',
						),
					array(
						'link' => 'index.php?route='.MODULE_NAME.'&action=reset',
						'caption' => 'Resetuj',
						'icon' => 'img/files.png',
						),
					array(
						'link' => 'index.php?route=admin',
						'caption' => 'Zamknij',
						'icon' => 'img/stop.png',
						),
					);

				$data = $this->app->get_model_object()->GetContent($file);
				
				$this->app->get_page()->set_options($options);

				$this->app->get_page()->set_content($this->app->get_view_object()->ShowForm($data));
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}

	public function Reset_Action()
	{
		if ($this->app->get_acl()->allowed(ADMIN)) // są uprawnienia
		{
			switch ($_SESSION['template_mode'])
			{
				case 1: 
					$layout_name = 'page_template_default'; break;
				case 2: 
					$layout_name = 'page_template_extended'; break;
				case 3: 
					$layout_name = 'page_template_admin'; break;
				default: 
					$layout_name = 'page_template_default'; break;
			}

			$layout = $this->app->get_settings()->get_config_key($layout_name);

			$file = TEMPL_DIR . 'pages/' . $layout . '.php';

			if (isset($_GET['confirm']))
			{
				$result = $this->app->get_model_object()->Reset($file);

				if ($result) $this->app->get_page()->set_message(MSG_INFORMATION, 'Szablon został pomyślnie zresetowany.');
				else $this->app->get_page()->set_message(MSG_ERROR, 'Szablon nie został zresetowany.');

				header('Location: index.php?route='.MODULE_NAME);
				exit;
			}
			else
			{
				parent::ConfirmReset();
			}
		}
		else // brak uprawnień
		{
			parent::AccessDenied();
		}
	}
}

?>
