<?php

	session_start();
    session_regenerate_id();
    session_name(md5('TousUnisContreLeSida'));
	
	if (!isset($_SESSION['connect']))
		$_SESSION['connect']=0;

	require_once("bdd/BaseDeDonnees.php");
	require_once("lib/DisplayManager.php");

	$baseDeDonnees = new BaseDeDonnees();
	
	$baseDeDonnees->connexion();
	
	$displayManager = new DisplayManager($baseDeDonnees);
	
	$displayManager->display();

	$baseDeDonnees->deconnexion();

?>