<?php 
	session_start();
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	// AJOUT :
	if($_POST["identifiant"]==0){
		
		if(!isset($_POST["purchase_order"])){
			$_POST["purchase_order"]="";
			$_POST["description"]="";
		}
		
		// Ajout demande dans la liste classique (on récupere l'id) :
		$id_ordre= $traitements->ajoutordre($_POST["societe"], $_POST["client"], $_SESSION["identification"], "", $_POST["archive"],"1", $_POST["delai"]);
		// Ajout des détails mandataire pour chaque produit existant :
		
		$id_demande = $traitements->ajoutordremandataire($id_ordre, $_POST["produit"], $_POST["groupe"], $_POST["date_deb"], $_POST["date_fin"], $_POST["facture"], $_POST["spot"], $_POST["net"], $_POST["netnet"], $_POST["honoraires"], $_POST["nbspots_infosupp"], $_POST["info_supp"], $_POST["purchase_order"], $_POST["description"]);
		// Ajout des supports pour chaque produit de chaque demande
		if(isset($_POST["supports"])){
			foreach($_POST["supports"] as $support){
				$traitements->ajoutsupportproduit($id_demande, $_POST["produit"], $support);
			}
		}
		
	// MODIFICATION
	}else{
		
		if(!isset($_POST["purchase_order"])){
			$_POST["purchase_order"]="";
			$_POST["description"]="";
		}
		
		// Modification infos générales de la demande (client, reglement, état de la demande) :
		$traitements->modifordre($_POST["societe"], $_POST["client"], "", $_POST["archive"], $_POST["identifiant"], $_POST["delai"]);
		$id_ordre=$_POST["identifiant"];
		// Modification du produit en cours si un produit est en cours :
		if(isset($_POST["ordre_mandataire"])){
			$traitements->modifordremandataire($_POST["identifiant"], $_POST["produit"], $_POST["supports"], $_POST["groupe"], $_POST["date_deb"], $_POST["date_fin"], $_POST["facture"], $_POST["spot"], $_POST["net"], $_POST["netnet"], $_POST["honoraires"], $_POST["nbspots_infosupp"], $_POST["info_supp"], $_POST["purchase_order"], $_POST["description"], $_POST["ordre_mandataire"]);
		// Ou ajout d'un produit dans un ordre déja existant :
		}else{
			if(isset($_POST["produit"])){
				$id_demande = $traitements->ajoutordremandataire($id_ordre, $_POST["produit"], $_POST["groupe"], $_POST["date_deb"], $_POST["date_fin"], $_POST["facture"], $_POST["spot"], $_POST["net"], $_POST["netnet"], $_POST["honoraires"], $_POST["nbspots_infosupp"], $_POST["info_supp"], $_POST["purchase_order"], $_POST["description"]);
				// Ajout des supports :
				if(isset($_POST["supports"])){
					foreach($_POST["supports"] as $support){
						$traitements->ajoutsupportproduit($id_demande, $_POST["produit"], $support);
					}
				}
			}
		}
		
		
		
	}
		
		//Ajout des différentes lignes...
		/*
		for($i=1; $i<=$nblignes; $i++){
			if(isset($_POST["nomenc".$i])) $nomenc = $_POST["nomenc".$i];
			else $nomenc="";
			$traitements->ajoutligne($_POST["produit".$i], $_POST["cplt".$i], $_POST["montant".$i], $_POST["frais".$i], $id, $nomenc);	
		}*/
	
	// OU MODIFICATION
	/*
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
	*/
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
	
	
	// Enfin, on redirige vers la demande en mode modification pour un ajout de plusieurs produits 
	if($_POST["multi"]=="1"){
			header("location:../demande-mandataire.php?id=".$id_ordre);
	// ou vers la liste si ajout normal.
	}else{
		header("location:../liste-demandes.php");
	}
?>
