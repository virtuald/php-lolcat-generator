LOLCat Generator 0.1 (6/29/2007)
---------------------------------

	(C)2007 Dustin Spicuzza <dustin@virtualroadside.com>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License (GPL)
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	To read the license please visit http://www.gnu.org/copyleft/gpl.html

How to use this?
-----------------

You need a webserver with PHP on it (preferably PHP 5) and that has GD enabled. Additionally,
you should probably have fopen_url_wrappers enabled for the demo page, though normal
usage does not require that. 

To make the demo work
-----------------------

You MUST put the appropriate font file in the cats directory! I use the impact.ttf file
that comes with Office 2003, but I cannot distribute it. Don't ask. Just copy the font file from
c:\windows\fonts to the appropriate directory and you should be set. Also, you should fill the
cats directory with random pictures of cats, otherwise it will just use the one photo that is there,
which happens to be a photo of my roommate's cat. :)

img.php is the frontend that shows how you can use the library. lolcats.inc.php is the library 
you can use with your php scripts.


If you use this script, let me know! If you find bugs or make enhancements, let me know as well!
Thanks. 

Dustin <dustin@virtualroadside.com>
