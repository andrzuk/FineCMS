<?php

class Install_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowInstallForm()
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Ustawienia serwisu';
		$form_image = 'img/32x32/options.png';
		$form_width = '600px';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=receive';

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => 'Krótka nazwa serwisu', 
				'data' => array(
					'type' => 'text', 'id' => 'short_title', 'name' => 'short_title', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Tytuł serwisu', 
				'data' => array(
					'type' => 'text', 'id' => 'main_title', 'name' => 'main_title', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Opis serwisu', 
				'data' => array(
					'type' => 'textarea', 'id' => 'main_description', 'name' => 'main_description', 'rows' => 3, 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Słowa kluczowe serwisu', 
				'data' => array(
					'type' => 'textarea', 'id' => 'main_keywords', 'name' => 'main_keywords', 'rows' => 2, 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Domena serwisu', 
				'data' => array(
					'type' => 'text', 'id' => 'base_domain', 'name' => 'base_domain', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Konto e-mail nadawcze', 
				'data' => array(
					'type' => 'email', 'id' => 'email_sender_address', 'name' => 'email_sender_address', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Konto e-mail administratora', 
				'data' => array(
					'type' => 'email', 'id' => 'email_admin_address', 'name' => 'email_admin_address', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Login administratora', 
				'data' => array(
					'type' => 'text', 'id' => 'admin_login', 'name' => 'admin_login', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Hasło administratora', 
				'data' => array(
					'type' => 'password', 'id' => 'admin_password', 'name' => 'admin_password', 'value' => NULL, 'required' => 'required',
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array(
			array(
				'type' => 'hidden', 'id' => 'save_settings', 'name' => 'save_settings', 'value' => TRUE,
				),
			);
			
		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'submit', 'id' => 'install', 'name' => 'install', 'value' => 'Zapisz', 'onclick' => 'disable(this); submit();',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
}

?>
