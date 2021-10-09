<?php
	if ( ISSET( $_OFFICE_UTIL_ERROR_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_ERROR_LOADED = true ;
	error_reporting(0) ;

	/*****  Util_ERROR_Database  ****************************************
	 *
	 *  History:
	 *	Kyle Hicks				June 24, 2003
	 *
	 *****************************************************************/
	function userErrorHandler ( $errno, $errmsg, $filename, $linenum, $vars ) 
	{
		global $l ;
		$time = date( "D m/d/Y H:i:s" ) ;
		$ip = $_SERVER['REMOTE_ADDR'] ;

		// define an assoc array of error string
		// in reality the only entries we should
		// consider are 2,8,256,512 and 1024
		$errortype = array (
			1   =>  "Error",
			2   =>  "Warning",
			4   =>  "Parsing Error",
			8   =>  "Notice",
			16  =>  "Core Error",
			32  =>  "Core Warning",
			64  =>  "Compile Error",
			128 =>  "Compile Warning",
			256 =>  "User Error",
			512 =>  "User Warning",
			1024=>  "User Notice"
		);
		// set of errors for which a var trace will be saved
		$user_errors = array( E_ALL ) ;

		$err = "Time: $time<br>" ;
		$err .= "Error Type: $errortype[$errno]<br>" ;
		$err .= "ERROR MESSAGE: $errmsg<br>" ;
		$err .= "File Name: $filename<br>" ;
		$err .= "File Line #: $linenum<br>" ;
		$err .= "Remote IP: $ip<br>" ;
		$err .= "Site Login: $l<br><br>" ;

		// save to the error log, and e-mail if there is a critical user error
		//error_log($err, 3, "/usr/local/php4/error.log");
		if ( $errno )
		{
			$admin_email = $_SERVER['SERVER_ADMIN'] ;
			print "<font color=#FF0000 size=2 face=arial>Service is Temporarily not available.  An email has been sent to <a href=mailto:$admin_email>$admin_email</a> to correct this situation. Please try back at a later time.</font><pre>$err</pre><hr><font size=2 face=arial>" ;
			if ( isset( $admin_email ) && $admin_email )
			{
				$err = preg_replace( "/<br>/", "\r\n", $err ) ;
				mail( $admin_email, "Your System Error", "Automated Email from  Error Reporting System.   may be down or has errors!\n\nError Below:\n\n$err\n\nPlease take appropriate action to correct this problem.", "From:  System <$admin_email>") ;
			}
			exit ;
		}
}
$my_error_handler = set_error_handler( "userErrorHandler" ) ;
?>