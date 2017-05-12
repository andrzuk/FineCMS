<?php

class Page_Controller extends Controller
{
	public function __construct($app)
	{
		parent::__construct($app);
	}
	
	public function Index_Action()
	{
		$user_status = $this->app->get_user()->get_value('user_status');

		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		// ustala permission taki jak dla powiązanej kategorii:

		$data = $this->app->get_model_object()->GetCategory($id);

		if ($data) // strona powiązana z kategorią
		{
			$permission = $data['permission'];
			$visible = $data['visible'];

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

				$data['id'] = $id;
				$data['skip_bar_visible'] = $this->app->get_settings()->get_config_key('skip_bar_visible') == 'true';
				$data['skip_bar'] = $this->app->get_menu()->GetSiblings($data['category_id'], $id);
				$data['social_buttons_visible'] = $this->app->get_settings()->get_config_key('social_buttons_visible') == 'true';
				$data['social_buttons'] = $this->app->get_settings()->get_config_key('social_buttons');
				$data['logged_in'] = $user_status > 0;

				if (empty($data['contents'])) // strona bez treści - ładuje podkategorie
				{
					$data['contents'] = $this->app->get_model_object()->GetChildren($id);
				}
				// load article comments:
				$data['comments_panel_visible'] = $this->app->get_settings()->get_config_key('comments_panel_visible') == 'true';
				$data['comments'] = $this->app->get_model_object()->GetComments($id);

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

	public function Comment_Action()
	{
		parent::Add_Action();

		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		$record = array(
			'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0,
			'page_id' => $id,
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
