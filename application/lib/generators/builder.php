<?php

/*
 * Klasa bazowa dla generatorów
 */

class Builder
{
	public function __construct()
	{
	}

	public function get_split_text($source, $length)
	{
		$result = NULL;
		$idx = 0;
		$broken = FALSE;

		$source = str_replace(chr(13) . chr(10), chr(32), $source);
		$words = explode(chr(32), $source);

		foreach ($words as $k => $v)
		{
			$result .= $v . chr(32);
			if ($idx++ >= $length) 
			{
				$broken = TRUE;
				break;
			}
		}
		$result = $broken ? $result . '...&nbsp;»' : trim($result);
		
		return $result;
	}
}

?>
