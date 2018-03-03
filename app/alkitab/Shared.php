<?php
/*
 * '$Author: closari $
 * '$Id: Shared.php 177 2008-10-25 07:47:17Z closari $
 * '$Rev: 177 $ 
 */
class Shared
{
    public static function EncodeForSQL($msg)
    {
        $msg = preg_replace("/\s+$/", "", $msg);
        return str_replace( array("\\","'"),
                            array("","''"),
                            $msg
        
        );
    }
    
    public static function FormatFilename($variable)
    {
            $pattern = array("'","?;","<",">","/","\\");
            $replacement = "_";
            return str_replace($pattern, $replacement, $variable);
    }
    
    public static function PrepareXML($val)
    {
		$val = str_replace("\\\"", "\"", $val);
    	$val = str_replace("\\'", "'", $val);
    	
    	$val = str_replace("&", "&amp;", $val);
    	$val = str_replace("\"", "&quot;", $val);
    	$val = str_replace("'", "&apos;", $val);
    	$val = str_replace("<", "&lt;", $val);
    	$val = str_replace(">", "&gt;", $val);
    	return $val;
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
    
    public static function SaveXML($xml, $filename)
	{	
		$fh = fopen($filename, 'w') or die("can't open file for writing: $filename");
		fwrite($fh, $xml);
		fclose($fh);
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
    
    public static function mkdir_recursive($pathname, $mode)
	{
	    is_dir(dirname($pathname)) || Shared::mkdir_recursive(dirname($pathname), $mode);
	    return is_dir($pathname) || @mkdir($pathname, $mode);
	}
	
//	public static function rmdir_recurse($path)
//	{
//	    $path= rtrim($path, '/').'/';
//	    $handle = opendir($path);
//	    for (;false !== ($file = readdir($handle));)
//	        if($file != "." and $file != ".." )
//	        {
//	            $fullpath= $path.$file;
//	            if( is_dir($fullpath) )
//	            {
//	                Shared::rmdir_recurse($fullpath);
//	                rmdir($fullpath);
//	            }
//	            else
//	              unlink($fullpath);
//	        }
//	    closedir($handle);
//	}
	
	public static function recursive_remove_directory($directory, $empty=FALSE)
	{
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}
		if(!file_exists($directory) || !is_dir($directory))
		{
			return FALSE;
		}elseif(is_readable($directory))
		{
			$handle = opendir($directory);
			while (FALSE !== ($item = readdir($handle)))
			{
				if($item != '.' && $item != '..')
				{
					$path = $directory.'/'.$item;
					if(is_dir($path)) 
					{
						Shared::recursive_remove_directory($path);
					}else{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if($empty == FALSE)
			{
				if(!rmdir($directory))
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}
    
    public static function CropImage($nw, $nh, $source, $stype, $dest)
    {

		$size = getimagesize($source);
		$w = $size[0];
		$h = $size[1];
		
		switch($stype) {
		
		    case 'gif':
				$simg = imagecreatefromgif($source);
				break;
			case 'jpg':
				$simg = imagecreatefromjpeg($source);
				break;
			case 'jpeg':
				$simg = imagecreatefromjpeg($source);
				break;
		    case 'png':
		        $simg = imagecreatefrompng($source);
				break;
			default:
				return "";
		}
		
		$dimg = imagecreatetruecolor($nw, $nh);
		
		$wm = $w/$nw;
		$hm = $h/$nh;
		
		$h_height = $nh/2;
		$w_height = $nw/2;
		
		if($w> $h) {
		    $adjusted_width = $w / $hm;
		    $half_width = $adjusted_width / 2;
		    $int_width = $half_width - $w_height;
		
		    imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
		} elseif(($w <$h) || ($w == $h)) {
			$adjusted_height = $h / $wm;
			$half_height = $adjusted_height / 2;
			$int_height = $half_height - $h_height;
		
			imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
		} else {
		    imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
		}
		
		imagejpeg($dimg,$dest,80);
    }
    
    public static function LogUser($dbCon, $userID, $activity)
    {
            // Get User IP;
            if (isset($_SERVER['HTTP_X_FORWARD_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARD_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $query =	"INSERT INTO `log` ( `logID` , `userIP` , `userID` , `tanggal` , `activity` ) " . 
                                    "VALUES ( " .
                                    "NULL, '" . $ip . "', '" . $userID . "', NOW(), '" . str_replace(array("\\","'"), array("", "''"), $activity) . "' )";

            $result = mysql_query($query, $dbCon->GetFileCon()) 
                  or die ("Error logging to file DB: " . mysql_error());;
    }
}
?>
