<?php

class Statistics_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowStatisticsForm($data)
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Statystyki serwisu - Odsłony';
		$form_image = 'img/32x32/chart_line.png';
		$form_width = '100%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php';

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => NULL, 
				'data' => array(
					'type' => 'canvas', 'id' => 'chart', 'name' => 'chart', 'width' => '280px', 'height' => '100px',
					),
				),
			array(
				'caption' => NULL, 
				'data' => array(
					'type' => 'simple', 'id' => 'period', 'name' => 'period', 'value' => 'From - To', 'style' => 'text-align: center; font-size: 1.5em; color: #c00; margin: 5px 0 5px 0;',
					),
				),
			array(
				'caption' => NULL, 
				'data' => array(
					'type' => 'button',
					'items' => array(
						array(
							'id' => 'prev', 'name' => 'prev', 'value' => '◄', 'action' => 'update_chart(\'offset_prev\')', 'style' => 'margin: 10px 5px 0 20px;',
							),
						array(
							'id' => 'next', 'name' => 'next', 'value' => '►', 'action' => 'update_chart(\'offset_next\')', 'style' => 'margin: 10px 20px 0 5px;',
							),
						array(
							'id' => 'days_7', 'name' => 'days_7', 'value' => '7 dni', 'action' => 'update_chart(\'days_7\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'days_14', 'name' => 'days_14', 'value' => '14 dni', 'action' => 'update_chart(\'days_14\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'days_21', 'name' => 'days_21', 'value' => '21 dni', 'action' => 'update_chart(\'days_21\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'months_1', 'name' => 'months_1', 'value' => '1 miesiąc', 'action' => 'update_chart(\'months_1\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'months_2', 'name' => 'months_2', 'value' => '2 miesiące', 'action' => 'update_chart(\'months_2\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'months_3', 'name' => 'months_3', 'value' => '3 miesiące', 'action' => 'update_chart(\'months_3\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'months_6', 'name' => 'months_6', 'value' => '6 miesięcy', 'action' => 'update_chart(\'months_6\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						array(
							'id' => 'months_12', 'name' => 'months_12', 'value' => '12 miesięcy', 'action' => 'update_chart(\'months_12\')', 'style' => 'margin: 10px 5px 0 5px;',
							),
						),
					),
				),
			array(
				'caption' => 'Liczniki odwiedzin', 
				'data' => array(
					'type' => 'list',
					'id' => 'counters', 'name' => 'counters', 'style' => 'text-align: center; padding: 10px;',
					'items' => array(
						array(
							'value' => 'Dzienny: <b>'.$data['day']['period_counter'].'</b>', 'style' => 'display: inline; margin: 5px;',
							),
						array(
							'value' => 'Tygodniowy: <b>'.$data['week']['period_counter'].'</b>', 'style' => 'display: inline; margin: 5px;',
							),
						array(
							'value' => 'Miesięczny: <b>'.$data['month']['period_counter'].'</b>', 'style' => 'display: inline; margin: 5px;',
							),
						array(
							'value' => 'Narastający: <b>'.$data['all']['period_counter'].'</b>', 'style' => 'display: inline; margin: 5px;',
							),
						array(
							'value' => '<a href="/?route=statistics&mode=ip">Statystyka adresów IP</a>', 'style' => 'display: inline; margin: 5px;',
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
				'type' => 'submit', 'id' => 'submit', 'name' => 'submit', 'value' => 'Zamknij',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
	
	public function ShowIpStatsForm($data)
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Statystyki serwisu - Adresy IP';
		$form_image = 'img/32x32/chart_line.png';
		$form_width = '100%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php';

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => NULL, 
				'data' => array(
					'type' => 'canvas', 'id' => 'chart', 'name' => 'chart', 'width' => '220px', 'height' => '100px',
					),
				),
			array(
				'caption' => '', 
				'data' => array(
					'type' => 'list',
					'id' => 'counters', 'name' => 'counters', 'style' => 'text-align: center; padding: 10px;',
					'items' => array(
						array(
							'value' => 'Licznik adresów IP: <b>'.$data['all']['ip_counter'].'</b>', 'style' => 'display: inline; margin: 5px;',
							),
						array(
							'value' => '<a href="/?route=statistics">Statystyka odsłon</a>', 'style' => 'display: inline; margin: 5px;',
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
				'type' => 'submit', 'id' => 'submit', 'name' => 'submit', 'value' => 'Zamknij',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
}

?>
