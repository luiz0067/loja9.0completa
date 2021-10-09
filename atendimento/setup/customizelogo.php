<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	include_once("../API/sql.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php" ) ;

	// initialize
	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	$action = $error_mesg = "" ;
	$success = 0 ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['success'] ) ) { $success = $_GET['success'] ; }

	// conditions
	if ( $action == "upload_logo" )
	{
		$now = time() ;
		$pic_name = $_FILES['pic']['name'] ;
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
			if( move_uploaded_file( $_FILES['pic']['tmp_name'], "../web/$filename" ) )
			{
				chmod( "../web/$filename", 0777 ) ;
				if ( $_POST['logo_name'] == "LOGO" )
				{
					if ( file_exists ( "../web/$LOGO_ASP" ) && $LOGO_ASP )
						unlink( "../web/$LOGO_ASP" ) ;
					$LOGO = $filename ;
				}

				$SITE_NAME = addslashes( $SITE_NAME ) ;

				if ( !isset( $ASP_KEY ) ) { $ASP_KEY = "" ; }
				$conf_string = "0LEFT_ARROW0?php
					\$ASP_KEY = '$ASP_KEY' ;
					\$NO_PCONNECT = '$NO_PCONNECT' ;
					\$DATABASETYPE = '$DATABASETYPE' ;
					\$DATABASE = '$DATABASE' ;
					\$SQLHOST = '$SQLHOST' ;
					\$SQLLOGIN = '$SQLLOGIN' ;
					\$SQLPASS = '$SQLPASS' ;
					\$DOCUMENT_ROOT = '$DOCUMENT_ROOT' ;
					\$BASE_URL = '$BASE_URL' ;
					\$SITE_NAME = '$SITE_NAME' ;
					\$LOGO_ASP = '$LOGO' ;
					\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;
				$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
				$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
				$fp = fopen ("../web/conf-init.php", "wb+") ;
				fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
				fclose( $fp ) ;
			}

			HEADER( "location: customizelogo.php?success=1" ) ;
			exit ;
		}
		else if ( $pic_name != "" )
			$error_mesg = "Please upload ONLY GIF or JPEG formats.<br>" ;
	}

	if ( file_exists( "../web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "../web/$LOGO_ASP" ;
	else
		$logo = "../images/logo.gif" ;
?>
<script language="JavaScript">
<!--
	function do_upload(the_form)
	{
		if ( the_form.pic.value == "" )
			alert( "Input cannot be blank." ) ;
		else
			the_form.submit() ;
	}
//-->
</script><style type="text/css">
<!--
.estilouplogo {
	font-family: Arial, Helvetica, sans-serif;
}
.estilouplogotop {
	font-family: Arial, Helvetica, sans-serif;
	font-size:22px;
}
#logo {
	height: 61px;
	max-width: 440px;
	overflow: hidden;
	margin:0px auto;
}
-->
</style>

<span class="title"><span class="estilouplogotop">Customiza&ccedil;&atilde;o/Layout: Logomarca Geral do Sistema</span></span><span class="estilouplogo"> - <a href="options.php">Voltar para a Home</a><br />
Customize a logomarca da sua empresa (apenas GIF/JPEG).<br>
</span>
<p><span class="estilouplogo"> Tamanho M&aacute;ximo Sugerido:<br>
    <big><b>(440px de largura(width) - 60px de altura(height).</b></big>

    <br />
  <br />
  Logomarca Atual:<br>
</span>
<div id="logo"><span class="estilouplogo"><img src="<?php echo $logo ?>"></span></div><p>
  <span class="estilouplogo"><font color="#FF0000"><?php echo $error_mesg ?></font>
Enviar Logomarca  (apenas GIF/JPEG).
  </span>
<form method="POST" action="customizelogo.php" enctype="multipart/form-data" name="logo">
<span class="estilouplogo">
<input type="hidden" name="action" value="upload_logo">
<input type="hidden" name="logo_name" value="LOGO">
Logomarca
<input type="file" name="pic" size="20">
</span>
<p>
  <span class="estilouplogo">
  <input type="button" class="mainButton" value="Enviar Imagem" OnClick="do_upload(document.logo)">
  </span>
</form>