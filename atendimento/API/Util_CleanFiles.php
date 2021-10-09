<?php
	/*****  CleanFiles::util  ************************************
	 *
	 *  $Id: Util_CleanFiles.php,v 1.5 2005/02/05 12:03:47 atendchat Exp $
	 *
	 *  Purpose:
	 *	[PURPOSE HERE]
	 *
	 *  Functions:
	 *
	 ****************************************************************/

	if ( ISSET( $_OFFICE_UTIL_CleanFiles_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_CleanFiles_LOADED = true ;

	/*****

	   Internal Dependencies

	*****/

	/*****

	   Module Specifics

	*****/

	/*****

	   Module Functions

	*****/

	/*****  CleanFiles_util_CleanChatSessionFiles  *******************
	 *
	 *  History:
	 *	Kyle Hicks				Nov 13, 2002
	 *
	 *****************************************************************/
	FUNCTION CleanFiles_util_CleanChatSessionFiles( )
	{
		global $DOCUMENT_ROOT ;

		$chatsessions_dir = "$DOCUMENT_ROOT/web/chatsessions" ;
		$time_to_delete = time() - (60*60*5) ;
		if ( $dir = @opendir( $chatsessions_dir ) )
		{
			while( $file = readdir( $dir ) )
			{
				if ( preg_match( "/(.txt)/", $file ) )
				{
					$mod_time = filemtime( "$chatsessions_dir/$file" ) ;
					if ( $mod_time < $time_to_delete )
					unlink( "$chatsessions_dir/$file" ) ;
				}
			}
			closedir($dir) ;
		}
		// let's remove the DUMP.txt file (this file is a central dump location
		// for messages AFTER the operator has transferred the call to another department.
		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/DUMP.txt" ) )
			unlink( "$DOCUMENT_ROOT/web/chatsessions/DUMP.txt" ) ;
		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/DUMP_TR.txt" ) )
			unlink( "$DOCUMENT_ROOT/web/chatsessions/DUMP_TR.txt" ) ;

		return true ;
	}
?>