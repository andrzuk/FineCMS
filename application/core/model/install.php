<?php

class Install_Model extends Model
{
	public function __construct($db)
	{
		parent::__construct($db);
	}

	public function Save($record, $script)
	{
		$inserted_id = 0;

		try
		{
			// sprawdza, czy istnieja constrainty:
			$query = "SELECT COUNT(*) AS licznik FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' AND TABLE_SCHEMA = '". DB_NAME ."'";
			$statement = $this->db->prepare($query);			
			$statement->execute();
			$check_item = $statement->fetch(PDO::FETCH_ASSOC);
			$constraints_exist = $check_item['licznik'];

			foreach($script as $k => $v)
			{
				foreach($v as $key => $val)
				{
					if (is_array($val))
					{
						foreach($val as $i => $query)
						{
							// jesli nie ma constraintow, omija sekcje:
							if ($key == 'drop_constraints' && $constraints_exist == 0) continue;

							$statement = $this->db->prepare($query);
							
							if (strstr($query, 'INSERT INTO `configuration`'))
							{
								$statement->bindValue(':main_title', $record['main_title'], PDO::PARAM_STR); 
								$statement->bindValue(':main_description', $record['main_description'], PDO::PARAM_STR); 
								$statement->bindValue(':main_keywords', $record['main_keywords'], PDO::PARAM_STR); 
								$statement->bindValue(':short_title', $record['short_title'], PDO::PARAM_STR); 
								$statement->bindValue(':base_domain', $record['base_domain'], PDO::PARAM_STR); 
								$statement->bindValue(':email_sender_address', $record['email_sender_address'], PDO::PARAM_STR); 
								$statement->bindValue(':email_admin_address', $record['email_admin_address'], PDO::PARAM_STR); 
								$statement->bindValue(':email_report_address', $record['email_report_address'], PDO::PARAM_STR); 
								$statement->bindValue(':save_time', $record['save_time'], PDO::PARAM_STR); 
							}
							if (strstr($query, 'INSERT INTO `pages`'))
							{
								$statement->bindValue(':main_description', $record['main_description'], PDO::PARAM_STR); 
								$statement->bindValue(':save_time', $record['save_time'], PDO::PARAM_STR); 
							}
							if (strstr($query, 'INSERT INTO `users`'))
							{
								$statement->bindValue(':admin_login', $record['admin_login'], PDO::PARAM_STR); 
								$statement->bindValue(':admin_password', $record['admin_password'], PDO::PARAM_STR); 
								$statement->bindValue(':first_name', $record['first_name'], PDO::PARAM_STR); 
								$statement->bindValue(':last_name', $record['last_name'], PDO::PARAM_STR); 
								$statement->bindValue(':email_admin_address', $record['email_admin_address'], PDO::PARAM_STR); 
								$statement->bindValue(':save_time', $record['save_time'], PDO::PARAM_STR); 
							}

							$statement->execute();

							$last_id = $this->db->lastInsertId();

							if ($last_id) $inserted_id = $last_id;
						}
					}
				}
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $inserted_id;
	}
}

?>
