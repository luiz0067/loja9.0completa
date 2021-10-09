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
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("../system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	$section = 1;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)
?>
<?php
	// initialize
	$action = "" ;
	$deptid = 0 ;
	$now = time() ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "30" ;
		$textbox_width = "80" ;
	}
	else
	{
		$text_width = "15" ;
		$textbox_width = "40" ;
	}

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }

	$nav_line = '<a href="options.php" class="nav">:: Home</a>' ;
	if ( $action )
		$nav_line = '<a href="code.php" class="nav">:: Previous</a>' ;

	// conditions
?>
<?php include_once("./header.php"); ?>
<script language="JavaScript">
<!--
	function gen_text_code()
	{
		if ( document.form_text.link_text.value == "" )
			alert( "Please provide a text." ) ;
		else
		{
			window.open( "code_text.php?deptid=<?php echo $deptid ?>&text="+escape( document.form_text.link_text.value ), "text_code", "scrollbars=yes,menubar=no,resizable=1,location=no,width=450,height=250" ) ;
		}
	}
//-->
</script>

<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
<td width="15%" valign="top" align="center"><img src="../images/gerenciarg.png" /></td>
  <td height="350" valign="top"> <p><span class="title">Gerenciador: Gerar C&oacute;digo
	  HTML </span><br />
Gerar e Inserir o c&oacute;digo HTML no seu website para fazer o atendimento.
</p>
<?php
	if ( $action == "generate" ):
	$BASE_URL_STRIP = $BASE_URL ;
	//$BASE_URL_STRIP = preg_replace( "/http:/", "", $BASE_URL ) ;
?>
<b>C&Oacute;DIGO HTML PARA O SITE.</b> Copie e Cole o c&oacute;digo abaixo nas p&aacute;ginas do seu site. </p>
    <ul>
		<span class="hilight"></span>
        <li> Insira o c&oacute;digo exatamente como ele &eacute; exibido. </li>
        <li><b>Insira o c&oacute;digo dentro das tags &lt;body&gt;&lt;/body&gt;</b>        </li><span class="hilight">        </span>
	</ul>
	<form>
	<textarea cols="<?php echo $textbox_width ?>" rows="8" wrap="virtual" onFocus="this.select(); return true;"><!-- Começo Código Atendimento Online -->
<script language="JavaScript" src="<?php echo $BASE_URL_STRIP ?>/js/status_image.php?base_url=<?php echo $BASE_URL_STRIP ?>&l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=<?php echo $deptid ?>&"></script>
<!-- FIM Código Atendimento Online --></textarea>
	</form>
	O c&oacute;digo acima ir&aacute; produzir a seguinte imagem de link de atendimento.<br>
<!-- Começo Código Atendimento Online -->
<script language="JavaScript" src="<?php echo $BASE_URL_STRIP ?>/js/status_image.php?base_url=<?php echo $BASE_URL_STRIP ?>&l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=<?php echo $deptid ?>&"></script>
<!-- FIM Código Atendimento Online -->
	<p>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="hdash">&nbsp;</td>
	  </tr>
	</table>
	<form name="form_text">
	 <b>C&Oacute;DIGO HTML PARA LINK DE TEXTO APENAS (SEM IMAGEM).</b>
	<span class="medium">
	<li>Insira um texto abaixo para gerar o c&oacute;digo para criar um link de texto  sem imagem.
	<br><input type="text" name="link_text" size="<?php echo $text_width ?>" maxlength="255"> <input type="button" OnClick="gen_text_code()" value="Gerar Codigo" class="mainButton">
	</form>
	<p>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="hdash">&nbsp;</td>
	  </tr>
	</table>
	<form>
	 <b>C&Oacute;DIGO PARA ASSINATURA DE EMAIL</b>
	<br>
	<span class="hilight">* N&atilde;o utilize este c&oacute;digo para inserir no seu website. Este c&oacute;digo &eacute; apenas para assinatura de emails.</span>
	<span class="medium">
	<li> C&oacute;digo para assinatura de email.
	  <p>
      <textarea cols="<?php echo $textbox_width ?>" rows="5" wrap="virtual" onFocus="this.select(); return true;"><a href="<?php echo $BASE_URL ?>/request_email.php?l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=<?php echo $deptid ?>" target="new"><img src="<?php echo $BASE_URL ?>/image.php?l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=<?php echo $deptid ?>&refer=Email+Signature" border=0 alt="Clique Aqui Para Atendimento Online!"></a></textarea>
	</form>



<?php
	else:
	$departments = AdminUsers_get_AllDepartments( $dbh, $session_setup['aspID'], 1 ) ;
	$totalusers = AdminUsers_get_TotalUsers( $dbh, $session_setup['aspID'] ) ;
?>
	<ul>
		<?php if ( count( $departments ) <= 0 ): ?>
		<span class="hilight">Antes de gerar o c&oacute;digo HTML para inserir no seu website voc&ecirc; deve <a href="<?php echo $BASE_URL ?>/setup/adddept.php">Criar um Departamento no Sistema</a>.</span>


		<?php elseif ( $totalusers <= 0 ): ?>
		<span class="hilight">Antes de gerar o c&oacute;digo HTML para inserir no seu website voc&ecirc; deve <a href="<?php echo $BASE_URL ?>/setup/adduser.php">Criar um Operador no Sistema</a>.</span>



		<?php else: ?>
		<ul>
			<?php
				$ok = 0 ;
				$output_string = "" ;
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$department = $departments[$c] ;
					$dept_name = stripslashes( $department['name'] ) ;
					$totaluserdeptlist = AdminUsers_get_TotalUsersDeptList( $dbh, $session_setup['aspID'], $department['deptID'] ) ;
					if ( $totaluserdeptlist )
					{
						$ok = 1 ;
						if ( $department['visible'] )
							$output_string .= "<li> <a href=\"code.php?action=generate&deptid=$department[deptID]&\">$dept_name</a>\n" ;
						else
							$output_string .= "<li> $dept_name (<span class=\"hilight\">Departamento Escondido - c&oacute;digo indispon&iacute;vel</span>)\n" ;
					}
					else
					{
						$output_string .= "<li> $dept_name (<span class=\"hilight\">Aviso: Nenhum Operador Atribu&iacute;do ao departamento.</span> <a href=\"adduser.php\">Adicionar Operadores</a>)\n" ;
					}
				}

				if ( !$ok )
					print "<span class=\"hilight\">Voc&ecirc; n&atilde;o atribuiu nenhum operador a um departamento. Antes de gerar o c&oacute;digo HTML voc&ecirc; precisa atribuir um operador a um departamento.</span>" ;
				else
					print "<li> <a href=\"$BASE_URL/setup/code.php?action=generate&deptid=0\"><strong>GERAR C&Oacute;DIGO HTML PARA TODOS OS DEPARTAMENTOS.</strong></a> <p><ul>Gerar HTML apenas para o departamento especificado abaixo. $output_string</ul>" ;
			?>
		  </ul>
		  <?php endif ; ?>
	</ul>
	
    <?php endif ;?>  </td>
</tr>
</table>


<?php include_once( "./footer.php" ) ; ?>