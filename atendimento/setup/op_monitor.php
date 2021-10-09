<?php
	/*******************************************************
	* Atendimento
	*******************************************************/
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
?>
<?php

	// initialize
	$action = $error_mesg = $adminid = $sessionid = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	$success = 0 ;
	// update all admins status to not available if they have been idle

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['adminid'] ) ) { $adminid = $_GET['adminid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }

	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
	$last_updated = date( "d/m/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", time()+$TIMEZONE ) ;
	
	$dat111 = date( "D", time()+$TIMEZONE ) ;
	
	                    if ($dat111 == 'Mon')
						{
						  $dat111 = 'Segunda-feira';
						}
						if ($dat111 == 'Tue')
						{
						  $dat111 = 'Ter&ccedil;a-feira';
						}
						if ($dat111 == 'Wed')
						{
						  $dat111 = 'Quarta-feira';
						}
						if ($dat111 == 'Thu')
						{
						  $dat111 = 'Quinta-feira';
						}
						if ($dat111 == 'Fri')
						{
						  $dat111 = 'Sexta-feira';
						}
						if ($dat111 == 'Sat')
						{
						  $dat111 = 'S&aacute;bado';
						}
						if ($dat111 == 'Sun')
						{
						  $dat111 = 'Domingo';
						}
?>
<?php
	// functions
?>
<?php
	// conditions
?>
<html>
<head>
<title> Monitoramento do Console dos Operadores </title>
<?php $css_path = ( !isset( $css_path ) ) ? $css_path = "../" : $css_path ; include_once( $css_path."css/default.php" ) ; ?>

<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<script language="JavaScript">
<!--
	function do_alert()
	{
		// every minute
		var refresh = setTimeout( "window.location.reload( true );", 60000 ) ;
	}
//-->
</script>

<body onLoad="do_alert()" bgColor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="35" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr>
	<td valign="top" class="bg" align="center">Monitoramento dos Operadores
	  <table width="98%" border="0" cellspacing="0" cellpadding="2">
		<tr> 
		  <td valign="top" align="center"><span class="medium">&Uacute;ltima Atualiza&ccedil;&atilde;o: <?php echo $dat111 . ", " . $last_updated ?></span>
			<table width="100%" border=0 cellpadding=2 cellspacing=1>
			  <tr align="left"> 
				<th nowrap>Nome</th>
				<th align="center" nowrap>Online</th>
				<th align="center" nowrap>Console</th>
			  </tr>
			 <?php
					for ( $c = 0; $c < count( $admins ); ++$c )
					{
						$admin = $admins[$c] ;

						$bgcolor = "#EEEEF7" ;
						if ( $c % 2 )
							$bgcolor = "#E6E6F2" ;

						$online_status = "Offline" ;
						$bgcolor_status = "#FFE8E8" ;
						if ( $admin['available_status'] == 1 )
						{
							$online_status = "Online" ;
							$bgcolor_status = "#E1FFE9" ;
						}
						else if ( $admin['available_status'] == 2 )
						{
							$online_status = "Away" ;
							$bgcolor_status = "#FEC65B" ;
						}

						$consol_status = "Closed" ;
						$bgcolor_consol = "#FFE8E8" ;
						if ( $admin['signal'] == 9 )
						{
							$consol_status = "Open" ;
							$bgcolor_consol = "#E1FFE9" ;
						}
						else if ( $admin['last_active_time'] > $admin_idle )
						{
							$consol_status = "Open" ;
							$bgcolor_consol = "#E1FFE9" ;
						}

						print "
							<tr class=\"altcolor2\">
								<td><a href=\"mailto:$admin[email]?subject=Console Status: $online_status\">$admin[name]</a></td>
								<td align=\"center\" bgColor=\"$bgcolor_status\">$online_status</td>
								<td align=\"center\" bgColor=\"$bgcolor_consol\">$consol_status</td>
							</tr>
						" ;
					}
				?>
			</table></td>
		</table>
	</td>
  </tr>
  <tr> 
	<td height="20" align="center" class="bgFooter" style="height:30px" valign="middle"><?php echo $LANG['DEFAULT_BRANDING'] ?></td>
  </tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->

</body>
</html>
