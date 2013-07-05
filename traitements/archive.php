<?php 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();

	global $conn;
	if(!isset($_POST["facture_m"])){
		$req = $conn->prepare('UPDATE ordres SET archive=2, facture=?, date_archive="'.date("Y-m-d").'" WHERE identifiant=?');
		$req->execute(array($_POST["facture"], $_POST["identifiant"]));
	}else{
		$req = $conn->prepare('UPDATE ordres SET archive=2, facture=? WHERE identifiant=?');
		$req->execute(array($_POST["facture_m"], $_POST["identifiant"]));	
	}
	
	header("location:../liste-demandes-archivees.php");
?>
