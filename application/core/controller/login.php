<?php

class Login_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=login' => 'Logowanie',
			));
	}

	public function Index_Action()
	{
		if ($this->app->get_user()->get_value('user_status')) // zalogowany
		{
			header('Location: index.php?route=admin');
			exit;
		}
		else // nie zalogowany
		{
			$this->app->get_page()->set_content($this->app->get_view_object()->ShowLoginForm());

			$layout = $this->app->get_settings()->get_config_key('page_template_default');

			$this->app->get_page()->set_layout($layout);

			$this->app->get_page()->set_template('login');
		}
	}

	public function Check_Action()
	{
		$data = $this->app->get_model_object()->Authenticate($_POST['login'], $_POST['password']);

		if ($data) // logowanie poprawne
		{
			$_SESSION['user_id'] = $data['id'];
			$_SESSION['user_status'] = $data['status'];
			$_SESSION['user_name'] = $data['user_name'];
			$_SESSION['user_surname'] = $data['user_surname'];
			$_SESSION['user_login'] = $data['user_login'];
			$_SESSION['user_email'] = $data['email'];

			$this->app->get_page()->set_message(MSG_INFORMATION, 'Zostałeś pomyślnie zalogowany do serwisu.');
			
			header('Location: index.php?route=admin');
			exit;
		}
		else // logowanie nieudane
		{
			$this->app->get_page()->set_message(MSG_ERROR, 'Login lub e-mail lub hasło są nieprawidłowe lub też konto zostało zablokowane bądź usunięte.');

			header('Location: index.php?route=login');
			exit;
		}
	}
}

?>
