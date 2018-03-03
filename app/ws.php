<?php
define("LOCALHOST", "http://127.0.0.1/gloria/");

include_once('Player.php');
include_once('Event.php');
include_once('MediaFile.php');
include_once('Verse.php');
include_once('DatabaseManager.php');
include_once('Playlist.php');
include_once('Authentication.php');

$dbCon = new DatabaseManager();
$auth = new Authentication($dbCon);
if(isset($_POST['object']))
{
	switch($_POST['object'])
	{
		case 'player':
			Player::HandleRequest("", $dbCon);
			break;
		case 'event':
			Event::HandleRequest("", $dbCon);
			break;
		case 'verse':
			Verse::HandleRequest("", $dbCon);
			break;
		case 'media':
			MediaFile::HandleRequest("", $dbCon);
			break;
		case 'playlist':
			Playlist::HandleRequest("", $dbCon);
			break;
		case 'sync':
			if(isset($_POST['type']) && $_POST['type'] == "json") {
				generateJSONFile($_POST['player_id'], $dbCon);
			} else {
				GeenerateXMLFile($_POST['player_id'], $dbCon);	
			}
			break;
	}
}

function generateJSONFile($playerID, $dbCon) 
{
	$data = array();
	$data['id'] = $playerID;

	$_POST['request'] = 'getmediajson';
	$resp = array();
	$resp['videos'] = MediaFile::HandleRequest($data, $dbCon);

	$_POST['request'] = 'getversejson';
	$resp['verses'] = Verse::HandleRequest($data, $dbCon);


	echo json_encode($resp);
}

function GeenerateXMLFile($playerID, $dbCon)
{
	$data = array();
	$data['id'] = $playerID;

	// generate XML
	$_POST['request'] = 'getmediaxml';
	$mediaXML = MediaFile::HandleRequest($data, $dbCon);

	header ("content-type: text/xml");

	$_POST['request'] = 'geteventxml';
	$eventXML = Event::HandleRequest($data, $dbCon);

	$_POST['request'] = 'getversexml';
	$verseXML = Verse::HandleRequest($data, $dbCon);

	$bannerXML = "<banner>$eventXML $verseXML</banner>\n";

	echo "<scene>" . $mediaXML . $bannerXML . "</scene>";

}

?>