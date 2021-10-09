<?php
	/*******************************************************
	* Atendimento On-Line
	*******************************************************/
	include_once("./web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;

	// initialize
	$action = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	$success = 0 ;
	// update all admins status to not available if they have been idle

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
?>
<html>
<head>
<title> Atendimento - Quick Help </title>
<?php $css_path = ( !isset( $css_path ) ) ? $css_path = "./" : $css_path ; include_once( $css_path."css/default.php" ) ; ?>

<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<script language="JavaScript">
<!--

//-->
</script>
<body bgColor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr>
	<td valign="top" class="bg" width="100%">

		<table cellspacing=1 cellpadding=3 border=0 width="95%">
		<tr>
			<td><span class="basicTitle">Atendimento Online, Ajuda! </span>
			  <p>

			<?php if ( $action == "sp_cell" ): ?>
			<b>
			<li> Formato de telefone Celular</b><br>
			  Incluir sempre seu C&Oacute;DIGO de PA&Iacute;S em seu telefone. Para  clientes do Brasil, incluir por favor o  seu c&oacute;digo de  &aacute;rea. Para todos os clientes internacionais, incluir seu c&oacute;digo de  pa&iacute;s:
			  <ul>
			     Exemplo de formato de celular
				 <li> 444636806 (exemplo BR)
			    <li> 448885550000 (exemplo UK)
			</ul>
				

			<?php elseif ( $action == "share_transcripts" ): ?>
			<b>
			<li> Compartilhar Conversas Gravadas</b><br>
			Se voc� permitir compartilhar os transcripts, todos os operadores que s�o atribu�dos a um departamento v�em cada um - outros transcripts. Se voc� n�o permitir compartilhar dos transcripts, os operadores somente v�em seus pr�prios transcripts.


			<?php elseif ( $action == "visible" ): ?>
			<b><li>Departamento vis&iacute;vel ao p&uacute;blico (n&atilde;o escondido)</b><br>
			  s departamentos escondidos permitir&atilde;o que os gerentes/admins monitorem  e recebem as chamadas que s&atilde;o transferidas somente a elas ou atrav&eacute;s do  bate-papo do operador-&agrave;-operador. Os departamentos escondidos n&atilde;o s&atilde;o  vis&iacute;veis ou diretamente acess&iacute;vel ao p&uacute;blico.
			  <?php elseif ( $action == "show_que" ): ?>
			<b><li>Bate-papo Que da exposi&ccedil;&atilde;o</b><br>
			  Quando a sustenta&ccedil;&atilde;o do pedido dos visitantes, o sistema indicar&aacute; o  n&uacute;mero dos bate-papos atuais ativos (que). Voc&ecirc; pode esconder ou  indicar esta informa&ccedil;&atilde;o ao visitante.
			  <?php elseif ( $action == "sp_cpage" ): ?>
			<b><li>P&aacute;gina completa da confirma&ccedil;&atilde;o da compra</b><br>
			  Tipicamente, os Web site de cada eCommerce t&ecirc;m um processo b&aacute;sico da  ordem: browse e bens seletos/servi&ccedil;os, capturar o pagamento em linha e  &uacute;ltima uma p&aacute;gina completa da confirma&ccedil;&atilde;o da compra ou obrigado paginar.
			  <p>Das &ldquo;o c&oacute;digo do HTML do trajeto vendas&rdquo; pertence na p&aacute;gina completa da confirma&ccedil;&atilde;o da compra.
			  <p><strong>Pergunta:</strong> Que se eu tiver um certificado que cr�e dinamicamente uma p�gina completa da confirma��o da compra? <br> 
			    <strong>Resposta:</strong> Colocar simplesmente das �o c�digo do HTML do trajeto vendas� na parcela da confirma��o do certificado (direito depois que a sa�da do recibo � muito bem).


			<?php elseif ( $action == "sp_message" ): ?>
			<b>
			<li> &lt;Sua Messagem &gt;</b><br>
			Se voc� observar das �no c�digo do HTML do trajeto vendas�, h� <span class="hilight">&lt;Sua Mensagem&gt;</span> 	
corda. Voc� necessita substituir este com o aquele de sua mensagem pessoal da notifica��o. Esta mensagem estar� emitida a seu celular ou email cada vez que das �um trajeto vendas� � seguido. Mensagens da notifica��o do exemplo do <ul> O <li> come�ou apenas vendas! Pessoa do <li> A assinada apenas acima! </ul> Se voc� for computador e codificar savvy, voc� pode din�micamente p�r vari�veis a sua mensagem. Por exemplo, se voc� tiver produtos diferentes e os gostar de incluir essa informa��o em suas notifica��es, gerar simplesmente a mensagem da notifica��o de seu certificado! Mensagens din�micas da notifica��o do exemplo do <li> &lt;? print $total ?&gt; units sold at $&lt;? print $cost ?&gt;     </ul>

			<?php elseif ( $action == "commands" ): ?>
			<ul>
				<li> <span class="hilight"><b>url:</b></span><i>URL</i> (hyperlink a URL) 
				<li> <span class="hilight"><b>image:</b></span><i>URL/sample.gif</i> (embed an image)
				<li> <span class="hilight"><b>email:</b></span><i>example@atendchat.c0m</i> (hyperlink an email)
				<li> <span class="hilight"><b>push:</b></span><i>URL</i> (opens new browser containing URL of webpage, word docs, etc.)
			</ul>

			<p>
			exemplos:<br>
			<code>url:http://www.website.com/</code><br>
			<code>email:exemplo@dominio.com</code>


			<?php elseif ( $action == "email_transcripts" ): ?>
			<b><li> Visitor Email Transcripts</b><br>
			When a visitor ends the chat session, he/she can request a copy of the transcript to their email address.  You can enable or disable this feature.  In some sales environment, this option may be useful.



			<?php elseif ( $action == "traffic_monitor" ): ?>
			<b><li> Operator Traffic Monitor</b><br>
			Enabling this feature allows operators within this department to view the website traffic on their operator consoles.  This also allows operators to initiate chat with your website visitors.


			<?php endif ; ?>

			<p>
			<form><input type="button" class="mainButton" value="Fechar Janela" OnClick="window.close()">
			</form>
			</td>
		</tr>
		</table>
	</td>
  </tr>
  <tr> 
	<td height="20" align="right" class="bgFooter" style="height:20px"><img src="<?php echo $css_path ?>images/bg_corner_footer.gif" alt="" width="94" height="20"></td>
  </tr>
  <tr>
  <!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
	<td height="20" align="center" class="bgCopyright" style="height:20px">
		<?php echo $LANG['DEFAULT_BRANDING'] ?>
	</td>
  </tr>
</table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->

</body>
</html>
