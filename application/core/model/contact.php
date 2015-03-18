<?php

class Contact_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'user_messages';
	}

	public function GetIntro()
	{
		$this->row_item = array();

		$main_page = 0;
		$system_page = 1;
		$visible = 1;

		try
		{
			$query = 	'SELECT * FROM pages' .
						' WHERE main_page = :main_page AND system_page = :system_page AND visible = :visible' .
						' ORDER BY id DESC LIMIT 0, 1';
			
			$statement = $this->db->prepare($query);

			$statement->bindValue(':main_page', $main_page, PDO::PARAM_INT); 
			$statement->bindValue(':system_page', $system_page, PDO::PARAM_INT); 
			$statement->bindValue(':visible', $visible, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function Receive($record, $sendme, $message_options)
	{
		if (!parent::check_required($record)) return NULL;

		$login = $record['login'];
		$email = $record['email'];
		$contents = $record['contents'];

		foreach ($message_options as $key => $value)
		{
			if ($key == 'send_new_message_report') $send_new_message_report = $value;
			if ($key == 'base_domain') $base_domain = $value;
			if ($key == 'email_sender_name') $email_sender_name = $value;
			if ($key == 'email_sender_address') $email_sender_address = $value;
			if ($key == 'email_report_address') $email_report_address = $value;
			if ($key == 'email_report_subject') $email_report_subject = $value;
			if ($key == 'email_report_body_1') $email_report_body_1 = $value;
			if ($key == 'email_report_body_2') $email_report_body_2 = $value;
		}

		if ($send_new_message_report == 'true')
		{
			// wysyła e-mailem informację do admina o napisaniu wiadomosci przez usera:
			$recipient = $email_report_address;
			$mail_body = $email_report_body_1 ."\n\nUżytkownik {".$login."} (e-mail: ".$email.") napisał do serwisu wiadomość:\n\n\"".$contents."\"\n\n".$base_domain."\n";
			$subject = $email_report_subject;
			$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
			$header = "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
			$mail_body = $this->convert_to_html($subject, $mail_body);
			mail($recipient, $subject, $mail_body, $header);
		}

		if ($sendme)
		{
			// wysyła e-mailem kopie wiadomosci do autora:
			$recipient = $email;
			$mail_body = "Drogi Użytkowniku,\n\nPodając się jako {".$login."} napisałe(a)ś do serwisu wiadomość:\n\n\"".$contents."\"\n\nBardzo dziękujemy.\n\n".$base_domain."\n";
			$subject = $email_report_subject;
			$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
			$header = "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
			$mail_body = $this->convert_to_html($subject, $mail_body);
			mail($recipient, $subject, $mail_body, $header);
		}

		$result = $this->Store($login, $email, $contents); // rejestruje wiadomość

		return $result;
	}

	private function Store($login, $email, $contents)
	{
		$inserted_id = 0;

		$send_date = date("Y-m-d H:i:s");
		$close_date = '2000-01-01 12:00:00';
		$requested = 1;

		try
		{
			$query = 	'INSERT INTO ' . $this->table_name .
						' (client_ip, client_name, client_email, message_content, requested, send_date, close_date) VALUES' .
						' (:client_ip, :client_name, :client_email, :message_content, :requested, :send_date, :close_date)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':client_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR); 
			$statement->bindValue(':client_name', $login, PDO::PARAM_STR); 
			$statement->bindValue(':client_email', $email, PDO::PARAM_STR); 
			$statement->bindValue(':message_content', $contents, PDO::PARAM_STR); 
			$statement->bindValue(':requested', $requested, PDO::PARAM_STR); 
			$statement->bindValue(':send_date', $send_date, PDO::PARAM_STR); 
			$statement->bindValue(':close_date', $close_date, PDO::PARAM_STR); 
			
			$statement->execute();

			$inserted_id = $this->db->lastInsertId();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $inserted_id;
	}
}

?>
