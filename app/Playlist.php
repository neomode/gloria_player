<?php

class Playlist
{
	private $param;
    private $dbCon;
     
	public function __construct($id, $player_id, $type, $content, $dbCon)
	{
		$this->param = Array();

		$this->param['id'] = $id;
		$this->param['player_id'] = $player_id;
		$this->param['type']= $type;
		$this->param['content'] = $content;
		$this->dbCon = $dbCon;

		if($this->param['id'] == -1)
			$this->InsertPlaylistToDB();
	}
     
	private function InsertPlaylistToDB()
	{
		if($this->param['id'] == -1)
		{
			$query = "INSERT INTO  `gloria`.`playlist` (
						`id` ,
						`player_id` ,
						`type` ,
						`content` 
						)
						VALUES (
						NULL ,  '" . mysql_escape_string($this->param['player_id']) . "',  '" . 
							mysql_escape_string($this->param['type']) . "',  '" . 
							mysql_escape_string($this->param['content']) . "'
						);";
			// echo $query;
			$result = mysql_query($query, $this->dbCon->GetCon()) 
						or die("Error inserting playlist to DB: " . mysql_error());
			$this->id = mysql_insert_id($this->dbCon->GetCon());
		}
	}

	public function GetHTML()
	{
	
	}

	public function GetParam($type = "")
	{
		//print_r($this->param);
		if($type == "")
			return $this->param;
		else
			return $this->param[$type];
	}

     
	public function UpdatePlaylist($type, $value)
	{
		$query = "UPDATE gloria.playlist SET $type = '" . mysql_escape_string($value) . "' WHERE id='" . $this->param['id'] . "'";
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error updating playlist to DB: " . mysql_error());

		$this->param[$type] = $value;
	}

	public function DeletePlaylist()
	{
		$query = "DELETE FROM gloria.playlist WHERE id=" . $this->param['id'];
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error deleting playlist from DB: " . mysql_error());
	}

	public static function GetAllPlaylist($filter, $dbCon)
	{
		$query = "SELECT * FROM gloria.playlist $filter  ";
		
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error obtaining all playlist: " . mysql_error());

		$allPlaylist = array();
		while($result_ar = mysql_fetch_assoc($result))
		{
			$playlist = new Playlist($result_ar['id'],
							$result_ar['player+id'],
							$result_ar['type'],
							$result_ar['content'],
							$dbCon);
			array_push($allPlaylist, $playlist);
		}

		return $verses;
	}
     
	public static function GetPlaylist($id, $dbCon)
	{
		$query = "SELECT * FROM gloria.playlist WHERE id = $id";
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error getting playlist from DB: " . mysql_error());

		if($result_ar = mysql_fetch_assoc($result))
		{
			return new Playlist($result_ar['id'],
							$result_ar['player_id'],
							$result_ar['type'],
							$result_ar['content'],
							$dbCon);
		}
	}

	public static function GetPlaylistByPlayerID($playerID, $type, $dbCon)
	{
		$query = "SELECT * FROM gloria.playlist WHERE player_id = $playerID AND type='$type'";
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error getting playlist from DB: " . mysql_error());

		if($result_ar = mysql_fetch_assoc($result))
		{
			return new Playlist($result_ar['id'],
							$result_ar['player_id'],
							$result_ar['type'],
							$result_ar['content'],
							$dbCon);
		}
	}

	public static function HandleRequest($content, $dbCon)
	{
		if(isset($_POST['request']))
		{
			$resp = array();
			switch($_POST['request'])
			{
				case 'addplaylist':
					$verse = new Verse(-1, $_POST['player_id'], $_POST['type'], $content, $dbCon);
					$resp['result'] = 1;
					$resp['request'] = $_POST['request'];

					//$resp['data'] = $verse->GetHTML();

					echo json_encode($resp);
					break;
				case 'addtoplaylist':
					$playlist  = Playlist::GetPlaylist($_POST['id'], $dbCon);
					if(is_object($playlist))
					{
						$playlist->GetParam('content');
						$content .= ($content == "") ? $_POST['value'] : "," . $_POST['value'];
						$playlist->UpdatePlaylist('content', $content);
						$resp['result'] = 1;
					}
					else
					{
						$resp['result'] = 0;
					}

					

					$resp['request'] = $_POST['request'];
					$resp['id'] = $_POST['id'];
					echo json_encode($resp);

					break;
				case 'updateplaylist';
					$content = implode(',', $_POST['organizer']);
					$playlist  = Playlist::GetPlaylistByPlayerID($_POST['player_id'], $_POST['type'], $dbCon);
					if(!$playlist) {
						$playlist = new Playlist(-1, $_POST['player_id'], $_POST['type'], $content, $dbCon);
					} else {
						$playlist->UpdatePlaylist($_POST['param'], $content);	
					}
					

					$resp['result'] = 0;
					$resp['data'] = $playlist->GetParam($_POST['param']);

					echo json_encode($resp);
					break;
				case 'deleteplaylist':
					$playlist = Playlist::GetPlaylist($_POST['id'], $dbCon);
					if(is_object($playlist))
					{
						$playlist->DeletePlaylist();
						$resp['result'] = 1;
					}
					else
					{
						$resp['result'] = 0;
					}

					
					$resp['request'] = $_POST['request'];

					$resp['id'] = $_POST['id'];

					echo json_encode($resp);
					break;
			}
		}
		else
		{
			$html = Playlist::GetAllPlaylistHTML($dbCon);
			return $html;
		}
	}

	public static function GetAllPlaylistHTML($dbCon)
	{
		$html = "";
		$allPlaylist = Verse::GetVerses("", $dbCon);
		foreach($allPlaylist as $playlist)
		{
			$html .= $playlist->GetHTML();
		}
		return $html;
	}
}

?>