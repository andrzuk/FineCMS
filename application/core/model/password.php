<?php

class Password_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'users';
	}

	public function Reset($login, $email)
	{
		$this->row_item = NULL;

		$id = 0;
		$login = substr($login, 0, 32);
		$email = substr($email, 0, 64);
		$date_time = date("Y-m-d H:i:s");

		try
		{
			$query = 	'SELECT * FROM ' . $this->table_name . 
						' WHERE user_login = :login' .
						' AND email = :email' .
						' AND active = 1';

			$statement = $this->db->prepare($query);

			$statement->bindParam(':login', $login, PDO::PARAM_STR);
			$statement->bindParam(':email', $email, PDO::PARAM_STR);
			
			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			if ($this->row_item)
			{
				$id = $this->row_item['id'];

				// ustawia nowe hasło i rejestruje datę i czas modyfikacji usera:
				
				$length = 8;
				$code = md5(uniqid(rand(), true));
				$phrase = substr($code, 0, $length);
				$password = sha1($phrase);
				$this->row_item['new_password'] = $phrase;

				$query =	'UPDATE ' . $this->table_name .
							' SET user_password = :password, modified = :date_time' .
							' WHERE id = :id';
	
				$statement = $this->db->prepare($query);
			
				$statement->bindParam(':password', $password, PDO::PARAM_STR);
				$statement->bindParam(':date_time', $date_time, PDO::PARAM_STR);
				$statement->bindParam(':id', $id, PDO::PARAM_INT);
			
				$statement->execute();
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function Send($data, $message_options)
	{
		$new_password = $data['new_password'];

		foreach ($message_options as $key => $value)
		{
			if ($key == 'base_domain') $base_domain = $value;
			if ($key == 'email_sender_name') $email_sender_name = $value;
			if ($key == 'email_sender_address') $email_sender_address = $value;
			if ($key == 'email_remindpwd_subject') $email_remindpwd_subject = $value;
			if ($key == 'email_remindpwd_body_1') $email_remindpwd_body_1 = $value;
			if ($key == 'email_remindpwd_body_2') $email_remindpwd_body_2 = $value;
		}

		// wysyła e-maila do usera z nowym hasłem:
		$recipient = $data['email'];
		$mail_body = "Szanowny użytkowniku,\n\n" . $email_remindpwd_body_1 . "\n\n login: <b>". $data['user_login']. "</b>\n hasło: <b>". $new_password . "</b>\n\n" . $email_remindpwd_body_2 . "\n\nPozdrawiamy,\n\n" . $base_domain . "\n";
		$subject = $email_remindpwd_subject;
		$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
		$header = "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
		$mail_body = $this->convert_to_html($subject, $mail_body);
		mail($recipient, $subject, $mail_body, $header);
	}
}

?>