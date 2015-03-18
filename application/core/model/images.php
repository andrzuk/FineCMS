<?php

class Images_Model extends Model
{
	private $table_name;

	public function __construct($db)
	{
		parent::__construct($db);
		
		$this->table_name = MODULE_NAME;
	}

	public function GetAll()
	{
		$this->rows_list = array();

		$condition = NULL;

		$fields_list = array('file_name');

		$filter = empty($_SESSION['list_filter']) ? NULL : $this->make_filter($fields_list);

		try
		{
			$query = 	'SELECT ' . $this->table_name . '.id, NULL AS preview, file_format, file_name,' .
						' file_size, picture_width, picture_height, user_login, ' . $this->table_name . '.modified' .
						' FROM ' . $this->table_name . 
						' INNER JOIN users ON users.id = ' . $this->table_name . '.owner_id' .
						' WHERE 1' . $condition . $filter .
						' ORDER BY ' . $this->list_params['sort_field'] . ' ' . $this->list_params['sort_order'] . 
						' LIMIT ' . $this->list_params['start_from'] . ', ' . $this->list_params['show_rows'];

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$this->rows_list = $statement->fetchAll(PDO::FETCH_ASSOC);

			$this->GetCount($query);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->rows_list;
	}

	public function GetOne($id)
	{
		$this->row_item = array();

		try
		{
			$query =	'SELECT NULL AS preview, ' . $this->table_name . '.id, file_name, file_format,' .
						' file_size, picture_width, picture_height, user_login, ' . $this->table_name . '.modified' .
						' FROM ' . $this->table_name .
						' INNER JOIN users ON users.id = ' . $this->table_name . '.owner_id' .
						' WHERE ' . $this->table_name . '.id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $this->row_item;
	}

	public function Add($records)
	{
		$inserted_id = 0;

		try
		{
			$query =	'INSERT INTO ' . $this->table_name .
						' (owner_id, file_format, file_name, file_size, picture_width, picture_height, modified) VALUES' .
						' (:owner_id, :file_format, :file_name, :file_size, :picture_width, :picture_height, :modified)';

			$statement = $this->db->prepare($query);

			foreach ($records as $k => $v)
			{
				if ($k == 'owner_id') $owner_id = $v;
				if ($k == 'modified') $modified = $v;
				if ($k == 'files')
				{
					foreach ($v as $i => $j) 
					{
						foreach ($j as $key => $value) 
						{
							if ($key == 'name') $name = $value;
							if ($key == 'type') $type = $value;
							if ($key == 'tmp_name') $tmp_name = $value;
							if ($key == 'error') $error = $value;
							if ($key == 'size') $size = $value;
						}
						if ($size && !$error)
						{
							if (substr($type, 0, 5) == 'image') // plik graficzny
							{
								list($picture_width, $picture_height) = getimagesize($tmp_name); 

								$statement->bindValue(':owner_id', $owner_id, PDO::PARAM_INT); 
								$statement->bindValue(':file_format', $type, PDO::PARAM_STR); 
								$statement->bindValue(':file_name', $name, PDO::PARAM_STR); 
								$statement->bindValue(':file_size', $size, PDO::PARAM_INT); 
								$statement->bindValue(':picture_width', $picture_width, PDO::PARAM_INT); 
								$statement->bindValue(':picture_height', $picture_height, PDO::PARAM_INT); 
								$statement->bindValue(':modified', $modified, PDO::PARAM_STR); 
								
								$statement->execute();
			
								$inserted_id = $this->db->lastInsertId();

								// zapisuje oryginalny obrazek na serwer:
								move_uploaded_file($tmp_name, GALLERY_DIR . IMG_DIR . $inserted_id);
							}
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

	public function Save($id, $record)
	{
		$affected_rows = 0;

		try
		{
			foreach ($record as $k => $v)
			{
				if ($k == 'new_name') $new_name = $v;
				if ($k == 'owner_id') $owner_id = $v;
				if ($k == 'modified') $modified = $v;
				if ($k == 'files')
				{
					foreach ($v as $i => $j) 
					{
						foreach ($j as $key => $value) 
						{
							if ($key == 'name') $name = $value;
							if ($key == 'type') $type = $value;
							if ($key == 'tmp_name') $tmp_name = $value;
							if ($key == 'error') $error = $value;
							if ($key == 'size') $size = $value;
						}
						if ($size && !$error) // zmieniany jest obrazek
						{
							if (substr($type, 0, 5) == 'image') // plik graficzny
							{
								list($picture_width, $picture_height) = getimagesize($tmp_name); 

								$query =	'UPDATE ' . $this->table_name .
											' SET owner_id = :owner_id, file_format = :file_format, file_name = :file_name,' .
											' file_size = :file_size, picture_width = :picture_width, picture_height = :picture_height, modified = :modified' .
											' WHERE id = :id';

								$statement = $this->db->prepare($query);

								$statement->bindValue(':id', $id, PDO::PARAM_INT); 
								$statement->bindValue(':owner_id', $owner_id, PDO::PARAM_INT); 
								$statement->bindValue(':file_format', $type, PDO::PARAM_STR); 
								$statement->bindValue(':file_name', $name, PDO::PARAM_STR); 
								$statement->bindValue(':file_size', $size, PDO::PARAM_INT); 
								$statement->bindValue(':picture_width', $picture_width, PDO::PARAM_INT); 
								$statement->bindValue(':picture_height', $picture_height, PDO::PARAM_INT); 
								$statement->bindValue(':modified', $modified, PDO::PARAM_STR); 
								
								$statement->execute();
			
								$affected_rows = $statement->rowCount();

								// zapisuje oryginalny obrazek na serwer:
								move_uploaded_file($tmp_name, GALLERY_DIR . IMG_DIR . $id);
							}
						}
						else // zmieniana jest nazwa pliku
						{
							$query =	'UPDATE ' . $this->table_name .
										' SET owner_id = :owner_id, file_name = :file_name, modified = :modified' .
										' WHERE id = :id';

							$statement = $this->db->prepare($query);

							$statement->bindValue(':id', $id, PDO::PARAM_INT); 
							$statement->bindValue(':owner_id', $owner_id, PDO::PARAM_INT); 
							$statement->bindValue(':file_name', $new_name, PDO::PARAM_STR); 
							$statement->bindValue(':modified', $modified, PDO::PARAM_STR); 
							
							$statement->execute();
		
							$affected_rows = $statement->rowCount();
						}
					}
				}
			}
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $affected_rows;
	}

	public function Delete($id)
	{
		$affected_rows = 0;

		try
		{
			$query =	'DELETE FROM ' . $this->table_name .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 
			
			$statement->execute();
			
			$affected_rows = $statement->rowCount();

			// usuwa plik z dysku serwera:

			$delete_result = unlink(GALLERY_DIR . IMG_DIR . $id);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $affected_rows;
	}

	public function Download($id)
	{
		try
		{
			$query =	'SELECT file_name, file_format FROM ' . $this->table_name .
						' WHERE id = :id';

			$statement = $this->db->prepare($query);
			
			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();
			
			$this->row_item = $statement->fetch(PDO::FETCH_ASSOC);

			$file_name = $this->row_item['file_name'];
			$file_format = $this->row_item['file_format'];

			$file_name = str_replace(" ", "_", $file_name);
			
			$picture_name = GALLERY_DIR . IMG_DIR . $id;
			
			// wczytuje plik z serwera:
			$fp = fopen($picture_name, 'rb');
			$image_data = fread($fp, filesize($picture_name));
			fclose($fp);
			
			// wysyła plik do przeglądarki:
			header('Content-disposition: attachment; filename='. $file_name);
			header('Content-type: '. $file_format .'; charset=utf-8');
			
			// wysyła dane:
			if (IsSet($image_data)) echo $image_data;
			
			// przerywa, aby nie dołączać treści strony:
			die;
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}
	}

	public function GetImages()
	{
		$rows_result = array();

		try
		{
			$query = 	'SELECT * FROM images ORDER BY id';

			$statement = $this->db->prepare($query);

			$statement->execute();
			
			$rows_result = $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			die ($e->getMessage());
		}

		return $rows_result;
	}
}

?>
