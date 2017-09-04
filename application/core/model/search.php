<?php

class Search_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = 'pages';
	}

	public function GetResults($user_status, $search_text)
	{
		$this->rows_list = array();

		$this->Store($search_text); // rejestruje wyszukiwanie

		$search_text = str_replace(' ', '%', $search_text);
		$search_text = '%' . $search_text . '%';
		$visible = 1;
		$free = FREE;

		try
		{
			$query = 	"SELECT pages.title, pages.description, pages.contents, pages.id," .
						" CONCAT(users.user_login, ' @ ', pages.modified)" . 
						" FROM " . $this->table_name . 
						" INNER JOIN users ON users.id = pages.author_id" .
						" LEFT JOIN categories ON categories.id = " . $this->table_name . ".category_id" .
						" WHERE (pages.contents LIKE :search_text" .
						" OR pages.description LIKE :search_text".
						" OR pages.title LIKE :search_text)".
						" AND pages.visible = :visible" . 
						" AND (permission >= :user_status OR permission = :free)" .
						" ORDER BY title";

			$statement = $this->db->prepare($query);

			$statement->bindValue(':search_text', $search_text, PDO::PARAM_STR);
			$statement->bindValue(':visible', $visible, PDO::PARAM_INT);
			$statement->bindValue(':user_status', $user_status, PDO::PARAM_INT);
			$statement->bindValue(':free', $free, PDO::PARAM_INT);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}

	private function Store($search_text)
	{
		$search_time = date("Y-m-d H:i:s");

		try
		{
			$query = 	'INSERT INTO searches' .
						' (agent, user_ip, search_text, search_time) VALUES' .
						' (:agent, :user_ip, :search_text, :search_time)';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':agent', $_SERVER['HTTP_USER_AGENT'], PDO::PARAM_STR); 
			$statement->bindValue(':user_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR); 
			$statement->bindValue(':search_text', $search_text, PDO::PARAM_STR); 
			$statement->bindValue(':search_time', $search_time, PDO::PARAM_STR); 
			
			$statement->execute();
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
	}
}

?>
