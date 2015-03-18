<?php

class Config_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowList($columns, $data)
	{
		$title = 'Konfiguracja serwisu';
		$image = 'img/32x32/options.png';

		$attribs = array(
			array('width' => '5%',  'align' => 'center', 'visible' => '1'),
			array('width' => '20%', 'align' => 'left',   'visible' => '1'),
			array('width' => '23%', 'align' => 'left',   'visible' => '1'),
			array('width' => '25%', 'align' => 'left',   'visible' => '1'),
			array('width' => '5%',  'align' => 'center', 'visible' => '1'),
			array('width' => '5%',  'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%',  'align' => 'center', 'visible' => '1'),
		);
		
		$actions = array(
			array('action' => 'view',   'icon' => 'info.png',  'title' => 'Podgląd'),
			array('action' => 'edit',   'icon' => 'edit.png',  'title' => 'Edytuj'),
			array('action' => 'delete', 'icon' => 'trash.png', 'title' => 'Usuń'),
		);
	
		include GENER_DIR . 'list.php';

		$list_object = new ListBuilder();

		$list_object->init($title, $image, $columns, $data, $this->get_list_params(), $attribs, $actions);

		$result = $list_object->build_list();

		return $result;
	}

	public function ShowForm($data)
	{
		if ($data) // edycja
		{
			foreach ($data as $key => $value) 
			{
				if ($key == 'id') $main_id = $value;
				if ($key == 'key_name') $main_key_name = $value;
				if ($key == 'key_value') $main_key_value = $value;
				if ($key == 'meaning') $main_meaning = $value;
				if ($key == 'field_type') $main_field_type = $value;
				if ($key == 'active') $main_active = $value;
				if ($key == 'modified') $main_modified = $value;
			}
		}
		else // nowa pozycja
		{
			$main_id = NULL;
			$main_key_name = NULL;
			$main_key_value = NULL;
			$main_meaning = NULL;
			$main_field_type = 1;
			$main_active = 1;
			$main_modified = NULL;
		}

		$sel = range(0, 4);
		$sel[$main_field_type] = 'selected';

		$chk = array(NULL, NULL);
		$chk[$main_active] = 'checked';

		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = $data ? 'Edycja klucza konfiguracji' : 'Nowy klucz konfiguracji';
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '50%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$action = $data ? 'edit&id=' . $main_id : 'add';

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=' . $action;

		$form_object->set_action($form_action);

		switch ($main_field_type)
		{
			case 1:
				$key_value_element = array(
					'caption' => 'Wartość klucza', 
					'data' => array(
						'type' => 'text', 'id' => 'key_value', 'name' => 'key_value', 'value' => $main_key_value, 'required' => 'required',
						),
					);
				break;
			case 2:
				$key_value_element = array(
					'caption' => 'Wartość klucza', 
					'data' => array(
						'type' => 'textarea', 'id' => 'key_value', 'name' => 'key_value', 'value' => $main_key_value, 'rows' => 4, 'required' => 'required',
						),
					);
				break;
			case 3:
				$set = array(NULL, NULL);
				$set_key_value = in_array($main_key_value, array('true', '1')) ? 1 : 0;
				$set[$set_key_value] = 'checked';

				$key_value_element = array(
					'caption' => 'Wartość klucza', 
					'data' => array(
						'type' => 'radio', 'name' => 'key_value', 
						'items' => array(
							array(
								'id' => 'setting_yes', 'label' => 'Aktywne (włączone)', $set[1] => $set[1], 'value' => 'true',
								),
							array(
								'id' => 'setting_no', 'label' => 'Nieaktywne (wyłączone)', $set[0] => $set[0], 'value' => 'false',
								),
							),
						),
					);
				break;
		}

		$form_inputs = array(
			array(
				'caption' => 'Nazwa klucza', 
				'data' => array(
					'type' => 'text', 'id' => 'key_name', 'name' => 'key_name', 'value' => $main_key_name, 'required' => 'required',
					),
				),
			$key_value_element,
			array(
				'caption' => 'Typ wartości', 
				'data' => array(
					'type' => 'select', 'id' => 'field_type', 'name' => 'field_type', 
					'option' => array(
						array(
							'value' => '1', 'caption' => 'string (pole tekstowe - krótkie)', $sel[1] => $sel[1],
							),
						array(
							'value' => '2', 'caption' => 'area (obszar opisowy - długi)', $sel[2] => $sel[2],
							),
						array(
							'value' => '3', 'caption' => 'option (wartość true - false)', $sel[3] => $sel[3],
							),
						), 
					),
				),
			array(
				'caption' => 'Znaczenie', 
				'data' => array(
					'type' => 'text', 'id' => 'meaning', 'name' => 'meaning', 'value' => $main_meaning,
					),
				),
			array(
				'caption' => 'Aktywny w serwisie', 
				'data' => array(
					'type' => 'radio', 'name' => 'active', 
					'items' => array(
						array(
							'id' => 'active_yes', 'label' => 'Tak', $chk[1] => $chk[1], 'value' => 1,
							),
						array(
							'id' => 'active_no', 'label' => 'Nie', $chk[0] => $chk[0], 'value' => 0,
							),
						),
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array();
			
		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Zapisz',
				),
			array(
				'type' => 'submit', 'id' => 'update_button', 'name' => 'update_button', 'value' => 'Zamknij',
				),
			array(
				'type' => 'cancel', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Anuluj',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}

	public function ShowDetails($data)
	{
		include GENER_DIR . 'view.php';

		$view_object = new ViewBuilder();

		$view_title = 'Szczegóły klucza konfiguracji';
		$view_image = 'img/32x32/list_information.png';
		$view_width = '50%';
		
		$view_object->init($view_title, $view_image, $view_width);

		$view_action = 'index.php?route=' . MODULE_NAME;

		$view_object->set_action($view_action);

		$view_inputs = array();

		if (is_array($data))
		{
			foreach ($data as $key => $value) 
			{
				$view_inputs[] = array('caption' => $key, 'value' => $value);
			}
		}
		
		$view_object->set_inputs($view_inputs);
		
		$view_buttons = array(
			array(
				'type' => 'cancel', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Zamknij',
				),
			);

		$view_object->set_buttons($view_buttons);

		$result = $view_object->build_view();

		return $result;
	}
}

?>
