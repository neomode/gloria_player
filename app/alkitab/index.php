<?php

include_once("Verse.php");
include_once("DatabaseManager.php");

$dbCon = new DatabaseManager();

header("Content-type: text/xml");

if(isset($_GET['keyword'])) {
	$keyword = $_GET['keyword'];
	if(preg_match("/(.*) \d+/", $keyword))
		echo Verse::Search($keyword, $dbCon->GetAlkitabCon());	
	else
		echo Verse::SearchForWord($keyword, $dbCon->GetAlkitabCon());
}
else
	echo "<result msg=\"Please specify keyword\"/>";


?>