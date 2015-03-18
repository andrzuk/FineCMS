<?php

/*
 * Klasa odpowiedzialna za tworzenie list - Generator List
 */

include GENER_DIR . 'builder.php';

class ListBuilder extends Builder
{
	private $title;
	private $image;
	
	private $columns;
	private $data;
	private $params;
	private $attribs;
	private $actions;
	private $dates;

	public function __construct()
	{
		parent::__construct();
	}

	public function init($title, $image, $columns, $data, $params, $attribs, $actions)
	{
		$this->image = $image;
		$this->title = $title;
		$this->columns = $columns;
		$this->data = $data;
		$this->params = $params;
		$this->attribs = $attribs;
		$this->actions = $actions;
	}

	public function show_dates($value)
	{
		$this->dates = $value;
	}

	public function build_list()
	{
		$main_text = NULL;

		$active_fields = array();

		if (count($this->actions))
		{
			$this->columns[] = array(
				'db_name' => NULL, 'column_name' => 'Akcje', 'sorting' => NULL,
				);
		}

		foreach ($this->attribs as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'width') $width = $value;
				if ($key == 'align') $align = $value;
				if ($key == 'visible') $visible = $value;
				if ($key == 'type') $type = $value;
			}
			if ($visible)
			{
				$active_fields[] = $this->columns[$k]['db_name'];
			}
		}

		include CLASS_DIR . 'paginator.php';
		
		$paginator = new List_Paginator();

		$base_link = 'index.php?route=' . MODULE_NAME;
						
		$paginator->init($base_link, $this->params['show_page'], $this->params['page_counter'], $this->params['page_band']);
		
		$main_text .= '<div class="panel panel-default">';
		$main_text .= '<div class="panel-heading">';

		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';
		$main_text .= '<span class="FormSearch">';
		$main_text .= '<form action="index.php?route=' . MODULE_NAME . '&sort=' . $this->params['sort_column'] . '&order=' . $this->params['sort_order'] . '" class="navbar-form" role="search" method="post">';
		$main_text .= '<div class="form-group">';
		$main_text .= '<input type="text" id="ListSearchText" name="ListSearchText" value="" class="form-control" />&nbsp;';
		$main_text .= '</div>';
		$main_text .= '<button type="submit" name="ListSearchButton" id="ListSearchButton" class="btn btn-default">Szukaj</button>';
		$main_text .= '</form>';
		$main_text .= '</span>';
		if ($this->dates)
		{
			$main_text .= '<span class="FormDates">';
			$main_text .= '<form action="index.php?route=' . MODULE_NAME . '&sort=' . $this->params['sort_column'] . '&order=' . $this->params['sort_order'] . '" class="navbar-form" role="search" method="post">';
			$main_text .= '<input type="date" id="date_from" name="date_from" value="'.$_SESSION['date_from'].'" class="form-control" />&nbsp;-&nbsp;';
			$main_text .= '<input type="date" id="date_to" name="date_to" value="'.$_SESSION['date_to'].'" class="form-control" />&nbsp;';
			$main_text .= '<button type="submit" id="SetDatesButton" name="SetDatesButton" class="btn btn-default">OK</button>';
			$main_text .= '</form>';
			$main_text .= '</span>';
		}

		$main_text .= '</div>';

		$main_text .= '<table class="table">';
		
		// jeśli wprowadzono filtr:
		
		if (!empty($_SESSION['list_filter']))
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="FormSearchBar" colspan="'.count($active_fields).'">';
			$main_text .= '<form id="form_search_close" action="index.php?route=' . MODULE_NAME . '&sort=' . $this->params['sort_column'] . '&order=' . $this->params['sort_order'] . '" method="post">';
			$main_text .= '<span class="FormSearchCaption">Wyszukiwanie:</span>&nbsp;';
			$main_text .= '<span class="FormSearchValue">" <b>' . $_SESSION['list_filter'] . '</b> "</span>';
			$main_text .= '<span class="FormSearchClose">';
			$main_text .= '<input type="hidden" name="ListSearchClose" value="Close" />';
			$main_text .= '<img src="img/16x16/cross_button.png" onclick="document.getElementById(\'form_search_close\').submit();" alt="close" title="Usuń filtr" />';
			$main_text .= '</span>'; 
			$main_text .= '</form>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}

		// nagłówki:

		$main_text .= '<tr class="HeaderRow">';

		$idx = 0;

		foreach ($this->columns as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'db_name') $db_name = $value;
				if ($key == 'column_name') $column_name = $value;
				if ($key == 'sorting') $sorting = $value;
			}

			if (in_array($db_name, $active_fields))
			{
				if ($this->params['sort_column'] == $k) // zaznaczona kolumna
				{
					$sort_icon = $this->params['sort_order'] ? 'img/sort/descending.png' : 'img/sort/ascending.png';
					$order = 1 - $this->params['sort_order'];
				}
				else // pozostałe kolumny
				{
					$sort_icon = 'img/sort/none.png';
					$order = $this->params['sort_order'];
				}

				if ($sorting) // kolumna sortowalna
				{
					$main_text .= '<th class="TitleCell" style="width: '.$this->attribs[$idx]['width'].'; text-align: '.$this->attribs[$idx]['align'].';">'.'<a href="index.php?route='.MODULE_NAME.'&sort='.$k.'&order='.$order.'">'.$column_name.'</a>'.'<div class="sorting">'.'<img src="'.$sort_icon.'" />'.'</div>'.'</th>';
				}
				else // kolumna niesortowalna
				{
					$main_text .= '<th class="TitleCell">'.$column_name.'</th>';
				}
			}
			$idx++;
		}

		$main_text .= '</tr>';

		// dane:

		$line = 0;

		foreach ($this->data as $k => $row)
		{
			$idx = 0;

			$class_name = ($line++) % 2 ? 'DataRowDark' : 'DataRowBright';

			$data_class_name = NULL;

			if (isset($row['visible'])) $data_class_name = $row['visible'] ? 'DataCell' : 'DataLock';
			if (isset($row['active'])) $data_class_name = $row['active'] ? 'DataCell' : 'DataLock';
			if (isset($row['user_id'])) $data_class_name = $row['user_id'] ? 'DataCell' : 'DataLock';

			$main_text .= '<tr class="' . $class_name . '">';

			foreach ($row as $key => $value) // dane
			{
				if (in_array($key, $active_fields))
				{
					if (isset($this->attribs[$idx]['image']))
					{
						$main_text .= '<td class="'.$data_class_name.'" style="text-align: '.$this->attribs[$idx]['align'].'">'.$value.'</td>';
					}
					else if (isset($this->attribs[$idx]['array']))
					{
						$main_text .= '<td class="'.$data_class_name.'" style="text-align: '.$this->attribs[$idx]['align'].'">';
						foreach ($value as $i => $j) $main_text .= '<div>' . $j . '</div>';
						$main_text .= '</td>';
					}
					else // normalne dane
					{
						$main_text .= '<td class="'.$data_class_name.'" style="text-align: '.$this->attribs[$idx]['align'].'">'.$this->get_split_text(strip_tags($value), 20).'</td>';
					}
				}
				$idx++;
			}

			if (count($this->actions)) // akcje
			{
				$main_text .= '<td class="ActionCell" style="text-align: '.$this->attribs[$idx]['align'].'">';
				
				foreach ($this->actions as $k => $v)
				{
					foreach ($v as $key => $value)
					{
						if ($key == 'action') $action = $value;
						if ($key == 'icon') $icon = $value;
						if ($key == 'title') $title = $value;
					}
					$main_text .= '<a href="index.php?route=' . MODULE_NAME . '&action=' . $action . '&id=' . $row['id'] . '"><img src="img/16x16/' . $icon . '" class="ActionIcon" alt="' . $title . '" title="' . $title . '" /></a>';
				}
								
				$main_text .= '</td>';
			}

			$main_text .= '</tr>';
		}

		if (!count($this->data))
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="DataCellMsg" colspan="'.count($active_fields).'">';
			$main_text .= '<div><img src="img/32x32/warning.png" alt="empty result" /></div>';
			$main_text .= '<div>(brak wyników)</div>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}

		$main_text .= '</table>';
		$main_text .= '<div class="panel-footer">';
		$main_text .= $paginator->show();
		$main_text .= '</div>';
		$main_text .= '</div>';

		return $main_text;
	}
}

?>
