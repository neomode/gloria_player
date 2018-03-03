<?php

class Event
{
	private $param;
    private $dbCon;
    
	public static $MONTH = array("Januari", "Februari", "Maret", "April", "Mei",
							"Juni", "Juli", "Agustus", "September", "Oktober",
							"November", "Desember");

	public function __construct($id, $duration, $day, $month, $year, $hour, $coordinator, $theme, $speaker, $place, $dbCon)
	{
		$this->param = Array();

		$this->param['id'] = $id;
		$this->param['duration'] = $duration;
		$this->param['day']= $day;
		$this->param['month'] = $month;
		$this->param['year'] = $year;
		$this->param['hour'] = $hour;

		$fmt_day = $day;
		if(preg_match("/\d+\-\d+/", $day))
		 {
			$param = explode("-", $day);
			$fmt_day = $param[0];
		 }

		$this->param['datetime'] = $year . "-" . $month . "-" . $fmt_day . " " . $hour;
		$this->param['coordinator'] = $coordinator;
		$this->param['theme'] = $theme;
		$this->param['speaker'] = $speaker;
		$this->param['place'] = $place;
		$this->dbCon = $dbCon;

		if($this->param['id'] == -1)
			$this->InsertEventToDB();
	}
     
	private function InsertEventToDB()
	{
		if($this->param['id'] == -1)
		{
			$query = "INSERT INTO  `gloria`.`event` (
					`id` ,
					`duration` ,
					`day` ,
					`month` ,
					`year` ,
					`hour` ,
					`datetime` ,
					`coordinator` ,
					`theme` ,
					`speaker` ,
					`place`
					)
					VALUES (
					NULL ,  '" . mysql_escape_string($this->param['duration']) . "',  '" . mysql_escape_string($this->param['day']) . "',  '" . 
						mysql_escape_string($this->param['month']) . "',  '" . mysql_escape_string($this->param['year']) . "',  '" . 
						mysql_escape_string($this->param['hour']) . "',  '" . mysql_escape_string($this->param['datetime']) . "',  '" . 
						mysql_escape_string($this->param['coordinator']) . "',  '" . mysql_escape_string($this->param['theme']) . "',  '" . 
						mysql_escape_string($this->param['speaker']) . "',  '" . mysql_escape_string($this->param['place']) . "'
					);
					";
			$result = mysql_query($query, $this->dbCon->GetCon()) 
						or die("Error inserting event to DB: " . mysql_error());
			$this->id = mysql_insert_id($this->dbCon->GetCon());
		}
	}

	public function GetHTML()
	{
		$id = $this->param['id'];
		$coordinator = ($this->param['coordinator'] != "") ? $this->param['coordinator'] : "&nbsp;";
		$speaker = ($this->param['speaker'] != "") ? $this->param['speaker'] : "&nbsp;";
		$theme = ($this->param['theme'] != "") ? $this->param['theme'] : "&nbsp;";
		$place = ($this->param['place'] != "") ? $this->param['place'] : "&nbsp;";


			$script = "<script>
						new Ajax.InPlaceEditor('coordinator_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=coordinator&id=$id&value=' + encodeURIComponent(value) + '&object=event&request=updateevent' }
						});
						
						new Ajax.InPlaceEditor('speaker_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=speaker&id=$id&value=' + encodeURIComponent(value) + '&object=event&request=updateevent' }
						});

						new Ajax.InPlaceEditor('theme_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=theme&id=$id&value=' + encodeURIComponent(value) + '&object=event&request=updateevent' }
						});

						new Ajax.InPlaceEditor('place_$id', 'ws.php', {
							cancelControl: 'button',
							highlightcolor: '#f5f5f5',
							highlightendcolor: '#f5f5f5',
							savingText: 'saving ...',
							callback: function(form, value) { return 'param=place&id=$id&value=' + encodeURIComponent(value) + '&object=event&request=updateevent' }
						});
						
						</script>";

		$updateDateTimeHTML = $this->GetUpdateDatetimeHTML();


		return "<div class=\"event\" id=\"event_$id\">
					<div class=\"event_row coordinator\"><div id=\"coordinator_$id\">".  $coordinator . "</div></div>
					<div class=\"event_row speaker\"><div id=\"speaker_$id\">". $speaker . "</div></div>
					<div class=\"event_row theme\"><div id=\"theme_$id\">". $theme . "</div></div>
					<div class=\"event_row place\"><div id=\"place_$id\">". $place . "</div></div>
					<div class=\"event_row datetime\" id=\"datetime_col_$id\">". $updateDateTimeHTML  . "</div>
					<div class=\"event_row delete\" id=\"deleteButton_$id\"><input class=\"button deleteButton\" type=\"button\"  onclick=\"DeleteEvent('". $this->param['id'] . "')\" value=\"Remove\"></div>
					<div style=\"clear: both\"></div>
				</div> $script";
	}

	public function GetUpdateDatetimeHTML()
	{
		$id = $this->param['id'];
		$now = new DateTime();
		
		$hour = ($this->param['hour'] != "") ? $this->param['hour'] . ", " : $this->param['hour'];

		$year = "";
		for($i = 0; $i < 2; $i++)
		{
			$selected = ((int)($this->param['year']) == $now->format('Y')) ? "selected=\"yes\"" : "";
			$year .= "<option value=\"" . $now->format('Y') . "\" $selected>" . $now->format('Y') . "</option>\n";
			$now->modify("+1 year");
		}

		$month = "";
		for($i = 0; $i < sizeof(Event::$MONTH); $i++)
		{
			$selected = (((int)$this->param['month'] - 1) == $i) ? "selected=\"yes\"" : "";
			$month .= "\t<option value=\"" . ($i + 1) . "\"  $selected>" . Event::$MONTH[$i] . "</option>\n";
		}

		$updateDateTimeHTML = " <div id=\"datetime_$id\" class=\"datetime_edit\" onclick=\"ShowDatetimeEditor('$id')\">" . 
									$hour . $this->param['day'] . " " . Event::$MONTH[$this->param['month']-1] . " " . $this->param['year'] . 
								"</div>
								<div id=\"datetime_editor_$id\"  style=\"display: none\">
									<input style=\"width:80%\" type=\"text\" id=\"hour_$id\"  name=\"hour_$id\" placeholder=\"Hour\" value=\"" . $this->param['hour'] . "\"><br/>
									<input style=\"width:80%\" type=\"text\" id=\"day_$id\"  name=\"day_$id\" placeholder=\"Day\" value=\"" . $this->param['day'] . "\">
									<select id=\"month_$id\" name=\"month_$id\" style=\"width:80%\">
										$month
									</select><br/>
									<select id=\"year_$id\" name=\"year_$id\" style=\"width:80%\">
										$year
									</select>
								<input class=\"button\" type=\"button\"  onclick=\"javascript:UpdateEventDateTime('$id');\" value=\"ok\">
								<input class=\"button\" type=\"button\"  onclick=\"javascript:Cancel($id);\" value=\"cancel\"></div>
								<div id=\"loader_$id\" style=\"display: none\"><img src=\"img/ajax-loader.gif\"></div>";
		return $updateDateTimeHTML;
	}

	public function GetXML()
	{
		return "\t<item  duration=\"" . htmlentities($this->param['duration'], ENT_COMPAT , "UTF-8" ) . "\"  day=\"" . 
										htmlentities($this->param['day'], ENT_COMPAT , "UTF-8" ) . "\" month=\"" . 
										htmlentities($this->param['month'], ENT_COMPAT , "UTF-8" ) . 
					"\" year=\"" .		htmlentities($this->param['year'], ENT_COMPAT , "UTF-8" ) . "\" hour=\"" . 
										htmlentities($this->param['hour'], ENT_COMPAT , "UTF-8" ) . "\" coordinator=\"" . 
										htmlentities($this->param['coordinator'], ENT_COMPAT , "UTF-8" ) . "\" theme=\"" . 
										htmlentities($this->param['theme'], ENT_COMPAT , "UTF-8" ) . 
					"\" speaker=\"" .	htmlentities($this->param['speaker'], ENT_COMPAT , "UTF-8" ) . "\" place=\"" . 
										htmlentities($this->param['place'], ENT_COMPAT , "UTF-8" ) . "\"/>\n";

	}

	public function GetParam($type = "")
	{
		//print_r($this->param);
		if($type == "")
			return $this->param;
		else
			return $this->param[$type];
	}

     
	public function UpdateEvent($type, $value)
	{
		$query = "UPDATE gloria.event SET $type = '" . mysql_escape_string($value) . "' WHERE id='" . $this->param['id'] . "'";
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error updating event to DB: " . mysql_error());

		$this->param[$type] = $value;
	}

	public function DeleteEvent()
	{
		$query = "DELETE FROM gloria.event WHERE id='" . $this->param['id'] . "';";
		$result = mysql_query($query, $this->dbCon->GetCon()) 
					or die("Error deleting event to DB $query: " . mysql_error());
	}

	public static function GetEvents($filter, $dbCon)
	{
		$query = "SELECT * FROM gloria.event $filter  ORDER by datetime";
		
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error obtaining events: " . mysql_error());

		$events = array();
		while($result_ar = mysql_fetch_assoc($result))
		{
			$event = new Event($result_ar['id'],
							$result_ar['duration'],
							$result_ar['day'],
							$result_ar['month'],
							$result_ar['year'],
							$result_ar['hour'],
							$result_ar['coordinator'],
							$result_ar['theme'],
							$result_ar['speaker'],
							$result_ar['place'],
							$dbCon);
			array_push($events, $event);
		}

		return $events;
	}
     
	public static function GetEvent($id, $dbCon)
	{
		$query = "SELECT * FROM gloria.event WHERE id = $id";
		$result = mysql_query($query, $dbCon->GetCon()) 
					or die("Error getting event from DB: " . mysql_error());

		if($result_ar = mysql_fetch_assoc($result))
		{
			return new Event($result_ar['id'],
							$result_ar['duration'],
							$result_ar['day'],
							$result_ar['month'],
							$result_ar['year'],
							$result_ar['hour'],
							$result_ar['coordinator'],
							$result_ar['theme'],
							$result_ar['speaker'],
							$result_ar['place'],
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
				case 'addevent':
					$event = new Event(-1, $_POST['duration'], $_POST['day'], $_POST['month'], $_POST['year'], $_POST['hour'], 
										$_POST['coordinator'], $_POST['theme'], $_POST['speaker'], $_POST['place'], 
										$dbCon);
					$resp['result'] = 1;
					$resp['request'] = $_POST['request'];

					$resp['data'] = $event->GetHTML();

					echo json_encode($resp);
					break;
				case 'updateevent';
					$event = Event::GetEvent($_POST['id'], $dbCon);
					$event->UpdateEvent($_POST['param'], $_POST['value']);
					echo $event->GetParam($_POST['param']);
					break;
					break;
				case 'deleteevent':
					$event = Event::GetEvent($_POST['id'], $dbCon);
					if(is_object($event))
					{
						$event->DeleteEvent();
						$resp['result'] = 1;
					}
					else
					{
						$resp['result'] = 0;
					}

					
					$resp['request'] = $_POST['request'];

					$resp['data'] = $html;
					$resp['id'] = $_POST['id'];

					echo json_encode($resp);
					break;
				case 'updatedateevent':
					$id = $_POST['id'];
					$event = Event::GetEvent($_POST['id'], $dbCon);
					if(is_object($event))
					{
						$event->UpdateEvent('hour', $_POST["hour_$id"]);
						$event->UpdateEvent('day', $_POST["day_$id"]);
						$event->UpdateEvent('month', $_POST["month_$id"]);
						$event->UpdateEvent('year', $_POST["year_$id"]);
						$fmt_day = $day = $_POST["day_$id"];
						$param = "";
						if(preg_match("/\d+\-\d+/", $day))
						 {
							$param = explode("-", $day);
							$fmt_day = $param[0];
						 }

						$datetime = $_POST["year_$id"] . "-" . $_POST["month_$id"] . "-" . $fmt_day . " " . $hour;
						
						$event->UpdateEvent('datetime', $datetime);
						$resp['result'] = 1;
					}
					else
					{
						$resp['result'] = 0;
					}

					$resp['id'] = $_POST['id'];
					$resp['request'] = $_POST['request'];

					$resp['data'] = $event->GetUpdateDatetimeHTML();			

					echo json_encode($resp);
					break;
				case 'geteventxml':
					$xml = "";
					$events = Event::GetEvents("WHERE datetime >= now()", $dbCon);
					foreach($events as $event)
					{
						$xml .= $event->GetXML();
					}
					return "<event>\n" . $xml . "</event>\n";
			}
		}
		else
		{
			$html = Event::GetAllEventHTML($dbCon);
			
			$now = new DateTime();
			$year = "";
			for($i = 0; $i < 2; $i++)
			{
				$year .= "<option value=\"" . $now->format('Y') . "\">" . $now->format('Y') . "</option>\n";
				$now->modify("+1 year");
			}

			$month = "";
			for($i = 0; $i < sizeof(Event::$MONTH); $i++)
			{
				$month .= "\t<option value=\"" . ($i + 1) . "\">" . Event::$MONTH[$i] . "</option>\n";
			}

			return str_replace(array("[CONTENT]", "[YEAR]", "[MONTH]"), 
								array($html, $year, $month),
								$content);
		}
	}

	public static function GetAllEventHTML($dbCon)
	{
		$html = "";
		$events = Event::GetEvents("WHERE datetime >= now()", $dbCon);
		foreach($events as $event)
		{
			$html .= $event->GetHTML();
		}
		return $html;
	}
}

?>