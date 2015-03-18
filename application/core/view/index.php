<?php

class Index_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowPage($data)
	{
		$result = NULL;

		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				if ($key == 'contents')
				{
					$result .= $value;
				}
			}
		}

		return $result;
	}
}

?>
