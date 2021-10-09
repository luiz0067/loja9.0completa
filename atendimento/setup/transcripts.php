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
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util_Page.php") ;
	include_once("$DOCUMENT_ROOT/API/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Transcripts/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Transcripts/remove.php") ;
	$section = 4;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	// initialize
	$action = $error_mesg = $userid = $deptid = $chat_session = $search_string = $searched_string = "" ;
	$success = $page = $deptid = $userid = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['userid'] ) ) { $userid = $_GET['userid'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_GET['chat_session'] ) ) { $chat_session = $_GET['chat_session'] ; }
	if ( isset( $_GET['page'] ) ) { $page = $_GET['page'] ; }
	if ( isset( $_GET['search_string'] ) ) { $search_string = $_GET['search_string'] ; }

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';
	if ( $action )
		$nav_line = '<a href="transcripts.php" class="nav">:: Previous</a>';

	$rating_hash = Array() ;
	$rating_hash[4] = "Excelente" ;
	$rating_hash[3] = "Muito bom" ;
	$rating_hash[2] = "Bom" ;
	$rating_hash[1] = "Precisa Melhorar" ;
	$rating_hash[0] = "&nbsp;" ;

	ServiceChat_remove_CleanChatSessions( $dbh ) ;

	// conditions
	
	if ( $action == "delete" )
	{
		ServiceTranscripts_remove_Transcript( $dbh, $session_setup['aspID'], $chat_session ) ;
		HEADER( "location: transcripts.php?action=view&userid=$userid&deptid=$deptid&page=$page" ) ;
		exit ;
	}
?>
<?php include_once( "./header.php" ) ; ?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<script language="JavaScript">
<!--
	function view_transcript( chat_session )
	{
		url = "<?php echo $BASE_URL ?>/admin/view_transcript.php?chat_session="+chat_session+"&x=<?php echo $session_setup['aspID'] ?>&l=<?php echo $session_setup['login'] ?>" ;
		newwin = window.open(url, "transcript", "scrollbars=0,menubar=no,resizable=1,location=no,width=450,height=360") ;
		newwin.focus() ;

	}

	function do_search()
	{
		string = replace( document.form.search_string.value, " ", "" ) ;
		if ( string.length < 3 )
			alert( " A Palavra de pesquisa precisa ter pelo menos 3 caracteres." )
		else
			document.form.submit() ;
	}

	function do_delete( sessionid )
	{
		if ( confirm( "Deletar esta conversa?" ) )
			location.href = "transcripts.php?action=delete&deptid=<?php echo $deptid ?>&userid=<?php echo $userid ?>&page=<?php echo $page ?>&chat_session="+sessionid ;
	}
//-->
</script>


<?php
	if ( $action == "view" ):
	ServiceLogs_remove_DeptExpireTranscripts( $dbh, $deptid, $session_setup['aspID'] ) ;
	if ( $userid )
	{
		$info = AdminUsers_get_UserInfo( $dbh, $userid, $session_setup['aspID'] ) ;
		$transcripts = ServiceTranscripts_get_UserDeptTranscripts( $dbh, $session_setup['aspID'], $userid, 0, "", "", $page, 20, $search_string ) ;
		$total_transcripts = ServiceTranscripts_get_TotalUserDeptTranscripts( $dbh, $userid, 0, $search_string ) ;

	}
	else
	{
		$info = AdminUsers_get_DeptInfo( $dbh, $deptid, $session_setup['aspID'] ) ;
		$transcripts = ServiceTranscripts_get_DeptTranscripts( $dbh, $session_setup['aspID'], $deptid, "", "", $page, 20, $search_string ) ;
		$total_transcripts = ServiceTranscripts_get_TotalDeptTranscripts( $dbh, $deptid, $search_string ) ;
	}
	$page_string = Page_util_CreatePageString( $dbh, $page, "transcripts.php?action=view&deptid=$deptid&userid=$userid&search_string=$search_string", 20, $total_transcripts ) ;

	if ( $search_string )
		$searched_string = "Searched: \"$search_string\" &nbsp;|&nbsp; Transcripts Found: $total_transcripts &nbsp;|&nbsp; [ <a href=\"transcripts.php?userid=$userid&deptid=$deptid&action=view\">resetar</a> ]<br>" ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Sess&otilde;es: Conversas Gravadas: <?php echo stripslashes( $info['name'] ) ?></span><br>
	  Conversas gravadas. Departamento: <?php echo stripslashes( $info['name'] ) ?> </p>

		<?php echo $searched_string ?><br>
		Page: <?php echo $page_string ?><br>
	  <table width="100%" border=0 cellpadding=2 cellspacing=1>
		<tr> 
			<th>&nbsp;</th>
			<th nowrap align="left">Operador</th>
			<th nowrap align="left">Visitante</th>
			<th nowrap align="left">Avalia&ccedil;&atilde;o</th>
			<th nowrap align="left">Cria&ccedil;&atilde;o</th>
			<th align="left" nowrap>Pergunta do Visitante</td>
			<th nowrap align="left">Dura&ccedil;&atilde;o</th>
			<th nowrap align="left">Tamanho</th>
			<th nowrap align="left">&nbsp;</th>
	  </tr>
	   <?php
			for ( $c = 0; $c < count( $transcripts ); ++$c )
			{
				$transcript = $transcripts[$c] ;
				$userinfo = AdminUsers_get_UserInfo( $dbh, $transcript['userID'], $session_setup['aspID'] ) ;
				$date = date( " d/m/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $transcript['created'] + $TIMEZONE ) ) ;
				
				$dat1 = date( "D", ( $transcript['created'] + $TIMEZONE ) ) ;
				
				        if ($dat1 == 'Mon')
						{
						  $dat1 = 'Segunda-feira';
						}
						if ($dat1 == 'Tue')
						{
						  $dat1 = 'Terca-feira';
						}
						if ($dat1 == 'Wed')
						{
						  $dat1 = 'Quarta-feira';
						}
						if ($dat1 == 'Thu')
						{
						  $dat1 = 'Quinta-feira';
						}
						if ($dat1 == 'Fri')
						{
						  $dat1 = 'Sexta-feira';
						}
						if ($dat1 == 'Sat')
						{
						  $dat1 = 'Sabado';
						}
						if ($dat1 == 'Sun')
						{
						  $dat1 = 'Domingo';
						}

				// take out the tags to make it more accurate size. (gets rid of all
				// the javascript tags and all that
				$size = Util_Format_Bytes( strlen( strip_tags( $transcript['plain'] ) ) ) ;
				$rating = ( isset( $transcript['rating'] ) ) ? $transcript['rating'] : 0 ;
				$rating = $rating_hash[$rating] ;

				$class = "altcolor1" ;
				if ( $c % 2 )
					$class = "altcolor2" ;

				$duration = $transcript['created'] - $transcript['chat_session'] ;
				if ( $duration <= 0 ) { $duration = 1 ; }
				if ( $duration > 60 )
					$duration = round( $duration/60 ) . " min" ;
				else
					$duration = $duration . " sec" ;

				if ( preg_match( "/<question>(.*)<\/question>/s", $transcript['formatted'], $matches ) )
					$question = ( isset( $matches[0] ) ) ? $matches[0] : "&nbsp;" ;
				else
					$question = "&nbsp;" ;

				if ( preg_match( "/<initiated>/", $transcript['formatted'] ) )
					$question = "[ Operator Initiated Chat ]" ; // initiated chat

				$admin_name = stripslashes( $userinfo['name'] ) ;
				$visitor_name = stripslashes( $transcript['from_screen_name'] ) ;

				print "
				<tr class=\"$class\">
					<td><a href=\"JavaScript:view_transcript( $transcript[chat_session] )\"><img src=\"../images/view.gif\" border=0 width=28 height=16></a></td>
					<td nowrap>$admin_name</td>
					<td nowrap>$visitor_name</td>
					<td>$rating</td>
					<td nowrap>$dat1 $date</td>
					<td><i>$question</i></td>
					<td nowrap>$duration</td>
					<td nowrap>$size</td>
					<td nowrap><a href=\"JavaScript:do_delete( $transcript[chat_session] )\">deletar</a></td>
				</tr>
				" ;
			}
		?>
	</table>
	Page: <?php echo $page_string ?><br>

	<p></p> 
	<table cellspacing=1 cellpadding=1 border=0>
	<form method="GET" action="transcripts.php" name="form">
	<input type="hidden" name="action" value="view">
	<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
	<input type="hidden" name="userid" value="<?php echo $userid ?>">
	<tr> 
		<td><strong>Procurar:</strong></td>
		<td><input type="text" name="search_string" value="<?php echo $search_string ?>" size="25" maxlength="50" style="width:200px"></td>
		<td><input type="button" OnClick="do_search()" class="mainButton" value="Procurar"></td>
	</tr></form>
	</table>
	
  </td>

<?php
	else:
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
	$departments = AdminUsers_get_AllDepartments( $dbh, $session_setup['aspID'], 1 ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr>
  <td width="15%" valign="top" align="center"><img src="../images/sessoesg.png"></td>
  <td height="350" valign="top" width="100%"> <p><span class="title">Sess&otilde;es: Conversas Gravadas</span><br>
	  Visualizar/Pesquisar conversas por departamento ou operador.</p>
	  <ul>
		<b><big><strong>Conversas Gravadas por Departamento</strong></big></b>
		<?php
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$department = $departments[$c] ;
				
				$hidden_string = "" ;
				if ( !$department['visible'] )
					$hidden_string = "(hidden department)" ;

				$name = stripslashes( $department['name'] ) ;
				print "<li> <a href=\"transcripts.php?action=view&deptid=$department[deptID]\">$name</a> $hidden_string<br>" ;
			}
		?>
	  </ul>

	  <ul>
		<b><big><strong>Conversas Gravadas por Operador</strong></big></b>
		<?php
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				$name = stripslashes( $admin['name'] ) ;
				print "<li> <a href=\"transcripts.php?action=view&userid=$admin[userID]\">$name</a><br>" ;
			}
		?>
	  </ul>
	  </td>

<?php endif ;?>
</tr>
</table>

<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->

<?php include_once( "./footer.php" ) ; ?>
