<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	$req = $conn->prepare('SELECT identifiant, libelle, adresse, tax_id FROM clients WHERE identifiant='.$_GET["id"]);
	$req->execute();
	$row = $req->fetch();
	// On encode en UTF8 car json ne travaille que dans ce charset :
	$row["libelle"]=utf8_encode(stripslashes($row["libelle"]));
	$row["adresse"]=utf8_encode(stripslashes($row["adresse"]));
	$row["tax_id"]=utf8_encode(stripslashes($row["tax_id"]));
	echo json_encode($row);

?>