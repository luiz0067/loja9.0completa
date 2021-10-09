<?php
	/*****  Util::Image  ***************************************
	 *
	 *  $Id: Util_Image.php,v 1.2 2005/02/05 12:03:47 atendchat Exp $
	 *
	 *  Purpose:
	 *	[PURPOSE HERE]
	 *
	 *  Functions:
	 *
	 ****************************************************************/

	if ( ISSET( $_OFFICE_UTIL_IMAGE_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_IMAGE_LOADED = true ;

	/*****

	   Internal Dependencies

	*****/

	/*****

	   Module Specifics

	*****/

	/*****

	   Module Functions

	*****/

	/*****  Util_Image_ThumbSize  *******************************
	 *
	 *  History:
	 *	Nate Lee				April 28, 2001
	 *
	 *****************************************************************/
	function Util_Image_ThumbSize( $width, $height, $max_width, $max_height )
	{
		if ( !$max_width || !$max_height )
		{
			$max_width = 100 ;
			$max_height = 100 ;
		}

		if ( $width > $max_width )
		{
			$orig_width = $width ;
			$width = $max_width ;
			$diff = $orig_width - $max_width ;

			$percent_scale = round( ( $diff/$orig_width ) * 100 ) ;

			$height = round( ( 100 - $percent_scale )/100 * $height ) ;
			$dimensions = Util_Image_ThumbSize( $width, $height, $max_width, $max_height ) ;
			if ( count( $dimensions ) > 0 )
				return $dimensions ;
		}
		elseif ( $height > $max_height )
		{
			$orig_height = $height ;
			$height = $max_height ;
			$diff = $orig_height - $max_height ;

			$percent_scale = round( ( $diff/$orig_height ) * 100 ) ;

			$width = round( ( 100 -  $percent_scale )/100 * $width ) ;
			$dimensions = Util_Image_ThumbSize( $width, $height, $max_width, $max_height ) ;
			if ( count( $dimensions ) > 0 )
				return $dimensions ;
		}
		else
		{
			$dimensions = ARRAY( $width, $height ) ;
			return $dimensions ;
		}
	}
?>
