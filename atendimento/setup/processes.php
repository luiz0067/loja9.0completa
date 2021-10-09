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
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	$section = 4 ;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$action = $error_mesg = $adminid = $sessionid = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	$success = 0 ;
	// update all admins status to not available if they have been idle
	AdminUsers_update_IdleAdminStatus( $dbh, $admin_idle ) ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['adminid'] ) ) { $adminid = $_GET['adminid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }

	// conditions

	if ( $action == "kill_chat" )
	{
		$file_visitor = $sessionid."_admin.txt" ;
		$file_admin = $sessionid."_visitor.txt" ;

		$string = "<STRIP_FOR_PLAIN><font color=\"#FF0000\"><b>** Session was closed by root user.  Session has ended. **</b></font><br><script language=\"JavaScript\">alert( \"Session has been closed by root user.  Window will now close in 10 seconds!\" ) ; setTimeout(\"parent.window.close()\", 10000) ;</script></STRIP_FOR_PLAIN>" ;
		UtilChat_AppendToChatfile( $file_visitor, $string ) ;
		UtilChat_AppendToChatfile( $file_admin, $string ) ;

		// call the script again to give it some time so the message above gets
		// written to the chat screen.  Why?  the system auto cleans chat files if
		// there is no chat parties for that session... thus, the message above could
		// get wiped out without ever making it on the screen.  so let's delay it a bit
		HEADER( "location: processes.php?sessionid=$sessionid&action=kill_done" ) ;
		exit ;
	}
	else if ( $action == "kill_done" )
	{
		// just delete the chatsessionlist content... why?  because there is an
		// auto clean that will sweep through and delete the chat session and
		// all chat files for sessions that are not active (no parties in the session)
		ServiceChat_remove_ChatSessionlist( $dbh, $sessionid ) ;
		$action = "chat" ;
		$success = 1 ;
	}
	else if ( $action == "close_consol" )
	{
		// in UNIX -9 is kill... so let's use 9 as kill signal
		AdminUsers_update_Signal( $dbh, $adminid, 9, $session_setup['aspID'] ) ;
		$action = "consol" ;
		$success = 1 ;
	}
?>
<?php include_once( "./header.php" ) ; ?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<script language="JavaScript">
<!--
	function confirm_kill( sessionid )
	{
		if ( confirm( "Isto ira finalizar a sessao de chat! Deseja Continuar?" ) )
			location.href = "processes.php?action=kill_chat&sessionid="+sessionid ;
	}

	function confirm_close( adminid )
	{
		if ( confirm( "Isto ira fechar o console do operador! Deseja Continuar?" ) )
			location.href = "processes.php?action=close_consol&adminid="+adminid ;
	}

	function launch_monitor()
	{
		url = "op_monitor.php" ;
		newwin = window.open(url, "op_monitor", "scrollbars=yes,menubar=no,resizable=1,location=no,width=255,height=305") ;
		newwin.focus() ;
	}

	function do_alert()
	{
		<?php if ( $success ) { print "		alert( 'Sucesso!' ) ;\n" ; } ?>
	}

	function console_stats( userid )
	{
		url = "op_status.php?userid="+userid ;
		newwin = window.open(url, "op_console", "scrollbars=yes,menubar=no,resizable=1,location=no,width=350,height=450") ;
		newwin.focus() ;
	}
//-->
</script>
<?php
	if ( $action == "chat" ):
	$chatsessions = ServiceChat_get_ChatSessions( $dbh, $session_setup['aspID'] ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
  <td width="15%" valign="top" align="center"><img src="../images/sessoesg.png" /></td>  
  <td height="350" valign="top"> <p><span class="title">Sessões de Chats Ativos.</span><br>
  Lista completa dos processos de conversas ativas no momento. Você pode 
	    finalizar o processo clicando no link Finalizar Processo. </p>
	<p><a href="processes.php?action=chat"><strong>Recarregar lista</strong></a> 
	</p>
	  <table width="100%" border=0 cellpadding=2 cellspacing=1>
		<tr> 
			<th nowrap>ID</th>
			<th nowrap>Inicio do Processo</th>
			<th nowrap> Nome do Operador</th>
			<th nowrap> Nome do Visitante</th>
			<th>&nbsp;&nbsp;</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $chatsessions ); ++$c )
			{
				$session = $chatsessions[$c] ;

				$sessionlogins = ServiceChat_get_ChatSessionLogins( $dbh, $session['sessionID'] ) ;
				$date = date( " d/m/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $session['created'] + $TIMEZONE ) ) ;
				
				$dat1 = date( "D", ( $transcript['created'] + $TIMEZONE ) ) ;
				
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

				$bgcolor = "#EEEEF7" ;
				if ( $c % 2 )
					$bgcolor = "#E6E6F2" ;

				// only print out if there are active chat parties
				if ( count( $sessionlogins ) > 0 )
				{
					print "
						<tr class=\"altcolor2\">
							<td>$session[sessionID]</td>
							<td>$dat1 $date</td>
							<td>$sessionlogins[admin]</td>
							<td>$sessionlogins[visitor]</td>
							<td><a href=\"JavaScript:confirm_kill( $session[sessionID] )\">Finalizar Processo</a></td>
						</tr>
					" ;
				}
			}
		?>
	</table></td>
</tr>
</table>
		
<?php
	elseif ( $action == "consol" ):
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
  <td width="15%" valign="top" align="center"><img src="../images/sessoesg.png" /></td> 
  <td height="350" valign="top"> <p><span class="title">Sessões: Status dos Operadores.</span><br> 
    Lista Completa de todos os operadores e o status dos operadores no sistema. <br />
    Voc&ecirc; pode finalizar os processos dos operadores clicando em &quot;Finalizar Processo&quot;.</p> <p> 
    <Li> Monitoramento continuo do console de atendimento do operador.<br />
      O Status dos operadores &eacute; atualizado autom&aacute;ticamente atrav&eacute;s do monitoramento continuo do console de atendimento.<Br>
	[ <big><strong><a href="JavaScript:launch_monitor()">Clique aqui para o monitoramento continuo do console de atendimento</a></strong></big> ]
	</p>
	<p>
	<li>Online/offline atividade do operador de vis&atilde;o clicando no &quot;visualize hist&oacute;ria de condi&ccedil;&atilde;o&quot; v&iacute;nculo.
<table width="100%" border=0 cellpadding=2 cellspacing=1>
	  <tr align="left"> 
		<th nowrap>Nome</th>
		<th nowrap>Login</th>
		<th nowrap width="150" align="center">Hist&oacute;rico Online/Offline</th>
		<th align="center" nowrap>Online</th>
		<th align="center" nowrap>Console</th>
		<th>&nbsp;</th>
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

				$consol_status = "Fechado" ;
				$bgcolor_consol = "#FFE8E8" ;
				$kill_string = "&nbsp;" ;
				if ( $admin['signal'] == 9 )
				{
					$consol_status = "Aberto" ;
					$kill_string = "closing console..." ;
					$bgcolor_consol = "#E1FFE9" ;
				}
				else if ( $admin['last_active_time'] > $admin_idle )
				{
					$consol_status = "Aberto" ;
					$kill_string = "<a href=\"JavaScript:confirm_close( $admin[userID] )\">fechar console</a>" ;
					$bgcolor_consol = "#E1FFE9" ;
				}

				print "
					<tr class=\"altcolor2\">
						<td>$admin[name]</td>
						<td>$admin[login]</td>
						<td align=\"center\">[ <a href=\"JavaScript:console_stats( $admin[userID] )\">visualizar o historico do status</a> ]</td>
						<td align=\"center\" bgColor=\"$bgcolor_status\">$online_status</td>
						<td align=\"center\" bgColor=\"$bgcolor_consol\">$consol_status</td>
						<td align=\"center\" class=\"altcolor3\">$kill_string</td>
					</tr>
				" ;
			}
		?>
	</table></td>
</tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php endif ;?>
<?php include_once( "./footer.php" ) ; ?>
