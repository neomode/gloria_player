<?php

include_once("Shared.php");
include_once("Playlist.php");

class MediaFile
{
	private $param;
    private $dbCon;
     
	public function __construct($id, $name, $src, $duration, $md5sum, $dbCon)
	{
		$this->param = Array();

		$this->param['id'] = $id;
		$this->param['duration'] = $duration;
		$this->param['name']= $name;
		$this->param['src'] = $src;

		$this->param['md5sum'] = ($md5sum == "") ? md5_file($this->param['src']) : $md5sum;
		$this->dbCon = $dbCon;

		if($this->param['id'] == -1)
			$this->InsertMediaToDB();
	}
     
	private function InsertMediaToDB()
	{
		if($this->param['id'] == -1)
		{
			$query = "INSERT INTO  `gloria`.`media_file` (
						`id` ,
						`name` ,
						`src` ,
						`duration`,
						`md5sum`
						)
						VALUES (
						NULL ,  '" . mysql_escape_string($this->param['name']) . "',  '" . 
							mysql_escape_string($this->param['src']) . "', '" . 
							mysql_escape_string($this->param['duration']) . "', '" .
							mysql_escape_string($this->param['md5sum']) . "'
						);";

			$result = mysql_query($query, $this->dbCon->GetCon()) 
						or die("Error inserting media to DB: $query " . mysql_error());
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

     
	public function UpdateMediaFile($type, $value)
	{
		$query = "UPDATE gloria.`media_file` SET $type = '" . mysql_escape_string($value) . "' WHERE id=" . $this->param['id'];
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error updating `media_file` to DB: " . mysql_error());
		$this->param[$type] = $value;
	}

	public function DeleteMediaFile()
	{
		$query = "DELETE FROM gloria.`media_file` WHERE id=" . $this->param['id'];
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error deleting `media_file` to DB: " . mysql_error());
		unlink($this->param['src']);
	}

	public static function GetMediaFiles($filter, $dbCon)
	{
		$query = "SELECT * FROM gloria.`media_file` $filter  ";
		
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error obtaining `media_file`: " . mysql_error());

		$medias = array();
		while($result_ar = mysql_fetch_assoc($result))
		{
			$media = new MediaFile($result_ar['id'],
							$result_ar['name'],
							$result_ar['src'],
							$result_ar['duration'],
							$result_ar['md5sum'],
							$dbCon);
			array_push($medias, $media);
		}

		return $medias;
	}

	public function GetXML()
	{
		return "\t<item name=\"" . htmlentities($this->param['name'], ENT_COMPAT , "UTF-8" ) . "\" src=\"" . str_replace("player_rsc/", "", htmlentities($this->param['src'])) . "\" md5sum=\"" . md5_file($this->param['src']). "\"/>\n";
	}

	public function GetJSON()
	{
		$name = htmlentities($this->param['name'], ENT_COMPAT , "UTF-8" );
		$src = str_replace("player_rsc/", "", htmlentities($this->param['src']));
		$resp = array();
		$resp["name"] = $name;
		$resp["src"] = $src;
		return $resp;
	}

	public function GetHTML()
	{
		$id = $this->param['id'];
		$duration = "";
		if($this->param['duration'] != "" && $this->param['duration'] != 0)
		{
			$duration = "<div class=\"\" >
							<div style=\"float: left; padding-right: 5px;\">Duration: </div>
							<div style=\"float: left\"><div id=\"duration_$id\" >" . $this->param['duration'] . "</div></div>
							<div style=\"float: left; padding-left: 5px\"> seconds</div>
							<div style=\"clear: both\"></div>
						</div>
						<script>
							new Ajax.InPlaceEditor('duration_$id', 'ws.php', {
								cancelControl: 'button',
								highlightcolor: '#f5f5f5',
								highlightendcolor: '#f5f5f5',
								savingText: 'saving ...',
								callback: function(form, value) { return 'param=duration&id=$id&value=' + encodeURIComponent(value) + '&object=media&request=updatemedia' }
							});
						</script>";
		}

		$script = "<script>
						/*new Draggable('media_$id', {
							revert: true,
							handle: 'drag_icon'});*/

						new Ajax.InPlaceEditor('name_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=name&id=$id&value=' + encodeURIComponent(value) + '&object=media&request=updatemedia' }
						});

						
					</script>";

		return "<div class=\"media\" id=\"media_$id\">
					<div class=\"media_row\">
						<div class=\"media_name\"><div id=\"name_$id\">" . $this->param['name'] . "</div></div>
						$duration
					</div>
					<div class=\"del_col delete\" id=\"deleteButton_$id\">
						<input class=\"button deleteButton\" type=\"button\"  onclick=\"DeleteMediaFile($id)\" value=\"Delete\">
						<input class=\"button\" type=\"button\"  onclick=\"window.open('" . $this->param['src'] . "')\" value=\"Download\">
						<input class=\"button\" type=\"button\"  onclick=\"AddToPlaylist($id, '" . $this->param['name'] . "')\" value=\"Add\">
					</div>
					<div style=\"clear: both\"></div>
					
				</div>$script";
	}

	public function GetHTMLPlaylist()
	{
		$id = $this->param['id'];
		
		return "<li id=\"pl_" . $id . "\"><div class=\"media_pl\" >" .
								"<div style=\"float: left\"><div class=\"media_name\">" . $this->param['name']  . "</div></div>" .
								"<div style=\"float: right; padding-left: 10px; position: relative; top: -0.5em\"><span class=\"sort_icon\"><img src=\"img/drag.png\"></span></div>" .
								"<div id=\"deleteButton_" . $id . "\" style=\"float: right; padding-left: 10px; position: relative; top: -0.25em\"><input class=\"button deleteButton\"  type=\"button\"  onclick=\"DeleteFromPlaylist(this)\" value=\"Remove\"></div>" .	"<div style=\"clear: both\"></div>" .
							"</div></li>";
	}
     
	public static function GetMediaFile($id, $dbCon)
	{
		$query = "SELECT * FROM gloria.`media_file` WHERE id = $id";
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error getting media_file from DB $query: " . mysql_error());

		if($result_ar = mysql_fetch_assoc($result))
		{
			return  new MediaFile($result_ar['id'],
							$result_ar['name'],
							$result_ar['src'],
							$result_ar['duration'],
							$result_ar['md5sum'],
							$dbCon);
		}
	}

	 // There must be FILES variables exist before calling this function
     public static function ProcessFileUpload($maxFile, $userID, $dbCon)
     {
		 // create directory if it doesn't exist
         $targetVideo = "player_rsc/video_files/";
		 $targetImage = "player_rsc/image_files/";
		 if(!file_exists($targetVideo)) mkdir($targetVideo, 0755, true);
		 if(!file_exists($targetImage)) mkdir($targetImage, 0755, true);

		 //time stamp the filename
         $fileIDs = "";
         $timestamp = date('Ymdhis');
         
         for($i = 0; $i < $maxFile; $i++ )
         {
             $filename = explode(".", basename($_FILES['media-file' . $i]['name']));
             
			 $target = (preg_match("/jpg/", $filename[1])) ? $targetImage : $targetVideo;

			 if($filename[0])
             {
				$target_path = $target . Shared::FormatFilename($filename[0]) . "_$timestamp." . $filename[sizeof($filename) - 1];
                if(move_uploaded_file($_FILES['media-file' . $i]['tmp_name'], 
                                      $target_path)) 
                {
                      $file = new MediaFile(-1, 
                                  $_POST['name'], 
                                  $target_path,
                                  $_POST['duration'],
								  "", 
								$dbCon);
                      
					  $fileID = $file->GetParam('id');
                }
                else
                {
					die("Failed to upload. Oops, this is embarassing!");
					break;
                }
             }
         }

         return ;
     }


	public static function HandleRequest($content, $dbCon)
	{
		if(isset($_POST['request']))
		{
			$resp = array();
			switch($_POST['request'])
			{
				case 'addmedia':
					echo "here";
					$content = MediaFile::ProcessFileUpload(1, "", $dbCon);
					header("Location: index.php?page=rsc/Media.htm");
					break;
				case 'updatemedia';
					$file = MediaFile::GetMediaFile($_POST['id'], $dbCon);
					$file->UpdateMediaFile($_POST['param'], $_POST['value']);
					echo $file->GetParam($_POST['param']);
					break;
					break;
				case 'deletemedia':
					$file = MediaFile::GetMediaFile($_POST['id'], $dbCon);
					if(is_object($file))
					{
						$file->DeleteMediaFile();
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
				case 'getmediaxml':
					$playlist = Playlist::GetPlaylistByPlayerID($content['id'], "media", $dbCon);
					if(is_object($playlist))
					{
						$content = $playlist->GetParam('content');
						$contentList = explode(",", $content);
						
						$xml = "";
						foreach($contentList as $mediaID)
						{
							if($mediaID != "")
							{
								$media = MediaFile::GetMediaFile($mediaID, $dbCon);
								if(is_object($media))
								{
									$xml .= $media->GetXML();
									$newContent = ($newContent == "") ? $mediaID : "," . $mediaID;
								}
							}
						}
						return "<video enabled=\"1\">\n" . $xml . "</video>\n";
					}
					break;
				case 'getmediajson':
					$playlist = Playlist::GetPlaylistByPlayerID($content['id'], "media", $dbCon);
					$mediaList = array();
					if(is_object($playlist))
					{
						$content = $playlist->GetParam('content');
						$contentList = explode(",", $content);
						
						foreach($contentList as $mediaID)
						{
							if($mediaID != "")
							{
								$media = MediaFile::GetMediaFile($mediaID, $dbCon);
								if(is_object($media))
								{
									$json = $media->GetJSON();
									array_push($mediaList, $json);
									$newContent = ($newContent == "") ? $mediaID : "," . $mediaID;
								}
							}
						}
						return $mediaList;
					}
					break;
			}
		}
		else
		{
			$html = MediaFile::GetAllMediaHTML($dbCon);
			
			// TODO: currently hard coded for one player
			$playlist = MediaFile::GetPlaylistHTML(1, $dbCon);

			return str_replace(array("[ASSETS]", "[PLAYLIST]"), 
								array($html, $playlist),
								$content);
		}
	}

	public static function GetPlaylistHTML($id, $dbCon)
	{
		$html = "";
		$playlist = Playlist::GetPlaylistByPlayerID($id, "media", $dbCon);
		if(is_object($playlist))
		{
			$content = $playlist->GetParam('content');
			$contentList = explode(",", $content);
			$newContent = "";
			foreach($contentList as $mediaID)
			{
				if($mediaID)
				{
					$media = MediaFile::GetMediaFile($mediaID, $dbCon);
					if(is_object($media))
					{
						$html .= $media->GetHTMLPlaylist();
						$newContent = ($newContent == "") ? $mediaID : "," . $mediaID;
					}
				}
			}
			//$playlist->UpdatePlaylist("content", $newContent);
		}

		return $html;
	}

	public static function GetAllMediaHTML($dbCon)
	{
		$html = "";
		$medias = MediaFile::GetMediaFiles("", $dbCon);
		foreach($medias as $media)
		{
			$html .= $media->GetHTML();
		}
		return $html;
	}
}

?>