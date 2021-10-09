<?php
	/*******************************************************
	* Atendimento
	*******************************************************/
	error_reporting(0);
	//include_once("../API/Util_Error.php") ;

	// initialize
	if ( preg_match( "/unix/i", $_SERVER['SERVER_SOFTWARE'] ) )
		$server = "unix" ;
	else
		$server = "windows" ;
	
	$PHPLIVE_VERSION = "3.1" ;
	$success = 0 ;
	$error = "" ;

	// put php version check module here
	// check_version() ;

	// if system if configured, then let's go to the menu options
	if ( file_exists( "../web/conf-init.php" ) )
	{
		HEADER( "location: login.php" ) ;
		exit ;
	}

	// open the language pack if passed
	if ( isset( $_POST['language'] ) && $_POST['language'] )
		include_once( "../lang_packs/$_POST[language].php" ) ;

	// do initial checks to make sure setup can run
	// if ( !is_dir( session_save_path() ) )
	// {
	//	print "<font color=\"#FF0000\">'session.save_path' directory not set!  Please set your session.save_path in your php.ini file.  It is usally set to /tmp for UNIX, C:\Temp for windows. After you have done this, reload this page.</font>" ;
	//	exit ;
	//}
	if ( file_exists( "../web" ) )
	{
		if ( !is_writable( "../web" ) )
		{
			print "<font color=\"#FF0000\">Please give '<i>web</i>' directory READ/WRITE permission by the browser. (<code>chmod o+rw web</code>).  The '<i>web</i>' directory is located in your root install location.  After you have done this, reload this page.</,font>" ;
			exit ;
		}
		else
		{
			if ( is_dir( "../web/chatsessions" ) != true )
				mkdir( "../web/chatsessions", 0777 ) ;
			if ( is_dir( "../web/chatrequests" ) != true )
				mkdir( "../web/chatrequests", 0777 ) ;
			if ( is_dir( "../web/chatpolling" ) != true )
				mkdir( "../web/chatpolling", 0777 ) ;
		}
	}
	else
	{
		print "<font color=\"#FF0000\">Please create a '<i>web</i>' directory in your root install location.  Make it READ/WRITE permission by the browser. (<code>chmod o+rw web</code>).  After you have done this, reload this page.</font>" ;
		exit ;
	}

	srand((double)microtime());
	$rand = mt_rand(0,1000) ;

	// functions
	function checkVersion( $version )
	{
		if ( phpversion() >= $version )
			return true ;
		return false ;
	}

	function dump_db( $db_name, $db_host, $db_login, $db_password )
	{
		$connection = mysql_pconnect( $db_host, $db_login, $db_password ) ;
		if ( !mysql_select_db( $db_name ) )
			return "<p>Error: Could not locate database[ $db_name ]<p>" ;

		$fp = fopen ("../super/atendimentochat.txt", "r") ;
		while (!feof ($fp))
		{
			unset ( $query ) ;
			unset ( $error ) ;
			$buffer = fgets($fp, 1000);

			if ( preg_match( "/(DROP TABLE)/", $buffer ) )
			{
				$query = substr( $buffer, 0, strlen( $buffer ) - 2 ) ;
				$query = stripslashes( $query ) ;
				$result = mysql_query( $query, $connection ) ;
				$mysql_error .=  mysql_error() ;
			}
			
			if ( preg_match( "/(CREATE TABLE)/", $buffer ) )
			{
				$query .= $buffer ;
				if ( !preg_match( "/\) TYPE=MyISAM/", $buffer ) )
				{
					while ( $buffer = fgets( $fp, 500 ) )
					{
						if ( preg_match( "/\) TYPE=MyISAM/", $buffer ) ){ break 1 ; }
						$query .= $buffer ;
					}
					if ( !preg_match( "/\) TYPE=MyISAM/", $query ) )
						$query = "$query);" ;
				}
				$query = stripslashes( $query ) ;
				$result = mysql_query( $query, $connection ) ;
				$mysql_error .=  mysql_error() ;
			}

			if ( preg_match( "/(INSERT INTO)/", $buffer ) )
			{
				$query = substr( $buffer, 0, strlen( $buffer ) - 2 ) ;
				$query = stripslashes( $query ) ;
				$result = mysql_query( $query, $connection ) ;
				$mysql_error .=  mysql_error() ;
			}
		}
		fclose( $fp ) ;
		mysql_close( $connection ) ;

		if ( $mysql_error )
			$error = "<p>Error: Following database error(s) were generated: <br>$mysql_error<p><a href=\"http://www.atendchats.c0m/documentation/viewarticle.php?aid=35\" target=\"new\">Verifying your MySQL Information Help</a><p>" ;

		return $error ;
	}

	// initialize and get vars
	$action = $override = "" ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_POST['override'] ) ) { $override = $_POST['override'] ; }

	// conditions
	
	if ( $action == "update db" )
	{
		$db_host = $_POST['db_host'] ;
		$db_login = $_POST['db_login'] ;
		$db_password = $_POST['db_password'] ;
		$db_name = $_POST['db_name'] ;

		$connection = mysql_connect( $db_host, $db_login, $db_password ) ;
		mysql_select_db( $db_name ) ;
		$sth = mysql_query( "SHOW TABLES", $connection ) ;
		$error = mysql_error() ;
		if ( $error )
		{
			$action = "update company" ;
			$error = "<p>Error: Database produced the following error(s).  Please correct and submit.<br>-- $error --<p><a href=\"http://www.atendchats.c0m/documentation/viewarticle.php?aid=35\" target=\"new\">Verifying your MySQL Information Help Docs</a><p>" ;
		}
		else
		{
			$error = dump_db( $db_name, $db_host, $db_login, $db_password ) ;
			if ( !$error )
			{
				if ( !$error )
				{
					$document_root = stripslashes( $_POST['document_root'] ) ;
					$site_name = addslashes( $_POST['site_name'] ) ;
					$conf_string = "0LEFT_ARROW0?php
						\$ASP_KEY = '' ;
						\$NO_PCONNECT = '$_POST[no_pconnect]' ;
						\$DATABASETYPE = '$_POST[db_type]' ;
						\$DATABASE = '$db_name' ;
						\$SQLHOST = '$db_host' ;
						\$SQLLOGIN = '$db_login' ;
						\$SQLPASS = '$db_password' ;
						\$DOCUMENT_ROOT = '$_POST[document_root]' ;
						\$BASE_URL = '$_POST[base_url]' ;
						\$SITE_NAME = '$site_name' ;
						\$LOGO_ASP = 'phplive_logo.gif' ;
						\$LANG_PACK = '$_POST[language]' ;?0RIGHT_ARROW0" ;

					// create and put configuration data
					$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
					$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
					$fp = fopen ("../web/conf-init.php", "wb+") ;
					fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
					fclose( $fp ) ;

					if ( ( is_dir( "../web/$_POST[login]" ) != true ) && isset( $_POST['login'] ) )
						mkdir( "../web/$_POST[login]", 0777 ) ;

					if ( file_exists( "../admin/traffic/admin_puller.php" ) )
						$initiate = 1 ;
					else
						$initiate = 0 ;
					$COMPANY_NAME = addslashes( $_POST['company'] ) ;
					$conf_string = "0LEFT_ARROW0?php
						\$LOGO = '' ;
						\$COMPANY_NAME = '$COMPANY_NAME' ;
						\$SUPPORT_LOGO_ONLINE = 'atendimento_online.gif' ;
						\$SUPPORT_LOGO_OFFLINE = 'atendimento_offline.gif' ;
						\$SUPPORT_LOGO_AWAY = '' ;
						\$VISITOR_FOOTPRINT = '1' ;
						\$THEME = 'default' ;
						\$POLL_TIME = '45' ;
						\$INITIATE = '$initiate' ;
						\$INITIATE_IMAGE = '' ;
						\$IPNOTRACK = '' ;
						\$LANG_PACK = '$_POST[language]'; ?0RIGHT_ARROW0" ;

					$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
					$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
					$fp = fopen ("../web/$_POST[login]/$_POST[login]-conf-init.php", "wb+") ;
					fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
					fclose( $fp ) ;

					// let's create an index file for the user so
					// the path is more nice...
					// (/phplive/<user>/ instead of /phplive/index.php?l=<user>)
					$index_string = "0LEFT_ARROW0?php \$path = explode( \"/\", \$_SERVER['PHP_SELF'] ) ; \$total = count( \$path ) ; \$login = \$path[\$total-2] ; \$winapp = isset( \$_GET['winapp'] ) ? \$_GET['winapp'] : \"\" ; HEADER( \"location: ../../index.php?l=\$login&winapp=\$winapp\" ) ; exit ; ?0RIGHT_ARROW0" ;
					$index_string = preg_replace( "/0LEFT_ARROW0/", "<", $index_string ) ;
					$index_string = preg_replace( "/0RIGHT_ARROW0/", ">", $index_string ) ;
					$fp = fopen ("../web/$_POST[login]/index.php", "wb+") ;
					fwrite( $fp, $index_string, strlen( $index_string ) ) ;
					fclose( $fp ) ;

					// now let's create an index.php page in the web/ directory for
					// extra security
					$index_string = "&nbsp;" ;
					$fp = fopen ("../web/index.php", "wb+") ;
					fwrite( $fp, $index_string, strlen( $index_string ) ) ;
					fclose( $fp ) ;

					/*********** insert new data ***************/
					$now = time() ;
					$connection = mysql_connect( $db_host, $db_login, $db_password ) ;
					mysql_select_db( $db_name ) ;
					$trans_email = "Ola %%username%%,

Segue abaixo a copia da sua conversa de chat:

===
%%transcript%%
===

Muito Obrigado.

" ;
					$query = "INSERT INTO chat_asp VALUES (0, '$_POST[login]', '$_POST[password]', '$_POST[company]', '$_POST[contact_name]', '$_POST[contact_email]', '15', '100', '1', '$now', 0, 1, 1, 0, 0, '(opcional) Se voce deseja receber uma copia deste bate-papo, por favor insira o seu email abaixo e clique em enviar.', '$trans_email')" ;
					mysql_query( $query, $connection ) ;
					/********************************************/

					// create and put version file
					$version_string = "0LEFT_ARROW0?php \$PHPLIVE_VERSION = \"$PHPLIVE_VERSION\" ; ?0RIGHT_ARROW0" ;
					$version_string = preg_replace( "/0LEFT_ARROW0/", "<", $version_string ) ;
					$version_string = preg_replace( "/0RIGHT_ARROW0/", ">", $version_string ) ;
					$fp = fopen ("../web/VERSION_KEEP.php", "wb+") ;
					fwrite( $fp, $version_string, strlen( $version_string ) ) ;
					fclose( $fp ) ;

					$url = $_POST['base_url'] ;
					$os = $_SERVER['SERVER_SOFTWARE'] ;
					$os = urlencode( $os ) ;
					$fp = fopen ("http://www.atendchat.c0m/stats/patch.php?v=$PHPLIVE_VERSION&url=$url&os=$os&users=INSTALL&ops=0", "r") ;
					fclose( $fp ) ;

					copy( "../files/nodelete.php", "../web/$_POST[login]/nodelete.php" ) ;

					HEADER( "location: ../super" ) ;
					exit ;
				}
			}
			else
			{
				$action = "update company" ;
				$error = "<p>Error: Database produced the following error(s).  Please correct and submit.<br>-- $error --<p><a href=\"http://www.atendchats.c0m/documentation/viewarticle.php?aid=35\" target=\"new\">Verifying your MySQL Information Help Docs</a><p>" ;
			}
		}
	}
	else if ( $action == "update document root" )
	{
		$document_root = $_POST['document_root'] ;
		$str_len = strlen( $document_root ) ;
		$last = $document_root[$str_len-1] ;
		if ( ( $last == "/" ) || ( $last == "\\" ) )
			$document_root = substr( $document_root, 0, $str_len - 1 ) ;

		if ( !file_exists( "$document_root/super/atendimentochat.txt" ) )
		{
			$action = "update site name" ;
			$temp_root = stripslashes( $document_root ) ;
			$error = "Error: $temp_root - This is NOT the correct unpacked path of the system.  Please correct and submit." ;
		}
	}
	else if ( $action == "update base url" )
	{
		$document_root = $_POST['document_root'] ;
		$base_url = $_POST['base_url'] ;
		$str_len = strlen( $base_url ) ;
		$last = $base_url[$str_len-1] ;
		if ( ( $last == "/" ) || ( $last == "\\" ) )
			$base_url = substr( $base_url, 0, $str_len - 1 ) ;

		//if ( !fopen ("$base_url/super/atendimentochat.txt", "r") )
		//{
		//	$action = "update document root" ;
		//	$error = "Error: $base_url - This is NOT the correct URL of the system.  Please correct and submit." ;
		//}
	}
	else
	{
		if ( !checkVersion( "4.0.6" ) && !$override )
		{
			print "<font color=\"#FF0000\">Your current PHP version ".phpversion()." is not compatible with the Support system v".$PHPLIVE_VERSION.".  Please upgrade your PHP to 4.0.6 or greater.  We recommend you install the latest PHP version from <a href=\"http://www.php.net/downloads.php\" target=\"new\">PHP.net</a>.  Please contact your server admin to upgrade your current PHP build.</font>" ;
			exit ;
		}
	}
?>
<?php include_once( "../super/header.php" ) ?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<script language="JavaScript">
<!--

	var url = location.toString() ;
	url = replace( url, "setup/index.php", "" ) ;

	function do_db_update()
	{
		if ( ( document.form.db_name.value == "" ) || ( document.form.db_host.value == "" )
			|| ( document.form.db_login.value == "" ) || ( document.form.db_password.value == "" ) )
			alert( "All fields must be supplied." )
		else
			document.form.submit() ;
	}

	function do_user_update()
	{
		if ( ( document.form.company.value == "" ) || ( document.form.login.value == "" )
			|| ( document.form.password.value == "" ) || ( document.form.contact_name.value == "" )
			|| ( document.form.contact_email.value == "" ) )
			alert( "All fields MUST be filled." ) ;
		else if ( document.form.company.value.indexOf("'") != -1 )
			alert( "Company name cannot have a single quote (')." ) ;
		else
			document.form.submit() ;
	}
//-->
</script>

<font color="#FF0000"><?php echo $error ?></font><br>
<form method="POST" action="index.php" name="form">


		<?php if ( $action == "update document root" ): ?>
		<input type="hidden" name="action" value="update base url">
		<input type="hidden" name="language" value="<?php echo $_POST['language'] ?>">
		<input type="hidden" name="site_name" value="<?php echo $_POST['site_name'] ?>">
		<input type="hidden" name="document_root" value="<?php echo stripslashes( $document_root ) ?>">
		<span class="title">Configurar URL.</span>
		<br>
		<span class="basetxt">Esta &eacute; a URL Completa do sistema.
<p>

		Exemplo:<br>
		<font color="#660000">http://atendimento.dominio.com<br>
		http://www.dominio.com/atendimento</font>
		<br>
		<table cellpadding=5 cellspacing=1 border=0>
		<tr>
			<td><span class="basetxt">URL</td>
		  <td><span class="basetxt"> <input type="text" name="base_url" size=30 maxlength=120></td><td> <input type="submit" class="mainButton" value="Enviar" border=0></td>
		</tr>
		</table>
		<script language="JavaScript"> document.form.base_url.value = url ; </script>






		<?php elseif ( $action == "update base url" ): ?>
		<input type="hidden" name="action" value="update company">
		<input type="hidden" name="language" value="<?php echo $_POST['language'] ?>">
		<input type="hidden" name="site_name" value="<?php echo $_POST['site_name'] ?>">
		<input type="hidden" name="document_root" value="<?php echo stripslashes( $_POST['document_root'] ) ?>">
		<input type="hidden" name="base_url" value="<?php echo stripslashes( $base_url ) ?>">
		<span class="title">Informa&ccedil;&otilde;es da sua Empresa.</span>
		<br>
		
  <br>
		<font color="#FF0000">(n&atilde;o inclua aspas simples (') no nome da sua empresa!)</font>
        <p>
		<table cellpadding=1 cellspacing=1 border=0>
		<tr>
			<td><span class="basetxt">Empresa</td>
			<td><span class="basetxt"><font size=2 face="arial"> <input type="text" name="company" size="<?php echo $text_width ?>" maxlength="50"></td>
		</tr>
		<tr>
			<td><span class="basetxt">Login do Setup</td>
			<td><font size=2 face="arial"> <input type="text" name="login" size="<?php echo $text_width ?>" maxlength="15"></td>
			<td><span class="basetxt">Senha</td>
			<td><span class="basetxt"><font size=2 face="arial"> <input type="text" name="password" size="<?php echo $text_width ?>" maxlength="15"></td>
		</tr>
		<tr>
			<td><span class="basetxt">Nome</td>
		  <td><span class="basetxt"><font size=2 face="arial"> <input type="text" name="contact_name" size="<?php echo $text_width ?>" maxlength="50"></td>
			<td><span class="basetxt">Email</td>
			<td><span class="basetxt"><font size=2 face="arial"> <input type="text" name="contact_email" size="<?php echo $text_width ?>" maxlength="150"></td>
		</tr>
		<tr>
			<td colspan=4>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="button" OnClick="do_user_update()" class="mainButton" value="Enviar"></td>
		</tr>
		</table>
		</table>







		
		<?php elseif ( $action == "update company" ): ?>
		<input type="hidden" name="action" value="update db">
		<input type="hidden" name="language" value="<?php echo $_POST['language'] ?>">
		<input type="hidden" name="site_name" value="<?php echo $_POST['site_name'] ?>">
		<input type="hidden" name="document_root" value="<?php echo stripslashes( $_POST['document_root'] ) ?>">
		<input type="hidden" name="base_url" value="<?php echo stripslashes( $_POST['base_url'] ) ?>">
		<input type="hidden" name="company" value="<?php echo $_POST['company'] ?>">
		<input type="hidden" name="login" value="<?php echo $_POST['login'] ?>">
		<input type="hidden" name="password" value="<?php echo $_POST['password'] ?>">
		<input type="hidden" name="contact_name" value="<?php echo $_POST['contact_name'] ?>">
		<input type="hidden" name="contact_email" value="<?php echo $_POST['contact_email'] ?>">
		<span class="title">Configure Database.</span>
		<br>
		<br>
		<font color="#660000">
		<big><b></big></b>		</font>
        <p>
		
		<input type="hidden" value="1" name="no_pconnect">
		<p>
		<table cellpadding=2 cellspacing=1 border=0>
		<tr>
			<td><span class="basetxt">Tipo do Banco de Dados</td>
			<td><span class="basetxt"> <select name="db_type"><option value='mysql'>MySQL</select></td>
		</tr>
		<tr>
			<td><span class="basetxt">Nome do Banco de Dados</td>
			<td><span class="basetxt"> <input type="text" name="db_name" size=15 maxlength="200"></td>
		</tr>
		<tr>
			<td colspan=2><font size=1 face="arial">DB Host geralmente &eacute; configurado para localhost.</td>
		</tr>
		<tr>
			<td><span class="basetxt">BD Host</td>
			<td><span class="basetxt"> <input type="text" name="db_host" size=15 maxlength="200" value="localhost"></td>
		</tr>
		<tr>
			<td><span class="basetxt">Login do Banco de Dados</td>
		  <td><span class="basetxt"> <input type="text" name="db_login" size=15 maxlength="200"></td>
		</tr>
		<tr>
			<td><span class="basetxt">Senha do Banco de Dados</td>
			<td><span class="basetxt"> <input type="text" name="db_password" size=15 maxlength="200"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="button" OnClick="do_db_update()" class="mainButton" value="Enviar"></td>
		</tr>
		</table>












		<?php 
			elseif( $action == "update site name" ):
			$path_translated = ( isset( $_SERVER['PATH_TRANSLATED'] ) ) ? stripslashes( $_SERVER['PATH_TRANSLATED'] ) : stripslashes( $_SERVER['SCRIPT_FILENAME'] ) ;
			$temp_root = preg_replace( "/setup(.*?).php/i", "", $path_translated ) ;
		?>
		<input type="hidden" name="action" value="update document root">
		<input type="hidden" name="site_name" value="<?php echo $_POST['site_name'] ?>">
		<input type="hidden" name="language" value="<?php echo $_POST['language'] ?>">
		<span class="title">Pasta Raiz.</span>
		
		<span class="basetxt">
<p>

		Exemplo:<br>
		<font color="#660000">UNIX: /home/user/atendimento<br>
		Windows: C:\Apache\htdocs\atendimento</font>
		<br>
		<table cellpadding=5 cellspacing=1 border=0>
		<tr>
			<td><span class="basetxt">Pasta Raiz</td>
		  <td><span class="basetxt"> <input type="text" name="document_root" size=30 maxlength=120 value="<?php echo $temp_root ?>"></td><td> <input type="submit" class="mainButton" value="Enviar" border=0></td>
		</tr>
		</table>





		<?php else: ?>
		<input type="hidden" name="action" value="update site name">
		<span class="title">Nome do seu Site.</span>
  <br>
		<table cellpadding=5 cellspacing=1 border=0>
		<tr>
			<td><span class="basetxt">Nome do Site</td>
		  <td><span class="basetxt"> <input type="text" name="site_name" size=15 maxlength=35 value="Atendimento"></td>
		</tr>
		<tr>
			<td><span class="basetxt">Idioma</td>
			<td><span class="basetxt">
			<select name="language">
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
			<td>&nbsp;</td><td><input type="submit" class="mainButton" value="Enviar"></td>
		</tr>
		</table>





		<?php endif ; ?>
	</form>
	</td></tr></table>
	</td>
  </tr>
  <tr> 
	<td height="20" align="center" class="bgFooter" style="height:20px"><?php echo $LANG['DEFAULT_BRANDING'] ?></td>
  </tr>
</table>
<!-- This navigation layer is placed at the very botton of the HTML to prevent pesky problems with NS4.x -->
</body>
</html>