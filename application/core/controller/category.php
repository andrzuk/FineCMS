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

				$data[0]['category_id'] = $id;
				$data[0]['skip_bar_visible'] = $this->app->get_settings()->get_config_key('skip_bar_visible') == 'true';
				$data[0]['skip_bar'] = $this->app->get_menu()->GetSkipBar($id);
				$data[0]['social_buttons_visible'] = $this->app->get_settings()->get_config_key('social_buttons_visible') == 'true';
				$data[0]['social_buttons'] = $this->app->get_settings()->get_config_key('social_buttons');
				$data[0]['logged_in'] = $user_status > 0;

				if (empty($data[0]['contents'])) // strona bez treści - ładuje podkategorie
				{
					$data[0]['contents'] = $this->app->get_model_object()->GetChildren($id);
				}
				// load article comments:
				$data[0]['comments_panel_visible'] = $this->app->get_settings()->get_config_key('comments_panel_visible') == 'true';
				$data[0]['comments'] = $this->app->get_model_object()->GetComments($id);
				// set pagination:
				$data[0]['articles_pagination_enabled'] = $this->app->get_settings()->get_config_key('articles_pagination_enabled') == 'true';
				$data[0]['articles_per_page'] = $this->app->get_settings()->get_config_key('articles_per_page');

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

	public function Comment_Action()
	{
		parent::Add_Action();

		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		$record = array(
			'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0,
			'page_id' => $this->app->get_model_object()->GetPageId($id),
			'contents' => isset($_POST['contents']) ? $_POST['contents'] : NULL,
			'visible' => $this->app->get_settings()->get_config_key('moderate_comments') == 'true' ? 0 : 1,
			);

		$result = $this->app->get_model_object()->Comment($record);

		if ($result) // wysyłanie poprawne
		{
			$this->app->get_page()->set_message(MSG_INFORMATION, 'Twój komentarz został pomyślnie wysłany do serwisu.');
			
			header('Location: index.php?route=' . MODULE_NAME . '&id=' . $id);
			exit;
		}
		else // wysyłanie nieudane
		{
			$this->app->get_page()->set_message(MSG_ERROR, 'Twój komentarz nie został wysłany.');

			header('Location: index.php?route=' . MODULE_NAME . '&id=' . $id);
			exit;
		}
	}
}

?>
