<?php

class Category_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowPage($data)
	{
		$result = NULL;

		if (is_array($data))
		{
			$result .= '<table width="100%" cellpadding="5" cellspacing="5" align="center" id="results">';

			foreach ($data as $k => $v)
			{
				foreach ($v as $key => $value)
				{
					if ($key == 'id') $id = $value;
					if ($key == 'category_id') $category_id = $value;
					if ($key == 'user_id') $user_id = $value;
					if ($key == 'title') $title = $value;
					if ($key == 'contents') $contents = $value;
					if ($key == 'description') $description = $value;
					if ($key == 'user_login') $user_login = $value;
					if ($key == 'modified') $modified = $value;
					if ($key == 'previews') $previews = $value;
					if ($key == 'skip_bar') $skip_bar = $value;
					if ($key == 'social_buttons') $soc_buttons = $value;
				}

				$result .= '<tr><td width="100%">';

				if (count($data) > 1) // kilka artykułów
				{
					$social_buttons = str_replace(array('{{_url_}}', '{{_title_}}'), array($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/index.php?route=page&id='.$id, $title), $soc_buttons);

					$result .= '<div class="article">';
					$result .= '<div class="article-title">';
					$result .= '<h3>' . '<a href="index.php?route=page&id='.$id.'">' . $title . '</a>' . '</h3>';
					$result .= '</div>';
					$result .= '<div class="article-timestamp">';
					$result .= '<img src="img/16x16/user.png" />' . '<a href="index.php?route=users&action=view&id='.$user_id.'">' . $user_login. '</a>';
					$result .= '<img src="img/16x16/date.png" />' . $modified;
					$result .= '<img src="img/16x16/web.png" />' . $previews;
					$result .= $social_buttons;
					$result .= '</div>';
					$result .= '<div class="article-content">';
					if (is_array($contents))
					{
						$result .= '<ul>';
						foreach ($contents as $element)
						{
							foreach ($element as $key => $value)
							{
								if ($key == 'caption') $caption = $value;
								if ($key == 'link') $link = $value;
							}
							$result .= '<li>'.'<a href="'.$link.'">'.$caption.'</a>'.'</li>';
						}
						$result .= '</ul>';
					}
					else
					{
						$result .= $description;
					}
					$result .= '</div>';
					$result .= '<div class="article-continue">';
					$result .= '<a href="index.php?route=page&id='.$id.'">Czytaj dalej...</a>';
					$result .= '</div>';
					$result .= '</div>';
				}
				else // jeden artykuł
				{
					$social_buttons = str_replace(array('{{_url_}}', '{{_title_}}'), array($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], $title), $soc_buttons);

					$result .= '<div class="article">';
					$result .= '<div class="article-title">';
					$result .= '<h3>' . $title . '</h3>';
					$result .= '</div>';
					$result .= '<div class="article-timestamp">';
					$result .= '<img src="img/16x16/user.png" />' . '<a href="index.php?route=users&action=view&id='.$user_id.'">' . $user_login. '</a>';
					$result .= '<img src="img/16x16/date.png" />' . $modified;
					$result .= '<img src="img/16x16/web.png" />' . $previews;
					$result .= $social_buttons;
					$result .= '</div>';
					$result .= '<div class="article-content">';
					if (is_array($contents))
					{
						$result .= '<ul>';
						foreach ($contents as $element)
						{
							foreach ($element as $key => $value)
							{
								if ($key == 'caption') $caption = $value;
								if ($key == 'link') $link = $value;
							}
							$result .= '<li>'.'<a href="'.$link.'">'.$caption.'</a>'.'</li>';
						}
						$result .= '</ul>';
					}
					else
					{
						$result .= $contents;
					}					
					$result .= '</div>';
					$result .= '</div>';

					// Comments Panel:
					if ($data[0]['comments_panel_visible'])
					{
						if (count($data[0]['comments']))
						{
							// Comments List:
							$result .= '<div class="article-comments-title">Komentarze:</div>';
							$result .= '<div class="article-comments-list">';
							foreach ($data[0]['comments'] as $comment)
							{
								foreach ($comment as $key => $value)
								{
									if ($key == 'ip') $ip = $value;
									if ($key == 'user_id') $user_id = $value;
									if ($key == 'user_login') $user_login = $value;
									if ($key == 'comment_content') $comment_content = $value;
									if ($key == 'send_date') $send_date = $value;
								}
								$result .= '<div class="article-comments-header">';
								$result .= '<img src="img/16x16/user.png" />' . '<a href="index.php?route=users&action=view&id='.$user_id.'">' . $user_login. '</a>';
								$result .= '<img src="img/16x16/date.png" />' . $send_date;
								$result .= '<img src="img/16x16/web.png" />' . $ip;
								$result .= '</div>';
								$result .= '<div class="article-comments-content">';
								$result .= '<p>' . $comment_content . '</p>';
								$result .= '</div>';
							}
							$result .= '</div>';
						}
						if ($data[0]['logged_in'])
						{
							// Comment Form:
							$result .= '<div class="article-comment">';
							$result .= $this->ShowCommentForm($data[0]['category_id']);
							$result .= '</div>';
						}
						else
						{
							// Login required for sending comments:
							$result .= '<div class="article-comments-info">';
							$result .= 'Aby napisać komentarz, musisz być zalogowany. <a href="index.php?route=login">Zaloguj się.</a>';
							$result .= '</div>';
						}
					}
				}
				$result .= '</td></tr>';
			}
			
			$result .= '</table>';
			
			$articles_per_page = intval($data[0]['articles_per_page']) > 1 ? intval($data[0]['articles_per_page']) : 4;

			if ($data[0]['articles_pagination_enabled'])
			{
				$result .= '<div id="pageNavPosition"></div>';
				$result .= '
					<script type="text/javascript">
						var pager = new Pager("results", '.$articles_per_page.');
						pager.init();
						pager.showPageNav("pager", "pageNavPosition");
						pager.showPage(0);
					</script>
				';
			}
		}

		return $result;
	}

	private function ShowCommentForm($id)
	{
		include GENER_DIR . 'form.php';

		$form_object = new FormBuilder();

		$form_title = 'Napisz komentarz';
		$form_image = 'img/32x32/list_edit.png';
		$form_width = '50%';
		
		$form_object->init($form_title, $form_image, $form_width);

		$form_action = 'index.php?route=' . MODULE_NAME . '&action=comment&id=' . $id;

		$form_object->set_action($form_action);

		$form_inputs = array(
			array(
				'caption' => 'Treść', 
				'data' => array(
					'type' => 'textarea', 'id' => 'contents', 'name' => 'contents', 'rows' => 5, 'value' => NULL, 'required' => 'required',
					),
				),
			);

		$form_object->set_inputs($form_inputs);
		
		$form_hiddens = array();
			
		$form_object->set_hiddens($form_hiddens);

		$form_buttons = array(
			array(
				'type' => 'submit', 'id' => 'submit', 'name' => 'submit', 'value' => 'Wyślij',
				),
			);
		
		$form_object->set_buttons($form_buttons);

		$result = $form_object->build_form();

		return $result;
	}
}

?>
