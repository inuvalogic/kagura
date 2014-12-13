<?php
/*
 * Kagura - an Image Serve PHP Class
 * 
 * Author: Wisnu Hafid <www.wisnu-hafid.net>
 * 
 */

	error_reporting(0);
	
	define('_INC' ,1);
	
	if ($_SERVER['HTTP_HOST']=="localhost") {
		define('SITEPATH', '/kagura/');
	} else {
		define('SITEPATH', '');
	}
	
	include "class.kagura.php";
	
	$image = new kagura();
	$image->init();
	$image->render();