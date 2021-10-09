<?php
	if ( file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/$session_setup[login]/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	$initiate_chat_menu = $trackit_menu = $salespath_menu = "" ;
	if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) )
	{
		$initiate_chat_menu = " | <a href=\"$BASE_URL/setup/customize.php?action=initiate\" class=\"nav\">Imagem Inicial de Chat</a>" ;
		$trackit_menu = "<a href=\"$BASE_URL/admin/traffic/click_track.php\" class=\"nav\">Rastreio de Campanhas por Clique PPC (Pay Per Click)</a><!-- | <a href=\"$BASE_URL/admin/traffic/conversion.php\" class=\"nav\">Click Conversion</a> -->" ;
	}
	if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/salespath.php" ) )
		$salespath_menu = " | <a href=\"$BASE_URL/setup/salespath.php\" class=\"nav\">Sales Path</a>" ;
?>
<html>
<head>
<title>Atendimento - Setup</title>
<?php $css_path = ( !isset( $css_path ) ) ? $css_path = "../" : $css_path ; include_once( $css_path."css/default.php" ) ; ?>
<script language="JavaScript" type="text/JavaScript">
<!--

var section = <?php echo $section ; ?> ;	// Section number

var nav_start = '<table width="427" border="0" cellspacing="0" cellpadding="3"><tr><td align="center" class="nav">';
var nav_end = '</td></tr></table>';

var nav = new Array();
nav[0] = '<a href="<?php echo $BASE_URL ?>/setup/options.php" class="nav">Home</a>';
nav[1] = '<a href="<?php echo $BASE_URL ?>/setup/adddept.php" class="nav">Gerenciar Departamentos</a> | <a href="<?php echo $BASE_URL ?>/setup/adduser.php" class="nav">Gerenciar Operadores</a> | <a href="<?php echo $BASE_URL ?>/setup/code.php" class="nav">Gerar C&oacute;digo HTML</a>';
nav[2] = '<a href="<?php echo $BASE_URL ?>/setup/customize.php?action=logo" class="nav">Logomarca Janela Chat/Setup</a> | <a href="<?php echo $BASE_URL ?>/setup/customizelogo.php" class="nav">Logomarca Geral</a> | <a href="<?php echo $BASE_URL ?>/setup/customize.php?action=colors" class="nav">Temas</a> | <a href="<?php echo $BASE_URL ?>/setup/customize.php?action=icons" class="nav">Icones de Atendimento</a> <?php echo $initiate_chat_menu ?>';
nav[3] = '<a href="<?php echo $BASE_URL ?>/setup/footprints.php" class="nav">Tr&aacute;fego e Acessos</a> | <a href="<?php echo $BASE_URL ?>/setup/refer.php" class="nav">URLs de Refer&ecirc;ncia</a>';
nav[4] = '<a href="<?php echo $BASE_URL ?>/setup/processes.php?action=chat" class="nav">Chats Ativos</a> | <a href="<?php echo $BASE_URL ?>/setup/transcripts.php" class="nav">Conversas Gravadas</a> | <a href="<?php echo $BASE_URL ?>/setup/processes.php?action=consol" class="nav">Sess&otilde;es dos Operadores</a>';
nav[5] = '<a href="<?php echo $BASE_URL ?>/setup/prefs.php?action=footprints" class="nav">Excluir IP</a> | <a href="<?php echo $BASE_URL ?>/setup/email_transcript.php" class="nav">Enviar Conversas por Email</a> | <a href="<?php echo $BASE_URL ?>/setup/prefs.php?action=timezone" class="nav">Fuso Hor&aacute;rio (Time Zone)</a>';
nav[6] = '<a href="<?php echo $BASE_URL ?>/setup/chatprefs.php?action=polling" class="nav">Tempo de Espera do Pedido de Atendimento</a> | <a href="<?php echo $BASE_URL ?>/setup/chatprefs.php?action=polling_type" class="nav">Ordem dos Pedidos de Atendimento</a> | <a href="<?php echo $BASE_URL ?>/setup/chatprefs.php?action=language" class="nav">Idioma</a>';
nav[7] = '<?php echo $trackit_menu ?> <?php echo $salespath_menu ?>';
nav[8] = '<a href="<?php echo $BASE_URL ?>/setup/statistics.php" class="nav">Pedidos de Suporte</a> | <a href="<?php echo $BASE_URL ?>/setup/opratings.php" class="nav">Avalia&ccedil;&otilde;es dos Operadores</a> | <a href="<?php echo $BASE_URL ?>/setup/profiles.php?action=pics" class="nav">Fotos dos Operadores</a>';
nav[9] = '<a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_config.php" class="nav">Prefer&ecirc;ncias</a> | <a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_config.php?action=config" class="nav">Configurar</a>';

var button = new Array();
button[0] = 'home';
button[1] = 'gerenciar';
button[2] = 'layout';
button[3] = 'relatorios';
button[4] = 'sessoes';
button[5] = 'pref';
button[6] = 'chatpref';
button[7] = 'mark';
button[8] = 'perfil';
button[9] = 'basec';

// Onload
function init(){
	rollOver(section);					// Setup navigation
	if(section>0) sE(gE('navBack'));	// Show back button

	<?php if ( isset( $_GET['optimized'] ) ) { print "		alert( 'Otimizacao do banco de dados finalizada!' ) ;\n" ; } ?>
}

// Rollover and navigation change
// s = section number; n = button name
function rollOver(s){
	if(button[s] != '' ) MM_swapImage(button[s],'','<?php echo $css_path ?>images/botao_'+button[s]+'-over.png',1);
	
	// Only show sub-navigation links in none NS4 browsers
	if(!l){
		e = gE('navigation');
		wH(e,nav[s]);
	}
}

// Rollout
function rollOut(s){
	if(!s) s=0;
	if(s>0 && s!=section) MM_swapImgRestore();
}


// Basic functions
d=document;l=d.layers;op=navigator.userAgent.indexOf('Opera')!=-1;var ie=d.all?1:0;var w3c=d.getElementById?1:0;
function gE(e,f){if(l){f=(f)?f:self;var V=f.document.layers;if(V[e])return V[e];for(var W=0;W<V.length;)t=gE(e,V[W++]);return t;}if(d.all)return d.all[e];return d.getElementById(e);} // Get element
function wH(e,h){if(l){Y=e.document;Y.open();Y.write(h);Y.close();}else if(e.innerHTML)e.innerHTML=h;}		// Write html
function sE(e){l?e.visibility='show':e.style.visibility='visible';}
function hE(e){l?e.visibility='hide':e.style.visibility='hidden';}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}


//-->
</script>
<script language="JavaScript" src="<?php echo $css_path ?>js/global.js"></script>
</head>
<body onLoad="init();" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="65" valign="top" class="bgHead"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="20" height="65" rowspan="2" valign="bottom">&nbsp;</td>
		  <td height="65" rowspan="2"><div id="logo"><a href="<?php echo $BASE_URL ?>/setup/options.php"><img src="<?php echo $logo ?>" border="0" align="absmiddle"></a></div></td>
		  <td align="right" valign="top"><strong><a href="<?php echo $BASE_URL ?>/setup/login.php?action=logout">Logout</a>&nbsp;&nbsp;</strong></td>
		</tr>
		<tr> 
		  <td align="right"> <table border="0" cellspacing="0" cellpadding="0">
			  <tr align="center">
              <td><a href="<?php echo $BASE_URL ?>/setup/options.php" onMouseOut="rollOut(0)" onMouseOver="rollOver(0)"><img src="<?php echo $css_path ?>images/botao_home.png" alt="Home" name="home" width="48" height="48" hspace="5" border="0"></a></td>
              
				<td><a href="<?php echo $BASE_URL ?>/setup/manager.php" onMouseOut="rollOut(1)" onMouseOver="rollOver(1)"><img src="<?php echo $css_path ?>images/botao_gerenciar.png" alt="Gerenciar" name="gerenciar" width="48" height="48" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/interface.php" onMouseOut="rollOut(2)" onMouseOver="rollOver(2)"><img src="<?php echo $css_path ?>images/botao_layout.png" alt="Layout" name="layout" width="48" height="48" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/prefs.php" onMouseOut="rollOut(5)" onMouseOver="rollOver(5)"><img src="<?php echo $css_path ?>images/botao_pref.png" alt="Prefer&ecirc;ncias" name="pref" width="48" height="48" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/profiles.php" onMouseOut="rollOut(8)" onMouseOver="rollOver(8)"><img src="<?php echo $css_path ?>images/botao_perfil.png" alt="Perfis" name="perfil" width="48" height="48" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/sessions.php" onMouseOut="rollOut(4)" onMouseOver="rollOver(4)"><img src="<?php echo $css_path ?>images/botao_sessoes.png" alt="Sess&otilde;es" name="sessoes" width="48" height="48" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/reports.php" onMouseOut="rollOut(3)" onMouseOver="rollOver(3)"><img src="<?php echo $css_path ?>images/botao_relatorios.png" alt="Relat&oacute;rios" name="relatorios" width="48" height="48" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/chatprefs.php" onMouseOut="rollOut(6)" onMouseOver="rollOver(6)"><img src="<?php echo $css_path ?>images/botao_chatpref.png" alt="Prefer&ecirc;ncias do Atendimento" name="chatpref" width="48" height="48" hspace="5" border="0"></a></td>

				<?php if ( $INITIATE && ( file_exists( "$DOCUMENT_ROOT/admin/traffic/click_track.php" ) || file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/salespath.php" ) ) ): ?>
				<td><a href="<?php echo $BASE_URL ?>/setup/marketing.php" onMouseOut="rollOut(7)" onMouseOver="rollOver(7)"><img src="<?php echo $css_path ?>images/botao_mark.png" alt="Marketing" name="mark" width="48" height="48" hspace="5" border="0"></a></td>
				<?php endif ; ?>

				<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge.php" ) ): ?>
				<td><a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge.php" onMouseOut="rollOut(9)" onMouseOver="rollOver(9)"><img src="<?php echo $css_path ?>images/botao_basec.png" alt="Base de Conhecimento" name="basec" width="48" height="48" hspace="5" border="0"></a></td>
				<?php endif ; ?>

				<td width="15"><img src="<?php echo $css_path ?>images/spacer.gif" width="5" height="1"></td>
			  </tr>
			</table></td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td height="35" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="100%"></td>
		  <td width="625" valign="top" class="bgNav"><table width="625" border="0" cellspacing="0" cellpadding="1">
			  <tr>
				<td height="24" align="center" class="nav"><b><div style="position:relative" id="navigation">&nbsp;</div></b></td>
			  </tr>
			</table></td>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td valign="top" class="bg">  

<!-- **** Start of the page body area **** -->