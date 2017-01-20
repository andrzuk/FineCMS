<?php

class Register_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route='.MODULE_NAME => 'Rejestracja',
			));
		
		$this->required = array('login', 'name', 'surname', 'email', 'password');
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
			$this->app->get_page()->set_content($this->app->get_view_object()->ShowRegisterForm());

			$layout = $this->app->get_settings()->get_config_key('page_template_default');

			$this->app->get_page()->set_layout($layout);

			$this->app->get_page()->set_template('register');
		}
	}

	public function Check_Action()
	{
		parent::Add_Action();

		$data = $this->app->get_model_object()->Register($_POST['name'], $_POST['surname'], $_POST['login'], $_POST['email'], $_POST['password']);

		if ($data) // rejestracja poprawna
		{
			$_SESSION['user_id'] = $data['id'];
			$_SESSION['user_status'] = $data['status'];
			$_SESSION['user_name'] = $data['user_name'];
			$_SESSION['user_surname'] = $data['user_surname'];
			$_SESSION['user_login'] = $data['user_login'];
			$_SESSION['user_email'] = $data['email'];

			$this->app->get_page()->set_message(MSG_INFORMATION, 'Zostałeś pomyślnie zarejestrowany w serwisie.');
			
			header('Location: index.php?route=admin');
			exit;
		}
		else // rejestracja nieudana
		{
			$this->app->get_page()->set_message(MSG_ERROR, 'Podany login lub e-mail już istnieje. Podaj inny lub się zaloguj.');

			header('Location: index.php?route=register');
			exit;
		}
	}
}

?>
