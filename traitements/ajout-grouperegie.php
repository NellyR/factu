<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	
	// Ajout
	if((!isset($_POST["identifiant"]))||($_POST["identifiant"]=="")) $traitements->ajoutregie(utf8_decode($_POST["libelle"]),utf8_decode($_POST["frais"]));
	
	// Modification
	else $traitements->modifregie($_POST["identifiant"], utf8_decode($_POST["libelle"]),utf8_decode($_POST["frais"]));
	
	// Affichage
	//if(isset($_POST["select"])) 
	return $traitements->groupe_regies();
	//else return $traitements->clients("", "tableau", "limit 0,10", "AND libelle>='".$_GET["libelle"]."'");
?>