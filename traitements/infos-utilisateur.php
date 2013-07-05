<?php
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	$req = $conn->prepare('SELECT identifiant, nom, login, motdepasse, email FROM utilisateurs WHERE identifiant='.$_GET["id"]);
	$req->execute();
	$row = $req->fetch();
	// On encode en UTF8 car json ne travaille que dans ce charset :
	$row["nom"]=utf8_encode($row["nom"]);
	$row["login"]=utf8_encode($row["login"]);
	$row["motdepasse"]=utf8_encode($row["motdepasse"]);
	$row["email"]=utf8_encode($row["email"]);
	$req = $conn->prepare('SELECT droit FROM link_droits_utilisateurs WHERE utilisateur='.$_GET["id"]);
	$req->execute();
	$i=1;
	while($row2 = $req->fetch()){
		$row["droit"][]=$row2["droit"];
	}
	$req = $conn->prepare('SELECT societe FROM link_societes_utilisateurs WHERE utilisateur='.$_GET["id"]);
	$req->execute();
	$i=1;
	while($row2 = $req->fetch()){
		$row["societe"][]=$row2["societe"];
	}
	echo json_encode($row);

?>