<?php 
	session_start();
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();

	$nblignes=$_POST["lignes"];
	
	// AJOUT :
	if($_POST["identifiant"]==0){
		// Ajout demande (on récupère l'id) :
		$id= $traitements->ajoutordre($_POST["societe"], $_POST["client"], $_SESSION["identification"], $_POST["remarques"], $_POST["archive"],"0");
		//Ajout des différentes lignes...
		for($i=1; $i<=$nblignes; $i++){
			if(isset($_POST["nomenc".$i])) $nomenc = $_POST["nomenc".$i];
			else $nomenc="";
			$traitements->ajoutligne($_POST["produit".$i], $_POST["cplt".$i], $_POST["montant".$i], $_POST["frais".$i], $id, $nomenc);	
		}
	
	// OU MODIFICATION
	}else{
		// Modif demande
		$traitements->modifordre($_POST["societe"], $_POST["client"], $_POST["remarques"], $_POST["archive"], $_POST["identifiant"]);
		//Suppression lignes
		// suppressionligne(id de ma ligne, id de mon ordre)
		$traitements->suppressionligne("", $_POST["identifiant"]);
		//Ajout des différentes lignes...
		for($i=1; $i<=$nblignes; $i++){
			if(isset($_POST["nomenc".$i])) $nomenc = $_POST["nomenc".$i];
			else $nomenc="";
			$traitements->ajoutligne($_POST["produit".$i], $_POST["cplt".$i], $_POST["montant".$i], $_POST["frais".$i], $_POST["identifiant"], $nomenc);	
		}
	
	}
	
	//Si l'utilisateur valide ET envoie la demande, on envoie un mail à tous ceux qui peuvent archiver une demande.
	if($_POST["archive"]==1){
		$expediteurs="";
		global $conn;
		$req=$conn->prepare('SELECT email FROM utilisateurs LEFT JOIN link_droits_utilisateurs ON utilisateur = utilisateurs.identifiant WHERE droit =2');
		$req->execute();
		while ($row = $req->fetch()) {
			$expediteurs.=$row["email"].",";
		}
		$headers = "From: Outil facturation <nobody@sd1950.sivit.org>\r\n";
		mail($expediteurs, "Nouvel ordre de facturation", "Bonjour,\nNous vous informons qu'un nouvel ordre de facturation est en attente de traitement et d'archivage de votre part.\nMerci !\n\n\nPS : Ce message est envoyé automatiquement depuis l'outil de facturation. Merci de ne pas répondre à cet e-mail.", $headers);

	}
	
	// Enfin, on redirige vers la liste.
	header("location:../liste-demandes.php");
?>
