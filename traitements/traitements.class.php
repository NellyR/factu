<?php
//classe de connexion et d'identification
class traitements extends affichage{

	// fonction d'ajout d'un client
	public function ajoutclient($libelle="", $adresse="", $tax_id=""){
		if($libelle!=""){
			global $conn;
			$req = $conn->prepare('INSERT INTO clients(libelle, adresse, tax_id) VALUES(:libelle, :adresse, :tax_id)');
			$req->execute(array(":libelle"=>$libelle,":adresse"=>$adresse,":tax_id"=>$tax_id));
		}
	}
	// Modification d'un client
	public function modifclient($identifiant, $libelle, $adresse, $tax_id=""){
		global $conn;
		$req = $conn->prepare('UPDATE clients SET libelle=:libelle, adresse=:adresse, tax_id=:tax_id WHERE identifiant=:identifiant');
		$req->execute(array(":identifiant"=>$identifiant,":libelle"=>$libelle,":adresse"=>$adresse,":tax_id"=>$tax_id));
		
	}
	
	//Ajout d'un groupe de régies :
	public function ajoutregie($libelle, $frais){
		if($libelle!=""){
			global $conn;
			$req = $conn->prepare('INSERT INTO groupes_regie(libelle, frais_antenne) VALUES(:libelle, :frais)');
			$req->execute(array(":libelle"=>$libelle,":frais"=>$frais));
		}
	}
	
	// Modification d'un groupe de régies
	public function modifregie($identifiant, $libelle, $frais){
		global $conn;
		$req = $conn->prepare('UPDATE groupes_regie SET libelle=:libelle, frais_antenne=:frais WHERE identifiant=:identifiant');
		$req->execute(array(":identifiant"=>$identifiant,":libelle"=>$libelle,":frais"=>$frais));
		
	}
	
	// Suppression d'un client
	// Attention : on ne supprime un client que s'il n'y a aucun ordre de facturation associé (sinon, archive...)
	public function supprimclient($id){
		global $conn;
		$req= $conn->prepare('SELECT count(client) FROM ordres WHERE client=?');
		$req->execute(array($id));
		$row = $req->fetch();
		if(empty($row)){
			$req = $conn->prepare('DELETE FROM clients WHERE identifiant=?');
			$req->execute(array($id));
		}else{
			$req = $conn->prepare('UPDATE clients SET archive=1 WHERE identifiant=?');
			$req->execute(array($id));		
		}
	}
	
	public function supprimordremandataire($id){
		global $conn;
		$req= $conn->prepare('DELETE FROM supportsproduit WHERE ordre_mandataireID=?');
		$req->execute(array($id));
		
		$req = $conn->prepare('DELETE FROM ordres_mandataire WHERE identifiant=?');
		$req->execute(array($id));
		
	}
	
	// Suppression d'un ordre de facturation
	public function supprimordre($id){
		global $conn;
		$req = $conn->prepare('DELETE FROM ordres WHERE identifiant=?');
		$req->execute(array($id));
		/*
		$req = $conn->prepare('DELETE FROM lignesordre WHERE ordre=?');
		$req->execute(array($id)); */
		$this->suppressionligne("", $id);
		
		// On supprime également les ordres mandataires éventuellement rattachés, en supprimant dans un premier temps les supports rattachés :
		$req= $conn->prepare('SELECT identifiant FROM ordres_mandataire WHERE ordreID=?');
		$req->execute(array($id));
		$req2= $conn->prepare('DELETE FROM supportsproduit WHERE ordre_mandataireID=?');
		while ($row = $req->fetch()) {
			$req2->execute(array($row["identifiant"]));
		}
		
		$req = $conn->prepare('DELETE FROM ordres_mandataire WHERE ordreID=?');
		$req->execute(array($id));
	}
	
	// Fonction d'ajout d'un ordre de facturation
	public function ajoutordre($societe, $client, $utilisateur, $remarques, $archive, $ordre_mandat="0", $delai=0){
		global $conn;
		$req=$conn->prepare('INSERT INTO ordres(date, societe, client, utilisateur, remarques, archive, ordre_mandataire, delaiID) VALUES(:date, :societe, :client, :utilisateur, :remarques, :archive, :ordre_mandataire, :delai)');
		$req->execute(array(':date'=>date('Y-m-d'), ':societe'=>$societe, ':client'=>$client, ':utilisateur'=>$utilisateur, ':remarques'=>$remarques, ':archive'=>$archive, ':ordre_mandataire'=>$ordre_mandat, ':delai'=>$delai));
		return $conn->lastInsertId();
	}
	
	// Fonction d'ajout d'un ordre de facturation spécifique mandataire
	public function ajoutordremandataire($ordreID, $produitID, $groupeID, $date_deb, $date_fin, $facture, $nbspots, $net, $netnet, $honoraires, $nbspots_infosupp, $info_supp, $purchase_order, $description){
		global $conn;
		$req=$conn->prepare('INSERT INTO ordres_mandataire(ordreID, produitID, groupeID, date_deb, date_fin, facture, nbspots, montant_net, montant_netnet, honoraires, nbspots_infosupp, info_supp, purchase_order, description) VALUES(:ordreID, :produitID, :groupeID, STR_TO_DATE(:date_deb,"%d/%m/%Y"), STR_TO_DATE(:date_fin,"%d/%m/%Y"), :facture, :nbspots, :montant_net, :montant_netnet, :honoraires, :nbspots_infosupp, :info_supp, :purchase_order, :description)');
		$req->execute(array(':ordreID'=>$ordreID,':produitID'=>$produitID,':groupeID'=>$groupeID,':date_deb'=>$date_deb,':date_fin'=>$date_fin, ':facture'=>$facture,':nbspots'=>$nbspots,':montant_net'=>$net,':montant_netnet'=>$netnet,':honoraires'=>$honoraires,':nbspots_infosupp'=>$nbspots_infosupp, ':info_supp'=>$info_supp,':purchase_order'=>$purchase_order, ':description'=>$description ));
		return $conn->lastInsertId();
	}
	
	// Fonction d'ajout des supports pour un produit donné par demande mandataire
	public function ajoutsupportproduit($ordreID, $produitID, $supportID){
		global $conn;
		$req=$conn->prepare('INSERT INTO supportsproduit(ordre_mandataireID, supportID, produitID) VALUES(:ordreID, :supportID, :produitID)');
		$req->execute(array(':ordreID'=>$ordreID,':supportID'=>$supportID,'produitID'=>$produitID));
	}
	
	public function supprimsupportproduit($ordreID, $produitID){
		global $conn;
		$req=$conn->prepare('DELETE FROM supportsproduit WHERE ordre_mandataireID=:ordre_mandataireID AND produitID=:produitID');
		$req->execute(array(':ordre_mandataireID'=>$ordreID,'produitID'=>$produitID));
	}
	
	// Fonction de modification d'un ordre de facturation
	public function modifordre($societe, $client, $remarques, $archive, $identifiant, $delaiID=0){
		global $conn;
		$req=$conn->prepare('UPDATE ordres SET date="'.date("Y-m-d").'", societe=:societe, client=:client, remarques=:remarques, archive=:archive, delaiID=:delaiID WHERE identifiant=:identifiant');
		$req->execute(array(':societe'=>$societe, ':client'=>$client, ':remarques'=>$remarques, ':archive'=>$archive, ':identifiant'=>$identifiant, ':delaiID'=>$delaiID));
	}
	
	
	// Fonction de modif d'un ordre de facturation spécifique mandataire
	public function modifordremandataire($ordreID, $produitID, $supports, $groupeID, $date_deb, $date_fin, $facture, $nbspots, $net, $netnet, $honoraires, $nbspots_infosupp, $info_supp, $purchase_order, $description, $identifiant){
		global $conn;
		$req=$conn->prepare('UPDATE ordres_mandataire SET ordreID=:ordreID, groupeID=:groupeID, date_deb=STR_TO_DATE(:date_deb,"%d/%m/%Y"), date_fin=STR_TO_DATE(:date_fin,"%d/%m/%Y"), facture=:facture, nbspots=:nbspots, montant_net=:montant_net, montant_netnet=:montant_netnet, honoraires=:honoraires, nbspots_infosupp=:nbspots_infosupp, info_supp=:info_supp, purchase_order=:purchase_order, description=:description WHERE identifiant=:identifiant');
		$req->execute(array(':ordreID'=>$ordreID,':groupeID'=>$groupeID,':date_deb'=>$date_deb,':date_fin'=>$date_fin, ':facture'=>$facture,':nbspots'=>$nbspots,':montant_net'=>$net,':montant_netnet'=>$netnet,':honoraires'=>$honoraires, ':nbspots_infosupp'=>$nbspots_infosupp,  ':info_supp'=>$info_supp, ':purchase_order'=>$purchase_order, ':description'=>$description, ':identifiant'=>$identifiant));
		$this->supprimsupportproduit($identifiant, $produitID);
		if(!empty($supports)){
			foreach($supports as $supportID){
				$this->ajoutsupportproduit($identifiant, $produitID, $supportID);
			}
		}
	}
	
	
	
	
	// Fonction d'ajout d'une ligne dans un ordre de facturation
	public function ajoutligne($produit, $cplt, $montant, $frais, $ordre, $nomenc=""){
		global $conn;
		$req=$conn->prepare('INSERT INTO lignesordre (produit, cplt, montant, frais, ordre, nomenc) VALUES(:produit, :cplt, :montant, :frais, :ordre, :nomenc)');
		$req->execute(array(':produit'=>$produit, ':cplt'=>$cplt, ':montant'=>$montant, ':frais'=>$frais, ':ordre'=>$ordre, ':nomenc'=>$nomenc));
	}
	
	// Fonction de suppression d'une ligne dans un ordre de facturation :
	public function suppressionligne($id, $ordre=""){
		global $conn;
		$sql='DELETE FROM lignesordre ';
		if($id!="") $sql.='WHERE identifiant=:identifiant';
		else $sql.='WHERE ordre=:ordre';
		$req=$conn->prepare($sql);
		if($id!="") $req->execute(array(':identifiant'=>$id));
		else $req->execute(array(':ordre'=>$ordre));
	}	
	
	// Fonction de suppression d'un utilisateur :
	public function supprimutilisateur($id){
		global $conn;
		$req=$conn->prepare("DELETE FROM utilisateurs WHERE identifiant=:identifiant");
		$req->execute(array(":identifiant"=>$id));
		$this->supprimoptionsutilisateur($id);
	}
		
	public function supprimoptionsutilisateur($id){
		global $conn;
		$req2=$conn->prepare("DELETE FROM link_droits_utilisateurs WHERE utilisateur=:identifiant");
		$req2->execute(array(":identifiant"=>$id));	
		$req3=$conn->prepare("DELETE FROM link_societes_utilisateurs WHERE utilisateur=:identifiant");
		$req3->execute(array(":identifiant"=>$id));
	}
	
	// Fonction pour ajouter un utilisateur (et l'associer à ses droits et à ses societes...)
	public function ajoututilisateur($nom, $login, $mdp, $email, $arr_societes, $arr_droits){
		global $conn;
		$req=$conn->prepare("INSERT INTO utilisateurs (nom, login, motdepasse, email) VALUES (:nom, :login, :motdepasse, :email)");
		$req->execute(array(":nom"=>$nom, ":login"=>$login, ":motdepasse"=>$mdp, ":email"=>$email));
		$utilisateur=$conn->lastInsertId();
		$this->ajoutoptionsutilisateur($utilisateur, $arr_societes, $arr_droits);		
	}
	
	public function ajoutoptionsutilisateur($utilisateur, $arr_societes, $arr_droits){
		global $conn;
		$req2=$conn->prepare("INSERT INTO link_droits_utilisateurs(utilisateur, droit) VALUES(:utilisateur, :droit)");
		foreach($arr_droits as $droit){
			$req2->execute(array(":utilisateur"=>$utilisateur, ":droit"=>$droit));
		}
		$req3=$conn->prepare("INSERT INTO link_societes_utilisateurs(societe, utilisateur) VALUES(:societe, :utilisateur)");
		foreach($arr_societes as $societe){
			$req3->execute(array(":societe"=>$societe, ":utilisateur"=>$utilisateur));
		}
		if($utilisateur==$_SESSION["identification"]){
			$_SESSION["droits"]=$arr_droits;
			$_SESSION["societes"]=$arr_societes;
		} 
	
	}
	// Fonction pour modifier un  utilisateur (ses droits et ses sociétés également)
	public function modifutilisateur($identifiant, $nom, $login, $mdp, $email, $arr_societes, $arr_droits){
		global $conn;
		$req=$conn->prepare("UPDATE utilisateurs SET nom=:nom, login=:login, motdepasse=:motdepasse, email=:email WHERE identifiant=:identifiant");
		$req->execute(array(":nom"=>$nom, ":login"=>$login, ":motdepasse"=>$mdp, ":email"=>$email, ":identifiant"=>$identifiant));
		$this->supprimoptionsutilisateur($identifiant);
		$this->ajoutoptionsutilisateur($identifiant, $arr_societes, $arr_droits);	
	}
	
	
	// fonction d'ajout d'un produit
	public function ajoutproduit($libelle="", $client=""){
		if($libelle!=""){
			global $conn;
			$req = $conn->prepare('INSERT INTO produits(libelle, clientID) VALUES(:libelle, :client)');
			$req->execute(array(":libelle"=>$libelle,":client"=>$client));
		}
	}
	
	// fonction d'ajout d'un support
	public function ajoutsupport($libelle="", $groupe=""){
		if($libelle!=""){
			global $conn;
			$req = $conn->prepare('INSERT INTO supports(libelle, groupeID) VALUES(:libelle, :groupe)');
			$req->execute(array(":libelle"=>$libelle,":groupe"=>$groupe));
		}
	}
}
?>