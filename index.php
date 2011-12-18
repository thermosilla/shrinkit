<?php 
	
	define('BASEPATH','');

	include ('confiig.inc.php');
	include ('mysql.class.php')
	include ('includes/fitzgerald.class.php');

	
	class Application extends Fitzgerald
	{
		//El código va aquí
	}

	$app = new Application()


	$app->run();
 ?>