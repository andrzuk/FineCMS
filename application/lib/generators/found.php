<?php

/*
 * Klasa odpowiedzialna za tworzenie list - Generator List
 */

include GENER_DIR . 'builder.php';

class FoundBuilder extends Builder
{
	private $title;
	private $image;
	
	private $data;
	private $attribs;

	private $description_length;

	public function __construct()
	{
		parent::__construct();
	}

	public function init($title, $image, $data, $attribs, $length)
	{
		$this->image = $image;
		$this->title = $title;
		$this->data = $data;
		$this->attribs = $attribs;
		$this->description_length = $length;
	}

	public function build_found()
	{
		$main_text = NULL;

		$main_text .= '<div class="panel panel-default">';
		$main_text .= '<div class="panel-heading">';

		$main_text .= '<span class="FormIcon">';
		$main_text .= '<img src="'.$this->image.'" alt="Found" />';
		$main_text .= '</span>';
		$main_text .= '<span class="FormTitle">';
		$main_text .= $this->title;
		$main_text .= '</span>';

		$main_text .= '</div>';

		$main_text .= '<table class="table">';
		
		$line = 0;

		if (is_array($this->data))
		{
			foreach ($this->data as $k => $row)
			{
				$idx = 0;

				$class_name = ($line++) % 2 ? 'DataRowDark' : 'DataRowBright';
				$data_class_name = 'DataCell';

				$main_text .= '<tr class="' . $class_name . '">';
				$main_text .= '<td class="' . $data_class_name . '">';

				foreach ($row as $key => $value)
				{
					if ($this->attribs[$idx]['visible'])
					{
						$main_text .= '<div style="' . $this->attribs[$idx]['style'] . '">';
						
						$is_link = $key == 'title' ? TRUE : FALSE;
						
						if ($is_link) $main_text .= '<a href="index.php?route=page&id=' . $row['id'] . '">';

						$main_text .= $this->get_split_text(strip_tags($value), $this->description_length);

						if ($is_link) $main_text .= '</a>';
						
						$main_text .= '</div>';
					}
					$idx++;
				}

				$main_text .= '</td>';
				$main_text .= '</tr>';
			}
		}

		if (!count($this->data))
		{
			$main_text .= '<tr>';
			$main_text .= '<td class="DataCellMsg">';
			$main_text .= '<div><img src="img/32x32/warning.png" alt="empty result" /></div>';
			$main_text .= '<div>(brak wyników)</div>';
			$main_text .= '</td>';
			$main_text .= '</tr>';
		}

		$main_text .= '</table>';
		$main_text .= '</div>';

		return $main_text;
	}
}

?>
