<?php

define(LOCALHOST, "http://localhost/gloria/");
$logout = "<div style=\"float: right; margin-right: 30px\"><a href=\"?request=logout\"><b>Logout</b></a></div>";

include_once('Authentication.php');
include_once('DatabaseManager.php');

$dbCon = new DatabaseManager();
$auth = new Authentication($dbCon);
$error = "";

$content = "<form id=\"AddUserForm\" name=\"AddUserForm\" action=\"AddUser.php\" method=\"POST\">
		<input type=\"hidden\" id=\"request\"  name=\"request\" value=\"adduser\"><br/>
		<input type=\"text\" id=\"username\"  name=\"username\" placeholder=\"Username\" value=\"\"><br/>
		<input type=\"password\" id=\"password1\"  name=\"password1\" placeholder=\"Password\" value=\"\"><br/>
		<input type=\"password\" id=\"password2\"  name=\"password2\" placeholder=\"Retype password\" value=\"\"><br/>
		<input type=\"text\" id=\"firstname\"  name=\"firstname\" placeholder=\"Firstname\" value=\"\"><br/>
		<input type=\"text\" id=\"lastname\"  name=\"lastname\" placeholder=\"Lastname\" value=\"\"><br/>
		<input type=\"text\" id=\"email\"  name=\"email\" placeholder=\"Email\" value=\"\"><br/>	
		<input class=\"button\" type=\"submit\"  value=\"Add User\">
	</form>";

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
		$title = basename($page, ".htm");
		$fh = fopen($page, 'r');
		$content = fread($fh, filesize($page));
		fclose($fh);

	}
}
else if(isset($_POST['request']) && $_POST['request'] == 'adduser')
{
	if(isset($_POST['username']) && $_POST['username'] == '')
	{
		$error .= "Please enter your username<br/>";
	}
	else if(isset($_POST['password1']) && isset($_POST['password2']) && ($_POST['password1'] == '' || $_POST['password1'] != $_POST['password2']))
	{
		$error .= "Please enter valid password<br/>";
	}
	else if(isset($_POST['firstname']) && $_POST['firstname'] == '')
	{
		$error .= "Please enter your firstname<br/>";
	}
	else if(isset($_POST['lastname']) && $_POST['lastname'] == '')
	{
		$error .= "Please enter your lastname<br/>";
	}
	else if(isset($_POST['email']) && $_POST['email'] == '')
	{
		$error .= "Please enter your email<br/>";
	}
	else
	{
		$error = $auth->AddUser($_POST['username'],$_POST['password1'],$_POST['firstname'],$_POST['lastname'],$_POST['email']);
		
		if($error == "") $content = "User is added successfully";
	}

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
				<div class="menuItem"><a href="index.php?page=rsc/Player.htm">Player</a></div>
				<div class="menuItem"><a href="index.php?page=rsc/Media.htm">Media</a></div>
				<div class="menuItem"><a href="index.php?page=rsc/Event.htm">Event</a></div>
				<div class="menuItem"><a href="index.php?page=rsc/Verse.htm">Verse</a></div>
				<div style="clear: both"></div>
			</div>
			<div class="mainContent">
				<div class="box">
					<?php echo $content; ?>
					
					<div style="font-size: 90%; color: #ff3333; padding-top: 5px;">
					<?php echo $error; ?>
					</div>

				</div>
				<div style="clear: both"></div>
			</div>
			<div style="clear: both"></div>
		</div>
		
		<div style="clear: both"></div>
	</body>
</html>