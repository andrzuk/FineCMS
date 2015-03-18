<?php

/*
 * Klasa odpowiedzialna za generowanie komunikatów
 */

class Message
{
	function __construct($type, $content)
	{
		if (!$type || !$content) return;
		
		$_SESSION['message']['type'] = $type;
		$_SESSION['message']['text'] = $content;
	}

	public function show_message_box()
	{
		$main_message_body = NULL;

		if (!isset($_SESSION['message'])) return NULL;
		
		switch ($_SESSION['message']['type'])
		{
			case MSG_ERROR:
				$alert_type = 'danger';
				break;
			case MSG_WARNING:
				$alert_type = 'warning';
				break;
			case MSG_INFORMATION:
				$alert_type = 'info';
				break;
			case MSG_QUESTION:
				$alert_type = 'success';
				break;
			default:
				$alert_type = 'default';
				break;
		}

		$main_message_body .= '<div class="alert alert-'. $alert_type .'" style="text-align: center;" role="alert">';
		$main_message_body .= $_SESSION['message']['text'];
		$main_message_body .= '</div>';

		unset($_SESSION['message']);

		return $main_message_body;
	}
}

?>
