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
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Cal.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;

	/*************************************
	* note about status of request:
	* 0 = not taken
	* 1 = taken
	* 2 = not taken
	* 3 = rejected
	* 4 = initiated by operator
	* 5 = initiated but rejected by visitor
	*************************************/
	$section = 8;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$action = "" ;
	$m = $y = $d = 0 ;

	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }

	$departments = AdminUsers_get_AllDepartments( $dbh, $session_setup['aspID'], 1 ) ;
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
	$browsers = ARRAY (
		"IE 6.0" => "MSIE 6.0",
		"IE 5.0" => "MSIE 5.0",
		"IE 5.01" => "MSIE 5.01",
		"IE 5.5" => "MSIE 5.5",
		"Netscape 4.7x" => "Mozilla/4.7",
		"Netscape 6/6.x" => "Netscape6"
	) ;

	if ((!$m) || (!$y))
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}

	// the timespan to get the stats
	$stat_begin = mktime( 0,0,1,$m,$d,$y ) ;
	$stat_end = mktime( 23,59,59,$m,$d,$y ) ;

	$stat_date = date( "D F d, Y", $stat_begin ) ;
	
	$dat1 = date( "D", $stat_begin ) ;
	
	                    if ($dat1 == 'Mon')
						{
						  $dat1 = 'Segunda-feira';
						}
						if ($dat1 == 'Tue')
						{
						  $dat1 = 'Ter&ccedil;a-feira';
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
						  $dat1 = 'S&aacute;bado';
						}
						if ($dat1 == 'Sun')
						{
						  $dat1 = 'Domingo';
						}
	
	$dat2 = date( "d", $stat_begin ) ;
	
	$dat3 = date( "F", $stat_begin ) ;
	
	                    if ($dat3 == 'January')
						{
						  $dat3 = 'Janeiro';
						}
						if ($dat3 == 'February')
						{
						  $dat3 = 'Fevereiro';
						}
						if ($dat3 == 'March')
						{
						  $dat3 = 'Marco';
						}
						if ($dat3 == 'April')
						{
						  $dat3 = 'Abril';
						}
						if ($dat3 == 'May')
						{
						  $dat3 = 'Maio';
						}
						if ($dat3 == 'June')
						{
						  $dat3 = 'Junho';
						}
						if ($dat3 == 'July')
						{
						  $dat3 = 'Julho';
						}
						if ($dat3 == 'August')
						{
						  $dat3 = 'Agosto';
						}
						if ($dat3 == 'September')
						{
						  $dat3 = 'Setembro';
						}
						if ($dat3 == 'October')
						{
						  $dat3 = 'Outubro';
						}
						if ($dat3 == 'November')
						{
						  $dat3 = 'Novembro';
						}
						if ($dat3 == 'December')
						{
						  $dat3 = 'Dezembro';
						}
	
	$dat4 = date( "Y", $stat_begin ) ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
?>
<?php
	// functions
?>
<?php
	// conditions
?>
<?php include_once("./header.php"); ?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Relat&oacute;rios do operador: Pedidos de Atendimento e Atendimentos Realizados</span><br>
	  	
Esta página exibe o relat&oacute;rio de pedidos de chat atendidos e n&atilde;o atendidos. Selecionando a data no calendário, você pode visualizar os pedidos efetuados no dia e os pedidos que foram atendidos, n&atilde;o atendidos e os pedidos de chat rejeitados pelos operadores.  </p>
	<p><b><?php echo ( isset( $action ) && $action ) ? "" : $dat1 . ", " . $dat2 . " de " . $dat3 . " de " . $dat4 ?></b> </p>
	<p>
	  <!-- begin departments -->

	<?php if ( $action == "expand_month" ): ?>
	<table cellspacing=1 cellpadding=1 border=0 width="100%">
	<tr bgColor="#8080C0">
		<th width="200" align="left">Dia</th>
		<th>Pedidos Atendidos</th>
		<th>Pedidos N&atilde;o Atendidos</th>
		<th>Rejeitados</th>
	</tr>
	<?php
		$total_requests = $total_requests_not = $total_rejects = 0 ;
		for ( $c = 1; $m == date( "m", mktime( 0,0,0,$m,$c,$y ) ); ++$c )
		{
			$day = date( "F d, Y D", mktime( 0,0,0,$m,$c,$y ) ) ;

			$stat_begin = mktime( 0,0,0,$m,$c,$y ) ;
			$stat_end = mktime( 23,59,59,$m,$c,$y ) ;
			
			$dat11 = date( "D", $stat_begin ) ;
	
	                    if ($dat11 == 'Mon')
						{
						  $dat11 = 'Segunda-feira';
						}
						if ($dat11 == 'Tue')
						{
						  $dat11 = 'Ter&ccedil;a-feira';
						}
						if ($dat11 == 'Wed')
						{
						  $dat11 = 'Quarta-feira';
						}
						if ($dat11 == 'Thu')
						{
						  $dat11 = 'Quinta-feira';
						}
						if ($dat11 == 'Fri')
						{
						  $dat11 = 'Sexta-feira';
						}
						if ($dat11 == 'Sat')
						{
						  $dat11 = 'S&aacute;bado';
						}
						if ($dat11 == 'Sun')
						{
						  $dat11 = 'Domingo';
						}
						
			 $dat22 = date( "d", $stat_begin ) ;
	
	         $dat33 = date( "F", $stat_begin ) ;
	
	                    if ($dat33 == 'January')
						{
						  $dat33 = 'Janeiro';
						}
						if ($dat33 == 'February')
						{
						  $dat33 = 'Fevereiro';
						}
						if ($dat33 == 'March')
						{
						  $dat33 = 'Marco';
						}
						if ($dat33 == 'April')
						{
						  $dat33 = 'Abril';
						}
						if ($dat33 == 'May')
						{
						  $dat33 = 'Maio';
						}
						if ($dat33 == 'June')
						{
						  $dat33 = 'Junho';
						}
						if ($dat33 == 'July')
						{
						  $dat33 = 'Julho';
						}
						if ($dat33 == 'August')
						{
						  $dat33 = 'Agosto';
						}
						if ($dat33 == 'September')
						{
						  $dat33 = 'Setembro';
						}
						if ($dat33 == 'October')
						{
						  $dat33 = 'Outubro';
						}
						if ($dat33 == 'November')
						{
						  $dat33 = 'Novembro';
						}
						if ($dat33 == 'December')
						{
						  $dat33 = 'Dezembro';
						}
						
			     $dat44 = date( "Y", $stat_begin ) ;		
			
			$total_taken = ServiceLogs_get_TotalRequestsPerDay( $dbh, "", $stat_begin, $stat_end, 1, $session_setup['aspID'] ) ;
			$total_nottaken = ServiceLogs_get_TotalRequestsPerDay( $dbh, "", $stat_begin, $stat_end, 0, $session_setup['aspID'] ) ;
			$total_rejected = ServiceLogs_get_TotalRequestsPerDay( $dbh, "", $stat_begin, $stat_end, 3, $session_setup['aspID'] ) ;
			$total_requests += $total_taken ;
			$total_rejects += $total_rejected ;
			$total_requests_not += $total_nottaken ;

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;

			print "<tr $class><td><a href=\"statistics.php?d=$c&m=$m&y=$y\">$dat11" . ", " . "$dat22" . " de " . "$dat33" . " de " . "$dat44</td><td align=center>$total_taken</td><td align=center>$total_nottaken</td><td align=center>$total_rejected</td></tr>" ;
		}
	?>
	<tr>
		<th width="180" nowrap align="left">Total de pedidos de atendimento</th>
		<th><?php echo $total_requests ?></th>
		<th><?php echo $total_requests_not ?></th>
		<th><?php echo $total_rejects ?></th>
	</tr>
	</table>



	<?php else: ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th width="150" nowrap align="left">Departamento</th>
		<th nowrap>Pedidos Atendidos</th>
		<th nowrap>Pedidos N&atilde;o Atendidos</th>
		<th nowrap>Rejeitados</th>
		<th nowrap>Iniciado/Rejeitado</th>
	  </tr>
		<?php
			// 0-request not taken, 1-request taken, 3-rejected
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;
				$department = $departments[$c] ;
				$dept_name = stripslashes( $department['name'] ) ;
				$total_taken = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 1, $session_setup['aspID'] ) ;
				$total_nottaken = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 0, $session_setup['aspID'] ) ;
				$total_rejected = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 3, $session_setup['aspID'] ) ;
				$total_initiated = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 4, $session_setup['aspID'] ) ;
				$total_initiated_reject = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 5, $session_setup['aspID'] ) ;
				$total_initiated += $total_initiated_reject ;

				print "
				<tr $class>
					<td width=\"120\">$dept_name</td>
					<td><a href=\"transcripts.php?action=view&deptid=$department[deptID]\">$total_taken</a></td>
					<td>$total_nottaken</td>
					<td>$total_rejected</td>
					<td>$total_initiated/$total_initiated_reject</td>
				</tr>
				" ;
			}
		?>
	</table>
	<!-- end departments -->
	<br> 
	<!-- begin user stats -->
	Operadores:<br>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th width="60" nowrap>Login</th>
		<th width="80" nowrap>Nome</th>
		<th nowrap>Pedidos Atendidos</th>
		<th nowrap>Pedidos N&atilde;o Atendidos</th>
		<th nowrap>Rejeitados</th>
		<th nowrap>Iniciado/Rejeitado</th>
	  </tr>
		<?php
			// 0-request not taken, 1-request taken, 3-rejected
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;

				$total_taken = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 1, $session_setup['aspID'] ) ;
				$total_nottaken = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 0, $session_setup['aspID'] ) ;
				$total_rejected = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 3, $session_setup['aspID'] ) ;
				$total_initiated = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 4, $session_setup['aspID'] ) ;
				$total_initiated_reject = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 5, $session_setup['aspID'] ) ;
				$total_initiated += $total_initiated_reject ;

				//$date = date( "m/d/y", $admin['created'] ) ;

				print "
					<tr $class>
						<td>$admin[login]</td>
						<td>$admin[name]</td>
						<td><a href=\"transcripts.php?action=view&userid=$admin[userID]\">$total_taken</a></td>
						<td>$total_nottaken</td>
						<td>$total_rejected</td>
						<td>$total_initiated/$total_initiated_reject</td>
					</tr>
				" ;
			}
		?>
	</table>
	<!-- end user stats -->
	<p>Pedidos Atendidos - 	
O operador realizou o atendimento ao visitante.<br>
	  <span class="hilight">N&atilde;o Feito exame</span> - O operador n&atilde;o realizou o atendimento ao visitante mas tamb&eacute;m n&atilde;o rejeitou o pedido de atendimento.<br>
	  <span class="hilight">Rejeitado</span> - O operador clicou em &quot;Ocupado&quot; e rejeitou o pedido de atendimento.<br>
	  Iniciado -  O operador abordou e iniciou o chat com o visitante.
<?php endif ; ?>
		</td>
  <td height="350" align="center" valign="top" style="background-image: url(../images/g_profile_big);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"><br><img src="../images/spacer.gif" width="1" height="220"><br>
	<?php Util_Cal_DrawCalendar( $dbh, $m, $y, "statistics.php?", "statistics.php?", "statistics.php?action=expand_month", $action ) ; ?>
	</td>
</tr>
 </table>

<?php include_once( "./footer.php" ) ; ?>
