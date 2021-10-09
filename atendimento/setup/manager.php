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
	$section = 1;			// Section number - see header.php for list of section numbers

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
<td width="15%" valign="top" align="center"><img src="../images/gerenciarg.png" /></td>
  <td width="100%" valign="top"> 
	<p><span class="title">Gerenciar Operadores e Departamentos</span><br>
	</p>
	  <p>Aqui voc&ecirc; pode criar, ediatr e deletar os departamentos do Atendimento Online.<br>
		<big>
		<li> <strong><a href="adddept.php">Criar e Editar Departamentos</a></strong></big></p>
	      <p>Aqui voc&ecirc; pode criar, editar e deletar os operadores do Atendimento Online.<br>
		<big>
	  <li> <strong><a href="adduser.php">Criar e Editar Operadores</a></strong></big></p>
	      <p>
		Gerar c&oacute;digo HTML de atendimento para inserir no seu site.<br>
		<big>
	  <li> <strong><a href="code.php">Gerar HTML</a></strong></big></p>
	</td>
  
</tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php
// Include Footer
include_once("footer.php");


?>
