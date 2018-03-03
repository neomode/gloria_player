<?php

define("LOCALHOST", "http://192.168.7.20/gloria/");
$logout = "<div style=\"float: right; margin-right: 30px\"><a href=\"?request=logout\"><b>Logout</b></a></div>";

include_once('Player.php');
include_once('Event.php');
include_once('Verse.php');
include_once('MediaFile.php');
include_once('Authentication.php');
include_once('DatabaseManager.php');

$dbCon = new DatabaseManager();
$auth = new Authentication($dbCon);

$page = (isset($_GET['page']) && file_exists($_GET['page'])) ? $_GET['page'] : 'rsc/Player.htm';

if(isset($_GET['request']) && $_GET['request'] == 'logout')
{
	$auth->Logout();
	header("Location: index.php");
}
else if(!$auth->IsAuthenticated())
{
	if(isset($_POST['username']) && isset($_POST['username']))
	{
		$result = $auth->Authenticate($_POST['username'], $_POST['password']);
	}

	if(!$result) {
		$logout = "";
		$page = 'rsc/Login.htm';
	}
}


$title = basename($page, ".htm");
$fh = fopen($page, 'r');
$content = fread($fh, filesize($page));
fclose($fh);

if($page == 'rsc/Player.htm')
{
	$content = Player::HandleRequest($content, $dbCon);
}
else if($page == 'rsc/Event.htm')
{
	$content = Event::HandleRequest($content, $dbCon);
}
else if($page == 'rsc/Verse.htm')
{
	$content = Verse::HandleRequest($content, $dbCon);
}
else if($page == 'rsc/Media.htm')
{
	$content = MediaFile::HandleRequest($content, $dbCon);
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>GKA Gloria CMS - <?php echo $title; ?></title>
	<link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 
	<script src="js/scriptaculous-js-1.9.0/lib/prototype.js" type="text/javascript"></script>
	<script src="js/scriptaculous-js-1.9.0/src/scriptaculous.js" type="text/javascript"></script>
	<script src="js/coniques/main.js" type="text/javascript"></script>
	<link href="css/main.css" rel="stylesheet" type="text/css" /> 
</head>

	<body id="">
	<!--iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe-->
		<div id="center_content" style="height: 100%">
			<div id="logo">
				<div style="float: left">
					<div  style="float: left"><img src="img/glorialogo.png"/> </div>
					<div style="float: left; padding-left: 1em;">
						<div class="logoText1" >GKA Gloria Satelit</div>
						<div class="logoText2" >Signage Content Management System</div>
					</div>
					<div style="clear: both"></div>
				</div>
				<?php echo $logout; ?>
				<div style="clear: both"></div>
			</div>
			<div id="menuBar">
				<div class="menuItem"><a href="?page=rsc/Player.htm">Player</a></div>
				<div class="menuItem"><a href="?page=rsc/Media.htm">Media</a></div>
				<div class="menuItem"><a href="?page=rsc/Event.htm">Event</a></div>
				<div class="menuItem"><a href="?page=rsc/Verse.htm">Verse</a></div>
				<div style="clear: both"></div>
			</div>
			<div class="mainContent">
				<div class="box">
					<?php echo $content; ?>

				</div>
				<div style="clear: both"></div>
			</div>
			<div style="clear: both"></div>
		</div>
		
		<div style="clear: both"></div>
	</body>
</html>