<?php

class Statistics_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
		
		$this->app->get_page()->set_path(array(
			'index.php' => 'Strona główna',
			'index.php?route=statistics' => 'Statystyki',
			));
	}

	public function Index_Action()
	{
		$mode = isset($_GET['mode']) ? $_GET['mode'] : NULL;
		
		if ($mode == 'ip')
		{
			$data = $this->app->get_model_object()->GetIpStats();
			
			$this->app->get_page()->set_content($this->app->get_view_object()->ShowIpStatsForm($data));
		}
		else
		{
			$data = $this->app->get_model_object()->GetStatistics();
			
			$this->app->get_page()->set_content($this->app->get_view_object()->ShowStatisticsForm($data));
		}

		$layout = $this->app->get_settings()->get_config_key('page_template_default');

		$this->app->get_page()->set_layout($layout);

		$this->app->get_page()->set_template('statistics');
	}
}

?>
