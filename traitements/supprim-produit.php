<?php
	session_start();
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	// on supprime
	$traitements->supprimordremandataire($_GET["id"]);
	
	
	// on remet la liste des produits a jour
	$traitements->ordres_mandataire($_GET["ordre"]);
	echo "<tr><td><i><a href='demande-mandataire.php?id=".$_GET["ordre"]."'>Ajouter un produit</a></i></td><td></td><td></td></tr>";

?>