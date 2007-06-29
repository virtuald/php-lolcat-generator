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

$img = '';
$r_img = get_get_var('img');
if ($r_img != ""){
	// try and determine if its a URL or not
	$url = parse_url($r_img);
	if ($url !== false && isset($url['scheme']) && $url['scheme'] == 'http')
		$img = $r_img;
}

if ($img == ''){
	// pick a random one from the cats directory
	$images = glob(dirname(__FILE__) . '/cats/*.jpg');
	
	// seed the generator (for static links, for example)
	$seed = get_get_var('seed');
	if ($seed != "")
		mt_srand(intval($seed));
	
	$img = $images[mt_rand(0,count($images)-1)];
}

if (!$lolcat->Create($img,get_get_var('text'),get_get_var('align'),'impact.ttf',15,300))
	if (!headers_sent())
		return $lolcat->ShowError("Error: I couldn't create a image for you :(");
	else
		return;

if (!$lolcat->Show())
	if (!headers_sent())
		return $lolcat->ShowError("Error: I couldn't show you the image :(");
	else
		return;


// utility function
function get_get_var($str){
	if (isset($_GET[$str]))
		return $_GET[$str];
	else
		return "";
}

?>