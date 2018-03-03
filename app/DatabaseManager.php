<?php
/**
 * /**
/*
 * '$Author: closari $
 * '$Id: DatabaseManager.php 612 2010-03-15 17:33:23Z closari $
 * '$Rev: 612 $ 
 *
 * There are currently 3 databases available, Jemaat, File, dan Warta
 */
 

    class DatabaseManager
    {
        private $gloriaCon;
		
        public function __construct()
        {
            $user="[YOUR_USERNAME]";
            $password="[YOUR_PASSWORD]";
            $database="gloria";
            $this->gloriaCon = mysql_connect("127.0.0.1",$user,$password, true) or die ("Cannot connect to database");
            @mysql_select_db($database, $this->gloriaCon) or die( "Unable to select gloria database");

        }

        public function GetCon()
        {
            return $this->gloriaCon;
        }
        
    }

?>
