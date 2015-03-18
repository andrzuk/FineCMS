<?php

class Contact_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowContactForm()
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Twoja wiadomość';
		$form_image = 'img/32x32/mail_yellow.png';
		$form_width = '100%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=receive';

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => 'Imię lub nick', 
				'data' => array(
					'type' => 'text', 'id' => 'login', 'name' => 'login', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Adres e-mail', 
				'data' => array(
					'type' => 'email', 'id' => 'email', 'name' => 'email', 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => 'Treść', 
				'data' => array(
					'type' => 'textarea', 'id' => 'contents', 'name' => 'contents', 'rows' => 5, 'value' => NULL, 'required' => 'required',
					),
				),
			array(
				'caption' => NULL, 
				'data' => array(
					'type' => 'checkbox', 'id' => 'sendme', 'name' => 'sendme', 'label' => 'Przyślij kopię tej wiadomości na mój adres e-mail', 'checked' => 'checked', 'value' => NULL,
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array();
			
		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'submit', 'id' => 'submit', 'name' => 'submit', 'value' => 'Wyślij',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
}

?>
