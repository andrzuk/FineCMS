<?php

/*
 * Klasa odpowiedzialna za generowanie paginacji list
 */

class List_Paginator
{
	private $pointer_band;
	private $current_pointer;
	private $pointer_count;
	private $base_link;
	private $route;
	private $action;
	private $page_rows;
	
	function __construct()
	{
		$this->page_rows = Array(NULL, 5, 10, 15, 20, 50, 100);
	}
	
	public function init($link, $current, $count, $band)
	{
		$this->base_link = $link;
		$this->current_pointer = $current;
		$this->pointer_count = $count;
		$this->pointer_band = $band;

		$route_segment = array();
		$action_segment = array();

		if (strpos($this->base_link, 'route=') !== FALSE)
			$route_segment = explode('route=', $this->base_link);

		if (strpos($this->base_link, 'action=') !== FALSE)
			$action_segment = explode('action=', $this->base_link);

		$this->route = sizeof($route_segment) > 1 ? $route_segment[1] : NULL;
		$this->action = sizeof($action_segment) > 1 ? $action_segment[1] : NULL;

		if (strpos($this->route, '&') !== FALSE)
			$this->route = substr($this->route, 0, strpos($this->route, '&'));

		if (strpos($this->action, '&') !== FALSE)
			$this->action = substr($this->action, 0, strpos($this->action, '&'));
	}
	
	public function show()
	{
		$output = NULL;
		
		$output .= '<table class="NaviBar" width="100%" align="center" cellpadding="2" cellspacing="2">';
		$output .= '<tr>';
		
		$output .= '<td class="NaviPointersLeft">';

		$output .= '<form action="'.$this->base_link.'" method="get" class="FormShowRows">';
		if ($this->route)
			$output .= '<input type="hidden" name="route" value="'.$this->route.'" />';
		if ($this->action)
			$output .= '<input type="hidden" name="action" value="'.$this->action.'" />';
		$output .= '<select name="page_rows" id="page_rows" class="form-control" onchange="submit()">';
		foreach ($this->page_rows as $key => $value) 
		{
			$selected = NULL;
			if (isset($_SESSION['page_list_rows']))
			{
				if ($value == $_SESSION['page_list_rows'])
					$selected = 'selected="selected"';
				if ($key == 0) continue;
			}
			$output .= '<option '.$selected.'>';
			$output .= $value;
			$output .= '</option>';
		}
		$output .= '</select>';
		$output .= '</form>';	

		$output .= '</td>';
		
		$output .= '<td class="NaviPointers">';

		$output .= '<ul class="pagination">';
		
		if ($this->current_pointer == 0)
		{
			$output .= '<li class="disabled"><a class="PagePointerDisabled">&lt;&lt;</a></li>';
			$output .= '<li class="disabled"><a class="PagePointerDisabled">&lt;</a></li>';
		}
		else
		{
			$output .= '<li><a href="'.$this->base_link.'&skip=first" class="PagePointer">&lt;&lt;</a></li>';
			$output .= '<li><a href="'.$this->base_link.'&skip=prev" class="PagePointer">&lt;</a></li>';
		}

		$shown = 1;
		$min_p = intval($this->current_pointer) + 1;
		$max_p = $min_p;
		
		for ($i = 1; $i <= intval($this->pointer_count); $i++)
		{
			$cur_p = $min_p - 1;
			if ($cur_p < $min_p && $cur_p > 0) { $min_p = $cur_p; $shown++; }
			$cur_p = $max_p + 1;
			if ($cur_p > $max_p && $cur_p <= intval($this->pointer_count)) { $max_p = $cur_p; $shown++; }
			if ($shown >= 2 * $this->pointer_band + 1) break;
		}
		for ($i = $min_p; $i <= $max_p; $i++)
		{
			if ($i == $this->current_pointer + 1)
				$output .= '<li class="active"><a class="PagePointerCurrent">'.$i.'</a></li>';
			else
				$output .= '<li><a href="'.$this->base_link.'&page='.$i.'" class="PagePointer">'.$i.'</a></li>';
		}

		if (intval($this->current_pointer) == intval($this->pointer_count - 1) || $this->pointer_count == 0)
		{
			$output .= '<li class="disabled"><a class="PagePointerDisabled">&gt;</a></li>';
			$output .= '<li class="disabled"><a class="PagePointerDisabled">&gt;&gt;</a></li>';
		}
		else
		{
			$output .= '<li><a href="'.$this->base_link.'&skip=next" class="PagePointer">&gt;</a></li>';
			$output .= '<li><a href="'.$this->base_link.'&skip=last" class="PagePointer">&gt;&gt;</a></li>';
		}
		
		$output .= '</ul>';

		$output .= '</td>';
		
		$output .= '<td class="NaviPointersRight">';
		
		$output .= '<form action="'.$this->base_link.'" method="get" class="FormGoToPage">';
		$output .= '<div class="col-lg-6" id="goto-container">';
		$output .= '<div class="input-group" id="goto-page">';
		if ($this->route)
			$output .= '<input type="hidden" name="route" value="'.$this->route.'" />';
		if ($this->action)
			$output .= '<input type="hidden" name="action" value="'.$this->action.'" />';
		$output .= '<input type="text" name="page" id="page-number" class="form-control">';
		$output .= '<span class="input-group-btn">';
		$output .= '<button class="btn btn-default" name="navi" type="submit" value="go">idź</button>';
		$output .= '</span>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</form>';

		$output .= '</td>';
		
		$output .= '</tr>';
		$output .= '<tr>';

		$output .= '<td colspan="3" class="NaviPointersCount">';
		$output .= 'Pozycji: <b>' . number_format($_SESSION['result_capacity'], 0, ',', '.') . '</b>';
		$output .= '&nbsp; ▪ &nbsp;';
		$output .= 'Stron: <b>' . number_format($_SESSION['page_counter'], 0, ',', '.') . '</b>';
		$output .= '</td>';
		
		$output .= '</tr>';
		$output .= '</table>';
		
		return $output;
	}
}

?>