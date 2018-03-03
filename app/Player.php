<?php

include_once("Communicator.php");

class Player
{
	private $param;
    private $dbCon;
     
	public function __construct($id, $name, $ip, $reboot, $restart, $sync, $health, $last_checkedin, $dbCon)
	{
		$this->param = Array();

		$this->param['id'] = $id;
		$this->param['name'] = $name;
		$this->param['ip']= $ip;
		$this->param['reboot']= $reboot;
		$this->param['restart']= $restart;
		$this->param['sync']= $sync;
		$this->param['health']= $health;
		$this->param['last_checked_in'] = $last_checkedin;


		$this->dbCon = $dbCon;

		if($this->param['id'] == -1)
			$this->InsertPlayerToDB();
	}
     
	private function InsertPlayerToDB()
	{
		if($this->param['id'] == -1)
		{
			$query = "INSERT INTO  `gloria`.`player` (
						`id` ,
						`name` ,
						`ip`,
						`reboot`,
						`restart`,
						`sync`,
						`health`,
						`last_checked_in`
						)
						VALUES (
						NULL ,  '" . mysql_escape_string($this->param['name']) . "',  '" . 
							mysql_escape_string($this->param['ip']) . "', '" .
							mysql_escape_string($this->param['reboot']) . "',  '" . 
							mysql_escape_string($this->param['restart']) . "',  '" . 
							mysql_escape_string($this->param['sync']) . "', '" .
							mysql_escape_string($this->param['health']) . "', ''
						);";

			$result = mysql_query($query, $this->dbCon->GetCon()) 
						or die("Error inserting player to DB: " . mysql_error());
			$this->id = mysql_insert_id($this->dbCon->GetCon());
		}
	}

	public function GetParam($type = "")
	{
		//print_r($this->param);
		if($type == "")
			return $this->param;
		else
			return $this->param[$type];
	}

	public function SendRemoteCommand($cmd)
	{
		$communicator = new Communicator($this->param['ip'], 10000, 30);

		$timestamp = time();
		$md5sum = md5($timestamp . "|&" . $this->param['id']);
		return $communicator->Send($md5sum . "|" . $timestamp . "|" . $cmd);
	}
     
	public function UpdatePlayer($type, $value)
	{
		$query = "UPDATE gloria.player SET $type = '" . mysql_escape_string($value) . "' WHERE id='" . $this->param['id'] . "'";

		if($type == 'last_checked_in')
			$query = "UPDATE gloria.player SET $type = now() WHERE id='" . $this->param['id'] . "'";
		
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error updating player to DB: " . mysql_error());

		$this->param[$type] = $value;
	}

	public function GetHTML()
	{
		$id = $this->param['id'];
		$health = "&nbsp;";

		$status = ($this->param['reboot'] == 1) ? "Reboot Required<br/>" : "";
		$status .= ($this->param['restart'] == 1) ? "Restart Required<br/>" : "";
		$status .= ($this->param['sync'] == 1) ? "Sync Required" : "";
		$status = ($this->param['last_checked_in'] == "") ? "&nbsp;" : $this->param['last_checked_in'];

		if($this->param['last_checked_in'] != "")
		{
			$timestamp = strtotime($this->param['last_checked_in']);
			$health = (time() - $timestamp) - (7 *60 *60);

			if($health > 500)
				$health = "<div class=\"badHealth\">&nbsp;</div>";
			else
				$health = "<div class=\"goodHealth\">&nbsp;</div>";
		}
		$script = "<script>
						/*new Ajax.InPlaceEditor('name_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=name&id=$id&value=' + encodeURIComponent(value) + '&object=player&request=updateplayer' }
						});
						*/
						new Ajax.InPlaceEditor('ip_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=ip&id=$id&value=' + encodeURIComponent(value) + '&object=player&request=updateplayer' }
						});
						
						</script>";

		return "<div class=\"player\">
					<div class=\"player_row name\" ><div id=\"name_$id\">" . $this->param['name'] . "</div></div>
					<div class=\"player_row ip\"> <div id=\"ip_$id\">" . $this->param['ip'] . "</div></div>
					<div class=\"player_row status\">$status</div>
					<div class=\"player_row health\">$health</div>
					<div class=\"player_row opt\">
						<input class=\"button\" type=\"button\"  onclick=\"RestartPlayer($id);\" value=\"Restart Player\">
						<input class=\"button\" type=\"button\"  onclick=\"RebootMachine($id);\" value=\"Reboot\">
						<input class=\"button\" type=\"button\"  onclick=\"ShutdownMachine($id);\" value=\"Shutdown\">
						<!--input class=\"button\" type=\"button\"  onclick=\"\" value=\"Sync\"-->
					</div>
					<div style=\"clear: both\"></div>
				</div>$script";

	}

	public function DeletePlayer($id)
	{
		$query = "DELETE FROM gloria.player WHERE id=$id";
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error deleting player to DB: " . mysql_error());
	}

	public static function GetPlayers($filter, $dbCon)
	{
		$query = "SELECT * FROM gloria.player $filter  ";
		
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error obtaining players: " . mysql_error());

		$players = array();
		while($result_ar = mysql_fetch_assoc($result))
		{
			$player = new Player($result_ar['id'],
							$result_ar['name'],
							$result_ar['ip'],
							$result_ar['reboot'],
							$result_ar['restart'],
							$result_ar['sync'],
							$result_ar['health'],
							$result_ar['last_checked_in'],
							$dbCon);
			array_push($players, $player);
		}

		return $players;
	}
     
	public static function GetPlayer($id, $dbCon)
	{
		$query = "SELECT * FROM gloria.player WHERE id = $id";
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error getting player from DB: " . mysql_error());

		if($result_ar = mysql_fetch_assoc($result))
		{
			return new Player($result_ar['id'],
							$result_ar['name'],
							$result_ar['ip'],
							$result_ar['reboot'],
							$result_ar['restart'],
							$result_ar['sync'],
							$result_ar['health'],
							$result_ar['last_checked_in'],
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
				case 'addplayer':
					$player = new Player(-1, $_POST['name'], $_POST['ip'], 0, 0, 0, 0, $dbCon);
					$resp['result'] = 1;
					$resp['request'] = 'addplayer';
					$resp['data'] = $player->GetHTML();

					echo json_encode($resp);
					break;
				case 'updateplayer':
					$player = Player::GetPlayer($_POST['id'], $dbCon);
					$player->UpdatePlayer($_POST['param'], $_POST['value']);
					echo $player->GetParam($_POST['param']);
					break;
				case 'remotecommand':
					$player = Player::GetPlayer($_POST['id'], $dbCon);
					$data = $player->SendRemoteCommand($_POST['cmd']);

					$resp['request'] = $_POST['request'];
					$resp['data'] = $data;
					echo json_encode($resp);
					break;
			}
		}
		else
		{
			$playerHTML = Player::GetAllPlayerHTML($dbCon);
			

			return str_replace(array("[CONTENT]"), 
								array($playerHTML),
								$content);
		}
	}

	public static function GetAllPlayerHTML($dbCon)
	{
		$playerHTML = "";
		$players = Player::GetPlayers("", $dbCon);
		foreach($players as $player)
		{
			$playerHTML .= $player->GetHTML();
		}
		return $playerHTML;
	}
}

?>