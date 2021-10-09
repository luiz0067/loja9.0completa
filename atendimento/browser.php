<?php
	$mac = 0 ;
	$browser_os = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "" ;
	$action = ( isset( $_GET['action'] ) && $_GET['action'] ) ? $_GET['action'] : "" ;

	$ip = $_SERVER['REMOTE_ADDR'] ;

	if ( preg_match( "/Mac( |_)/", $browser_os ) )
		$mac = 1 ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Please update your browser.</title>

<script type="text/javascript" src="js/xmlhttp.js"></script>
<script language="JavaScript">
<!--

	function init()
	{
		// Check for browser support
		<?php if ( !preg_match( "/(ok)|(no)|(noxml)/", $action ) ): ?>
		if ( !document.createElement && !document.createElementNS )
			self.location.href = "http://www.kjdskjsdksdkjksdj.c0m/demos/atendimentoonline/browser.php?action=no";
		if ( !initxmlhttp() )
			self.location.href = "http://www.kjdskjsdksdkjksdj.c0m/demos/atendimentoonline/browser.php?action=noxml" ;
		
		self.location.href = "http://www.kjdskjsdksdkjksdj.c0m/demos/atendimentoonline/browser.php?action=ok";
		<?php endif ; ?>
	}

//-->
</script>

</head>

<body bgColor="#FFFFFF" text="#000000" OnLoad="init()">
<font size=2 face="arial">
	Seu Browser: <font color="#FF0000"><b><?php echo $browser_os ?></b></font>
	<br>
	<?php if ( $mac && ( $action == "no" ) ): ?>
	<h3>Error: Browser n&atilde;o supotado </h3>
	Por favor usar os seguintes browsers para o Mac:
	<ul>
		<li> <a href="http://www.apple.com/safari/" target="new">Safari >= 1.2</a>
		<li> <a href="http://www.mozilla.org/products/firefox/" target="new">Firefox >= 1.0</a>
		<li> <a href="http://channels.netscape.com/ns/browsers/download.jsp" target="new">Netscape >= 7.0</a>
	</ul>

	<?php elseif ( $action == "noxml" ): ?>
	<h3>Error: Atualize o componete xml do seu Browser</h3>

	Olha como seu browser &eacute; atual, mas um componente crucial necessita ser  atualizado. Seguir por favor abaixo o URL para promover suas  bibliotecas de MSXML de seu browser.
	<ul>
		<li> <a href="http://www.microsoft.com/downloads/details.aspx?FamilyID=3144b72b-b4f2-46da-b4b6-c5d7485f2b42&DisplayLang=en" target="new">MSXML HTTP parser</a>
	</ul>

	<?php elseif ( $action == "no" ): ?>
	<h3>Error: Browser n&atilde;o suportado </h3>
	Por favor usar os seguintes browsers para o Windows:
	<ul>
		<li> <a href="http://www.microsoft.com/windows/ie/downloads/default.mspx" target="new">Internet Explorer >= 6.0</a>
		<li> <a href="http://www.mozilla.org/products/firefox/" target="new">Firefox >= 1.0</a>
		<li> <a href="http://channels.netscape.com/ns/browsers/download.jsp" target="new">Netscape >= 7.0</a>
	</ul>

	<?php elseif ( $action == "ok" ): ?>
	<h3>Messagem: Browser &eacute; suportado </h3>
	Seu browser est&aacute;  OK!

	<?php else: ?>
	<h3>Detectando...</h3>

	<?php endif ; ?>
	<br><br>
	<form><input type="button" OnClick="window.close()" value="Fechar janela">
	</form>
</body>
</html>