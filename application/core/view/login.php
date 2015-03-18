<?php

class Login_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowLoginForm()
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Logowanie do serwisu';
		$form_image = 'img/32x32/login.png';
		$form_width = '300px';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=check';

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => 'Login lub e-mail', 
				'data' => array(
					'type' => 'text', 'id' => 'login', 'name' => 'login', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Hasło', 
				'data' => array(
					'type' => 'password', 'id' => 'password', 'name' => 'password', 'value' => NULL, 'required' => 'required',
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array();
			
		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'submit', 'id' => 'submit', 'name' => 'submit', 'value' => 'Zaloguj',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
}

?>
