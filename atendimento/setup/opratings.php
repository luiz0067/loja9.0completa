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
	include_once("$DOCUMENT_ROOT/API/Survey/get.php") ;
	$section = 8;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>' ;
?>
<?php

	// initialize
	$action = "" ;
	$m = $y = $d = "" ;
	$rating_hash = Array() ;
	$rating_hash[4] = "Excelente" ;
	$rating_hash[3] = "Muito Bom" ;
	$rating_hash[2] = "Bom" ;
	$rating_hash[1] = "Precisa Melhorar" ;
	$rating_hash[0] = "&nbsp;" ;

	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }

	$departments = AdminUsers_get_AllDepartments( $dbh, $session_setup['aspID'], 1 ) ;
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;

	if ((!$m) || (!$y))
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}

	// the timespan to get the stats
	$stat_begin = mktime( 0,0,0,$m,$d,$y ) ;
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
<?php include_once("./header.php") ; ?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Avalia&ccedil;&otilde;es dos Operadores.</span><br> 
  Relat&oacute;rio de avalia&ccedil;&otilde;es dos visitantes relativas ao atendimento efetuado pelos operadores do sistema.</p>
	<p><b><?php echo ( isset( $action ) && $action ) ? "" : $dat1 . ", " . $dat2 . " de " . $dat3 . " de " . $dat4 ?></b> </p>
	<p>
	  <!-- begin departments -->

	<?php if ( $action == "expand_month" ): ?>
	<table cellspacing=1 cellpadding=1 border=0 width="100%">
	<tr bgColor="#8080C0">
		<th width="200">Dia</th>
		<th>Total de Avalia&ccedil;&otilde;es</th>
		<th>M&eacute;dia das Avalia&ccedil;&otilde;es</th>
	</tr>
	<?php
		$total_requests = $c_total_rates = $c_sum_rates = 0 ;
		$c_sum_ave_rate = "" ;
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
			
			$total_rates = ServiceSurvey_get_DeptTotalRates( $dbh, 0, 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;
			$sum_rates = ServiceSurvey_get_DeptTotalRatings( $dbh, 0, 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;

			$ave_rating = 0 ;
			if ( $sum_rates > 0 )
				$ave_rating = round( $sum_rates/$total_rates ) ;
				
			$c_total_rates += $total_rates ;
			$c_sum_rates += $sum_rates ;

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;
			$ave_rating_string = $rating_hash[$ave_rating] ;

			print "<tr $class><td><a href=\"opratings.php?d=$c&m=$m&y=$y\">$dat11" . ", " . "$dat22" . " de " . "$dat33" . " de " . "$dat44</td><td align=center>$total_rates</td><td align=center>$ave_rating_string</td></tr>" ;
		}

		if ( $c_total_rates > 0 )
			$c_sum_ave_rate = $rating_hash[round( $c_sum_rates/$c_total_rates )] ;
	?>
	<tr>
		<th width="180" nowrap align="left">Total</th>
		<th><?php echo $c_total_rates ?></th>
		<th><?php echo $c_sum_ave_rate ?></th>
	</tr>
	</table>


	<?php else: ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th width="150" nowrap align="left">Departamento</th>
		<th nowrap>Total de Avalia&ccedil;&otilde;es</th>
		<th nowrap>M&eacute;dia das Avalia&ccedil;&otilde;es</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$department = $departments[$c] ;
				$dept_name = stripslashes( $department['name'] ) ;
				$total_rates = ServiceSurvey_get_DeptTotalRates( $dbh, 0, $department['deptID'], $stat_begin, $stat_end, $session_setup['aspID'] ) ;
				$sum_rates = ServiceSurvey_get_DeptTotalRatings( $dbh, 0, $department['deptID'], $stat_begin, $stat_end, $session_setup['aspID'] ) ;

				if ( $sum_rates > 0 )
					$ave_rating = round( $sum_rates/$total_rates ) ;
				else
					$ave_rating = 0 ;

				$ave_rating_string = $rating_hash[$ave_rating] ;

				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;

				print "
				<tr $class>
					<td width=\"120\">$dept_name</td>
					<td>$total_rates</td>
					<td>$ave_rating_string</td>
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
		<th nowrap>Total de Avalia&ccedil;&otilde;es</th>
		<th nowrap>Média das Avaliações no dia </th>
		<th nowrap>M&eacute;dia Geral</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;

				$total_rates = ServiceSurvey_get_DeptTotalRates( $dbh, $admin['userID'], 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;
				$sum_rates = ServiceSurvey_get_DeptTotalRatings( $dbh, $admin['userID'], 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;

				if ( $sum_rates > 0 )
					$ave_rating = round( $sum_rates/$total_rates ) ;
				else
					$ave_rating = 0 ;

				$ave_rating_string = $rating_hash[$ave_rating] ;
				$overall_rating_string = $rating_hash[$admin['rate_ave']] ;

				//$date = date( "m/d/y", $admin['created'] ) ;

				print "
					<tr $class>
						<td>$admin[login]</td>
						<td>$admin[name]</td>
						<td>$total_rates</td>
						<td>$ave_rating_string</td>
						<td>$overall_rating_string</td>
					</tr>
				" ;
			}
		?>
	</table>
<?php endif ; ?>
	</td>
  <td height="350" align="center" valign="top" style="background-image: url(../images/g_profile_big);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"><br><img src="../images/spacer.gif" width="1" height="220"><br>
	<?php Util_Cal_DrawCalendar( $dbh, $m, $y, "opratings.php?", "opratings.php?", "opratings.php?action=expand_month", $action ) ; ?>
	</td>
</tr>
 </table>

<?php include_once( "./footer.php" ) ; ?>