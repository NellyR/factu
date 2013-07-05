<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	
	$traitements= new traitements();
	$identification= new identification();
	
	if((isset($_POST["identifiant"]))&&($_POST["identifiant"]!=0)){
		return $traitements->frais_antenne($_POST["identifiant"]);
	}

?>