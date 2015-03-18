<?php

class Search_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=search' => 'Wyszukiwanie stron',
			));
	}
	
	public function Index_Action()
	{
		$user_status = $this->app->get_user()->get_value('user_status');

		$search_text = isset($_POST['text-search']) ? $_POST['text-search'] : (isset($_SESSION['search_text']) ? $_SESSION['search_text'] : NULL);

		$_SESSION['search_text'] = $search_text;

		$data = $this->app->get_model_object()->GetResults($user_status, $search_text);

		$length = $this->app->get_settings()->get_config_key('description_length');

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowFound($data, $search_text, $length));

		$layout = $this->app->get_settings()->get_config_key('page_template_extended');

		$this->app->get_page()->set_layout($layout);

		$this->app->get_page()->set_template('index');
	}
}

?>
