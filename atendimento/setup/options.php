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
	include_once("$DOCUMENT_ROOT/web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util_Optimize.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/get.php") ;
	$section = 0;			// Section number - see header.php for list of section numbers

	$current_phplive_version = "3.1" ;

	// auto detect if correct version is used... if not, redict to patch
	if ( $PHPLIVE_VERSION != $current_phplive_version )
	{
		HEADER( "location: patches/" ) ;
		exit ;
	}

	$nav_line = '';
?>
<?php
	// initialize

	// update all admins status to not available if they have been idle
	AdminUsers_update_IdleAdminStatus( $dbh, $admin_idle ) ;

	$now = mktime( 0,0,0,date("m"),date("j"),date("Y") ) ;
	$oldest_footprintstat_date = ServiceFootprint_get_LatestFootprintStatDate( $dbh, $session_setup['aspID'] ) ;
	
	if ( $oldest_footprintstat_date )
		$oldest_footprintstat_date = mktime( 0,0,0,date("m", $oldest_footprintstat_date),date("j", $oldest_footprintstat_date),date("Y", $oldest_footprintstat_date) ) ;
	// > 0 because if there is no data, database spits out negative numbers
	if ( ( $oldest_footprintstat_date < $now ) && ( $oldest_footprintstat_date > 0 ) && !isset( $session_setup['daylight'] ) )
	{
		if ( isset( $_GET['timestamp'] ) && ( $_GET['timestamp'] == $now ) )
		{
			// daylight savings has errors
			$_SESSION['session_setup']['daylight'] = 1 ;
		}
		$month = date("m", $oldest_footprintstat_date ) ;
		$day = date("j", $oldest_footprintstat_date ) ;
		$year = date("Y", $oldest_footprintstat_date ) ;
		HEADER( "location: optimize.php?month=$month&day=$day&year=$year&timestamp=$oldest_footprintstat_date" ) ;
		exit ;
	}
	else
	{
		if ( isset( $_GET['optimized'] ) )
		{
			$tables = ARRAY( "chat_admin", "chatcanned", "chatdepartments", "chatfootprints", "chatfootprintsunique", "chatrequestlogs", "chatrequests", "chatsessionlist", "chatsessions", "chattranscripts", "chatuserdeptlist" ) ;

			if ( !preg_match( "/(chatsupportlive.c0m)|(atendcha.c0m)|(atendchat.c0m)|(atendchat.n3t)|(atendchats.c0m)|(phplivesupportasp.c0m)|(phplivesupport.n3t)|(ositalk.c0m)|(phproi.c0m)|(phpliveasp.c0m)|(phplive.n3t)/", $_SERVER['SERVER_NAME'] ) )
			{
				//Util_OPT_Database( $dbh, $tables ) ;
			}
		}
	}
?>
<?php include_once("./header.php"); ?>
<style type="text/css">
.inset {   background:transparent;   width:100%;   } .inset h1, .inset p {   margin:0 10px;   } .inset h1 {   font-size:2em; color:#fff;   } .inset p {   padding-bottom:0.5em;   } .inset .b1, .inset .b2, .inset .b3, .inset .b4, .inset .b1b, .inset .b2b, .inset .b3b, .inset .b4b {   display:block;   overflow:hidden;   font-size:1px;   } .inset .b1, .inset .b2, .inset .b3, .inset .b1b, .inset .b2b, .inset .b3b {   height:1px;   } .inset .b2 {   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #aaa;   } .inset .b3 {   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #ddd;   } .inset .b4 {   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #eee;   } .inset .b4b {   background:#E7E7E7;   border-left:1px solid #aaa;   border-right:1px solid #fff;   } .inset .b3b {   background:#E7E7E7;   border-left:1px solid #ddd;   border-right:1px solid #fff;   } .inset .b2b {   background:#E7E7E7;   border-left:1px solid #eee;   border-right:1px solid #fff;   } .inset .b1 {   margin:0 5px;   background:#999;   } .inset .b2, .inset .b2b {   margin:0 3px;   border-width:0 2px;   } .inset .b3, .inset .b3b {   margin:0 2px;   } .inset .b4, .inset .b4b {   height:2px; margin:0 1px;   } .inset .b1b {   margin:0 5px;   background:#fff;   } .inset .boxcontent {   display:block;   background:#E7E7E7;   border-left:1px solid #999;   border-right:1px solid #fff;   }
</style>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellspacing="15" cellpadding="0">
  <tr>
	<td width="100%" align="center"><table width="700" border="0" cellspacing="0">
    
		<tr> 
        
        <td width="128"><img src="../images/gerenciarg.png"></td>
        
		  <td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="manager.php"><span class="panelTitle">Gerenciador</span></a><br>
			
			<a href="adddept.php" class="sectionLink">Gerenciar Departamentos</a><br>
			<a href="adduser.php" class="sectionLink">Gerenciar Operadores</a><br>
			<a href="code.php" class="sectionLink">Gerar C&oacute;digo HTML</a><br>
            <a href="systemadm.php" class="sectionLink">Mudar Senha do Setup</a>
			</td>
            
            <td width="24"></td>
            <td width="128"><img src="../images/layoutg.png"></td>
            
             <td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="interface.php"><span class="panelTitle">Customiza&ccedil;&atilde;o/Layout</span></a><br>

			<a href="customize.php?action=logo" class="sectionLink">Logomarca Janela Chat/Setup</a><br>
            <a href="customizelogo.php" class="sectionLink">Logomarca Geral</a><br>
            <a href="customize.php?action=themes" class="sectionLink">Temas Para Chat</a><br>
			<a href="customize.php?action=icons" class="sectionLink">Icones Para Atendimento</a><br>
			<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
			<a href="customize.php?action=initiate" class="sectionLink">Imagem Inicial de Chat</a>
			<?php endif ; ?>			</td>
            
		</tr>
	  </table></td>
      <tr>
	  
	  <td width="33%" align="center"> <table width="700" border="0" cellspacing="0" cellpadding="0">
		<tr>
        
        <td width="128"><img src="../images/prefg.png"></td>
        
		  <td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="prefs.php"><span class="panelTitle">Prefer&ecirc;ncias</span></a><br>

			<a href="prefs.php?action=footprints" class="sectionLink">Excluir Rastreamento de IP</a><br>
			<a href="email_transcript.php" class="sectionLink">Enviar Conversa por Email</a><br>
			<a href="prefs.php?action=timezone" class="sectionLink">Fuso Hor&aacute;rio (Time Zone)</a>			</td>
            
            <td width="24"></td>
            <td width="128"><img src="../images/perfilg.png"></td>
            
            <td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="profiles.php"><span class="panelTitle">Relat&oacute;rios/ Prefer&ecirc;ncias Operadores</span></a><br>
			
			<a href="statistics.php" class="sectionLink">Pedidos de Suporte</a><br>
			<a href="opratings.php" class="sectionLink">Avalia&ccedil;&otilde;es dos Operadores</a><br>
			<a href="profiles.php?action=pics" class="sectionLink">Fotos dos Operadores</a>			</td>
            
		</tr>
	  </table></td>
</tr>
<tr>
	
	<td width="33%" align="center"> <table width="700" border="0" cellspacing="0" cellpadding="0">
		<tr>
        
        <td width="128"><img src="../images/sessoesg.png"></td>
        
		  <td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="sessions.php"><span class="panelTitle">Sess&otilde;es</span></a><br>

			<a href="processes.php?action=chat" class="sectionLink">Chats Ativos</a><br>
			<a href="transcripts.php" class="sectionLink">Conversas Gravadas</a><br>
			<a href="processes.php?action=consol" class="sectionLink">Sess&otilde;es dos Operadores</a>
			</td>
            
            <td width="24"></td>
            <td width="128"><img src="../images/relatoriosg.png"></td>
            
            <td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="reports.php"><span class="panelTitle">Relat&oacute;rios</span></a><br>

			<a href="footprints.php" class="sectionLink">Tr&aacute;fego e Acessos</a><br>
			<a href="refer.php" class="sectionLink">URLs de Refer&ecirc;ncia</a>
			</td>
            
		</tr>
	  </table></td>
</tr>
<tr>
	<td width="33%" align="center"><table width="700" border="0" cellspacing="0">
		<tr>
        
        <td width="128"><img src="../images/chatprefg.png"></td>
        
			<td width="165" height="138" valign="top" class="opcoes"><br>
			<a href="chatprefs.php"><span class="panelTitle">Prefer&ecirc;ncias do Atendimento</span></a><br>

			<a href="chatprefs.php?action=polling" class="sectionLink">Tempo de Espera do Pedido de Atendimento</a><br>
			<a href="chatprefs.php?action=polling_type" class="sectionLink">Ordem de Pedidos de Atendimento</a><br>
			<a href="chatprefs.php?action=language" class="sectionLink">Idioma</a><br>
			</td>
            
            <td width="24"></td>
            <td width="128"><img src="../images/markg.png"></td>
            
            <?php if ( $INITIATE && ( file_exists( "$DOCUMENT_ROOT/admin/traffic/click_track.php" ) || file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/salespath.php" ) ) ): ?>
            
            <td width="165" height="138" valign="top" class="opcoes"><br>
			<a href="marketing.php"><span class="panelTitle">Marketing</span></a><br>

			<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/click_track.php" ) ): ?>
			<a href="../admin/traffic/click_track.php" class="sectionLink">Rastreio de Campanhas por Clique PPC (Pay Per Click)</a><br>
			<!-- <a href="../admin/traffic/click_conversion.php" class="sectionLink">Track'it Conversion</a><br> -->
			<?php endif ; ?>
			</td>
            </tr>
	  </table></td>
	<?php endif ; ?>
    <tr>
	<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge.php" ) ): ?>
	<td width="33%" align="center">
		<table width="297" border="0" cellspacing="0">
		<tr>
        
        <td width="128"><img src="../images/basecg.png"></td>
        
			<td width="165" height="138" valign="top" class="opcoes"><br> 
			<a href="../admin/traffic/knowledge.php"><span class="panelTitle">Base de Conhecimento</span></a><br>

			<a href="../admin/traffic/knowledge_config.php" class="sectionLink">Prefer&ecirc;ncias</a><br>
			<a href="../admin/traffic/knowledge_config.php?action=config" class="sectionLink">Configurar</a><br>
			</td>
            
		</tr>
		</table>
	</td>
	<?php endif ; ?>
</tr>
</table>
<?php include("footer.php") ; ?>
