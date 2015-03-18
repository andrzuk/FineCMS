<?php

class View
{
	protected $page;

	protected $list_params;

	public function __construct($page)
	{
		$this->page = $page;
	}
	
	public function ShowDialog()
	{
		$result = NULL;

		if ($this->page->get_dialog())
		{
			$result = $this->page->get_dialog()->show_dialog_box();
		}

		return $result;		
	}

	public function set_list_params($params)
	{
		$this->list_params = $params;
	}

	public function get_list_params()
	{
		return $this->list_params;
	}
}

?>
