<?
	/*******************************************************
	* Atendimento Online
	* checks to see how many visitors are on the site
	* used in the admin traffic monitor
	*******************************************************/
	session_start() ;
	$sid = $x = "" ;
	if ( isset( $HTTP_GET_VARS['sid'] ) ) { $sid = $HTTP_GET_VARS['sid'] ; }
	if ( isset( $HTTP_GET_VARS['x'] ) ) { $x = $HTTP_GET_VARS['x'] ; }

	include_once("../web/conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/get.php") ;

	// get all the non-idle/non-expired visitors
	$total_active_footprints = ServiceFootprintUnique_get_TotalActiveFootprints( $dbh, $x, $HTTP_SESSION_VARS['session_admin'][$sid]['dept_string'] ) ;
	mysql_close( $dbh['con'] ) ;

	$image_path = "$DOCUMENT_ROOT/images/counters/0.gif" ;
	if ( file_exists( "$DOCUMENT_ROOT/images/counters/$total_active_footprints.gif" ) )
		$image_path = "$DOCUMENT_ROOT/images/counters/$total_active_footprints.gif" ;
	else if ( $total_active_footprints > 40 )
		$image_path = "$DOCUMENT_ROOT/images/counters/40.gif" ;

	// if the active visitors are changed (reduced or increased), let's put the
	// image so it tells the system to reload the window
	if ( ( $total_active_footprints != $HTTP_SESSION_VARS['session_admin'][$sid]['active_footprints'] ) )
	{
		Header( "Content-type: image/gif" ) ;
		readfile( $image_path ) ;
	}
	else if ( $HTTP_SESSION_VARS['session_admin'][$sid]['traffic_timer'] > 10 )
	{
		Header( "Content-type: image/gif" ) ;
		readfile( $image_path ) ;
	}
?>
