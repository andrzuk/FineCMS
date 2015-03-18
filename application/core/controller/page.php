<?php

class Page_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
	}
	
	public function Index_Action()
	{
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		// ustala permission taki jak dla powiązanej kategorii:

		$data = $this->app->get_model_object()->GetCategory($id);

		if ($data) // strona powiązana z kategorią
		{
			$permission = $data['permission'];
			$visible = $data['visible'];

			$user_status = $this->app->get_user()->get_value('user_status');

			$access = $user_status ? $user_status <= $permission : $permission == FREE;
			$access &= $visible;

			$this->app->get_page()->set_path(array_reverse($this->make_path($data['category_id'])));

			$this->app->get_page()->set_selected($data['category_id']);
		}
		else // strona niezależna od kategorii
		{
			$access = TRUE;
		}

		if ($access)
		{
			$data = $this->app->get_model_object()->GetPage($id);

			if ($data)
			{
				$main_title = $this->app->get_page()->get_metadata('main_title');
				$this->app->get_page()->set_metadata('main_title', $main_title .' - '. $data['title']);
				$this->app->get_page()->set_metadata('main_description', $data['description']);

				$data['skip_bar'] = $this->app->get_menu()->GetSkipBar($data['category_id']);
				$data['social_buttons'] = $this->app->get_settings()->get_config_key('social_buttons');

				if (empty($data['contents'])) // strona bez treści - ładuje podkategorie
				{
					$data['contents'] = $this->app->get_model_object()->GetChildren($id);
				}
				$this->app->get_page()->set_content($this->app->get_view_object()->ShowPage($data));
			}
			else
			{
				parent::PageNotFound();
			}
		}
		else
		{
			parent::AccessDenied();
		}

		$layout = $this->app->get_settings()->get_config_key('page_template_extended');

		$this->app->get_page()->set_layout($layout);

		$this->app->get_page()->set_template('index');
	}
}

?>
