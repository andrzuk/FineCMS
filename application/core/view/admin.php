<?php

class Admin_View extends View
{
	public function __construct($page)
	{
		parent::__construct($page);
	}

	public function ShowPage($data, $user)
	{
		$result = NULL;

		foreach ($data as $i => $j)
		{
			$item = NULL;
			$count = 0;
			
			foreach ($j as $k => $v)
			{
				if ($k == 'group')
				{
					$group = $v;

					$item .= '<div class="GroupName">';
					$item .= $group;
					$item .= '</div>';
				}
				if ($k == 'elements')
				{
					$item .= '<div class="GroupElements">';
					$item .= '<table align="center">';
					$item .= '<tr>';

					foreach ($v as $kk => $vv)
					{
						foreach ($vv as $key => $value)
						{
							if ($key == 'profile') $profile = $value;
							if ($key == 'caption') $caption = $value;
							if ($key == 'link') $link = $value;
							if ($key == 'icon') $icon = $value;
						}
						if ($user->get_value('user_status') <= $profile)
						{
							$item .= '<td class="GroupElement">';
							$item .= '<a href="'.$link.'">';
							$item .= '<p>';
							$item .= '<img src="img/48x48/'.$icon.'" alt="" />';
							$item .= '</p>';
							$item .= '<p>';
							$item .= $caption;
							$item .= '</p>';
							$item .= '</a>';
							$item .= '</td>';
							$count++;
						}
					}

					$item .= '</tr>';
					$item .= '</table>';
					$item .= '</div>';
				}
			}
			if ($count)
			{
				$result .= $item;
			}
		}

		return $result;
	}
}

?>
