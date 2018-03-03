<?php
/**
/*
 * '$Author: closari $
 * '$Id: Authentication.php 636 2010-03-21 20:26:00Z closari $
 * '$Rev: 636 $ 
 * 
 * Authentication.php
 * Managing user authentication variables
 * 
 */
 include_once('Shared.php');

class Authentication
{
    private $dbCon;
    private $error;
    
    public function __construct($dbCon)
    {
        session_start();
        $this->dbCon = $dbCon;
    }
    
    public function IsAuthenticated($contLevel = "")
    {
        if($_SESSION['username'] )
			return true;
		else
			return false;
    }
    
    public function Authenticate($username, $password)
    {
        $md5_password = md5($password); // Encrypt password with md5() function.
		$query = "SELECT * FROM authentication WHERE username='" . mysql_escape_string($username) . "' AND password='$md5_password'";
		
		// Check matching of username and password.
        $result = mysql_query( $query, 
                    $this->dbCon->GetCon()) or die ("Error authentication: " . mysql_error());

        if(mysql_num_rows($result)!='0')
        { 
        	$result_ar = mysql_fetch_assoc($result);
			$_SESSION['username'] = $result_ar['username'];
	        $_SESSION['email'] = $result_ar['email'];
	        $_SESSION['firstName'] = $jemaatData['firstName'];        	
        	
			Shared::LogUser($this->dbCon, $result_ar['userid'], "Login OK");
        	return true;
        }
        else
		{
            return false;
		}
    }
    

	public function AddUser($username, $password, $firstname, $lastname, $email)
	{
		$md5_password = md5($password);
		$query = "INSERT INTO  `gloria`.`authentication` (
					`username` ,
					`password` ,
					`firsname` ,
					`lastname` ,
					`email`
					)
					VALUES (
					'" . mysql_escape_string($username) . "', '$md5_password' ,  '" . mysql_escape_string($firstname) . "',  '" . mysql_escape_string($lastname) . "',  '" . mysql_escape_string($email) . "'
					);";

		if($result = mysql_query( $query, $this->dbCon->GetCon()))
		{
			return "";
		}
		else
		{
			return  mysql_error();
		}
		
	}
    
    public function Logout()
    {
        session_destroy();
    }
    
    public function GetFirstname()
    {
        return $_SESSION['firstName'];
    }
    
    public function GetUsername()
    {
        return $_SESSION['username'];
    }
    
}
?>
