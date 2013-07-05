<?php
	session_start();
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include("../connexion.class.php");
	include("../affichage.class.php");
	include("traitements.class.php");
	include("../identification.class.php");
	$traitements= new traitements();
	$identification= new identification();
	
	// on va chercher les nouvelles infos du récapitulatif
	$bid = $traitements->recap_facture($_GET["ordre"]);
	print '
	<tr>
            <td><p>&Agrave; l\'attention de : <span style="font-weight:bold; color:black" id="bidclient">'. $bid["client"].'</span> <br />
              Vous devez nous r&eacute;gler <span id="delai" style="text-decoration:underline;">'.$bid["delai"].'</span>, la somme de :
              <span style="font-weight:bold; color:black"><span id="montant_total_facture">'.$bid["somme_totale"].'</span> &euro; TTC</span><br />
              Correspondant aux factures r&eacute;gie :<br />';
			  
              
	$txt_groupe="";
	foreach($bid["groupes"] as $groupe){
		$txt_groupe.=$groupe." n&deg;";
		$txt_facture ="";
		foreach($bid["liste_factures"][$groupe] as $facture){
			$txt_facture.=$facture." + n&deg;";
		}
		$txt_groupe.=substr($txt_facture, 0, -9); ;
		$txt_groupe.=", ";
	}
	print substr($txt_groupe,0,-2);
	print " : <span style='font-weight:bold; color:black'>".$bid["somme1"]." &euro; TTC</span><br />";
		         
    print '+ la facture Bipmod';
	if($bid["facture_bipmod"]!="") echo "n&deg;".$bid["facture_bipmod"]; 
	else echo"<i>en cours de création</i>"; 
		
	print 'de : <span style="font-weight:bold; color:black">'.$bid["somme2"].' &euro; TTC</span></p>
          
    <p>En votre aimable r&egrave;glement. </p></td></tr>';
	
	
?>