<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	
	if((!isset($_POST["identifiant"]))||($_POST["identifiant"]=="")){
		$traitements->ajoututilisateur(utf8_decode($_POST["nom"]), utf8_decode($_POST["login"]), utf8_decode($_POST["mdp"]), utf8_decode($_POST["email"]), $_POST["societe"], $_POST["droit"]);
	}else{
		$traitements->modifutilisateur($_POST["identifiant"], utf8_decode($_POST["nom"]), utf8_decode($_POST["login"]), utf8_decode($_POST["mdp"]), utf8_decode($_POST["email"]), $_POST["societe"], $_POST["droit"]);
	}
	
	return $traitements->utilisateurs("","tableau");
?>