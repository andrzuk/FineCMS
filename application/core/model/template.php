<?php

class Template_Model extends Model
{
	public function __construct($db)
	{
		parent::__construct($db);
	}

	public function GetContent($file)
	{
		$contents = NULL;

		if (file_exists($file))
		{
			$fh = fopen($file, 'r');
			$contents = fread($fh, filesize($file));
			fclose($fh);
		}
		
		$result = array('filename' => $file, 'contents' => $contents);

		return $result;
	}

	public function Save($record)
	{
		$result = FALSE;

		if (!parent::check_required($record)) return NULL;

		foreach ($record as $k => $v)
		{
			if ($k == 'filename') $filename = $v;
			if ($k == 'contents') $contents = $v;
		}

		if (file_exists($filename))
		{
			$fh = fopen($filename, 'w');
			fwrite($fh, $contents);
			fclose($fh);

			$result = TRUE;
		}

		return $result;
	}

	public function Reset($file)
	{
		$result = FALSE;

		$path = explode('/', $file);

		array_splice($path, count($path) - 1, 0, 'orig');

		$orig_file = implode('/', $path);

		if (file_exists($orig_file) && file_exists($file))
		{
			$fh = fopen($orig_file, 'r');
			$contents = fread($fh, filesize($orig_file));
			fclose($fh);
			
			$fh = fopen($file, 'w');
			fwrite($fh, $contents);
			fclose($fh);

			$result = TRUE;
		}

		return $result;
	}
}

?>
