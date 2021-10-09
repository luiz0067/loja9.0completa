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
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/ASP/update.php") ;
	$section = 5;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';
?>
<?php

	// initialize
	$action = $error = "" ;
	$success = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "60" ;
		$textbox_width = "70" ;
	}
	else
	{
		$text_width = "35" ;
		$textbox_width = "35" ;
	}

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
?>
<?php
	// functions
?>
<?php
	// conditions

	if ( $action == "update" )
	{
		AdminASP_update_TableValue( $dbh, $session_setup['aspID'], "trans_message", $_POST['trans_message'] ) ;
		AdminASP_update_TableValue( $dbh, $session_setup['aspID'], "trans_email", $_POST['trans_email'] ) ;
		$_SESSION['session_setup']['trans_message'] = $_POST['trans_message'] ;
		$_SESSION['session_setup']['trans_email'] = $_POST['trans_email'] ;
		$session_setup['trans_message'] = $_POST['trans_message'] ;
		$session_setup['trans_email'] = $_POST['trans_email'] ;
		$success = 1 ;
	}
?>
<?php include_once("./header.php"); ?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<script language="JavaScript">
<!--
	function do_submit()
	{
		if ( ( document.form.trans_message.value == "" ) || ( document.form.trans_email.value == "" ) )
			alert( "All fields MUST be provided." ) ;
		else if ( document.form.trans_email.value.indexOf("%%transcript%%") == -1 )
			alert( "Email body MUST contain the %%transcript%% variable." ) ;
		else
		{
			document.form.submit() ;
		}
	}

	function do_alert()
	{
		if( <?php echo $success ?> )
			alert( 'Success!' ) ;
	}
//-->
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
 <td width="15%" valign="top" align="center"><img src="../images/prefg.png"></td> 
  <td height="350" valign="top"> <p><span class="title">Prefer&ecirc;ncias: Enviar Conversas por Email</span><br>
	  Personalize a mensagem de Conversas Enviadas por Email ao Visitante.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Atualizado Com Sucesso!</b></big></font>" : "" ?></p>
	  <form method="POST" action="email_transcript.php" name="form">
	<input type="hidden" name="action" value="update">
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<td valign="top" align="right" nowrap><strong>Texto do Site:</strong></td>
		<td valign="top"> <input type="text" name="trans_message" size="<?php echo $text_width ?>" maxlength="255" style="width:300px" value="<?php echo stripslashes( $session_setup['trans_message'] ) ?>"></td>
	  </tr>
	  <tr>
		<td colspan=2>&nbsp;</td>
	  </tr>
	  <tr> 
		<td>&nbsp;</td>
		<td> <span class="hilight">%%username%%</span> - Nome de Usu&aacute;rio usado pelo Visitante (opcional)<br>
		  <span class="hilight">%%transcript%%</span> - Conversa de Chat Gravada.</td>
	  
	  <tr> 
		<td valign="top" align="right" nowrap><strong>Email:</strong></td>
		<td valign="top"> <textarea cols="<?php echo $textbox_width ?>" name="trans_email" rows="12" wrap="virtual" style="width:300px"><?php echo stripslashes( $session_setup['trans_email'] ) ?></textarea></td>
	  </tr>
	  <tr> 
		<td>&nbsp;</td>
		<td> <input type="button" class="mainButton" value="Enviar" OnClick="do_submit()"></td>
	  </tr>
	</table>
	</form>
	</td>
</tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php include_once( "./footer.php" ) ; ?>