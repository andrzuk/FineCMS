<?php

class Search_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowFound($data, $search, $length)
	{
		$title = 'Znalezione strony - dla: " <a style="color: green;">'.$search.'</a> " '.
		         '(pozycji: <a style="color: green;">'.count($data).'</a>)';
		$image = 'img/32x32/search.png';

		$attribs = array(
			array('style' => 'font-size: medium; font-weight: bold; padding: 10px 0px 5px 20px; color: #900;', 'visible' => '1'),
			array('style' => 'font-size: small; padding: 0px 20px 0px 20px; color: #666;', 'visible' => '1'),
			array('style' => NULL, 'visible' => '0'),
			array('style' => 'font-size: 0.85em; padding: 5px 20px 0px 0px; color: #999; text-align: right;', 'visible' => '1'),
		);
		
		include GENER_DIR . 'found.php';

		$list_object = new FoundBuilder();

		$list_object->init($title, $image, $data, $attribs, $length);

		$result = $list_object->build_found();

		return $result;
	}
}

?>
