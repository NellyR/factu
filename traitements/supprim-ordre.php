<?php
	session_start();
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	//$limitebasse=$_GET["page"]*10;
	//$limit="limit ".$limitebasse.", 10";
	
	$traitements->supprimordre($_GET["id"]);
	
	return $traitements->ordres("0", "", "AND ordres.utilisateur=".$_SESSION["identification"]);
?>