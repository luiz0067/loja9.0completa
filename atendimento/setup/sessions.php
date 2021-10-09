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
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	$section = 4;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// Include header
	include_once("./header.php");
?> 
<table width="100%" border="0" cellspacing="0" cellpadding="15">
  <tr> 
    <td width="15%" valign="top" align="center"><img src="../images/sessoesg.png" /></td> 
	<td width="100%" valign="top"> <p><span class="title">Sess&otilde;es de Chat</span><br></p>
	  <p>
		Lista Completa dos processos de chat ativos no momento.<br>
		<big>
	  <li> <strong><a href="processes.php?action=chat">Sess&otilde;es de Chats Ativos</a></strong></big></p>
	      <p>
		Visualizar/Procurar conversas gravadas por departamento ou operador.<br>
		<big>
	  <li> <strong><a href="transcripts.php">Conversas Gravadas</a></strong></big></p>
	    <p>Lista Completa de todos os operadores e o status dos operadores no sistema.<br>
		<big>
    <li> <strong><a href="processes.php?action=consol">Status dos Operadores</a></strong></big></p></td>
  </tr>
</table>
<?php
// Include Footer
include("footer.php");
?>