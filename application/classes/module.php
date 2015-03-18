<?php

class Module
{
	private $module_name;
	private $module_path;

	public function __construct($module_path)
	{
		$this->module_path = $module_path;
	}

	public function get_name()
	{
		$delim = array('/', '\\');

		$delimeter = substr_count($this->module_path, $delim[0]) > substr_count($this->module_path, $delim[1]) ? $delim[0] : $delim[1];

		$path = explode($delimeter, $this->module_path);

		$this->module_name = str_replace('.php', NULL, strtolower($path[count($path) - 1]));

		return $this->module_name;		
	}
}

?>