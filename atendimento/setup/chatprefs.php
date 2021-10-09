<?php
	/*******************************************************
	* ATENDIMENTO
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
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/update.php") ;
	$section = 6;			// Section number - see header.php for list of section numbers

	// initialize
	$action = "" ;
	$success = 0 ;
	$error_mesg = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['success'] ) ) { $success = $_POST['success'] ; }
	if ( isset( $_GET['success'] ) ) { $success = $_GET['success'] ; }

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)
	$nav_line = '<a href="options.php" class="nav">:: Home</a>' ;
	if ( $action )
		$nav_line = '<a href="chatprefs.php" class="nav">:: Previous</a>';

	// conditions
	if ( $action == "update_polling" )
	{
		$action = "polling" ;
		$COMPANY_NAME = addslashes( $COMPANY_NAME ) ;
		$conf_string = "0LEFT_ARROW0?php
			\$LOGO = '$LOGO' ;
			\$COMPANY_NAME = '$COMPANY_NAME' ;
			\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
			\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
			\$SUPPORT_LOGO_AWAY = '$SUPPORT_LOGO_AWAY' ;
			\$VISITOR_FOOTPRINT = '$VISITOR_FOOTPRINT' ;
			\$THEME = '$THEME' ;
			\$POLL_TIME = '$_POST[polltime]' ;
			\$INITIATE = '$INITIATE' ;
			\$INITIATE_IMAGE = '$INITIATE_IMAGE' ;
			\$IPNOTRACK = '$IPNOTRACK' ;
			\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

		$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
		$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
		$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
		fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
		fclose( $fp ) ;

		$POLL_TIME = $_POST['polltime'] ;
		$success = 1 ;
	}
	else if ( $action == "update_polling_type" )
	{
		/*
			legend:
				0 = set order
				1 = round-robin
				2 = random
		*/
		AdminASP_update_TableValue( $dbh, $session_setup['aspID'], "admin_polling_type", $_POST['type'] ) ;
		$_SESSION['session_setup']['admin_polling_type'] = $_POST['type'] ;
		HEADER( "location: chatprefs.php?action=polling_type&success=1" ) ;
		exit ;
	}
	if ( $action == "update_language" )
	{
		$action = "language" ;
		$COMPANY_NAME = addslashes( $COMPANY_NAME ) ;
		$conf_string = "0LEFT_ARROW0?php
			\$LOGO = '$LOGO' ;
			\$COMPANY_NAME = '$COMPANY_NAME' ;
			\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
			\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
			\$SUPPORT_LOGO_AWAY = '$SUPPORT_LOGO_AWAY' ;
			\$VISITOR_FOOTPRINT = '$VISITOR_FOOTPRINT' ;
			\$THEME = '$THEME' ;
			\$POLL_TIME = '$POLL_TIME' ;
			\$INITIATE = '$INITIATE' ;
			\$INITIATE_IMAGE = '$INITIATE_IMAGE' ;
			\$IPNOTRACK = '$IPNOTRACK' ;
			\$LANG_PACK = '$_POST[lang_pack]' ;?0RIGHT_ARROW0" ;

		$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
		$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
		$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
		fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
		fclose( $fp ) ;

		HEADER( "location: chatprefs.php?action=language&success=1" ) ;
		exit ;
	}
?>
<?php include_once("./header.php") ; ?>
<script language="JavaScript">
<!--
	function add_ip()
	{
		if ( ( document.ip.ip1.value == "" ) || ( document.ip.ip2.value == "" )
			|| ( document.ip.ip3.value == "" ) || ( document.ip.ip4.value == "" ) )
			alert( "IP is Invalid." ) ;
		else if ( ( document.ip.ip1.value > 255 ) || ( document.ip.ip2.value > 255 )
			|| ( document.ip.ip3.value > 255 ) || ( document.ip.ip4.value > 255 ) )
			alert( "Each IP value cannot be greater then 255." ) ;
		else
		{
			if ( confirm( "Don't track page view data and footprints for this IP?" ) )
				document.ip.submit() ;
		}
	}

	function do_remove_ip( index )
	{
		if ( index < 0 )
			alert( "Please select an IP to remove from list." ) ;
		else
		{
			if ( confirm( "Remove this IP from exclude list?" ) )
				document.ip_excluded.submit() ;
		}
	}

	function update_tracking()
	{
		if ( confirm( "Are you sure?" ) )
			document.tracking.submit() ;
	}

	function update_polling()
	{
		if ( document.polling.polltime.value < 20 )
			alert( "Must be at LEAST 20 seconds or more." ) ;
		else
			document.polling.submit() ;
	}

	function do_alert()
	{
		<?php if ( $success ) { print "		alert( 'Success!' ) ;\n" ; } ?>
	}
//-->
</script>

<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->

<?php if ( $action == "polling" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
  <td width="15%" valign="top" align="center"><img src="../images/chatprefg.png" /></td> 
  <td height="350" valign="top"><p><span class="title">Preferências do Atendimento: Tempo de Espera do Pedido de Atendimento.</span><br>
    <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Com Sucesso!</b></big></font>" : "" ?></p>
	<form method="POST" action="chatprefs.php" name="polling">
	  <input type="hidden" name="action" value="update_polling">
	  <p>Deve ser pelo menos 20 segundos ou mais, 30 segundos &eacute; recomendado.<br>
		<br>
		<input type="text" name="polltime" size=3 maxlength=3 style="width:30px;" onKeyPress="return numbersonly(event)" value="<?php echo ( $POLL_TIME ) ? $POLL_TIME : "30" ?>">
		segundos 
	  <p> 
		<input type="button" class="mainButton" value="Atualizar" OnClick="update_polling()">
	  
	</form>
	
  </td>


<?php elseif ( $action == "polling_type" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
  <td width="15%" valign="top" align="center"><img src="../images/chatprefg.png" /></td> 
	<form method="POST" action="chatprefs.php" name="polling">
	<input type="hidden" name="action" value="update_polling_type">
  <td height="350" valign="top"> <p><span class="title">Prefer&ecirc;ncias do Atendimento: Ordem dos Pedidos de Atendimento.</span><br>
    Configura&ccedil;&atilde;o da ordem de como os pedidos de atendimento devem ser distribu&iacute;dos entre os operadores.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Com Sucesso!</b></big></font>" : "" ?></p>
	<ul>
		<table cellspacing=0 cellpadding=3 border=0>
		<tr>
			<td><input type="radio" name="type" value="0" <?php echo ( $session_setup['admin_polling_type'] == 0 ) ? "checked" : "" ?>></td>
			<td>Ordem especificada na <a href="adduser.php">&aacute;rea de configura&ccedil;&atilde;o dos operadores</a>.</td>
		</tr>
		<tr>
			<td><input type="radio" name="type" value="1" <?php echo ( $session_setup['admin_polling_type'] == 1 ) ? "checked" : "" ?>></td>
			<td>O operador que est&aacute; h&aacute; mais tempo sem efetuar atendimentos   recebe o pedido de atendimento.</td>
		</tr>
		<tr>
			<td><input type="radio" name="type" value="2" <?php echo ( $session_setup['admin_polling_type'] == 2 ) ? "checked" : "" ?>></td>
			<td>Selecionar o operador aleat&oacute;riamente.</td>
		</tr>
		<!-- <tr>
			<td><input type="radio" name="type" value="3" <?php echo ( $session_setup['admin_polling_type'] == 3 ) ? "checked" : "" ?>></td>
			<td> First-Come-First-Serve: Chat request displays on ALL online operators' windows.  Call routs to the first operator who takes the request.</td>
		</tr> -->
		</table>
	</ul>
	<p> 
		<input type="submit" class="mainButton" value="Atualizar">
	  
	</form>
	
  </td>




<?php elseif ( $action == "language" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
  <td width="15%" valign="top" align="center"><img src="../images/chatprefg.png" /></td> 
	<form method="POST" action="chatprefs.php" name="language">
	<input type="hidden" name="action" value="update_language">
  <td height="350" valign="top"> <p><span class="title">Preferências do Atendimento: Idioma</span><br>
	  Configurar o idioma da janela de atendimento dos visitantes.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Sucesso!</b></big></font>" : "" ?></p>
	
	<table border="0" cellspacing="1" cellpadding="3">
	<tr>
		<td>Idioma:</td>
		<td>
			<select name="lang_pack">
			<?php
				if ( $dir = @opendir( "../lang_packs" ) )
				{
					while( $file = readdir( $dir ) )
					{
						if ( ( $file = preg_replace( "/\.php/", "", $file ) ) && !preg_match( "/(.bak)|(CVS)/", $file ) && preg_match( "/[0-9a-z]/i", $file ) )
						{
							$selected = "" ;
							if ( $file == $LANG_PACK )
									$selected = "selected" ;
							print "<option value=\"$file\" $selected>$file" ;
						}
					}
					closedir($dir) ;
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br><input type="submit" class="mainButton" value="Atualizar"></td>
	</tr>
	</table>
	
  </td>








<?php else: ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="15%" valign="top" align="center"><img src="../images/chatprefg.png" /></td> 
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Prefer&ecirc;ncias do Atendimento</span><br></p>
	  <p>
		Configura&ccedil;&atilde;o do tempo de espera do pedido de atendimento.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado com Sucesso!</b></big></font>" : "" ?><br>
		<big><li> <strong><a href="chatprefs.php?action=polling">Tempo de Espera do Pedido de Atendimento.</a></strong></big></p>
		Configura&ccedil;&atilde;o da ordem de como os pedidos de atendimento devem ser distribu&iacute;dos entre os operadores.<br>
		<big><li> <strong><a href="chatprefs.php?action=polling_type">Ordem dos Pedidos de Atendimento.</a></strong></big></p>
		Configurar o idioma da janela de atendimento dos visitantes.<br>
		<big><li> <strong><a href="chatprefs.php?action=language">Idioma</a></strong></big></p>
	  </td>


<?php endif ;?>
  <!-- <td height="350" align="center" style="background-image: url(../images/g_survey_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td> -->
</tr>
 </table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php include_once( "./footer.php" ) ; ?>