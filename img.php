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
	
	Sample frontend for lolcats PHP library
	
*/

require('lolcats.inc.php');


$lolcat = new Lolcat();

if (!$lolcat->Create('lolcat.jpg',get_get_var('text'),get_get_var('align'),'impact.ttf',15,300))
	die("I couldn't create a file for you. :(");

if (!$lolcat->Show())
	echo "No file for you. :(";


// utility function
function get_get_var($str){
	if (isset($_GET[$str]))
		return $_GET[$str];
	else
		return "";
}

?>