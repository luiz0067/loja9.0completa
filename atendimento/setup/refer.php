<?php
	/*******************************************************
	* Atendimento Online
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
	include_once("$DOCUMENT_ROOT/API/Refer/get.php") ;
	$section = 3 ;			// Section number - see header.php for list of section numbers

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$m = $d = $y = $error = $action = "" ;
	$success = 0 ;

	// get variables
	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	if ( $action )
		$nav_line = '<a href="refer.php" class="nav">:: Previous</a>';

	if ( !$m || !$y || !$d )
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}
	$stat_begin = mktime( 0,0,0,date( "m",time()+$TIMEZONE ),date( "j",time()+$TIMEZONE ),date( "Y",time()+$TIMEZONE ) ) ;

	$selected_begin = mktime( 0,0,0,$m,$d,$y ) ;
	$selected_date = date( "D F d, Y", $selected_begin ) ;
	
	$dat1 = date( "D", $selected_begin ) ;
	
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
						
			$dat2 = date( "d", $selected_begin ) ;
			
			$dat3 = date( "F", $selected_begin ) ;
			
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
						
				$dat4 = date( "Y", $selected_begin ) ;		
	
	$refers = ServiceRefer_get_ReferOnDate( $dbh, $session_setup['aspID'], $selected_begin, ( $selected_begin + (60*60*24) ) ) ;

include_once("./header.php") ;
?>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Relat&oacute;rios: URLs de Refer&ecirc;ncia.</span><br>
	  Visualize da onde est&atilde;o vindo as suas visitas.	</p>
	<ul>
	  <Li>Aqui voc&ecirc; consegue visualizar de quais sites os visitantes est&atilde;o sendo direcionados para o seu website. </li>
	  <Li>O sistema exibe 10 dias de estat&iacute;sticas. Verifique regularmente de onde est&atilde;o vindo as suas visitas. </li>
	</ul>

	<?php if ( $action == "export" ): ?>
	<!-- future version possibility -->



	<?php else: ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="center"> 
		<?php
			for ( $c = $stat_begin; $c > ( $stat_begin - (60*60*24*10) ); 1 )
			{
				$date = date( "d/m/y", $c ) ;
				$m = date( "m",$c ) ;
				$y = date( "Y",$c ) ;
				$d = date( "j",$c ) ;
				print "<th class=\"navcale\"><a href=\"refer.php?m=$m&d=$d&y=$y\" class=\"navcale\">$date</a></th>" ;
				$c -= (60*60*24) ;
			}
		?>
	  </tr>
	</table>
	<p><strong>Data: <?php echo $dat1 . ", " . $dat2 . " de " . $dat3 . " de " . $dat4 ?></strong> &nbsp; 
	  (max. 500 resultados)	</p>
	<table width="100%" border=0 cellpadding=2 cellspacing=1>
	  <tr> 
		<th align="left" width="30">Contador</th>
		<th align="left">URL de Refer&ecirc;ncia</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $refers ); ++$c )
			{
				$refer = $refers[$c] ;
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;
				$refer_url = wordwrap( stripslashes( $refer['refer_url'] ), 100, "<br>", 1 ) ;

				print "
					<tr $class>
						<td>$refer[total]</td>
						<td><a href=\"$refer[refer_url]\" target=\"new\">$refer_url</a></td>
					</tr>
				" ;
			}
		?>
	</table>
	<?php endif ; ?>

  </td>
</tr>
 </table>
<!-- DO NOT REMOVE  -->
<!--  [DO NOT DELETE] -->
<?php include_once( "./footer.php" ) ; ?>