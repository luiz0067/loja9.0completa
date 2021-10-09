<?php
	if ( ISSET( $_OFFICE_UTIL_OPT_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_OPT_LOADED = true ;

	/*****  Util_OPT_Database  ****************************************
	 *
	 *  History:
	 *	Holger				May 3, 2002
	 *
	 *****************************************************************/
	function Util_OPT_Database( $dbh,
				$tables )
	{

		if ( count( $tables ) > 0 )
		{
			// optimize tables:
			// tables that have data frequently being REMOVED will
			// take up extra space, due to space taken up by the removed
			// data, used later on.  optimize will defrag the data and
			// free up the deleted space.

			for ( $c = 0; $c < count( $tables ); ++$c )
			{
				$query = "OPTIMIZE TABLE $tables[$c]" ;
				database_mysql_query( $dbh, $query ) ;
			}
			
			return true ;
		}
		else
			return false ;
	}

?>