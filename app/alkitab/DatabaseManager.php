<?php
/**
 * /**
/*
 * '$Author: closari $
 * '$Id: DatabaseManager.php 123 2008-09-23 21:10:58Z closari $
 * '$Rev: 123 $ 

 */
 

    class DatabaseManager
    {
        private $alkitabCon;

        public function __construct()
        {
            $user="[YOUR_USERNAME]";
            $password="[YOUR_PASSWORD]";
            $database="alkitab";
            $this->alkitabCon = mysql_connect("localhost",$user,$password, true) or die ("Cannot connect to alkitab database");
            @mysql_select_db($database, $this->alkitabCon) or die( "Unable to select database");
            
            /*$user="topic_selector";
			$password="Coba123123";
			$database="topic_selector";
			$this->alkitabCon  = mysql_connect("p50mysql27.secureserver.net",$user,$password, true) or die ("Cannot connect to database");
			@mysql_select_db($database, $this->alkitabCon) or die( "Unable to select database");*/
        }

        public function GetAlkitabCon()
        {
            return $this->alkitabCon;
        }
    }

?>
