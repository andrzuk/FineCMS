<?php

class Contact_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=contact' => 'Kontakt z serwisem',
			));

		$this->required = array('login', 'email', 'contents');
	}

	public function Index_Action()
	{
		$data = $this->app->get_model_object()->GetIntro();

		$this->app->get_page()->set_content($data['contents'] . $this->app->get_view_object()->ShowContactForm());

		$layout = $this->app->get_settings()->get_config_key('page_template_default');

		$this->app->get_page()->set_layout($layout);

		$this->app->get_page()->set_template('contact');
	}

	public function Receive_Action()
	{
		parent::Add_Action();

		$record = array(
			'login' => isset($_POST['login']) ? $_POST['login'] : NULL,
			'email' => isset($_POST['email']) ? $_POST['email'] : NULL,
			'contents' => isset($_POST['contents']) ? $_POST['contents'] : NULL,
			);

		$message_options = array(
			'send_new_message_report' => $this->app->get_settings()->get_config_key('send_new_message_report'),
			'base_domain' => $this->app->get_settings()->get_config_key('base_domain'),
			'email_sender_name' => $this->app->get_settings()->get_config_key('email_sender_name'),
			'email_sender_address' => $this->app->get_settings()->get_config_key('email_sender_address'),
			'email_report_address' => $this->app->get_settings()->get_config_key('email_report_address'),
			'email_report_subject' => $this->app->get_settings()->get_config_key('email_report_subject'),
			'email_report_body_1' => $this->app->get_settings()->get_config_key('email_report_body_1'),
			'email_report_body_2' => $this->app->get_settings()->get_config_key('email_report_body_2'),
			);

		$sendme = isset($_POST['sendme']) ? TRUE : FALSE;
		
		$result = $this->app->get_model_object()->Receive($record, $sendme, $message_options);

		if ($result) // wysyłanie poprawne
		{
			$this->app->get_page()->set_message(MSG_INFORMATION, 'Twoja wiadomość została pomyślnie wysłana do serwisu.');
			
			header('Location: index.php?route=contact');
			exit;
		}
		else // wysyłanie nieudane
		{
			$this->app->get_page()->set_message(MSG_ERROR, 'Twoja wiadomość nie została wysłana.');

			header('Location: index.php?route=contact&check=true');
			exit;
		}
	}
}

?>
