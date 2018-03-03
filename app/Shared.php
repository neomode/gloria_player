<?php
/*
 * '$Author: closari $
 * '$Id: Shared.php 612 2010-03-15 17:33:23Z closari $
 * '$Rev: 612 $ 
 */
class Shared
{
    public static function EncodeForSQL($msg)
    {
        $msg = preg_replace("/\s+$/", "", $msg);
        return str_replace( array("\\'","'"),
                            array("'","''"),
                            $msg
        
        );
    }
    
    public static function FormatFilename($variable)
    {
            $pattern = array("'","?;","<",">","/","\\");
            $replacement = "_";
            return str_replace($pattern, $replacement, $variable);
    }
    
    public static function DecodeURL($variable)
    {
            $pattern = array("amp;","equal;","squote;","dquote;","br;","\n-");
            $replacement = array("&", "=", "''", "\"", "\n", "[|]");
            return str_replace($pattern, $replacement, $variable);
    }
    
    public static function GetHTMLTag($type)
    {
            $filepath = "html/$type.htm";
            $fh = fopen($filepath, "rb");
            $html_data = fread ($fh, filesize($filepath));
            fclose ($fh);
            return $html_data;
    }
    
	public static function GetJemaatData($jemaatID, $dbCon)
    {
        $query = "SELECT * FROM jemaat WHERE jemaatID=$jemaatID" ;
        $result = mysql_query($query, $dbCon->GetJemaatCon()) or die ("Error querying for jemaat: $jemaatID: " . mysql_error($dbCon->GetJemaatCon()));
        return mysql_fetch_assoc($result);
    }
    
    public static function GetJemaatName($jemaatID, $dbCon)
    {
        $query = "SELECT * FROM jemaat WHERE jemaatID=$jemaatID" ;
        $result = mysql_query($query, $dbCon->GetJemaatCon()) or die ("Error querying for jemaat: $jemaatID");
        $result_ar = mysql_fetch_assoc($result);
		return $result_ar['firstName'];
    }

	public static function GetJemaatEmail($jemaatID, $dbCon)
    {
        $query = "SELECT email FROM email WHERE jemaatID=$jemaatID" ;
        $result = mysql_query($query, $dbCon->GetJemaatCon()) or die ("Error querying for email: $jemaatID");
        $result_ar = mysql_fetch_assoc($result);
		return $result_ar['email'];
    }
    
    public static function GetIndonesianMonth($month)
    {
            switch($month){
                    case '01':
                            return "Januari";
                    case '02':
                            return "Februari";
                    case '03':
                            return "Maret";
                    case '04':
                            return "April";
                    case '05':
                            return "Mei";
                    case '06':
                            return "Juni";
                    case '07':
                            return "Juli";
                    case '08':
                            return "Agustus";
                    case '09':
                            return "September";
                    case '10':
                            return "Oktober";
                    case '11':
                            return "November";
                    case '12':
                            return "Desember";
            }
    }
    
    public static function LogUser($dbCon, $userID, $activity)
    {
            // Get User IP;
            if ($_SERVER['HTTP_X_FORWARD_FOR']) {
                $ip = $_SERVER['HTTP_X_FORWARD_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $query =	"INSERT INTO `log` ( `id` , `ip` , `username` , `datetime` , `activity` ) " . 
                                    "VALUES ( " .
                                    "NULL, '" . $ip . "', '" . $userID . "', NOW(), '" . str_replace(array("\\","'"), array("", "''"), $activity) . "' )";

            $result = mysql_query($query, $dbCon->GetCon()) 
                  or die ("Error logging to DB: " . mysql_error());;
    }
}
?>
