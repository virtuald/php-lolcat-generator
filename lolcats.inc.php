<?php
/*
	(C)2007 Dustin Spicuzza

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License (GPL)
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	To read the license please visit http://www.gnu.org/copyleft/gpl.html

	-----------
	
	This is a (relatively) simple PHP object that generates a lolcat meme. Yes,
	there are other sites/programs you can use to generate them, but as far as
	I know there is no open source lolcat generator. So now you can incorporate
	lolcats into your website without relying on external sites!
	
	Of course, the creative among you will figure out how to integrate this with
	other tools and make this script actually pseudo useful... 

	Parameters:
	
	image 	= filename/url of image. WARNING: the function assumes that the 
			image parameter has already been validated!! Otherwise, the user
			could try and view arbitrary image files on your server.. 
	
	text	= text to be written as a lolcat string to the specified image
	
	align 	= 'tl' top left, 'tr' top right, 'bl' bottom left, 'br' bottom right
		If no position given or invalid, it defaults to the top left
		
	font	= this is a reference
	
	fontsize = size (in pixels) of font. Defaults to 15
	
	
	
*/


class Lolcat {
	
	var $img;		// Lolcat image
	var $imgtype;	// type of image
	
	function Create($image,$text,$align,$font,$fontsize,$resize_width, $padding = 5, $text_color = -1){
		
		$this->img = false;
		
		// second, make sure its an image
		if (!(list($width, $height, $this->imgtype, $attr) = @getimagesize($image)))
			return false;
			
		// ensure position is correct
		if (!in_array($position,array('tl','tr','bl','br')))
			$position = 'tl';
		
		// fix up the font size
		$fontsize = str_replace('px','',trim($fontsize));
		if ($fontsize == '' || !is_int($fontsize))
			$fontsize = 15;
			
		// verify the font
		if (!($font = realpath($font)))
			return false;
			
		switch ($this->imgtype){
			case 1:		// gif
				$this->img = @imagecreatefromgif($image);
				break;
			case 2:		// jpg
				$this->img = @imagecreatefromjpeg($image);
				break;
			case 3:		// png
				$this->img = @imagecreatefrompng($image);
				break;
			default:	// i don't care about other formats
				return false;
		}
		
		if (!$this->img)
			return false;
		
		if ($text_color == -1)
			$text_color = imagecolorallocate($this->img, 255, 255, 255);
		
		// see if we need to resize the image first
		if ($resize_width != 0){

			// todo: Add a 'max-width, max-height' parameter... 
			$ratio = $width / $resize_width;
			$resize_height = intval($height / $ratio);

			$thumb_img = imagecreatetruecolor($resize_width, $resize_height);
			imagecopyresampled($thumb_img, $this->img, 0, 0, 0, 0, $resize_width, $resize_height, $width, $height);

			// fix these
			$height = $resize_height;
			$width = $resize_width;
			
			// destroy original object
			imagedestroy($this->img);
			$this->img = $thumb_img;
		}
		
		// next, we should try to create the text.. hopefully it wraps nicely
		putenv('GDFONTPATH=' . realpath('.'));		// hack
		
		// grab the font height, M is supposed to be big, with any random chars lying around
		$font_height = $this->img_height(imagettfbbox($fontsize,0,$font,'Mjg'));
		$row_spacing = intval($font_height * .2);	// purely arbitrary value

		$space_width = $this->img_width(imagettfbbox($fontsize,0,$font,' '));
		
		// try and do our best imitation of wordwrapping
		$text_elements = explode(' ',str_replace("\n",'',str_replace("\r\n",'',$text)));
		
		// adjust this depending on alignment
		$top = $padding + $font_height;
		$left = $padding;
		
		// initialize
		$line_width = 0;
		$line_beginning = 0;		// index of beginning of line
		$inc = 1;
		$c = count($text_elements);
		$i = 0;
		
		if ($align[0] == 'b'){
			$top = $height - $padding;
			$font_height = $font_height * -1;
			$row_spacing  = $row_spacing * -1;
			$inc = -1;
			$i = $c -1;
		}
		
		$dbg = get_get_var('dbg');
		$line_beginning = $i;
		
		// draw text elements starting from alignment position.. 
		for (;$i >= 0 && $i <= $c;$i += $inc){
			
			$lf_width = $this->img_width(imagettfbbox($fontsize,0,$font,$text_elements[$i]));
			
			// add a space
			if ($i != $line_beginning)
				$lf_width += $space_width;
			
			// see if we've exceeded the max width
			if ($lf_width + $line_width + $padding * 2 > $width){
				
				// draw it out then!
				if ($align[1] == 'r')
					$left = $width - $padding - $line_width;
			
				if ($align[0] == 'b')
					$text = implode(' ',array_slice($text_elements,$i+1,$line_beginning-$i));
				else
					$text = implode(' ',array_slice($text_elements,$line_beginning,$i - $line_beginning));
			
				// draw the text
				imagettftext($this->img,$fontsize,0,$left,$top,$text_color,$font,$text);
			
				// keep moving, reset params
				$top += $font_height + $row_spacing;
				$line_beginning = $i;
				$line_width = $lf_width;
			
			}else{
				// keep trucking
				$line_width += $lf_width;
			}
		}
		
		// get the last line too
		if ($line_width != 0){
			if ($align[1] == 'r')
				$left = $width - $padding - $line_width;
				
			if ($align[0] == 'b')
				$text = implode(' ',array_slice($text_elements,$i+1,$line_beginning-$i));
			else
				$text = implode(' ',array_slice($text_elements,$line_beginning,$i - $line_beginning));		
			
			imagettftext($this->img,$fontsize,0,$left,$top,$text_color,$font,$text);
		}
		
		return true;
	}

	// utility functions

	function img_width($sz_array){
		return abs(max($sz_array[0] - $sz_array[2], $sz_array[4] - $sz_array[6]));
	}

	function img_height($sz_array){
		return abs(max($sz_array[7] - $sz_array[1], $sz_array[5] - $sz_array[3]));
	}
	
	function Show(){
	
		// if the image is still valid, then we should output it at the end
		if (!$this->img)
			return false;
			
		switch ($this->imgtype){
			case 1:		// gif
				header("Content-Type: image/gif");
				$this->img = imagegif($this->img);
				break;
			case 2:		// jpg
				header("Content-Type: image/jpeg");
				$this->img = imagejpeg($this->img);
				break;
			case 3:		// png
				header("Content-Type: image/png");
				$this->img = imagepng($this->img);
				break;
			default:	// i don't care about other formats
				return false;
		}
		
		return true;
	}
	
	function WriteToFile($file){
	
		// if the image is still valid, then we should output it at the end
		if (!$this->img)
			return false;
			
		switch ($this->imgtype){
			case 1:		// gif
				return @imagegif($this->img,$file);
			case 2:		// jpg
				return @imagejpeg($this->img,$file);
			case 3:		// png
				return @imagepng($this->img,$file);
		}
		
		return false;
	}
	
	function ShowError($text){
		$img = imagecreate(400,50);
		imagefill($img,0,0,imagecolorallocate($img,0,0,0));
		imagestring($img,2,10,20,$text,imagecolorallocate($img,255,255,255));
		header("Content-Type: image/gif");
		imagegif($img);
	}
	
}


?>