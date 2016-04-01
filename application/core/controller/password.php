<?php

class Password_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=password' => 'Reset hasła',
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
			$this->app->get_page()->set_content($this->app->get_view_object()->ShowPasswordForm());

			$layout = $this->app->get_settings()->get_config_key('page_template_default');

			$this->app->get_page()->set_layout($layout);

			$this->app->get_page()->set_template('password');
		}
	}

	public function Check_Action()
	{
		$data = $this->app->get_model_object()->Reset($_POST['login'], $_POST['email']);

		if ($data) // dane poprawne
		{
			$message_options = array(
				'base_domain' => $this->app->get_settings()->get_config_key('base_domain'),
				'email_host' => $this->app->get_settings()->get_config_key('email_host'),
				'email_port' => $this->app->get_settings()->get_config_key('email_port'),
				'email_password' => $this->app->get_settings()->get_config_key('email_password'),
				'email_sender_name' => $this->app->get_settings()->get_config_key('email_sender_name'),
				'email_sender_address' => $this->app->get_settings()->get_config_key('email_sender_address'),
				'email_remindpwd_subject' => $this->app->get_settings()->get_config_key('email_remindpwd_subject'),
				'email_remindpwd_body_1' => $this->app->get_settings()->get_config_key('email_remindpwd_body_1'),
				'email_remindpwd_body_2' => $this->app->get_settings()->get_config_key('email_remindpwd_body_2'),
				);

			$this->app->get_model_object()->Send($data, $message_options);
	
			$this->app->get_page()->set_message(MSG_INFORMATION, 'Hasło zostało pomyślnie zresetowane. Nowe hasło zostało wysłane na podany adres e-mail.');
			
			header('Location: index.php?route=login');
			exit;
		}
		else // dane niepoprawne
		{
			$this->app->get_page()->set_message(MSG_ERROR, 'Login lub e-mail są nieprawidłowe lub też konto zostało zablokowane bądź usunięte.');

			header('Location: index.php?route=password');
			exit;
		}
	}
}

?>
