<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	
	if((!isset($_POST["identifiant"]))||($_POST["identifiant"]=="")) $traitements->ajoutclient(utf8_decode($_POST["libelle"]),utf8_decode($_POST["adresse"]),utf8_decode($_POST["tax_id"]));
	else $traitements->modifclient($_POST["identifiant"], utf8_decode($_POST["libelle"]),utf8_decode($_POST["adresse"]),utf8_decode($_POST["tax_id"]));
	
	if(isset($_POST["select"])) return $traitements->clients("", "menuderoulant");
	else return $traitements->clients("", "tableau", "limit 0,10", "AND libelle>='".$_GET["libelle"]."'");
?>