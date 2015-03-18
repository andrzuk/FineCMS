<?php

class Category_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
	}
	
	public function Index_Action()
	{
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		$this->app->get_page()->set_path(array_reverse($this->make_path($id)));

		$this->app->get_page()->set_selected($id);

		$user_status = $this->app->get_user()->get_value('user_status');

		$data = $this->app->get_model_object()->GetPage($id);

		if ($data)
		{
			$access = $user_status ? $user_status <= $data[0]['permission'] : $data[0]['permission'] == FREE;

			if ($access)
			{
				$main_title = $this->app->get_page()->get_metadata('main_title');
				$this->app->get_page()->set_metadata('main_title', $main_title .' - '. $data[0]['title']);
				$this->app->get_page()->set_metadata('main_description', $data[0]['description']);

				$data[0]['skip_bar'] = $this->app->get_menu()->GetSkipBar($id);
				$data[0]['social_buttons'] = $this->app->get_settings()->get_config_key('social_buttons');

				if (empty($data[0]['contents'])) // strona bez treści - ładuje podkategorie
				{
					$data[0]['contents'] = $this->app->get_model_object()->GetChildren($id);
				}
				$this->app->get_page()->set_content($this->app->get_view_object()->ShowPage($data));
			}
			else
			{
				parent::AccessDenied();
			}
		}
		else
		{
			parent::CategoryNotFound();
		}

		$layout = $this->app->get_settings()->get_config_key('page_template_extended');

		$this->app->get_page()->set_layout($layout);

		$this->app->get_page()->set_template('index');
	}
}

?>
