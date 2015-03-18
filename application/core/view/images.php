<?php

class Images_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowList($columns, $data)
	{
		$title = 'Galeria serwisu';
		$image = 'img/32x32/picture.png';

		$attribs = array(
			array('width' => '5%',  'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1', 'image' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'left',   'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%',  'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '15%', 'align' => 'center', 'visible' => '1'),
		);
		
		$actions = array(
			array('action' => 'preview',  'icon' => 'picture.png', 'title' => 'Podgląd'),
			array('action' => 'view',     'icon' => 'info.png',    'title' => 'Szczegóły'),
			array('action' => 'edit',     'icon' => 'edit.png',    'title' => 'Edytuj'),
			array('action' => 'download', 'icon' => 'save.png',    'title' => 'Pobierz'),
			array('action' => 'delete',   'icon' => 'trash.png',   'title' => 'Usuń'),
		);
	
		foreach ($data as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'id') $id = $value;
				if ($key == 'file_size') $file_size = $value;
			}
			$data[$k]['preview'] = 	'<a href="index.php?route=images&action=preview&id=' . $id . '">' .
									'<img src="' . GALLERY_DIR . IMG_DIR . $id.'" class="ListImage" alt="ico" />' .
									'</a>';
			$data[$k]['file_name'] = str_replace(array("-", "_"), array(" - ", " _ "), $data[$k]['file_name']);
			$data[$k]['file_size'] = strval(intval($file_size / 1024) . ' KB');
		}

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
				if ($key == 'owner_id') $main_owner_id = $value;
				if ($key == 'file_name') $main_file_name = $value;
				if ($key == 'file_size') $main_file_size = $value;
				if ($key == 'picture_width') $main_picture_width = $value;
				if ($key == 'picture_height') $main_picture_height = $value;
				if ($key == 'modified') $main_modified = $value;
			}
		}
		else // nowa pozycja
		{
			$main_id = NULL;
			$main_owner_id = NULL;
			$main_file_name = NULL;
			$main_file_size = NULL;
			$main_picture_width = NULL;
			$main_picture_height = NULL;
			$main_modified = NULL;
		}

		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = $data ? 'Edycja obrazka' : 'Nowe obrazki';
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '50%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$action = $data ? 'edit&id=' . $main_id : 'add';

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=' . $action;

		$form_object->set_action($form_action);

		$form_object->set_enctype('multipart/form-data');

		$main_cell = NULL;
		$main_cell .= '<a href="index.php?route=images&action=preview&id=' . $main_id . '">';
		$main_cell .= '<img src="' . GALLERY_DIR . IMG_DIR . $main_id.'" alt="ico" width="100%" style="border: 1px solid #ccc; padding: 1px;" />';
		$main_cell .= '</a>';

		$multiple = $data ? NULL : 'multiple';
		$caption = $data ? 'Zmień obrazek' : 'Wybierz obrazki';
		$required = $data ? NULL : 'required';

		$form_inputs = array(
			array(
				'caption' => 'Podgląd', 
				'data' => array(
					'type' => 'label', 'value' => $main_cell,
					),
				),
			array(
				'caption' => 'Nazwa pliku', 
				'data' => array(
					'type' => 'text', 'id' => 'file_name', 'name' => 'file_name', 'value' => $main_file_name, 'required' => 'required',
					),
				),
			array(
				'caption' => $caption, 
				'data' => array(
					'type' => 'file', 'id' => 'upload_files', 'name' => 'upload_files[]', 'required' => $required, 'multiple' => $multiple,
					),
				),
			);

		if (!$data) { unset($form_inputs[0]); unset($form_inputs[1]); }

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array(
			array(
				'type' => 'hidden', 'id' => 'file_name', 'name' => 'file_name', 'value' => $main_file_name,
				),
			);

		if ($data) unset($form_hiddens[0]);
			
		$form_object->set_hiddens($form_hiddens);

		if ($data) // edycja
		{
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
		}
		else // nowa pozycja
		{
			$form_buttons = array(
				array(
					'type' => 'submit', 'id' => 'upload_button', 'name' => 'upload_button', 'value' => 'Wyślij',
					),
				array(
					'type' => 'cancel', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Anuluj',
					),
				);
		}
	
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}

	public function ShowDetails($data)
	{
		include GENER_DIR . 'view.php';

		$view_object = new ViewBuilder();

		$view_title = 'Szczegóły obrazka';
		$view_image = 'img/32x32/list_information.png';
		$view_width = '50%';
		
		$view_object->init($view_title, $view_image, $view_width);

		$view_action = 'index.php?route=' . MODULE_NAME;

		$view_object->set_action($view_action);

		$view_inputs = array();

		if (is_array($data))
		{
			$id = isset($data['id']) ? $data['id'] : NULL;

			$data['preview'] = 	'<a href="index.php?route=images&action=preview&id=' . $id . '">' .
								'<img src="' . GALLERY_DIR . IMG_DIR . $id.'" alt="ico" width="100%" style="border: 1px solid #ccc; padding: 1px;" />' .
								'</a>';

			$data['file_size'] = strval(intval($data['file_size'] / 1024) . ' KB');

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

	public function ShowPicture($data)
	{
		$result = NULL;

		if (is_array($data))
		{
			$id = isset($data['id']) ? $data['id'] : NULL;
			
			$result .= '<img src="' . GALLERY_DIR . IMG_DIR . $id.'" alt="ico" width="100%" style="border: 1px solid #ccc; padding: 1px;" />';
			$result .= '<p>' . 'Image ' . $data['id'] . '. <b>' . $data['file_name'] . '</b> (' . $data['user_login'] . ' @ ' . $data['modified'] . ')' . '</p>';
		}

		return $result;
	}

	public function ShowLoaded($import)
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Przegląd obrazków';
		$form_image = 'img/32x32/image_view.png';
		$form_width = '70%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php?route=' . MODULE_NAME;

		$form_object->set_action($form_action);

		$images = array();

		$images[] = array(
			'value' => NULL, 'caption' => 'Wybierz...', 
			);

		foreach ($import as $k => $v) 
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'id') $id = $value;
				if ($key == 'file_name') $file_name = $value;
			}
			$images[] = array(
				'value' => $id, 'caption' => $file_name, 
				);
		}

		$form_inputs = array(
			array(
				'caption' => 'Obrazek', 
				'data' => array(
					'type' => 'select', 'id' => 'image_selector', 'name' => 'image_selector', 
					'onchange' => 'ajax_load()',
					'option' => $images, 
					),
				),
			array(
				'caption' => 'Podgląd', 
				'data' => array(
					'type' => 'label', 'value' => '<div id="image_container"><img id="image_item" src="img/ajax/blank.png" class="ListImage" style="width: 100%; height: 100%;" alt="blank" /></div>',
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array();

		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'cancel', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Zamknij',
				),
			);
	
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}

	public function ShowTiles($data, $last_url)
	{
		$title = 'Galeria serwisu';
		$image = 'img/32x32/picture.png';

		foreach ($data as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'id') $id = $value;
				if ($key == 'file_size') $file_size = $value;
			}
			$data[$k]['preview'] = 	'index.php?route=' . MODULE_NAME . '&action=preview&id=' . $id;
			$data[$k]['file_size'] = strval(intval($file_size / 1024) . ' KB');
		}

		include GENER_DIR . 'tiles.php';

		$tiles_object = new TilesBuilder();

		$tiles_object->init($title, $image, $data, $this->get_list_params(), $last_url);

		$result = $tiles_object->build_tiles();

		return $result;
	}
}

?>
