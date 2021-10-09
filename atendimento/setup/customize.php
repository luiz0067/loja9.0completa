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
	include_once("$DOCUMENT_ROOT/web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/Util.php" ) ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	$section = 2;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$action = $error_mesg = $extension = "" ;
	$success = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['success'] ) ) { $success = $_GET['success'] ; }

	// conditions
	if ( $action == "upload_logo" )
	{
		$pic_name = $_FILES['pic']['name'] ;
		$now = time() ;
		$filename = eregi_replace( " ", "_", $pic_name ) ;
		$filename = eregi_replace( "%20", "_", $filename ) ;

		$filesize = $_FILES['pic']['size'] ;
		$filetype = $_FILES['pic']['type'] ;

		if ( eregi( "gif", $filetype ) )
			$extension = "GIF" ;
		elseif ( eregi( "jpeg", $filetype ) )
			$extension = "JPEG" ;

		$filename = $_POST['logo_name']."_$now.$extension" ;
		if ( eregi( "gif", $filetype ) ||  eregi( "jpeg", $filetype ) )
		{
			if( move_uploaded_file( $_FILES['pic']['tmp_name'], "../web/$session_setup[login]/$filename" ) )
			{
				if ( $_POST['logo_name'] == "LOGO" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$LOGO" ) && $LOGO )
						unlink( "../web/$session_setup[login]/$LOGO" ) ;
					$LOGO = $filename ;
				}

				// set if not set in conf file
				if ( !isset( $IPNOTRACK ) ) { $IPNOTRACK = "" ; }
				$COMPANY_NAME = addslashes( Util_Format_CleanVariable( $COMPANY_NAME ) ) ;
				$LOGO = Util_Format_CleanVariable( $LOGO ) ;
				$SUPPORT_LOGO_ONLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_ONLINE ) ;
				$SUPPORT_LOGO_OFFLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_OFFLINE ) ;
				$SUPPORT_LOGO_AWAY = Util_Format_CleanVariable( $SUPPORT_LOGO_AWAY ) ;
				$VISITOR_FOOTPRINT = Util_Format_CleanVariable( $VISITOR_FOOTPRINT ) ;
				$THEME = Util_Format_CleanVariable( $THEME ) ;
				$POLL_TIME = Util_Format_CleanVariable( $POLL_TIME ) ;
				$INITIATE = Util_Format_CleanVariable( $INITIATE ) ;
				$INITIATE_IMAGE = Util_Format_CleanVariable( $INITIATE_IMAGE ) ;
				$IPNOTRACK = Util_Format_CleanVariable( $IPNOTRACK ) ;
				$LANG_PACK = Util_Format_CleanVariable( $LANG_PACK ) ;


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
				\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

				$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
				$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
				$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
				fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
				fclose( $fp ) ;
			}

			HEADER( "location: customize.php?action=logo&success=1" ) ;
			exit ;
		}
		else if ( $pic_name != "" )
		{
			$action = "logo" ;
			$error_mesg = "Please upload ONLY GIF or JPEG formats.<br>" ;
		}
	}
	else if ( $action == "upload_icons" )
	{
		$pic_name = $_FILES['pic']['name'] ;
		$now = time() ;
		$filename = eregi_replace( " ", "_", $pic_name ) ;
		$filename = eregi_replace( "%20", "_", $filename ) ;

		$filesize = $_FILES['pic']['size'] ;
		$filetype = $_FILES['pic']['type'] ;

		if ( eregi( "gif", $filetype ) )
			$extension = "GIF" ;
		elseif ( eregi( "jpeg", $filetype ) )
			$extension = "JPEG" ;

		$filename = $_POST['logo_name']."_$now.$extension" ;
		if ( eregi( "gif", $filetype ) ||  eregi( "jpeg", $filetype ) )
		{
			if( move_uploaded_file( $_FILES['pic']['tmp_name'], "../web/$session_setup[login]/$filename" ) )
			{
				if ( $_POST['logo_name'] == "SUPPORT_LOGO_ONLINE" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$SUPPORT_LOGO_ONLINE" ) && $SUPPORT_LOGO_ONLINE )
						unlink( "../web/$session_setup[login]/$SUPPORT_LOGO_ONLINE" ) ;
					$SUPPORT_LOGO_ONLINE = $filename ;
				}
				else if ( $_POST['logo_name'] == "SUPPORT_LOGO_OFFLINE" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$SUPPORT_LOGO_OFFLINE" ) && $SUPPORT_LOGO_OFFLINE )
						unlink( "../web/$session_setup[login]/$SUPPORT_LOGO_OFFLINE" ) ;
					$SUPPORT_LOGO_OFFLINE = $filename ;
				}
				else if ( $_POST['logo_name'] == "SUPPORT_LOGO_AWAY" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$SUPPORT_LOGO_AWAY" ) && $SUPPORT_LOGO_AWAY )
						unlink( "../web/$session_setup[login]/$SUPPORT_LOGO_AWAY" ) ;
					$SUPPORT_LOGO_AWAY = $filename ;
				}

				// set if not set in conf file
				if ( !isset( $IPNOTRACK ) ) { $IPNOTRACK = "" ; }
				$COMPANY_NAME = addslashes( Util_Format_CleanVariable( $COMPANY_NAME ) ) ;
				$LOGO = Util_Format_CleanVariable( $LOGO ) ;
				$SUPPORT_LOGO_ONLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_ONLINE ) ;
				$SUPPORT_LOGO_OFFLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_OFFLINE ) ;
				$SUPPORT_LOGO_AWAY = Util_Format_CleanVariable( $SUPPORT_LOGO_AWAY ) ;
				$VISITOR_FOOTPRINT = Util_Format_CleanVariable( $VISITOR_FOOTPRINT ) ;
				$THEME = Util_Format_CleanVariable( $THEME ) ;
				$POLL_TIME = Util_Format_CleanVariable( $POLL_TIME ) ;
				$INITIATE = Util_Format_CleanVariable( $INITIATE ) ;
				$INITIATE_IMAGE = Util_Format_CleanVariable( $INITIATE_IMAGE ) ;
				$IPNOTRACK = Util_Format_CleanVariable( $IPNOTRACK ) ;
				$LANG_PACK = Util_Format_CleanVariable( $LANG_PACK ) ;

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
				\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

				$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
				$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
				$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
				fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
				fclose( $fp ) ;
			}

			HEADER( "location: customize.php?action=icons&success=1" ) ;
			exit ;
		}
		else if ( $pic_name != "" )
		{
			$action = "icons" ;
			$error_mesg = "Please upload ONLY GIF or JPEG formats.<br>" ;
		}
	}
	else if ( $action == "update_theme" )
	{
		$theme = $_GET['theme'] ;

		// set variable if not set
		if ( !isset( $SUPPORT_LOGO_AWAY ) )
			$SUPPORT_LOGO_AWAY = "" ;
		else if ( !isset( $INITIATE_IMAGE ) )
			$INITIATE_IMAGE = "" ;

		$COMPANY_NAME = addslashes( Util_Format_CleanVariable( $COMPANY_NAME ) ) ;
		$LOGO = Util_Format_CleanVariable( $LOGO ) ;
		$SUPPORT_LOGO_ONLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_ONLINE ) ;
		$SUPPORT_LOGO_OFFLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_OFFLINE ) ;
		$SUPPORT_LOGO_AWAY = Util_Format_CleanVariable( $SUPPORT_LOGO_AWAY ) ;
		$VISITOR_FOOTPRINT = Util_Format_CleanVariable( $VISITOR_FOOTPRINT ) ;
		$THEME = Util_Format_CleanVariable( $theme ) ;
		$POLL_TIME = Util_Format_CleanVariable( $POLL_TIME ) ;
		$INITIATE = Util_Format_CleanVariable( $INITIATE ) ;
		$INITIATE_IMAGE = Util_Format_CleanVariable( $INITIATE_IMAGE ) ;
		$IPNOTRACK = Util_Format_CleanVariable( $IPNOTRACK ) ;
		$LANG_PACK = Util_Format_CleanVariable( $LANG_PACK ) ;

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
		\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

		$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
		$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
		$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
		fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
		fclose( $fp ) ;
		HEADER( "location: customize.php?success=1" ) ;
		exit ;
	}
	else if ( $action == "upload_initiate" )
	{
		$pic_name = $_FILES['pic']['name'] ;
		$now = time() ;
		$filename = eregi_replace( " ", "_", $pic_name ) ;
		$filename = eregi_replace( "%20", "_", $filename ) ;

		$filesize = $_FILES['pic']['size'] ;
		$filetype = $_FILES['pic']['type'] ;

		if ( eregi( "gif", $filetype ) )
			$extension = "GIF" ;
		elseif ( eregi( "jpeg", $filetype ) )
			$extension = "JPEG" ;

		$filename = $_POST['logo_name']."_$now.$extension" ;
		if ( eregi( "gif", $filetype ) ||  eregi( "jpeg", $filetype ) )
		{
			if( move_uploaded_file( $_FILES['pic']['tmp_name'], "../web/$session_setup[login]/$filename" ) )
			{
				if ( $_POST['logo_name'] == "INITIATE_IMAGE" )
				{
					if ( isset( $INITIATE_IMAGE ) && file_exists ( "../web/$session_setup[login]/$INITIATE_IMAGE" ) && $INITIATE_IMAGE )
						unlink( "../web/$session_setup[login]/$INITIATE_IMAGE" ) ;
					$INITIATE_IMAGE = $filename ;
				}

				// set if not set in conf file
				if ( !isset( $IPNOTRACK ) ) { $IPNOTRACK = "" ; }
				$COMPANY_NAME = addslashes( Util_Format_CleanVariable( $COMPANY_NAME ) ) ;
				$LOGO = Util_Format_CleanVariable( $LOGO ) ;
				$SUPPORT_LOGO_ONLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_ONLINE ) ;
				$SUPPORT_LOGO_OFFLINE = Util_Format_CleanVariable( $SUPPORT_LOGO_OFFLINE ) ;
				$SUPPORT_LOGO_AWAY = Util_Format_CleanVariable( $SUPPORT_LOGO_AWAY ) ;
				$VISITOR_FOOTPRINT = Util_Format_CleanVariable( $VISITOR_FOOTPRINT ) ;
				$THEME = Util_Format_CleanVariable( $THEME ) ;
				$POLL_TIME = Util_Format_CleanVariable( $POLL_TIME ) ;
				$INITIATE = Util_Format_CleanVariable( $INITIATE ) ;
				$INITIATE_IMAGE = Util_Format_CleanVariable( $INITIATE_IMAGE ) ;
				$IPNOTRACK = Util_Format_CleanVariable( $IPNOTRACK ) ;
				$LANG_PACK = Util_Format_CleanVariable( $LANG_PACK ) ;

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
				\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

				$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
				$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
				$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
				fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
				fclose( $fp ) ;
			}

			HEADER( "location: customize.php?action=initiate&success=1" ) ;
			exit ;
		}
		else if ( $pic_name != "" )
		{
			$action = "initiate" ;
			$error_mesg = "Please upload ONLY GIF or JPEG formats.<br>" ;
		}
	}
?>
<?php include_once("./header.php"); ?>
<style>
<!--
.relative { position:relative; }
//--></style>
<script language="JavaScript">
<!--
	function do_upload(the_form)
	{
		if ( the_form.pic.value == "" )
			alert( "Voce precisa selecionar um arquivo." ) ;
		else
			the_form.submit() ;
	}

	function view_theme(theme)
	{
		var url = "../request.php?l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=0&page=setup&theme="+theme ;
		var newwin = window.open( url, "newwin", 'status=no,scrollbars=no,menubar=no,resizable=no,location=no,screenX=50,screenY=100,width=450,height=360' ) ;
		newwin.focus() ;
	}

	function select_theme(theme)
	{
		if ( confirm( "Modificar Tema?" ) )
			location.href = "customize.php?action=update_theme&theme="+theme ;
		else
			document.form.elements[theme].checked = false ;
	}

	function do_alert()
	{
		<?php if ( $success ) { print "		alert( 'Sucesso!' ) ;\n" ; } ?>
	}

//-->
</script>

<?php if ( ( $action == "logo" ) ) :
	if ( file_exists( "../web/$session_setup[login]/$LOGO" ) && $LOGO )
		$logo = "../web/$session_setup[login]/$LOGO" ;
	else if ( file_exists( "../web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "../web/$LOGO_ASP" ;
	else
		$logo = "../images/logo.gif" ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
<td width="15%" valign="top" align="center"><img src="../images/layoutg.png"></td>
<td valign="top" width="100%"> <p><span class="title">Customiza&ccedil;&atilde;o/Layout: Logomarca Janela Chat de Atendimento e Setup.</span><br>
		    Customize a logomarca da sua empresa (apenas GIF/JPEG).  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Com Sucesso!</b></big></font>" : "" ?><br>
			  </p>
  Tamanho M&aacute;ximo Sugerido:<br>
			  <big><b>(440px de largura(width) - 60px de altura(height).</b></big>
<p>
			  <span class="smallTitle"><font color="#FF0000"><?php echo $error_mesg ?></font></span><br>
	<table width="100%" border="0" cellspacing="2" cellpadding="0">
	  <form method="POST" action="customize.php" enctype="multipart/form-data" name="logo">
	  <input type="hidden" name="action" value="upload_logo">
		<input type="hidden" name="logo_name" value="LOGO">
		<tr> 
		  		  <td>Logomarca Atual:<br>
				  <div id="logo"><img src="<?php echo $logo ?>"></div><p>Enviar Logomarca
				  <p>
				  
				  <input type="file" name="pic" size="20"> &nbsp; <input type="button" class="mainButton" value="Inserir Imagem" OnClick="do_upload(document.logo);">
			</td>
		</tr>
		<tr> 
	  </form>
	  <td colspan="2" class="hdash">&nbsp;</td>
	  </tr>
	  <tr>
	  <td colspan="2">&nbsp;<br>&nbsp;<br>&nbsp;</td>
	  </tr>
	</table></td>


<?php elseif ( $action == "icons" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
<td width="15%" valign="top" align="center"><img src="../images/layoutg.png"></td>
  		  <td valign="top"> <p><span class="title">Customiza&ccedil;&atilde;o/Layout: Icones de Status de Atendimento.</span> <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Com Sucesso!</b></big></font>" : "" ?></p>
			  <span class="smallTitle"><font color="#FF0000"><?php echo $error_mesg ?></font></span><br>

	<table width="100%" border="0" cellspacing="8" cellpadding="0">
	  <form method="POST" action="customize.php" enctype="multipart/form-data" name="support_logo_online">
		<input type="hidden" name="action" value="upload_icons">
		<input type="hidden" name="logo_name" value="SUPPORT_LOGO_ONLINE">
		<tr> 
		  		  <td>Enviar Imagem de Status de Atendimento <u>Online</u><br>
			</a></td>
		  <td rowspan="3" align="right"><img src="<?php echo ( file_exists( "../web/$session_setup[login]/$SUPPORT_LOGO_ONLINE" ) && $SUPPORT_LOGO_ONLINE ) ? "../web/$session_setup[login]/$SUPPORT_LOGO_ONLINE" : "../images/atendimento_online.gif" ?>"></td>
		</tr>
		<tr> 
		  <td align="right"> <input type="file" name="pic" size="20"></td>
		</tr>
		<tr> 
		  <td align="right"><input type="button" class="mainButton" value="Inserir Imagem" OnClick="do_upload(document.support_logo_online);"></td>
		</tr> 
	  </form>
	  <tr>
	  <td colspan="2" class="hdash">&nbsp;</td>
	  </tr>
	  <form method="POST" action="customize.php" enctype="multipart/form-data" name="support_logo_offline">
		<input type="hidden" name="action" value="upload_icons">
		<input type="hidden" name="logo_name" value="SUPPORT_LOGO_OFFLINE">
		<tr> 
		  		  <td>Enviar Imagem de Status de Atendimento <u>Offline</u><br>
			</a></td>
		  <td rowspan="3" align="right"><img src="<?php echo ( file_exists( "../web/$session_setup[login]/$SUPPORT_LOGO_OFFLINE" ) && $SUPPORT_LOGO_OFFLINE ) ? "../web/$session_setup[login]/$SUPPORT_LOGO_OFFLINE" : "../images/atendimento_offline.gif" ?>"></td>
		</tr>
		<tr> 
		  <td align="right"> <input type="file" name="pic" size="20"></td>
		</tr>
		<tr> 
		  <td align="right"><input type="button" class="mainButton" value="Inserir Imagem" OnClick="do_upload(document.support_logo_offline);"></td>
		</tr>
		</form>
		<!-- <tr>
	<td colspan="2" class="hdash">&nbsp;</td>
	  </tr>
	  <form method="POST" action="customize.php" enctype="multipart/form-data" name="support_logo_away">
		<input type="hidden" name="action" value="upload_icons">
		<input type="hidden" name="logo_name" value="SUPPORT_LOGO_AWAY">
		<tr> 
		  		  <td>Upload <u>Away</u> Support Image<br>
				  If operators' consoles are open but status is offline, below status icon will display.  ONLY when the operators' status are offline AND consoles are closed will the image show the above offline icon.
			</a></td>
		  <td rowspan="3" align="right"><img src="<?php echo ( file_exists( "../web/$session_setup[login]/$SUPPORT_LOGO_AWAY" ) && $SUPPORT_LOGO_AWAY ) ? "../web/$session_setup[login]/$SUPPORT_LOGO_AWAY" : "../images/support_away.gif" ?>"></td>
		</tr>
		<tr> 
		  <td align="right"> <input type="file" name="pic" size="20"></td>
		</tr>
		<tr> 
		  <td align="right"><input type="button" class="mainButton" value="Upload Image" OnClick="do_upload(document.support_logo_away);"></td>
		</tr> 
	  </form> -->
	</table></td>


<?php elseif ( ( $action == "initiate" ) && $INITIATE && !file_exists( "$DOCUMENT_ROOT/admin/auction/index.php" ) ) :
	if ( isset( $INITIATE_IMAGE ) && file_exists( "../web/$session_setup[login]/$INITIATE_IMAGE" ) && $INITIATE_IMAGE )
		$logo = "../web/$session_setup[login]/$INITIATE_IMAGE" ;
	else
		$logo = "../images/initiate_chat.gif" ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
          <td width="15%" valign="top" align="center"><img src="../images/layoutg.png" /></td>
  		  <td valign="top" width="100%"> <p><span class="title">Customiza&ccedil;&atilde;o/Layout: Imagem Inicial de abordagem do visitante do site.<br />
Esta &eacute; a imagem que aparecer&aacute; na tela do visitante quando um operador iniciar a abordagem do pedido de chat com o visitante.<br />
          </span>Formato da Imagem (GIF/JPEG). <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?></p>
			  <span class="smallTitle"><font color="#FF0000"><?php echo $error_mesg ?></font></span><br>

	<table width="100%" border="0" cellspacing="8" cellpadding="0">
	  <form method="POST" action="customize.php" enctype="multipart/form-data" name="initiate">
	  <input type="hidden" name="action" value="upload_initiate">
		<input type="hidden" name="logo_name" value="INITIATE_IMAGE">
		<tr> 
		  		  <td>Imagem Inicial Atual:<br>
				  <img src="<?php echo $logo ?>">
				  <p>Enviar uma nova imagem:
				  <p>
				  
				  <input type="file" name="pic" size="20"> &nbsp; <input type="button" class="mainButton" value="Enviar Imagem" OnClick="do_upload(document.initiate);">
			</td>
		</tr>
		<tr> 
	  </form>
	  <td colspan="2" class="hdash">&nbsp;</td>
	  </tr>
	  <tr>
	  <td colspan="2">&nbsp;<br>&nbsp;<br>&nbsp;</td>
	  </tr>
	</table></td>


<?php else:
	if ( !isset ( $THEME ) ) { $THEME = "default" ; }
	$themes = Array() ;
	if ( $dir = @opendir("$DOCUMENT_ROOT/themes/") )
	{
		while ( ( $file = readdir( $dir ) ) != false )
		{
			if ( !preg_match( "/\.|(cvs)/i", $file ) && file_exists( "$DOCUMENT_ROOT/themes/$file/style.css" ) )
				array_push( $themes, $file ) ;
		}
		closedir( $dir ) ; 
	}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
<td width="15%" valign="top" align="center"><img src="../images/layoutg.png"></td>
  <td height="350" valign="top"> <p><span class="title">Customiza&ccedil;&atilde;o/Layout: Temas Para Chat</span>

		<?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Com Sucesso!</b></big></font>" : "" ?>
		</p>
		<form name="form">
		<b>Temas Disponiveis</b><br>
		<table cellspacing=1 cellpadding=0 border=0>
		<?php
			$cols = 1 ;
			for ( $c = 0; $c < count( $themes ); ++$c )
			{
				$selected = "" ;
				if ( $themes[$c] == $THEME )
					$selected = "checked" ;
				$output = "" ;
				$col = $c + 1 ;
				if ( ( $col == 1 ) || is_int( ( $col - 1 )/$cols ) )
					$output = "<tr>" ;

				$output .= "<td class=\"altcolor2\"><a href=\"JavaScript:view_theme('$themes[$c]')\">$themes[$c]</a></td><td class=\"altcolor2\"><input type=checkbox name=\"$themes[$c]\" value=\"$themes[$c]\" $selected OnClick=\"select_theme( '$themes[$c]' )\"></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>" ;

				if ( is_int( $col/$cols ) )
					$output .= "</tr>\n" ;
				print $output ;
			}
		?>
		</table>
		</form>
		<p>
	</td>

<?php endif ; ?>
</tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php include_once( "./footer.php" ) ; ?>