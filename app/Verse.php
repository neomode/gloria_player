<?php

class Verse
{
	private $param;
    private $dbCon;
     
	public function __construct($id, $title, $src, $content, $duration, $dbCon)
	{
		$this->param = Array();

		$this->param['id'] = $id;
		$this->param['duration'] = $duration;
		$this->param['title']= $title;
		$this->param['src'] = $src;
		$this->param['content'] = $content;
		$this->dbCon = $dbCon;

		if($this->param['id'] == -1)
			$this->InsertVerseToDB();
	}
     
	private function InsertVerseToDB()
	{
		if($this->param['id'] == -1)
		{
			$query = "INSERT INTO  `gloria`.`verse` (
						`id` ,
						`title` ,
						`src` ,
						`content` ,
						`duration`
						)
						VALUES (
						NULL ,  '" . mysql_escape_string($this->param['title']) . "',  '" . 
							mysql_escape_string($this->param['src']) . "',  '" . 
							mysql_escape_string($this->param['content']) . "',  '" . 
							mysql_escape_string($this->param['duration']) . "'
						);";

			$result = mysql_query($query, $this->dbCon->GetCon()) 
						or die("Error inserting verse to DB: " . mysql_error());
			$this->id = mysql_insert_id($this->dbCon->GetCon());
		}
	}
	
	public function GetXML()
	{
		return "<item  duration=\"" . htmlentities($this->param['duration'], ENT_COMPAT , "UTF-8" ) . "\" src=\"" . htmlentities($this->param['src'], ENT_COMPAT , "UTF-8" ) . "\" content=\"" . htmlentities($this->param['content'], ENT_COMPAT , "UTF-8" ) . "\" title=\"" . htmlentities($this->param['title'], ENT_COMPAT , "UTF-8" ) . "\" />";
	}

	public function GetJSON()
	{
		$resp = array();
		$resp["duration"] = htmlentities($this->param['duration'], ENT_COMPAT , "UTF-8" );
		$resp["src"] = htmlentities($this->param['src'], ENT_COMPAT , "UTF-8" );
		$resp["content"] = $this->param['content'];
		$resp["title"] = htmlentities($this->param['title'], ENT_COMPAT , "UTF-8" );

		return $resp;
	}

	public function GetHTML()
	{
		$id = $this->param['id'];
		$content = ($this->param['content'] != "") ? $this->param['content'] : "&nbsp;";
		$src = ($this->param['src'] != "") ? $this->param['src'] : "&nbsp;";

		$script = "<script>
						new Ajax.InPlaceEditor('src_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=src&id=$id&value=' + encodeURIComponent(value) + '&object=verse&request=updateverse' }
						});
						
						new Ajax.InPlaceEditor('content_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							rows: 3,
							callback: function(form, value) { return 'param=content&id=$id&value=' + encodeURIComponent(value) + '&object=verse&request=updateverse' }
						});

						new Ajax.InPlaceEditor('duration_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=duration&id=$id&value=' + encodeURIComponent(value) + '&object=verse&request=updateverse' }
						});
						
						</script>";

		return "<div class=\"verse\" id=\"verse_$id\">
					<div class=\"verse_col\">
						<div class=\"verse_src\"><div id=\"src_$id\">" . $src. "</div></div>
						<div class=\"verse_content\"><div id=\"content_$id\">" . $content . "</div></div>
						<div class=\"\" ><div style=\"float: left; padding-right: 5px;\">Duration: </div><div style=\"float: left\"><div id=\"duration_$id\" >" . $this->param['duration'] . "</div></div><div style=\"float: left; padding-left: 5px\"> seconds</div><div style=\"clear: both\"></div></div>
					</div>
					<div class=\"del_col delete\" id=\"deleteButton_$id\">
						<input class=\"button deleteButton\" type=\"button\"  onclick=\"DeleteVerse('" . $this->param['id'] . "')\" value=\"Remove\">
					</div>
					<div style=\"clear: both\"></div>
				</div> $script";
	}

	public function GetParam($type = "")
	{
		//print_r($this->param);
		if($type == "")
			return $this->param;
		else
			return $this->param[$type];
	}

     
	public function UpdateVerse($type, $value)
	{
		$query = "UPDATE gloria.verse SET $type = '" . mysql_escape_string($value) . "' WHERE id='" . $this->param['id'] . "'";
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error updating verse to DB: " . mysql_error());

		$this->param[$type] = $value;
	}

	public function DeleteVerse()
	{
		$query = "DELETE FROM gloria.verse WHERE id=" . $this->param['id'];
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error deleting verse to DB: " . mysql_error());
	}

	public static function GetVerses($filter, $dbCon)
	{
		$query = "SELECT * FROM gloria.verse $filter ORDER BY id ";
		
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error obtaining verses: " . mysql_error());

		$verses = array();
		while($result_ar = mysql_fetch_assoc($result))
		{
			$verse = new Verse($result_ar['id'],
							$result_ar['title'],
							$result_ar['src'],
							$result_ar['content'],
							$result_ar['duration'],
							$dbCon);
			array_push($verses, $verse);
		}

		return $verses;
	}
     
	public static function GetVerse($id, $dbCon)
	{
		$query = "SELECT * FROM gloria.verse WHERE id = $id";
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error getting verse from DB: " . mysql_error());

		if($result_ar = mysql_fetch_assoc($result))
		{
			return new Verse($result_ar['id'],
							$result_ar['title'],
							$result_ar['src'],
							$result_ar['content'],
							$result_ar['duration'],
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
				case 'addverse':

					$xml = simplexml_load_file(LOCALHOST . "alkitab/?keyword=" . urlencode($_POST["src"]));
					if($xml['msg'] == 'ok')
					{
						$content = "";
						foreach($xml->item as $ayat)
						{
							$content .= $ayat . " ";
						}
					}									

					$verse = new Verse(-1, $_POST['title'], $_POST['src'], $content, $_POST['duration'], $dbCon);
					$resp['result'] = 1;
					$resp['request'] = $_POST['request'];

					$resp['data'] = $verse->GetHTML();

					echo json_encode($resp);
					break;
				case 'updateverse';
					$verse = Verse::GetVerse($_POST['id'], $dbCon);
					$verse->UpdateVerse($_POST['param'], $_POST['value']);
					echo $verse->GetParam($_POST['param']);
					break;
					break;
				case 'deleteverse':
					$event = Verse::GetVerse($_POST['id'], $dbCon);
					if(is_object($event))
					{
						$event->DeleteVerse();
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
				case 'getversexml':
					$xml = "";
					$verses = Verse::GetVerses("", $dbCon);
					foreach($verses as $verse)
					{
						$xml .= $verse->GetXML();
					}
					return "<verse title=\"Bible Verse\">" . $xml . "</verse>";
					break;
				case 'getversejson':
					$verseList = array();
					$verses = Verse::GetVerses("", $dbCon);
					foreach($verses as $verse)
					{
						$verseJSON = $verse->GetJSON();
						array_push($verseList, $verseJSON);
					}
					return $verseList;
			}
		}
		else
		{
			$html = Verse::GetAllVerseHTML($dbCon);
			
			
			return str_replace(array("[CONTENT]"), 
								array($html),
								$content);
		}
	}

	public static function GetAllVerseHTML($dbCon)
	{
		$html = "";
		$verses = Verse::GetVerses("", $dbCon);
		foreach($verses as $verse)
		{
			$html .= $verse->GetHTML();
		}
		return $html;
	}
}

?>