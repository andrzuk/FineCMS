<?php

class Install_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=install' => 'Instalacja serwisu',
			));
	}

	public function Index_Action()
	{
		if (isset($_SESSION['install_mode'])) // instalacja rozpoczęta
		{
			$this->app->get_page()->set_content($this->app->get_view_object()->ShowInstallForm());

			$this->app->get_page()->set_layout('default');

			$this->app->get_page()->set_template('install');
		}
		else // instalacja zakończona
		{
			unset($_SESSION['install_mode']);
			
			$this->app->get_page()->set_dialog(
				MSG_WARNING, 
				'Instalacja serwisu', 
				'Serwis został już zainstalowany. Aby dokonać ponownej instalacji, należy skopiować plik <b>script.php</b> do katalogu <b>install</b>.',
				array(
					array('link' => 'index.php', 'caption' => 'Anuluj', 'onclick' => NULL),
					)
				);

			$this->app->get_page()->set_content($this->app->get_view_object()->ShowDialog());
		}
	}

	public function Receive_Action()
	{
		if (!empty($_POST))
		{
			$domain_prefix = 'http://';
			$domain_suffix = '/';

			$base_domain = $_POST['base_domain'];

			if (stristr($base_domain, $domain_prefix) === FALSE)
			{
				$base_domain = $domain_prefix . $base_domain;
			}
			if (substr($base_domain, strlen($base_domain) - 1, 1) != $domain_suffix)
			{
				$base_domain .= $domain_suffix;
			}

			$first_name = '(Imię)';
			$last_name = '(Nazwisko)';
			
			$save_time = date("Y-m-d H:i:s");

			$record = array(
				'short_title' => $_POST['short_title'],
				'main_title' => $_POST['main_title'],
				'main_description' => $_POST['main_description'],
				'main_keywords' => $_POST['main_keywords'],
				'base_domain' => $base_domain,
				'email_sender_address' => $_POST['email_sender_address'],
				'email_report_address' => $_POST['email_admin_address'], 
				'email_admin_address' => $_POST['email_admin_address'],
				'admin_login' => $_POST['admin_login'],
				'admin_password' => sha1($_POST['admin_password']),
				'first_name' => $first_name,
				'last_name' => $last_name,
				'save_time' => $save_time,
				);

			if (isset($_POST['save_settings']))
			{
				include INSTALL_SCRIPT;

				$id = $this->app->get_model_object()->Save($record, $sql_script);

				if ($id) 
				{
					// usuwa index i skrypt instalacji z dysku serwera:

					$delete_result_1 = unlink(INSTALL_INDEX);
					$delete_result_2 = unlink(INSTALL_SCRIPT);

					// czyści sesję:

					unset($_SESSION['install_mode']);
					
					$this->app->get_page()->set_message(MSG_INFORMATION, 'Serwis został pomyślnie zainstalowany.');
				}
				else
				{
					$this->app->get_page()->set_message(MSG_ERROR, 'Serwis nie został zainstalowany.');
				} 

				header('Location: index.php');
				exit;
			}
		}
	}
}

?>
