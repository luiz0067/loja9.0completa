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
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Cal.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/get.php") ;
	$section = 3;			// Section number - see header.php for list of section numbers
	$page_title = "Atendimento - Administração";
	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';
?>
<?php

	// initialize
	// initialize
	$action = "" ;
	$m = $y = $d = "" ;
	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }
	$max_output = 25 ;

	if ((!$m) || (!$y))
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}

	if ( !$d )
	{
		// this is for the monthly breakdown
		$stat_begin = mktime( 0,0,0,$m,1,$y ) ;
		$stat_end = mktime( 23,59,59,$m,31,$y ) ;
	}
	else
	{
		$stat_begin = mktime( 0,0,0,$m,$d,$y ) ;
		$stat_end = mktime( 23,59,59,$m,$d,$y ) ;
	}

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
	
	$top_url_visits = ServiceFootprint_get_DayFootprint( $dbh, "", $stat_begin, $stat_end, $max_output, $session_setup['aspID'], $d, 0 ) ;
	$top_live_requests = ServiceLogs_get_DayMostLiveRequestPage( $dbh, $stat_begin, $stat_end, $max_output, $session_setup['aspID'] ) ;

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
  <td width="15%" valign="top" align="center"><img src="../images/sessoesg.png" /></td>  
  <td valign="top"> <p><span class="title">Relat&oacute;rios: Tr&aacute;fego e Acessos dos visitantes.</span><br>
    Este relat&oacute;rio exibe os acessos dos visitantes e as respectivas p&aacute;ginas visitadas.
	</p>
	<p>
	<?php if ( $action == "expand_month" ): ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th nowrap>Dia</th>
		<th width="456" nowrap>Visitas/P&aacute;ginas Visualizadas </th>
		<th width="243" nowrap>Visitas &Uacute;nicas</th>
	  </tr>
	<?php
		$grand_total_page_views = $grand_total_unique_visits = 0 ;
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
			
			$total_page_views = ServiceFootprint_get_TotalDayFootprint( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
			$total_unique_visits = ServiceFootprint_get_TotalUniqueDayVisits( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
			$grand_total_page_views += $total_page_views ;
			$grand_total_unique_visits += $total_unique_visits ;

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;

			print "
				<tr $class>
					<td><a href=\"footprints.php?d=$c&m=$m&y=$y\">$dat11" . ", " . "$dat22" . " de " . "$dat33" . " de " . "$dat44</td>
					<td align=\"left\">$total_page_views &nbsp;</td>
					<td align=\"left\">$total_unique_visits &nbsp;</td>
				</tr>" ;
		}
	?>
	<tr class="altcolor3">
		<th width="212" nowrap align="left">Total do M&ecirc;s</th>
		<th align="left"><?php echo $grand_total_page_views ?></th>
		<th align="left"><?php echo $grand_total_unique_visits ?></th>
	</tr>
	 </table>
	<br> <br>
	 <table cellspacing=1 cellpadding=2 border=0 width="100%">
	 <tr> 
		<th colspan="2">As <?php echo $max_output ?> p&aacute;ginas que mais foram efetuados pedidos de atendimento online este m&ecirc;s.</th>
	  </tr>
	<?php
		for ( $c = 0; $c < count( $top_live_requests );++$c )
		{
			$footprint = $top_live_requests[$c] ;
			if ( !$footprint['url'] )
				$url_string = "<i>data empty</i>" ;
			else
			{
				$goto_url = "$footprint[url]?phplive_notally" ;
				if ( preg_match( "/\?/", $footprint['url'] ) )
					$goto_url = "$footprint[url]&phplive_notally" ;
				$url_string = "<a href=\"$goto_url\" target=\"new\">$footprint[url]</a>" ;
			}

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;

			print "<tr $class><td>$footprint[total]</td><td>$url_string</td></tr>\n" ;
		}
	?>
	</table>
	





	<?php
		else:
		$total_page_views = ServiceFootprint_get_TotalDayFootprint( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
		$total_unique_visits = ServiceFootprint_get_TotalUniqueDayVisits( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
	?>
	<b><?php echo $dat1 . ", " . $dat2 . " de " . $dat3 . " de " . $dat4 ?></b><br>
		<li> Total de P&aacute;ginas Visualizadas = <?php echo $total_page_views ?>
		<li> Total de Visitas &Uacute;nicas = <?php echo $total_unique_visits ?></p>
	      <table cellspacing=1 cellpadding=2 border=0 width="100%">
	<tr> 
		<th colspan="2">As <?php echo $max_output ?> p&aacute;ginas mais visitadas. </th>
	  </tr>
	<?php
		for ( $c = 0; $c < count( $top_url_visits );++$c )
		{
			$footprint = $top_url_visits[$c] ;
			//$url_unique_hits = ServiceFootprint_get_TotalUniqueURLDayVisits( $dbh, $stat_begin, $stat_end, $session_setup[aspID], $footprint[url] ) ;

			if ( !$footprint['url'] )
				$url_string = "<i>data empty</i>" ;
			else
			{
				$goto_url = "$footprint[url]" ;
				//if ( preg_match( "/\?/", $footprint['url'] ) )
				//	$goto_url = "$footprint[url]&phplive_notally" ;
				$url_string = "<a href=\"$goto_url\" target=\"new\">$footprint[url]</a>" ;
			}

			print "<tr class=\"altcolor1\"><td>$footprint[total]</td><td>$url_string</td></tr>\n" ;
		}
	?>
	</table>

	<br> <br> 

	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<th colspan="2">As <?php echo $max_output ?> p&aacute;ginas que mais foram efetuados pedidos de atendimento online. </th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $top_live_requests );++$c )
			{
				$footprint = $top_live_requests[$c] ;
				if ( !$footprint['url'] )
					$url_string = "<i>data empty</i>" ;
				else
				{
					$goto_url = "$footprint[url]" ;
					//if ( preg_match( "/\?/", $footprint['url'] ) )
					//	$goto_url = "$footprint[url]&phplive_notally" ;
					$url_string = "<a href=\"$goto_url\" target=\"new\">$footprint[url]</a>" ;
				}

				print "<tr class=\"altcolor1\"><td>$footprint[total]</td><td>$url_string</td></tr>\n" ;
			}
		?>
	</table>

	<?php endif ; ?>
	</td>
  <td align="center" valign="top">
	<?php Util_Cal_DrawCalendar( $dbh, $m, $y, "footprints.php?", "footprints.php?", "footprints.php?action=expand_month", $action ) ; ?></td>
</tr>
 </table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php include_once( "./footer.php" ) ; ?>