<?php
include_once("String.php");
include_once("Shared.php");
class Verse {
	
	private static $versePattern = array(	
									"(.*) (\\d+)$",
									"(.*) (\\d+):(\\d+)$",
									"(.*) (\\d+):(\\d+)\\-(\\d+)$",
									"(.*) (\\d+):(\\d+)\\-(\\d+):(\\d+)$",
									"(.*) (\\d+)-(\\d+)$",
									"(.*) (\\d+)-(\\d+):(\\d+)$");
	
	public static function Search($keyword, $dbCon)
	{
	
		
		for($i = 0; $i < sizeof(Verse::$versePattern); $i++)
		{
			$pattern = Verse::$versePattern[$i];
			if(preg_match("/$pattern/", $keyword, $matches)) {
				$book = $matches[1];
				
				if(Verse::IsBookExist($book, $dbCon)) {
					$startChapter = $matches[2];
					if(Verse::IsChapterExist($book, $startChapter, $dbCon)) {
						switch($i) {
							case 0:
								return Verse::Search_1($book, $startChapter, $dbCon);
								break;
							case 1:
								$verse = $matches[3];
								return Verse::Search_2($book, $startChapter, $verse, $dbCon);
								break;
							case 2:
								$startVerse = $matches[3];
								$endVerse = $matches[4];
								return Verse::Search_3($book, $startChapter, $startVerse, $endVerse, $dbCon);
								break;
							case 3:
								$startVerse = $matches[3];
								$endChapter = $matches[4];
								$endVerse = $matches[5];
								return Verse::Search_4($book, $startChapter, $endChapter, $startVerse, $endVerse, $dbCon);
								break;
							case 4:
								$endChapter = $matches[3];
								return Verse::Search_4($book, $startChapter, $endChapter, 1, -1, $dbCon);
								break;
							case 5:
								$endChapter = $matches[3];
								$endVerse = $matches[4];
								return Verse::Search_4($book, $startChapter, $endChapter, 1, $endVerse, $dbCon);
								break;								
						}
					}
					else
						return "<result msg=\"" . String::$chapter_not_exist . "\" />";
				}
				else
					return "<result msg=\"" . String::$book_not_exist . "\" />";
				
				break;
			}
		}
	}
	
	public static function Search_1($book, $chapter, $dbCon) {
		$query = "SELECT verse, otherVerse, content FROM alkitab WHERE book='$book' AND chapter='$chapter' ORDER By verse";
		
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error searching\" />");
		
		if(mysql_num_rows($result) != 0) {
	
			$resp = "<result msg=\"ok\" book=\"$book\" startChapter=\"$chapter\">";
			$result_ar = mysql_fetch_assoc($result);
			while($result_ar) {
				
				$resp .= "<item verse=\"" . $result_ar['verse'] . "\" otherVerse=\"" . $result_ar['otherVerse'] . "\">" .
							Shared::PrepareXML($result_ar['content']) . "</item>";
				$result_ar = mysql_fetch_assoc($result);	
			}
			$resp .= "</result>";
			return $resp;
		}
			else return "<result msg=\"" . String::$verse_not_exist . "\"/>";
		
	}
	
	public static function Search_2($book, $chapter, $verse, $dbCon) {
		$query = "SELECT verse, otherVerse, content FROM alkitab WHERE book='$book' AND chapter='$chapter' AND verse='$verse'";
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error searching\" />");
		
		if(mysql_num_rows($result) != 0) {
	
			$resp = "<result msg=\"ok\" book=\"$book\" startChapter=\"$chapter\">";
			$result_ar = mysql_fetch_assoc($result);
			while($result_ar) {
				
				$resp .= "<item verse=\"" . $result_ar['verse'] . "\" otherVerse=\"" . $result_ar['otherVerse'] . "\">" .
							Shared::PrepareXML($result_ar['content']) . "</item>";
				$result_ar = mysql_fetch_assoc($result);	
			}
			$resp .= "</result>";
			return $resp;
		}
			else return "<result msg=\"" . String::$verse_not_exist . "\"/>";
	}
	
	public static function Search_3($book, $chapter, $startVerse, $endVerse, $dbCon) {
		$query = "SELECT verse, otherVerse, content FROM alkitab WHERE book='$book' AND chapter='$chapter' AND verse >= $startVerse AND verse <= $endVerse";
		
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error searching\" />");
		
		if(mysql_num_rows($result) != 0) {
	
			$resp = "<result msg=\"ok\" book=\"$book\" startChapter=\"$chapter\">";
			$result_ar = mysql_fetch_assoc($result);
			while($result_ar) {
				
				$resp .= "<item verse=\"" . $result_ar['verse'] . "\" otherVerse=\"" . $result_ar['otherVerse'] . "\">" .
							Shared::PrepareXML($result_ar['content']) . "</item>";
				$result_ar = mysql_fetch_assoc($result);	
			}
			$resp .= "</result>";
			return $resp;
		}
			else return "<result msg=\"" . String::$verse_not_exist . "\"/>";
	}
	
	public static function Search_4($book, $startChapter, $endChapter, $startVerse, $endVerse, $dbCon) {
		$query = "SELECT chapter, verse, otherVerse, content FROM alkitab WHERE book='$book' AND chapter >= $startChapter AND chapter <= $endChapter";
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error searching\" />");

		if(mysql_num_rows($result) != 0) {
	
			$resp = "<result msg=\"ok\" book=\"$book\" startChapter=\"$startChapter\" endChapter=\"$endChapter\">";
			$result_ar = mysql_fetch_assoc($result);
			$chapter = "";
			while($result_ar) {
				
				if($chapter != $result_ar['chapter'])
				{
					if($chapter != "")
						$resp .= "</chapter>";
					$chapter = $result_ar['chapter'];
					$resp .= "<chapter num=\"" . $chapter . "\">";
				}
				
				if(($result_ar['verse'] >= $startVerse && $chapter == $startChapter) || 
					(($result_ar['verse'] <= $endVerse || $endVerse == -1) && $chapter == $endChapter) ||
					($chapter != $endChapter && $chapter != $startChapter)) 
				{
					$resp .= "<item verse=\"" . $result_ar['verse'] . "\" otherVerse=\"" . $result_ar['otherVerse'] . "\">" .
								Shared::PrepareXML($result_ar['content']) . "</item>";
				}
				$result_ar = mysql_fetch_assoc($result);	
			}
			$resp .= "</chapter>";
			$resp .= "</result>";
			return $resp;
		}
			else return "<result msg=\"" . String::$verse_not_exist . "\"/>";
		
	}
	
	public static function SearchForWord($keyword, $dbCon) {

		$keywords = explode("+", $keyword);

		$filterClause = "";
		foreach($keywords as $filterParam)
		{
			$filterParam = preg_replace("/(\s+$)|(^\s+)/", "", $filterParam);

			if($filterClause == "")
				$filterClause = " WHERE (content LIKE '%$filterParam%') ";
			else
				$filterClause .= " AND (content LIKE '%$filterParam%') ";
		}
	
		$query = "SELECT book, chapter, verse, otherVerse, content FROM alkitab $filterClause";
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error searching\" />");
		
		if(mysql_num_rows($result) != 0) {
	
			$resp = "<result msg=\"ok\">";
			$result_ar = mysql_fetch_assoc($result);
			$chapter = "";
			$book = "";
			
			while($result_ar) {
				
				/*if($book != $result_ar['book'])
				{
					if($book != "")
						$resp .= "</chapter></book>";
					$book = $result_ar['book'];
					$resp .= "<book name=\"" . $book . "\">";
					
					$chapter = $result_ar['chapter'];
					$resp .= "<chapter num=\"" . $chapter . "\">";
				}
				else if($chapter != $result_ar['chapter'])
				{
					if($chapter != "")
						$resp .= "</chapter>";
					$chapter = $result_ar['chapter'];
					$resp .= "<chapter num=\"" . $chapter . "\">";
				}*/
				
				if($book != $result_ar['book'])
				{
					if($book != "")
						$resp .= "</book>";
					$book = $result_ar['book'];
					$resp .= "<book name=\"" . $book . "\">";
					
				}
				
				$resp .= "<item chapter=\"" .  $result_ar['chapter'] . 
							"\" verse=\"" . $result_ar['verse'] . "\" otherVerse=\"" . $result_ar['otherVerse'] . "\">" .
								Shared::PrepareXML($result_ar['content']) . "</item>";
				$result_ar = mysql_fetch_assoc($result);	
			}
			$resp .= "</book>";
			$resp .= "</result>";
			return $resp;
		}
			else return "<result msg=\"$keyword " . String::$not_found . "\"/>";
	}
	
	public static function IsBookExist($book, $dbCon) {
		$query = "SELECT * FROM alkitab WHERE book='$book'";
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error checking book existence\" />");
		if (mysql_num_rows($result) != 0) 
			return true; 
		else 
			return false;
	}
	
	public static function IsChapterExist($book, $chapter, $dbCon) {
		$query = "SELECT * FROM alkitab WHERE book='$book' AND chapter='$chapter'";
		$result = mysql_query($query, $dbCon) or die ("<result msg=\"Error checking chapter existence for $book\" />");
		if (mysql_num_rows($result) != 0) 
			return true; 
		else 
			return false;
	}
}

?>