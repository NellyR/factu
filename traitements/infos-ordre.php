<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	$req = $conn->prepare('SELECT societe, client, remarques FROM ordres WHERE identifiant='.$_POST["id"]);
	$req->execute();
	$row = $req->fetch();
	// On encode en UTF8 car json ne travaille que dans ce charset :
	$row["remarques"]=utf8_encode($row["remarques"]);
	echo json_encode($row);

?>