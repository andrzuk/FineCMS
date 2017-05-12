<?php

class Page_View extends View
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
			foreach ($data as $key => $value)
			{
				if ($key == 'title') $title = $value;
				if ($key == 'contents') $contents = $value;
				if ($key == 'user_id') $user_id = $value;
				if ($key == 'user_login') $user_login = $value;
				if ($key == 'modified') $modified = $value;
				if ($key == 'previews') $previews = $value;
				if ($key == 'skip_bar') $skip_bar = $value;
				if ($key == 'skip_bar_visible') $skip_bar_visible = $value;
				if ($key == 'social_buttons') $social_buttons = $value;
				if ($key == 'social_buttons_visible') $social_buttons_visible = $value;
			}

			$social_buttons = str_replace(array('{{_url_}}', '{{_title_}}'), array($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], $title), $social_buttons);

			$result .= '<div class="article">';
			$result .= '<div class="article-title">';
			$result .= '<h3>' . $title . '</h3>';
			$result .= '</div>';
			$result .= '<div class="article-timestamp">';
			$result .= '<img src="img/16x16/user.png" />' . '<a href="index.php?route=users&action=view&id='.$user_id.'">' . $user_login. '</a>';
			$result .= '<img src="img/16x16/date.png" />' . $modified;
			$result .= '<img src="img/16x16/web.png" />' . $previews;
			if ($social_buttons_visible)
				$result .= $social_buttons;
			$result .= '</div>';
			if ($skip_bar_visible)
			{
				$result .= '<div class="article-skip">';
				$result .= '<span class="skip-left">';
				if ($skip_bar['prev'])
					$result .= '<a href="index.php?route=page&id='.$skip_bar['prev']['id'].'">« '.$skip_bar['prev']['caption'].'</a>';
				$result .= '</span>';
				$result .= '<span class="skip-right">';
				if ($skip_bar['next'])
					$result .= '<a href="index.php?route=page&id='.$skip_bar['next']['id'].'">'.$skip_bar['next']['caption'].' »</a>';
				$result .= '</span>';
				$result .= '</div>';
			}
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
			if ($data['comments_panel_visible'])
			{
				if (count($data['comments']))
				{
					// Comments List:
					$result .= '<div class="article-comments-title">Komentarze:</div>';
					$result .= '<div class="article-comments-list">';
					foreach ($data['comments'] as $comment)
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
				if ($data['logged_in'])
				{
					// Comment Form:
					$result .= '<div class="article-comment">';
					$result .= $this->ShowCommentForm($data['id']);
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
