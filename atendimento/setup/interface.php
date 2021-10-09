<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;

	$section = 2;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// Include header
	include_once("./header.php");


?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="15%" valign="top" align="center"><img src="../images/layoutg.png" /></td>
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Layout</span><br></p>
	
	  Personalize o Layout do seu sistema.<br>
			<big><li> <strong><a href="customize.php?action=themes">Temas Para Chat</a></strong></big></p>
			<p>
			Configure as imagens de status de atendimento.<br>
			<big>
			<li> <strong><a href="customize.php?action=icons">Icones Para Atendimento</a></strong></big></p>

			<?php if ( $INITIATE && !file_exists( "$DOCUMENT_ROOT/admin/auction/index.php" ) && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
			<p>
			Imagem Inicial Para Chat.<br>
			<big>
			<li> <strong><a href="customize.php?action=initiate">Imagem Inicial para Abordagem do Visitante</a></strong></big></p>
			<?php endif ; ?>
	</td>
	
</tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php
// Include Footer
include_once("footer.php");


?>
