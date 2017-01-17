<?php

class Comments_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowList($columns, $data)
	{
		$title = 'Komentarze użytkowników';
		$image = 'img/32x32/list_go.png';

		$attribs = array(
			array('width' => '5%',  'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'left',   'visible' => '1'),
			array('width' => '15%', 'align' => 'left',   'visible' => '1'),
			array('width' => '15%', 'align' => 'left',   'visible' => '1'),
			array('width' => '30%', 'align' => 'left',   'visible' => '1'),
			array('width' => '5%',  'align' => 'center', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
		);
		
		$actions = array(
			array('action' => 'view',    'icon' => 'info.png',   'title' => 'Podgląd'),
			array('action' => 'confirm', 'icon' => 'accept.png', 'title' => 'Zatwierdź'),
			array('action' => 'disable', 'icon' => 'remove.png', 'title' => 'Zablokuj'),
			array('action' => 'delete',  'icon' => 'trash.png',  'title' => 'Usuń'),
		);
	
		include GENER_DIR . 'list.php';

		$list_object = new ListBuilder();

		$list_object->init($title, $image, $columns, $data, $this->get_list_params(), $attribs, $actions);

		$result = $list_object->build_list();

		return $result;
	}

	public function ShowDetails($data)
	{
		include GENER_DIR . 'view.php';

		$view_object = new ViewBuilder();

		$view_title = 'Szczegóły komentarza';
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
