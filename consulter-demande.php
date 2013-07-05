<?php include("inc_top.php"); 
if(isset($_GET["id"])){
	if(isset($_GET["ar"])) $row= $afficher->ordres("2", "", "AND ordres.identifiant=".$_GET["id"], "array");
	else $row= $afficher->ordres("1", "", "AND ordres.identifiant=".$_GET["id"], "array");
}
?>
<div id="contenu">
<script language="javascript" src="js/demande.js"></script>
<link href="css/ajout.client.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	.suggestionsBox {
		position: absolute;
		width: 200px;
		background-color:#e0ceec;
		border: 1px solid #7f9db9;	
		color: #3a234a;
		margin-left:40px;
	}
	.suggestionList {
		margin: 0px;
		padding: 0px;
	}
	.suggestionList li {	
		margin: 0px 0px 3px 0px;
		padding: 3px;
		cursor: pointer;
	}
	.suggestionList li:hover {
		background-color: #9662b9;
		color:#FFF;
	}
</style>
		<form class="demande" name='factu' action="traitements/archive.php" method="post">
		<h2>Consulter un ordre de facturation</h2>
		<div style="padding:5px; padding-top:8px; border:1px dotted #9966CC; margin-bottom:15px;">
		<p><label>Ordre de facturation de :</label><b>
		<?php echo $row["societe"]; ?>
		</b></p>
		<p>
		  <label>Client : </label><br/><b>
		  <?php echo $row["client"];?>
		 <br/>
         <?php echo nl2br($row["adresse"]); ?>
        </b> </p>
		  <div class="suggestionsBox" id="suggestions" style="display: none;">
				<div class="suggestionList" id="autoSuggestionsList" style="z-index:1000">
					&nbsp;
				</div>
		  </div>
		  <?php 
		if($row["remarques"]!=""){
			echo "Remarques &eacute;ventuelles :<br/><i><b>".$row["remarques"]."</b></i>"; 
		}elseif($row["tax_id"]!=""){
			echo "Remarques &eacute;ventuelles :<br/><i><b style='color:red'>Attention : ne pas appliquer de TVA sur cette facture (TAX ID disponible ci-dessous)</b></i>"; 
		}
		  ?>
		</div>
	    <table border="0" align="center" cellpadding="5" cellspacing="0" id="liste" class="stripeMe sample Style2">
		<thead>

		<tr class="alt">
			<th align="left"><b>Produit factur&eacute;</b></th>
			<th align="left">
            <?php if((isset($row))&&($row["ordre_mandataire"]==0)){ ?>
            <b>Cplt d'information  <br />
			(facult.)</b>
            <?php } ?>
            </th>
            <th align="right"><b>Montant</b></th>
          <th align="right"><b>Frais<br />
              fourniss.</b></th>
            <th align="right"><b>Marge</b></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(isset($row)){
		if($row["ordre_mandataire"]==0){
			$tab=$afficher->lignesordre($_GET["id"]);
			$i=0;
			$montanttotal=0;
			$margetotale=0;
			foreach($tab as $t){
				$i++;
				print '
				<tr id="'.$i.'">
				<td height="50" align="left" valign="top" class="moins" style="padding-left:10px">'.stripslashes($t["produit"]).'</td>
				<td align="left" valign="bottom" class="moins">'.stripslashes($t["cplt"]);
				if (($t["cplt"]!="") && ($t["nomenc"]!="")) print '<br/>';
				if($t["nomenc"]!="") print '<b>Nomenclature&nbsp;:</b><br/>'.$t["nomenc"];
				print '</td>
				<td align="center" valign="bottom" class="moins">'.$t["montant"].'</td>
				<td align="center" valign="bottom" class="moins">'.$t["frais"].'</td>
				<td align="center" valign="bottom" class="moins">'.($t["montant"]-$t["frais"]).'</td>
			  </tr>
				';
				$montanttotal+=$t["montant"];
				$margetotale+=($t["montant"]-$t["frais"]);
				 
			}
		// ORDRE SPECIFIQUE MANDATAIRE
		}else{
		 	print '<tr><td colspan="2">Concerne votre achat d\'espace via mandataire Bipmod</td><td></td><td></td><td></td></tr>';
			$tab=$afficher->ordres_mandataire($_GET["id"], "array");
			$montanttotal=0;
			$margetotale=0;
			foreach ($tab as $t){
				print '<tr><td colspan="2">';
				if($t["libelle"]!=""){
					print 'Produit : '.$t["libelle"];
				}else{
					print 'Purchase Order : '.$t["purchase_order"];
					if($t["description"]!=""){
						print "<br/>Description : ".nl2br($t["description"]);
					}
				}
				print '<br/>
				Campagne du '.$t["date_deb"].' au '.$t["date_fin"].'<br/>
				Nombre de spots : '.$t['nbspots'].' ';
				if($t['nbspots_infosupp']!="Précisions éventuelles..."){
					print $t['nbspots_infosupp'].'<br/>';
				}else print '<br/>';
				
				$tab2= $afficher->supports_demande($t["identifiant"], $t["produitID"],  "libelle");
				$liste="";
				for($i=0; $i<count($tab2); $i++){
					$liste.=$tab2[$i].' + ';
				}
				$liste = substr($liste, 0, -3);
				print 'Supports : '.$liste.'<br/>
				Selon facture Régie que vous avez reçue directement :<br/>
				Réf '.$t["regie"].' n°'.$t["facture"].'</td><td></td><td></td><td></td></tr>';
				if($t["info_supp"]!=""){
					print '<tr><td colspan="2">'.$t["info_supp"].'</td><td></td><td></td><td></td></tr>';
				}
				print '<tr><td>Honoraires de mandat : '.$t["honoraires"].'% du montant net</td><td><b>Nomenc. : </b>MAN-HONO</td></td><td style="text-align:right">'.str_replace(" ","&nbsp;",number_format(round((($t['montant_netnet']+($t["nbspots"]*$t["frais_antenne"]))*($t['honoraires']/100)),2), 2, '.', ' ')).'</td><td style="text-align:right">0.00</td><td style="text-align:right">'.str_replace(" ","&nbsp;",number_format(round((($t['montant_netnet']+($t["nbspots"]*$t["frais_antenne"]))*($t['honoraires']/100)),2), 2, '.', ' ')).'</td></tr>';
				$remise = round(($t["montant_net"]-$t["montant_netnet"]),2);
				if($remise!=0){
					print '<tr><td>Remise de mandat : Montant Net - Montant Net Net</td><td><b>Nomenc. : </b>MAN-RM</td><td style="text-align:right">'.str_replace(" ","&nbsp;",number_format($remise, 2, '.', ' ')).'</td><td style="text-align:right">0.00</td><td style="text-align:right">'.str_replace(" ","&nbsp;",number_format($remise, 2, '.', ' ')).'</td></tr>';
				}
				$montanttotal+=round((($t['montant_netnet']+($t["nbspots"]*$t["frais_antenne"]))*($t['honoraires']/100)),2)+round(($t["montant_net"]-$t["montant_netnet"]),2);
				
			}
			$margetotale=$montanttotal;
			
		}
	}
	if($row["tax_id"]!=""){
		print '<tr><td colspan="2">TAX ID '.$row["client"].': '.$row["tax_id"]."</td><td></td><td></td><td></td></tr>";	
	}
	?>
		  </tbody>
		  <tr id="derligne">
            <td colspan="2" align="right"><b>
			  <input type="hidden" name="identifiant" value="<?php if(isset($_GET["id"])) echo $_GET["id"]; else echo '0';  ?>"/>
			 
              Total &agrave; facturer : </b>
			  <?php echo number_format($montanttotal, 2, '.', ' '); ?> &euro; HT </td>
            <td colspan="3" align="right"><b>Total marge : </b>
			<?php echo number_format($margetotale, 2, '.', ' '); ?> &euro; HT </td>
          </tr>
        </table>
		<p>&nbsp;</p>
		<p>
		<?php if((in_array("2", $_SESSION["droits"]))&&(!isset($_GET["ar"]))){ 
		?>
		<label>Indiquez ici le n° de facture correspondant : </label><input type="text" name="facture" size="12" />
		<input type="submit" value="Archiver cette demande" />
		<?php } ?>
		
		<?php
		if(isset($_GET["ar"])){
		?>
		<label>N° de facture correspondant :</label><input type="text" name="facture_m" size="12" value="<?php echo $row["facture"]; ?>" />
		<input type="submit" value="Modifier" />
		<?php
		}
		?>
		</p>
		</form>
		<?php if($row["ordre_mandataire"]!=0){ ?>
        <p>
        <table class='sample' style="width:100%" id="recap"><thead><tr>
          <th>R&Eacute;CAPITULATIF :</th></tr></thead>
          <tbody>
          <?php $bid = $afficher->recap_facture($_GET["id"]); ?>
          <tr>
            <td><p>&Agrave; l'attention de : <span style="font-weight:bold; color:black" id="bidclient"> <?php echo $bid["client"]; ?></span> <br />
              Vous devez nous r&eacute;gler <span id="delai" style="text-decoration:underline;"><?php echo $bid["delai"]; ?></span>, la somme de :
              <span style="font-weight:bold; color:black"><span id="montant_total_facture"><?php echo number_format($bid["somme_totale"], 2, '.', ' '); ?></span> &euro; TTC</span><br />
              Correspondant aux factures r&eacute;gie :<br />
              <?php
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
			echo substr($txt_groupe,0,-2);
			echo " : <span style='font-weight:bold; color:black'>".number_format($bid["somme1"], 2, '.', ' ')." &euro; TTC</span><br />";
			?>
             
              
              + la facture Bipmod <?php if($bid["facture_bipmod"]!="") echo "n&deg;".$bid["facture_bipmod"]; else echo"<i>en cours de création</i>"; ?> de : <span style="font-weight:bold; color:black"><?php echo number_format($bid["somme2"], 2, '.', ' '); ?> &euro; TTC</span></p>
             
              <?php
              if($row["tax_id"]!="") print '<p>TAX ID '.$row["client"].': <b style="color:black">'.$row["tax_id"].'</b></p>';
		?>
            
            <p>En votre aimable r&egrave;glement. </p></td></tr></tbody>
        </table>
        </p>
       <?php } ?> 
		
		
	 </div>
	<?php include("inc_footer.php"); ?>
