<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	if(isset($_GET["where"])) $where="AND libelle LIKE '".$_GET["where"]."%'";
	else $where="";
	
	$limitebasse=$_GET["page"]*10;
	$limit="limit ".$limitebasse.", 10";

	$traitements->clients("", "tableau", $limit, $where);
	
?>