<?php

class Index_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			));
	}
	
	public function Index_Action()
	{
		$data = $this->app->get_model_object()->GetPage();

		$this->app->get_page()->set_content($this->app->get_view_object()->ShowPage($data));

		$layout = $this->app->get_settings()->get_config_key('page_template_extended');

		$this->app->get_page()->set_layout($layout);
		
		$this->app->get_page()->set_template('index');
	}
}

?>
