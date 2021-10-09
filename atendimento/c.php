<?php
	/*******************************************************
	* Atendimento On-Line
	*******************************************************/
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Clicks/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Clicks/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Refer/put.php") ;

	$k = "" ;
	if ( isset( $_GET['k'] ) ) { $k = $_GET['k'] ; }
	// take out letters for security
	$k = preg_replace( "/[^0123456789.]/", "", $k ) ;

	LIST( $aspid, $trackid, $key ) = explode( ".", $k ) ;
	$trackinfo = ServiceClicks_get_TrackingURLInfo( $dbh, $aspid, $trackid, $key ) ;
	$userinfo = AdminASP_get_UserInfo( $dbh, $aspid ) ;

	if ( isset( $trackinfo['landing_url'] ) )
	{
		// track refer
		$refer = isset( $_GET['refer'] ) ? $_GET['refer'] : "&nbsp;" ;
		if ( isset( $_SERVER['HTTP_REFERER'] ) )
			$refer = $_SERVER['HTTP_REFERER'] ;
		ServiceRefer_put_Refer( $dbh, $aspid, $_SERVER['REMOTE_ADDR'], $refer, $trackid ) ;
		ServiceClicks_put_Click( $dbh, $aspid, $trackid ) ;
		HEADER( "location: $trackinfo[landing_url]" ) ;
		exit ;
	}
	else
	{
		print "<font size=5><b>Error: Destination URL not found.</b></font><p>Please contact <a href=\"mailto:$userinfo[contact_email]?subject=Tracking URL\">$userinfo[contact_email]</a> to report this error." ;
	}
?>