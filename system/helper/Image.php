<?php
/**
 *	Image helper class.
 *
 *	@author      Raymond van Velzen <raymond@latunyi.com>
 *	@package     LatunyiFramework
 **/

namespace System\Helper;

/**
*	 ImageHelper
*
*	 Helper for handling image-related requests.
*
*	 @author		R.N. van Velzen
*	 @copyright		Latunyi
*	 @package		Meridium
*
**/
class Image {

	/**
	*	 Resizes an image (JPG, GIF, PNG) to the specified size (proportionally).
	*	 If the height or width is 0, the image will be resized
	*	 considering only that dimension.
	*
	*	 @param		   int		  $img			  Full path to the image file
	*	 @param		   int		  $target_w		   Target width
	*	 @param		   int		  $target_h		   Target height
	*	 @return		void
	**/
	static public function resizeImage($img, $target_w, $target_h) 
	{
		// Check file
		if (!file_exists($img)) 
		{
			throw new Exception('Can\'t resize: Image "' . $img . '" doesn\'t exist.');
		}
	
		// Get current and new dimensions
		list($cur_w, $cur_h) = getimagesize($img);
		list($new_h, $new_w) = self::CalculateNewDimensions($cur_h, $cur_w, $target_h, $target_w);
		//pr('current size: ' . $cur_w . ' x ' . $cur_h);
		//prx('new size: ' . $new_w . ' x ' . $new_h);
		
		// Quit if resize is not necessary
		if ($new_h == 0) {
			return;
		}
	
		// Find image type (using ext - can't use fileinfo on Windows)
		$type = FileHelper::GetExt($img);
		
		if ($type == 'jpg') 
		{
			$type = 'jpeg';
		}
		
		$create_func = 'imagecreatefrom' . $type;
		$image_func = 'image' . $type;
		
		$newimg = imagecreatetruecolor($new_w, $new_h);
		$curimg = $create_func($img);
		imagecopyresampled($newimg, $curimg, 0, 0, 0, 0, $new_w, $new_h, $cur_w, $cur_h);
		$result = $image_func($newimg, $img);

		if (!$result) 
		{
			throw new Exception('Error resizing image "' . $img . '".');
		}

	}

	/**
	*	 Calculates the new dimensions for a resized image. Returns an
	*	 an array with the new height and width. If given a zero target height
	*	 or width, it will calculate the other dimension accordingly.
	*
	*	 @param		   int		  $cur_h			Current height
	*	 @param		   int		  $cur_w			Current width
	*	 @param		   int		  $target_h		   Target height (0 for no limit)
	*	 @param		   int		  $target_w		   Target width (0 for no limit)
	*	 @return		mixed					 Array with new height and width
	**/
	static private function calculateNewDimensions($cur_h, $cur_w, $target_h, $target_w) 
	{
		// With target width AND height
		if ($target_h > 0 && $target_w > 0)
		{
			// Already smaller than or equal to target?
			if ($cur_h <= $target_h && $cur_w <= $target_w) 
			{
				return array(0, 0);
			}

			// Get ratio of source width vs. target width
			$w_ratio = $cur_w / $target_w;
	
			// Get ratio of source height vs. target height
			$h_ratio = $cur_h / $target_h;
			
			// Calculate new size, using biggest ratio
			if ($w_ratio > $h_ratio)
			{
				$new_w = $target_w;
				$new_h = round($cur_h / $w_ratio);
			}
			else
			{
				$new_w = round($cur_w / $h_ratio);
				$new_h = $target_h;
			}
		}
		else
		{
			// With target width OR height
		
			// Already smaller or equal to target?
			if ( ($target_h == 0 && $cur_w <= $target_w) || ($target_w == 0 && $cur_h <= $target_h) )
			{
				return;
			}
				
			// Find new height
			if ($target_h == 0) 
			{
				$factor = $target_w / $cur_w;
				$new_h = floor($cur_h * $factor);
				$new_w = $target_w;
			}

			// Find new width
			if ($target_w == 0) 
			{
				$factor = $target_h / $cur_h;
				$new_h = $target_h;
				$new_w = floor($cur_w * $factor);
			}
		}

		return array($new_h, $new_w);

	} 

	/**
	*	 Crops an image (JPG, GIF, PNG) to the specified size (proportionally),
	*	 to a rectangle of the specified width and height.
	*
	*	 @param		   int		  $img			  Full path to the image file
	*	 @param		   int		  $target_w		   Target width
	*	 @param		   int		  $target_h		   Target height
	*	 @return		void
	**/
	static public function cropToRectangle($img, $target_w, $target_h) 
	{
		// Get current image size
		list($cur_w, $cur_h) = getimagesize($img);

		// Get ratio of source width vs. target width
		$w_ratio = $cur_w / $target_w;

		// Get ratio of source height vs. target height
		$h_ratio = $cur_h / $target_h;
		
		// Calculate size of crop rectangle, using smallest ratio
		if ($w_ratio < $h_ratio)
		{
			$crop_w = $cur_w;
			$crop_h = round($target_h * $w_ratio);
			
			// Calculate top-left point
			$top_left = round(($cur_h / 2) - ($crop_h / 2));

			$src_x = 0;
			$src_y = $top_left;
		}
		else
		{
			$crop_w = round($target_w * $h_ratio);
			$crop_h = $cur_h;
			
			// Calculate top-left point
			$top_left = round(($cur_w / 2) - ($crop_w / 2));
			
			$src_x = $top_left;
			$src_y = 0;
		}
		
		// Rename parameters
		$src_w = $crop_w;
		$src_h = $crop_h;
		
		$new_w = $target_w;
		$new_h = $target_h;
		
		// Find image type (using ext - can't use fileinfo on Windows)
		$type = \System\Helper\File::GetExt($img);
		
		if ($type == 'jpg') 
		{
			$type = 'jpeg';
		}
		
		$create_func = 'imagecreatefrom' . $type;
		$image_func = 'image' . $type;
		
		$newimg = imagecreatetruecolor($new_w, $new_h);
		$curimg = $create_func($img);
		imagecopyresampled($newimg, $curimg, 0, 0, $src_x, $src_y, $new_w, $new_h, $src_w, $src_h);
		$result = $image_func($newimg, $img);

		if (!$result) 
		{
			throw new Exception('Error resizing image "' . $img . '".');
		}
	}

	// -------------------------------------------------------------------

	/**
	*	 checkMinimalDimensions
	*
	*	 Checks if the given image file has the required minimal
	*	 width and height.
	*
	*	 @param		   int		  $img			  Full path to the image file
	*	 @param		   int		  $min_w			Minimal width
	*	 @param		   int		  $min_h			Target height
	*	 @return		void
	**/
	static public function checkMinimalDimensions($img, $min_w, $min_h) 
	{
		// Check file exists
		if (!file_exists($img))
		{
			throw new Exception('Error: Image file "' . $img . '" was not found.');
		}		 

		// Get current image size
		list($cur_w, $cur_h) = getimagesize($img);

		if ($cur_w < $min_w || $cur_h < $min_h)
		{
			return false;
		}
		
		return true;
	}
}  