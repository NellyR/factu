<?php
//classe de connexion et d'identification
class affichage{

	private $dsn = "mysql:dbname=;host=localhost";	//Infos base
	private $user = "";	//login de connexion base
	private $password = "";	//mot de passe connexion base
	public $conn;

	//Le constructeur me connecte 
	function __construct(){
		global $conn;
		$conn = new connexion($this->dsn, $this->user, $this->password);
	}
	
	//fonction d'affichage de données dans un menu déroulant
	public function affichage_menuderoulant($option, $value, $selected=""){
		echo "<option value='".$value."'";
		if(is_array($selected)){
			if(in_array($value, $selected)) echo " selected='selected'"; 
		}else{
			if ( $selected == $value ) echo " selected='selected'";
		}
		echo " style='font-size:10px;'>".$option."</option>";
	}
	
	//fonction d'affichage de données dans un tableau <table>
	public function affichage_tableau($liste_values){
		echo "<tr>";
		foreach($liste_values as $value){
			echo "<td>".$value."</td>";
		}
		echo "</tr>";
	}
	
	// Fonction d'affichage des données dans une liste avec checkbox
	public function affichage_checkbox($nomliste, $option, $value, $selected=""){
		echo "<input type='checkbox' style='width:auto; margin:2px' name='".$nomliste."[]' value='".$value."'> ".$option."<br/>";
		//if (($selected!="")&&(is_array($selected))&&(in_array($value, $selected)) echo " checked";
	}
	
	// fonction d'affichage des societes (avec $selected, on peut indiquer une présélection dans le menu déroulant ou dans la liste)
	// $selected peut être un array...
	public function societes($selected="", $format="menuderoulant", $where=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle, nomenclature FROM societes '.$where);
		$req->execute();
		if($format=="array"){
			$row=$req->fetch();
			return $row;
		}else{
			while ($row = $req->fetch()) {
				if($format=="checkbox") $this->affichage_checkbox("societes", $row["libelle"],$row["identifiant"],$selected);
				elseif ($format=="champ_cache") echo "<input type='hidden' name='societe' value='".$row["identifiant"]."'/>".$row["libelle"]; 
				else $this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
				
			}
		}
	}
	// fonction d'affichage de la nomenclature :
	public function nomenclature($selected="", $format="menuderoulant", $where=""){
		global $conn;
		$req = $conn->prepare('SELECT nomenclatures.identifiant, nomenclatures.libelle, nomenclatures_cat.libelle AS categorie FROM nomenclatures LEFT JOIN nomenclatures_cat ON nomenclatures_cat.identifiant=nomenclatures.categorie '.$where.' ORDER BY categorie ASC, libelle ASC');
		$req->execute();
		$enr="";
		if($format=="menuderoulant"){
			while ($row = $req->fetch()) {
				if($enr!=$row["categorie"]){
					if($enr!="") echo "</optgroup>";
					echo "<optgroup label='".$row["categorie"]."'>";
				}
				echo "<option style='font-size:10px;' value='".$row["identifiant"]."'";
				if ( $selected == $row["identifiant"] ) echo " selected='selected'";
				echo ">".$row["libelle"]."</option>";
				$enr=$row["categorie"];
			}
			echo "</optgroup>";
		}else{
			$t="";
			while ($row = $req->fetch()) {
				if($enr!=$row["categorie"]){
					if($enr!="") $t.="</optgroup>";
					$t.="<optgroup label=_".$row["categorie"]."_>";
				}
				$t.="<option style=_font-size:10px;_ value=_".$row["identifiant"]."_";
				if ( $selected == $row["identifiant"] ) $t.=" selected=_selected_";
				$t.=">".$row["libelle"]."</option>";
				$enr=$row["categorie"];
			}
			$t.="</optgroup>";
			return $t;
			
		}
	}
	
	// fonction d'affichage des societes (avec $selected, on peut indiquer une présélection dans le menu déroulant ou dans la liste)
	// $selected peut être un array...
	public function droits($selected="", $format="checkbox"){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle FROM droits');
		$req->execute();
		while ($row = $req->fetch()) {
			if($format=="checkbox") $this->affichage_checkbox("droits", $row["libelle"],$row["identifiant"],$selected);
			else $this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
			
		}
	}
	
	
	// fonction d'affichage des clients 
	// $selected : présélection facultative
	public function clients($selected="", $format="menuderoulant", $limitation="", $where=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle FROM clients  WHERE archive=0 '.$where.' ORDER BY libelle '.$limitation);
		$req->execute();
		while ($row = $req->fetch()) {
			if($format=="menuderoulant") $this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
			elseif($format=="texte"){
				if($row["identifiant"]==$selected) echo "<b>".$row["libelle"]."</b>";
			}
			elseif($format=="autocompletion") echo '<li onClick="fill(\''.$row["libelle"].'\');">'.$row["libelle"].'</li>';
			else $this->affichage_tableau(array($row["libelle"],"<a href='#' title='".$row["identifiant"]."' class='modifier'>modifier</a>", "<a href='#' id='".$row["identifiant"]."' class='suppr'>supprimer</a>"));
		}
	}
	
	// fonction d'affichage des délais de réglement
	// $selected : présélection facultative
	public function delai_reglement($selected=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle FROM delai_reglement ORDER BY libelle ');
		$req->execute();
		while ($row = $req->fetch()) {
			$this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
		}
	}
	
	// fonction d'affichage des groupes de régie
	// $selected : présélection facultative
	public function groupe_regies($selected=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle, frais_antenne FROM groupes_regie ORDER BY libelle');
		$req->execute();
		while ($row = $req->fetch()) {
			$this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
		}
	}
	
	// fonction d'affichage des frais d'antenne
	// $selected : présélection facultative
	public function frais_antenne($identifiant){
		global $conn;
		$req = $conn->prepare('SELECT frais_antenne FROM groupes_regie WHERE identifiant='.$identifiant);
		$req->execute();
		$row = $req->fetch();
		echo $row["frais_antenne"];
	}
	
	// fonction d'affichage des produits :
	// $selected : présélection facultative
	public function produits($client, $selected=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle FROM produits WHERE clientID='.$client.' ORDER BY libelle');
		$req->execute();
		$this->affichage_menuderoulant("Sélectionner un produit","","");
		$this->affichage_menuderoulant("Pas de produit : PURCHASE ORDER","0","");
		while ($row = $req->fetch()) {
			$this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
		}
	}
	
	// fonction d'affichage des supports :
	// $selected : présélection facultative
	public function supports($groupe, $selected=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, libelle FROM supports WHERE groupeID='.$groupe.' ORDER BY libelle');
		$req->execute();
		while ($row = $req->fetch()) {
			$this->affichage_menuderoulant($row["libelle"],$row["identifiant"],$selected);
		}
	}
	
	// fonction de récupération des des supports pour un produit d'une demande donnée.
	// $selected : présélection facultative
	// $recup : données a récupérer (identifiant ou libellé)
	public function supports_demande($ordre, $prod, $recup="identifiant"){
		global $conn;
		if($recup=="identifiant"){
			$req = $conn->prepare('SELECT supportID as support FROM supportsproduit LEFT JOIN ordres_mandataire ON ordres_mandataire.identifiant=ordre_mandataireID WHERE ordre_mandataireID='.$prod.' AND ordreID='.$ordre);
		}else{
			$req = $conn->prepare('SELECT supports.libelle as support FROM supportsproduit LEFT JOIN supports ON supports.identifiant=supportsproduit.supportID WHERE ordre_mandataireID='.$ordre.' AND produitID='.$prod);
		}
		$req->execute();
		$retour=array();
		while ($row = $req->fetch()) {
			array_push($retour, $row["support"]);
		}
		return $retour;
	}
	
	
	// fonction d'affichage des utilisateurs
	// $selected : présélection facultative
	public function utilisateurs($selected="", $format="tableau", $limitation="", $where=""){
		global $conn;
		$req = $conn->prepare('SELECT identifiant, nom FROM utilisateurs '.$where.' ORDER BY nom '.$limitation);
		$req->execute();
		while ($row = $req->fetch()) {
			if($format=="menuderoulant") $this->affichage_menuderoulant($row["nom"],$row["identifiant"],$selected);
			elseif($format=="autocompletion") echo '<li onClick="fill(\''.$row["nom"].'\');">'.$row["nom"].'</li>';
			else $this->affichage_tableau(array($row["nom"],"<a href='#' title='".$row["identifiant"]."' class='modifier'>modifier</a>", "<a href='#' id='".$row["identifiant"]."' class='suppr'>supprimer</a>"));
		}
	}
	
	// fonction d'affichage des demandes
	// $selected : présélection facultative
	public function ordres($archive='0', $limitation="", $where="", $format=""){
		global $conn;
		$req = $conn->prepare('SELECT ordres.identifiant, date_format(date, "%d/%m/%Y") as date, ordres.archive, date_format(date_archive, "%d/%m/%Y") as date_archive, societes.libelle as societe, ordres.societe AS idsociete, ordres.client AS idclient, clients.libelle as client, clients.adresse, clients.tax_id, remarques, facture, ordre_mandataire, ordres.delaiID FROM ordres LEFT JOIN societes ON societes.identifiant=ordres.societe LEFT JOIN clients ON clients.identifiant=ordres.client  WHERE ordres.archive='.$archive.' '.$where.' ORDER BY facture DESC '.$limitation);
		$req->execute();
		if($format=="array"){
			$row=$req->fetch();
			
			/*if($row["ordre_mandataire"]==1){
				$req = $conn->prepare('SELECT identifiant as ordres_mandataireID, produitID, groupeID, date_deb, date_fin, delaiID, facture as factureRegie, nbspots, montant_net, montant_netnet, honoraires FROM ordres_mandataire WHERE ordreID='.$row["identifiant"]);
				$req->execute();
				
			}*/
			return $row;
		}else{
			while ($row = $req->fetch()) {
				if($archive==1) $montab=array($row["identifiant"], $row["date"],$row["client"], $row["societe"], "<a href='consulter-demande.php?id=".$row["identifiant"]."' title='".$row["identifiant"]."' class='voir'>consulter</a>");
				elseif($archive==2) $montab=array($row["date"],$row["client"], $row["societe"], $row["date_archive"], $row["facture"], "<a href='consulter-demande.php?id=".$row["identifiant"]."&ar=1' title='".$row["identifiant"]."' class='voir'>consulter</a>");
				else{
					if($row["ordre_mandataire"]==1) $lien="demande-mandataire.php";
					else $lien="demande.php";
					$montab=array($row["date"],$row["client"], $row["societe"], "<a href='".$lien."?id=".$row["identifiant"]."' class='modifier'>modifier</a>", "<a href='#' id='".$row["identifiant"]."' class='suppr'>supprimer</a>");
				}
				$this->affichage_tableau($montab);
			}
		}
	}
	// Fonction d'affhichage des infos produits :
	public function produits_mandat($ordre_mandat, $format="array"){
		global $conn;
		$req = $conn->prepare('SELECT produits.libelle, produitID, groupeID, date_format(date_deb, "%d/%m/%Y") as date_deb, date_format(date_fin, "%d/%m/%Y") as date_fin, delaiID, facture, nbspots, montant_net, montant_netnet, honoraires, frais_antenne, nbspots_infosupp, purchase_order, description, info_supp FROM ordres_mandataire LEFT JOIN produits ON produitID=produits.identifiant LEFT JOIN groupes_regie ON groupes_regie.identifiant=groupeID WHERE ordres_mandataire.identifiant='.$ordre_mandat);
		$req->execute();
		if($format=="array"){
			$row=$req->fetch();
			return $row;
		}
	}
	
	
	// Fonction d'affichage de la liste des produits ordre mandataire
	public function ordres_mandataire($ordreID, $format=""){
		global $conn;
		$req = $conn->prepare('SELECT ordres_mandataire.identifiant, produitID, produits.libelle, groupeID, groupes_regie.libelle as regie, date_format(date_deb, "%d/%m/%Y") as date_deb, date_format(date_fin, "%d/%m/%Y") as date_fin, delaiID, facture, nbspots, montant_net, montant_netnet, honoraires, nbspots_infosupp, frais_antenne, info_supp, purchase_order, description FROM ordres_mandataire LEFT JOIN produits ON produitID= produits.identifiant LEFT JOIN groupes_regie ON groupes_regie.identifiant=groupeID WHERE ordreID='.$ordreID);
		$req->execute();
		if($format=="array"){
			$row=$req->fetchAll();
			return $row;
		}else{
			while($row2=$req->fetch()){
				if(($row2["libelle"]=="")&&($row2["purchase_order"]!="")){
					$liste_tableau=array("PURCHASE ORDER : ".$row2["purchase_order"],"<a href='demande-mandataire.php?id=".$ordreID."&prod=".$row2["identifiant"]."'>modifier</a>","<a href='#' id='".$row2["identifiant"]."' name='".$ordreID."' class='suppr'>supprimer</a>");					
				}else{
					$liste_tableau=array($row2["libelle"],"<a href='demande-mandataire.php?id=".$ordreID."&prod=".$row2["identifiant"]."'>modifier</a>","<a href='#' id='".$row2["identifiant"]."' name='".$ordreID."' class='suppr'>supprimer</a>");
				}
				$this->affichage_tableau($liste_tableau);		
			}
		}
	}
	
	
	// Fonction d'affichage des différentes lignes de la demande :
	public function lignesordre($ordre){
		global $conn;
		$req = $conn->prepare('SELECT produit, cplt, montant, frais, nomenc FROM lignesordre WHERE ordre='.$ordre.' ORDER BY identifiant');
		$req->execute();
		$bid=array();
		while($row=$req->fetch()){
			array_push($bid, $row);	
		}
		return $bid;
	}
	
	// Récap facture mandataire
	public function recap_facture($ordre){
		global $conn;	
		$req = $conn->prepare("SELECT ROUND(((montant_netnet + nbspots * groupes_regie.frais_antenne) * 1.196) +
((honoraires/100 * (montant_netnet+nbspots*groupes_regie.frais_antenne) + (montant_net-montant_netnet) )*1.196),2) AS somme_totale, ROUND(((montant_netnet + nbspots * groupes_regie.frais_antenne) * 1.196),2) AS somme1, ROUND(((honoraires/100 * (montant_netnet+nbspots*groupes_regie.frais_antenne) + (montant_net-montant_netnet) )*1.196),2) AS somme2, clients.libelle AS client, delai_reglement.libelle AS delai, ordres_mandataire.facture, ordres.facture AS facture_bipmod, groupes_regie.libelle AS groupe, tax_id FROM ordres_mandataire LEFT JOIN groupes_regie ON groupes_regie.identifiant = groupeID LEFT JOIN ordres ON ordreID=ordres.identifiant LEFT JOIN clients ON clients.identifiant=client LEFT JOIN delai_reglement ON delai_reglement.identifiant=ordres.delaiID WHERE ordreID=".$ordre." ORDER BY groupeID ASC");
		$req->execute();
		$somme_tot=0;
		$somme1=0;
		$somme2=0;
		$liste_factures=array();
		$groupe=array();
		$dergroupe="";
		while($row=$req->fetch()){
			$client = $row["client"];
			$delai = $row["delai"];
			$facture_bipmod = $row["facture_bipmod"];
			
			$somme_tot+=$row["somme_totale"];
			$somme1+=$row["somme1"];
			$somme2+=$row["somme2"];

			if($dergroupe==$row["groupe"]){
				$liste_factures[$row["groupe"]][]=$row["facture"];
			}else{
				$groupe[]=$row["groupe"];
				$liste_factures[$row["groupe"]][]=$row["facture"];
			}
			$dergroupe = $row["groupe"];
			$tax=$row["tax_id"];
		}
		if($tax!=""){
			$somme_tot=round($somme_tot/1.196,2);
			$somme1=round($somme1/1.196,2);
			$somme2=round($somme2/1.196,2);
		}
		$bid=array("client"=>$client, "delai"=>$delai, "somme_totale"=>$somme_tot, "somme1"=>$somme1, "somme2"=>$somme2, "facture_bipmod"=>$facture_bipmod, "groupes"=>$groupe, "liste_factures"=>$liste_factures);
		return $bid;
		
	}
}
?>