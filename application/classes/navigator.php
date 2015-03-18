<?php

/*
 * Obliczenia wskaźników stron dla list nawigatora
 */

class ListNavigator
{
	private $app;

	private $display_rows;
	private $pointer_band;

	private $navigator_params;

	public function __construct($obj)
	{
		$this->app = $obj;

		$_SESSION['page_counter'] = isset($_SESSION['page_counter']) ? $_SESSION['page_counter'] : 0;
		$_SESSION['page_pointer'] = isset($_SESSION['page_pointer']) ? $_SESSION['page_pointer'] : 0;
		$_SESSION['starting_position'] = isset($_SESSION['starting_position']) ? $_SESSION['starting_position'] : 0;
		$_SESSION['result_capacity'] = isset($_SESSION['result_capacity']) ? $_SESSION['result_capacity'] : 0;

		$this->display_rows = $this->app->get_settings()->get_config_key('display_list_rows');
		$this->pointer_band = $this->app->get_settings()->get_config_key('page_pointer_band');

		if (isset($_GET['page_rows']))
		{
			$_SESSION['page_list_rows'] = intval($_GET['page_rows']);
			unset($_SESSION['keep_paginator']);
		}

		if (isset($_GET['paginator_reset']) || isset($_GET['mode']))
		{
			unset($_SESSION['keep_paginator']);
		}

		if (isset($_SESSION['page_list_rows']))
		{
			$this->display_rows = $_SESSION['page_list_rows'];
		}
	}
	
	public function init($list_columns)
	{
		if (!isset($_SESSION['sort_field'])) 
		{
			$_SESSION['sort_field'] = 0;
		}
		if (!isset($_SESSION['sort_order'])) 
		{
			$_SESSION['sort_order'] = 0;
		}
		if (isset($_POST['ListSearchButton']) || isset($_POST['SetDatesButton']))
		{
			unset($_SESSION['keep_paginator']);
		}
		
		$db_fields = array();

		foreach ($list_columns as $key => $value)
		{
			foreach ($value as $k => $v)
			{
				if ($k == 'db_name') $db_name = $v;
				if ($k == 'column_name') $column_name = $v;
				if ($k == 'sorting') $sorting = $v;
			}
			$db_fields[] = $db_name;
		}

		if (isset($_GET['skip'])) // zmiana strony
		{
			switch ($_GET['skip'])
			{
				case 'first':
					$_SESSION['starting_position'] = 0;
					$_SESSION['page_pointer'] = 0;
					break;
				case 'prev':
					if ($_SESSION['starting_position'] - $this->display_rows >= 0)
					{
						$_SESSION['starting_position'] -= $this->display_rows;
						$_SESSION['page_pointer']--;
					}
					else
					{
						$_SESSION['starting_position'] = 0;
						$_SESSION['page_pointer'] = 0;
					}
					break;
				case 'next':
					if ($_SESSION['starting_position'] + $this->display_rows < $_SESSION['result_capacity'])
					{
						$_SESSION['starting_position'] += $this->display_rows;
						$_SESSION['page_pointer']++;
					}
					break;
				case 'last':
					if ($_SESSION['result_capacity'] >= $this->display_rows)
					{
						$_SESSION['starting_position'] = $_SESSION['result_capacity'] - $this->display_rows;
						$_SESSION['page_pointer'] = $_SESSION['page_counter'] - 1;
					}
					break;
				default:
					break;
			}
		}
		else if (isset($_GET['page'])) // zmiana strony - idz do numeru
		{
			if (intval($_GET['page']) > 0 && intval($_GET['page']) <= $_SESSION['page_counter'])
			{
				$_SESSION['starting_position'] = $this->display_rows * intval($_GET['page'] - 1);
				$_SESSION['page_pointer'] = intval($_GET['page'] - 1);
			}
		}
		else // pierwsze uruchomienie
		{
			if (!isset($_SESSION['keep_paginator']))
			{
				$_SESSION['starting_position'] = 0;
				$_SESSION['page_pointer'] = 0;
			}
		}

		if (isset($_GET['sort'])) // zmiana sortowania
		{
			$_SESSION['sort_field'] = $_GET['sort'];
		}
		if (isset($_GET['order'])) // zmiana porządku
		{
			$_SESSION['sort_order'] = intval($_GET['order']);
		}

		$list_params = array(
			'show_rows' => $this->display_rows,
			'sort_column' => $_SESSION['sort_field'],
			'sort_order' => $_SESSION['sort_order'],
			'show_page' => $_SESSION['page_pointer'],
			'page_counter' => $_SESSION['page_counter'],
			'page_band' => $this->pointer_band,
		);

		$field_no = $_SESSION['sort_field'] >= 0 && $_SESSION['sort_field'] < sizeof($db_fields) ? $_SESSION['sort_field'] : 0;

		$db_params = array(
			'sort_field' => $db_fields[$field_no],
			'sort_order' => $_SESSION['sort_order'] ? 'DESC' : 'ASC',
			'start_from' => $_SESSION['starting_position'],
			'show_rows' => $this->display_rows,
		);
		
		$this->navigator_params = array(
			'record_object' => $db_fields,
			'db_params' => $db_params,
			'list_params' => $list_params, 
			);
	}
	
	public function set_paginator($count)
	{
		$show_rows = $this->navigator_params['list_params']['show_rows'];
		$show_pages = intval($count / $show_rows) + ($count % $show_rows > 0 ? 1 : 0);
		$this->navigator_params['list_params']['page_counter'] = $show_pages;

		$_SESSION['result_capacity'] = $count;
		$_SESSION['page_counter'] = $show_pages;
	}

	public function get_params($params = NULL)
	{
		if ($params) return $this->navigator_params[$params];
		else return $this->navigator_params;
	}
}

?>