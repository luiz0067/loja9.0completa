<?php
	/*******************************************************
	* Atendimento On-Line
	*******************************************************/
	session_start() ;
	$l = "" ;
	// try to get cookie value first
	if ( isset( $_COOKIE['COOKIE_PHPLIVE_SITE'] ) ) { $l = $_COOKIE['COOKIE_PHPLIVE_SITE'] ; }
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }
	if ( isset( $_POST['l'] ) ) { $l = $_POST['l'] ; }

	if ( !file_exists( "./web/conf-init.php" ) )
	{
		HEADER( "location: setup/index.php" ) ;
		exit ;
	}
	
	include_once( "./API/Util_Dir.php" ) ;
	if ( Util_DIR_CheckDir( ".", $l ) )
		include_once("./web/$l/$l-conf-init.php") ;
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Error.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/Util_CleanFiles.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;

	// initialize
	$action = $error = $sid = $site = $remember = "" ;
	$sound_file = "cellular.wav" ;
	$isadmin = $winapp = $autologin = $wflag = $closewin = 0 ;

	if ( !isset( $_SESSION['session_admin'] ) )
	{
		session_register( "session_admin" ) ;
		$session_admin = ARRAY() ;
		$_SESSION['session_admin'] = ARRAY() ;
	}

	// check to see if the site login is passes.  if not, then let's see how many
	// sites are in the asp model.  if only ONE, then default to that one.
	$total_sites = AdminASP_get_TotalUsers( $dbh ) ;
	if ( $total_sites == 1 )
	{
		$site = AdminASP_get_AllUsers( $dbh, 0, 1 ) ;
		$l = $site[0]['login'] ;
	}

	if ( isset( $LOGO ) && file_exists( "$DOCUMENT_ROOT/web/$l/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/$l/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['winapp'] ) ) { $winapp = $_POST['winapp'] ; }
	if ( isset( $_GET['winapp'] ) ) { $winapp = $_GET['winapp'] ; }
	if ( isset( $_GET['wflag'] ) ) { $wflag = $_GET['wflag'] ; }
	if ( isset( $_GET['closewin'] ) && ( $_GET['closewin'] != "undefined" ) ) { $closewin = $_GET['closewin'] ; }

	// conditions
	if ( ( isset( $_COOKIE['COOKIE_PHPLIVE_LOGIN'] ) && isset( $_COOKIE['COOKIE_PHPLIVE_PASSWORD'] ) && isset( $_COOKIE['COOKIE_PHPLIVE_SITE'] ) ) && !$action )
		$autologin = 1 ;

	if ( $action == "login" )
	{
		if ( $l )
			$site = $l ;
		else
			$site = $_POST['site'] ;

		$aspinfo = AdminASP_get_ASPInfoByASPLogin( $dbh, $site ) ;
		$admin = AdminUsers_get_UserInfoByLoginPass( $dbh, $_POST['login'], $_POST['password'], $aspinfo['aspID'] ) ;

		if ( !$aspinfo['active_status'] )
			$error = "Service is inactive.  Please contact your setup admin for details." ;
		else
		{
			if ( $admin['userID'] && ( $admin['aspID'] == $aspinfo['aspID'] ) )
			{
				CleanFiles_util_CleanChatSessionFiles() ;

				// set $sid.  $sid is used to keep track of this admin user.  $sid allows
				// so a user can log into several admin departments on same computer.  it is
				// passed everywhere the admin goes.
				$sid = time() ;

				$departments = AdminUsers_get_UserDepartments( $dbh, $admin['userID'] ) ;
				$dept_string = "" ;
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$the_department = $departments[$c] ;
					$dept_string .= "deptID = $the_department[deptID] OR " ;
				}
				$dept_string .= "deptID = 0" ;

				$_SESSION['session_admin'][$sid] = ARRAY() ;
				$_SESSION['session_admin'][$sid]['dept_string'] = $dept_string ;
				$_SESSION['session_admin'][$sid]['admin_id'] = $admin['userID'] ;
				$_SESSION['session_admin'][$sid]['requests'] = 0 ;
				$_SESSION['session_admin'][$sid]['aspID'] = $aspinfo['aspID'] ;
				$_SESSION['session_admin'][$sid]['asp_login'] = $aspinfo['login'] ;
				$_SESSION['session_admin'][$sid]['active_footprints'] = 0 ;
				$_SESSION['session_admin'][$sid]['winapp'] = "$winapp" ;
				$_SESSION['session_admin'][$sid]['close_timer'] = 0 ;
				$_SESSION['session_admin'][$sid]['traffic_monitor'] = 0 ;
				$_SESSION['session_admin'][$sid]['available_status'] = 1 ;
				$_SESSION['session_admin'][$sid]['sound'] = "on" ;
				$_SESSION['session_admin'][$sid]['request_ids'] = "" ;
				$_SESSION['session_admin'][$sid]['traffic_timer'] = $admin['console_refresh'] ;
				$isadmin = 1 ;

				// check to see if they want to be remembered... if so, just set cookie.
				// let's set it for 1 month for now.
				$cookie_lifespan = time() + 60*60*24*30 ;
				if ( isset( $_POST['remember'] ) )
				{
					setcookie( "COOKIE_PHPLIVE_LOGIN", $_POST['login'], $cookie_lifespan ) ;
					setcookie( "COOKIE_PHPLIVE_PASSWORD", $_POST['password'], $cookie_lifespan ) ;
					setcookie( "COOKIE_PHPLIVE_SITE", $aspinfo['login'], $cookie_lifespan ) ;
				}
			}
			else
			{
				// reset cookie if cookies are set
				if ( isset( $_COOKIE['COOKIE_PHPLIVE_LOGIN'] ) && isset( $_COOKIE['COOKIE_PHPLIVE_PASSWORD'] ) )
				{
					setcookie( "COOKIE_PHPLIVE_LOGIN", "", -1 ) ;
					setcookie( "COOKIE_PHPLIVE_PASSWORD", "", -1 ) ;
					setcookie( "COOKIE_PHPLIVE_SITE", "", -1 ) ;
				}
				$error = "Login failed.  NOTE: password is (CaSE senSiTiVE)." ;
			}
		}
	}
	else if ( $action == "logout" )
	{
		if ( isset( $_COOKIE['COOKIE_PHPLIVE_LOGIN'] ) && isset( $_COOKIE['COOKIE_PHPLIVE_PASSWORD'] ) && !$wflag )
		{
			setcookie( "COOKIE_PHPLIVE_LOGIN", "", -1 ) ;
			setcookie( "COOKIE_PHPLIVE_PASSWORD", "", -1 ) ;
			setcookie( "COOKIE_PHPLIVE_SITE", "", -1 ) ;
		}
		$sid = $_GET['sid'] ;
		$l = $_SESSION['session_admin'][$sid]['asp_login'] ;
		AdminUsers_update_Status( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], 0 ) ;
		AdminUsers_update_UserValue( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], "last_active_time", $admin_idle - 300 ) ;
		$_SESSION['session_admin'] = Array() ;
		HEADER( "location: index.php?wflag=$wflag&l=$l&winapp=$winapp&closewin=$closewin" ) ;
		exit ;
	}
	else
	{
		// do the cleaning of the chat database of old requests and sessions.
		ServiceChat_remove_CleanChatSessionList( $dbh ) ;
		ServiceChat_remove_CleanChatSessions( $dbh ) ;
		ServiceChat_remove_CleanChatRequests( $dbh ) ;
	}
?>
<html>
<head>
<title>Atendimento - Operador</title>
<?php $css_path = "./" ; include( "./css/default.php" ) ; ?>
<script language="JavaScript">
<!--
	// add a delay before taking them to admin area so the sound file can load.
	// this is for slow connections so the sound file is in memory and faster
	// refresh time when a support request is made.
	if ( <?php echo $isadmin ?> )
		var temp = setTimeout("location.href='<?php echo $BASE_URL ?>/admin/index.php?sid=<?php echo $sid ?>&start=1&winapp=<?php echo $winapp ?>'",4000) ;
	
	function do_alert()
	{
		<?php if ( $error ) print "alert( \"$error\" ) ;" ; ?>
		
		<?php if ( $closewin > 0 ) print "parent.window.close() ;" ; ?>
	}
//-->
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" OnLoad="do_alert()">
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="65" valign="top" class="bgHead"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
        <td width="20" height="65" rowspan="2" valign="bottom">&nbsp;</td>
		  <td height="65"><div id="logo"><img src="<?php echo $logo ?>" border="0"></div></td>
		  <td align="right" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td height="35" valign="top" class="bgMenuBack"></td>
  </tr>
  <tr> 
	<td height="20" valign="top"></td>
  </tr>
  <tr> 
	<td align="center" valign="top" class="bg">



<?php if ( $isadmin == 1 ): ?>
<table cellspacing=0 cellpadding=0 border=0 width="450">
<tr>
	<td align="center">
		<span class="title">Um momento ...<br>
		acessando a sua conta ...
	</td>
</tr>
</table>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="0" height="0" id="cellular" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="<?php echo $BASE_URL ?>/sounds/cellular.swf" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<embed src="<?php echo $BASE_URL ?>/sounds/cellular.swf" quality="high" bgcolor="#ffffff" width="0" height="0" name="cellular" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="//www.macromedia.com/go/getflashplayer" />
</object>


<?php elseif ( $autologin ): ?>
<form method="POST" action="<?php echo $BASE_URL ?>/index.php" name="form">
<input type="hidden" name="action" value="login">
<input type="hidden" name="winapp" value="<?php echo $winapp ?>">
<input type="hidden" name="l" value="<?php echo $_COOKIE['COOKIE_PHPLIVE_SITE'] ?>">
<input type="hidden" name="login" value="<?php echo $_COOKIE['COOKIE_PHPLIVE_LOGIN'] ?>">
<input type="hidden" name="password" value="<?php echo $_COOKIE['COOKIE_PHPLIVE_PASSWORD'] ?>">
</form>
<script language="JavaScript">
<!--
	document.form.submit()
//-->
</script>





<?php elseif ( $l ): ?>
	<form method="POST" action="<?php echo $BASE_URL ?>/index.php" name="form">
	<input type="hidden" name="action" value="login">
	<input type="hidden" name="winapp" value="<?php echo $winapp ?>">
	<input type="hidden" name="l" value="<?php echo $l ?>">
			<table cellspacing=1 cellpadding=3 border=0 width="300">
			  <tr align="center"> 
				<th colspan=2><span class="basicTitle22">Opera&ccedil;&atilde;o de Acesso </span></th>
			</tr>
			  <tr> 
				<td align="right"><strong>Usu&aacute;rio:</strong></td>
				<td> 
				  <input type="text" style="width: 150px" name="login" size="10" maxlength="25" value="<?php echo isset( $_POST['login'] ) ? $_POST['login'] : "" ?>"></td>
			</tr>
			  <tr> 
				<td align="right"><strong>Senha:</strong></td>
				<td> 
				  <input type="password"  style="width: 150px" name="password" size="10" maxlength="15"></td>
			</tr>
			  <tr> 
				<td align="center">&nbsp; </td>
				<td nowrap> 
				  <input type="checkbox" style="background:none;border:none" name="remember" value="1">
				  <?php echo ( $winapp ) ? "Fazer o login automaticamente da proxima vez" : "Lembrar meu ID neste computador" ?></td>
			</tr>
			  <tr> 
				<td>&nbsp;</td>
				<td> 
				  <input name="Submit" type="submit" class="mainButton" value="Entrar"></td>
			</tr>
		  </table>
	</form>



<?php else: ?>
<span class="panelTitle">Erro: Certifique-se de que voc&ecirc; que acessou a URL correta.<br>
(exemplo: <span class="hilight"><?php echo $BASE_URL ?>/web/&lt;sitelogin&gt;</span>)</span>




<?php endif ; ?>
</p></td>
  </tr>
  <tr> 
	<td height="20" align="center" class="bgFooter" style="height:30px" valign="middle"><?php echo $LANG['DEFAULT_BRANDING'] ?></td>
  </tr>
</table>
<!-- DO NOT REMOVE -->
<!--  [DO NOT DELETE] -->
<!-- This navigation layer is placed at the very botton of the HTML to prevent pesky problems with NS4.x -->
</body>
</html>

<?php
	mysql_close( $dbh['con'] ) ;
?>
