<?php

/*
 * Klasa odpowiedzialna za tworzenie widoków - Generator Widoków
 */

include GENER_DIR . 'builder.php';

class ViewBuilder extends Builder
{
	private $image;
	private $title;
	private $width;
	private $action;
	private $inputs = Array();	
	private $buttons = Array();
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function init($form_title, $form_image, $form_width)
	{
		$this->image = $form_image;
		$this->title = $form_title;
		$this->width = $form_width;
	}
	
	public function set_action($form_address)
	{
		$this->action = $form_address;
	}

	public function set_inputs($form_rows)
	{
		$this->inputs = Array();
		
		foreach ($form_rows as $k => $v)
		{
			$this->inputs[] = $v;
		}
	}
	
	public function set_buttons($form_submits)
	{
		$this->buttons = Array();

		foreach ($form_submits as $k => $v) $this->buttons[] = $v;
	}
	
	public function build_view()
	{
		$main_text = NULL;
		
		$main_text .= '<form action="'. $this->action .'" method="post" role="form">';

		$main_text .= '<div class="panel panel-default center" style="width: '. $this->width .';">';

		$main_text .= '<div class="panel-heading">';
		$main_text .= '<h3 class="panel-title">';
		$main_text .= '<img src="'.$this->image.'" alt="'.$this->title.'" />';
		$main_text .= $this->title;
		$main_text .= '</h3>';
		$main_text .= '</div>';

		$main_text .= '<div class="panel-body">';

		foreach ($this->inputs as $k => $v)
		{
			foreach ($v as $key => $val)
			{
				if ($key == 'caption') $caption = $val;
				if ($key == 'value') $value = $val;
			}
			$main_text .= '<div class="form-group">';
			$main_text .= '<table width="100%">';
			$main_text .= '<tr>';
			$main_text .= '<td class="ViewKey">';
			$main_text .= $caption.':';
			$main_text .= '</td>';
			$main_text .= '<td class="ViewData">';
			if (is_array($value))
			{
				foreach ($value as $i => $j) 
				{
					$main_text .= '<div>' . $j . '</div>';
				}
			}
			else // normalne dane
			{
				$main_text .= $value;
			}
			$main_text .= '</td>';
			$main_text .= '</tr>';
			$main_text .= '</table>';
			$main_text .= '</div>';
		}

		$main_text .= '</div>';

		$main_text .= '<div class="panel-footer" style="text-align: center;">';

		foreach ($this->buttons as $k => $v)
		{
			foreach ($v as $i => $j)
			{
				if ($i == 'type') $type = $j;
				if ($i == 'id') $id = $j;
				if ($i == 'name') $name = $j;
				if ($i == 'value') $value = $j;
			}
			if ($type == 'submit')
			{
				$main_text .= '<button type="'.$type.'" id="'.$id.'" name="'.$name.'" class="btn btn-primary" value="'.$value.'">'.$value.'</button>';
			}
			else // anuluj
			{
				$main_text .= '<button type="button" id="'.$id.'" name="'.$name.'" class="btn btn-warning" value="'.$value.'" onclick="submit()">'.$value.'</button>';
			}
		}

		$main_text .= '</div>';

		$main_text .= '</div>';

		$main_text .= '</form>';
		
		return $main_text;
	}
}

?>
