<?php
	/*******************************************************
	* Atendimento
	*******************************************************/
	session_start() ;
	Header( "Content-type: image/gif" ) ;
	if ( !isset( $_SESSION['session_admin'] ) && !isset( $_SESSION['session_chat'] ) && !isset( $_SESSION['session_setup'] ) )
	{
		readfile( "$DOCUMENT_ROOT/images/empty_nodelete2.gif" ) ;
		exit ;
	}

	$success = 0 ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	$chat_session = ( isset( $_GET['chat_session'] ) ) ? $_GET['chat_session'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
	$rate = ( isset( $_GET['rate'] ) ) ? $_GET['rate'] : "" ;

	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $l ) )
	{
		readfile( "$DOCUMENT_ROOT/images/empty_nodelete2.gif" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Transcripts/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Survey/put.php") ;

	// initialize

	if ( $action == "send" )
	{
		$aspinfo = AdminASP_get_UserInfo( $dbh, $x ) ;
		$transcriptinfo = ServiceTranscripts_get_TranscriptInfo(  $dbh, $chat_session, $x ) ;
		if ( !isset( $transcriptinfo['created'] ) )
		{
			$session_chat = $_SESSION['session_chat'] ;
			$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
			$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
			$deptid = ( isset( $_GET['deptid'] ) ) ? $_GET['deptid'] : "" ;

			$requestinfo = ServiceChat_get_ChatRequestInfo( $dbh, $requestid ) ;
			$requestloginfo = ServiceLogs_get_SessionRequestLog( $dbh, $chat_session ) ;
			$transcriptinfo = Array() ;
			$transcriptinfo['deptID'] = $deptid ;
			$transcriptinfo['created'] = $requestloginfo['created'] ;
			$transcriptinfo['rating'] = 0 ;
			$transcriptinfo['from_screen_name'] = $session_chat[$sid]['visitor_name'] ;
			$transcriptinfo['email'] = $requestinfo['email'] ;
			$transcriptinfo['userID'] = $session_chat[$sid]['admin_id'] ;
			$transcriptinfo['formatted'] = join( "", file( "$DOCUMENT_ROOT/web/chatsessions/".$chat_session."_transcript.txt" ) ) ;
		}
		$userinfo = AdminUsers_get_UserInfo( $dbh, $transcriptinfo['userID'], $x ) ;
		$date = date( " d/m/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $transcriptinfo['created'] + $TIMEZONE ) ) ;
		
		$dat1 = date( "D", ( $transcriptinfo['created'] + $TIMEZONE ) ) ;
		
		                if ($dat1 == 'Mon')
						{
						  $dat1 = 'Segunda-feira';
						}
						if ($dat1 == 'Tue')
						{
						  $dat1 = 'Terca-feira';
						}
						if ($dat1 == 'Wed')
						{
						  $dat1 = 'Quarta-feira';
						}
						if ($dat1 == 'Thu')
						{
						  $dat1 = 'Quinta-feira';
						}
						if ($dat1 == 'Fri')
						{
						  $dat1 = 'Sexta-feira';
						}
						if ($dat1 == 'Sat')
						{
						  $dat1 = 'Sabado';
						}
						if ($dat1 == 'Sun')
						{
						  $dat1 = 'Domingo';
						}

		$department = AdminUsers_get_DeptInfo( $dbh, $transcriptinfo['deptID'], $x ) ;
		$department = stripslashes( $department['name'] ) ;
		$company = stripslashes( $aspinfo['company'] ) ;
		$operator_name = stripslashes( $userinfo['name'] ) ;
		$operator_email = $userinfo['email'] ;
		$visitor_name = stripslashes( strip_tags( $transcriptinfo['from_screen_name'] ) ) ;
		$visitor_email = stripslashes( $transcriptinfo['email'] ) ;
		$transcript = stripslashes( $transcriptinfo['formatted'] ) ;

		$header = "Empresa: $company\r\nDepartamento: $department\r\nOperador: $operator_name <$operator_email>\r\nVisitante: $visitor_name <$visitor_email>\r\nChat Info: $dat1 $date" ;

		$transcript = preg_replace( "/<p class=\"alert\">(.*?)<\/p>/", "<p class=\"alert\">- \\1 -</p>", $transcript ) ;
		//$transcript = preg_replace( "/<span>(.*?):<\/span>/", "<span>(\\1)</span>", $transcript ) ;
		$transcript = strip_tags( preg_replace( "/<p class=(.*?)>/", "\r\n", preg_replace( "/<br>/", "", $transcript ) ) ) ;
		$transcript = preg_replace( "/&#039;/", "'", $transcript ) ;
		$transcript = preg_replace( "/&lt;/", "<", $transcript ) ;
		$transcript = preg_replace( "/&gt;/", ">", $transcript ) ;
		$transcript = preg_replace( "/\\$/", "\\$ ", $transcript ) ;
		$transcript = preg_replace( "/ã/", "a", $transcript ) ;
		$transcript = preg_replace( "/õ/", "o", $transcript ) ;
		$transcript = preg_replace( "/Ã/", "A", $transcript ) ;
		$transcript = preg_replace( "/Õ/", "O", $transcript ) ;
		$transcript = preg_replace( "/á/", "a", $transcript ) ;
		$transcript = preg_replace( "/é/", "e", $transcript ) ;
		$transcript = preg_replace( "/í/", "i", $transcript ) ;
		$transcript = preg_replace( "/ó/", "o", $transcript ) ;
		$transcript = preg_replace( "/ú/", "u", $transcript ) ;
		$transcript = preg_replace( "/Á/", "A", $transcript ) ;
		$transcript = preg_replace( "/É/", "E", $transcript ) ;
		$transcript = preg_replace( "/Í/", "I", $transcript ) ;
		$transcript = preg_replace( "/Ó/", "O", $transcript ) ;
		$transcript = preg_replace( "/Ú/", "U", $transcript ) ;
		$transcript = preg_replace( "/à/", "a", $transcript ) ;
		$transcript = preg_replace( "/À/", "A", $transcript ) ;
		$transcript = preg_replace( "/ê/", "e", $transcript ) ;
		$transcript = preg_replace( "/ô/", "o", $transcript ) ;
		$transcript = preg_replace( "/Ê/", "E", $transcript ) ;
		$transcript = preg_replace( "/Ô/", "O", $transcript ) ;
		$transcript = preg_replace( "/ç/", "c", $transcript ) ;
		$transcript = preg_replace( "/Ç/", "C", $transcript ) ;
		$transcript = "$header\r\n\r\n====\r\n$transcript" ;

		$message = preg_replace( "/%%transcript%%/", $transcript, stripslashes( $aspinfo['trans_email'] ) ) ;
		$message = preg_replace( "/%%username%%/", $visitor_name, $message ) ;
		if ( isset( $_GET['optmessage'] ) && $_GET['optmessage'] )
			$message = stripslashes( $_GET['optmessage'] ) ."\r\n\r\n----- INICIO DO CHAT -----\r\n\r\n$message" ;

		$subject = "$company [ Chat Transcript ]" ;
		//print "$message" ; exit ;

		if ( $rate )
			ServiceSurvey_put_AdminSurvey( $dbh, $userinfo['userID'], $transcriptinfo['deptID'], $x, $chat_session, $rate ) ;

		if ( isset( $_GET['email'] ) && $_GET['email'] )
			mail( $_GET['email'], $subject, $message, "From: $operator_name <$operator_email>") ;
		$success = 1 ;
	}

	Header( "Content-type: image/gif" ) ;
	if ( $success )
		readfile( "$DOCUMENT_ROOT/images/empty_nodelete.gif" ) ;
	else
		readfile( "$DOCUMENT_ROOT/images/empty_nodelete2.gif" ) ;
?>