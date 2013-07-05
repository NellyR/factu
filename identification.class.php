<?php
class identification{

	private $selectNom;
	private $selectIdentifiant;

	function __construct(){
		global $conn;
		$this->selectNom = $conn->prepare('SELECT identifiant, nom FROM utilisateurs WHERE identifiant= ?');
		$this->selectIdentifiant = $conn->prepare('SELECT identifiant FROM utilisateurs WHERE login=:login AND motdepasse=:motdepasse');
		$this->selectSocietes = $conn->prepare('SELECT societe FROM link_societes_utilisateurs WHERE utilisateur= ?');
		$this->selectDroits =  $conn->prepare('SELECT droit FROM link_droits_utilisateurs WHERE utilisateur= ?');
	}
	
	function verif_identification(){
		if(!isset($_SESSION["identification"])) header('Location: index.php');
		else $this->afficher_identite();
	}
	
	function afficher_identite(){
		$req=$this->selectNom->execute(array($_SESSION["identification"]));
		$req=$this->selectNom->fetch();
		echo "Connect&eacute; : ".$req["nom"];
	}
	function rechercher_identite($login, $motdepasse){
		$this->selectIdentifiant->execute(array(':login' => $login, ':motdepasse' => $motdepasse));
		$req=$this->selectIdentifiant->fetch();
		$arr_droit=array();
		$arr_societe=array();
		if(isset($req["identifiant"])){
			$_SESSION["identification"]=$req["identifiant"];
			$this->selectSocietes->execute(array($_SESSION["identification"]));
			$this->selectDroits->execute(array($_SESSION["identification"]));
			$arr_droits = $this->selectDroits->fetchAll();
			$arr_societes = $this->selectSocietes->fetchAll();
			foreach($arr_droits as $droits) $arr_droit[]=$droits["droit"];
			foreach($arr_societes as $societes) $arr_societe[]=$societes["societe"];
			$_SESSION["droits"]=$arr_droit;
			$_SESSION["societes"]=$arr_societe;
			header("Location:liste-demandes.php");
		}
		
	}
}
?>