<?php

class Password_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowPasswordForm()
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Reset hasła';
		$form_image = 'img/32x32/report_key.png';
		$form_width = '300px';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=check';

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => 'Login', 
				'data' => array(
					'type' => 'text', 'id' => 'login', 'name' => 'login', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'E-mail', 
				'data' => array(
					'type' => 'email', 'id' => 'email', 'name' => 'email', 'value' => NULL, 'required' => 'required',
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array();
			
		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'submit', 'id' => 'submit', 'name' => 'submit', 'value' => 'Zresetuj',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
}

?>
