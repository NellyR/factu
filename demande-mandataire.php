<?php include("inc_top.php");
// Préremplissage du formulaire pour un ordre existant : 
if(isset($_GET["id"])){
	$row= $afficher->ordres("0", "", $where="AND ordres.identifiant=".$_GET["id"], "array");
	if(isset($_GET["prod"])){
		$row3 = $afficher->produits_mandat($_GET["prod"]);
	}else{
		$row3["montant_net"] = $row3["montant_netnet"] = $row3["honoraires"]=0;	
	}
}
?>
<div id="contenu">
<script language="javascript" src="js/demande-mandataire.js"></script>
<script language="javascript" src="js/date.js"></script>
<script language="javascript" src="js/jquery.datePicker.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="js/ui.multiselect.js"></script>
<link href="css/datePicker.css" rel="stylesheet" type="text/css" />
<link href="css/ajout.client.css" rel="stylesheet" type="text/css" />
<link href="http://quasipartikel.at/multiselect_original/css/smoothness/jquery-ui-1.7.1.custom.css" rel="stylesheet" type="text/css" />

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

	a.dp-choose-date {
	width: 16px;
	height: 16px;
	padding: 0;
	/*margin: 5px 3px 0;*/
	display:block;
	float:left;
	text-indent: -2000px;
	overflow: hidden;
	background: url(images/calendar.png) no-repeat; 
}
a.dp-choose-date.dp-disabled {
	background-position: 0 -20px;
	cursor: default;
}
.multiselect {
	width: 500px;
	height: 130px;
}
.ui-multiselect{ margin-bottom:5px}


</style>
		<form class="demande" name='factu' action="traitements/demande-mandataire.php" method="post">
        <input type="hidden" name="identifiant" value="<?php if(isset($_GET["id"])) echo $_GET["id"]; else echo '0';  ?>"/>
		<h2>Créer un nouvel ordre de facturation mandataire</h2>
		<div style="padding:5px; padding-top:8px; border:1px dotted #9966CC; margin-bottom:15px;">
        <p>
		<label>Concerne achat d'espace via <span id="mandatairepayeur">mandataire payeur</span> :</label>
		<?php
		
		if(count($_SESSION["societes"])==1){
			echo "<b>";
			$afficher->societes("","champ_cache", "WHERE identifiant=".$_SESSION["societes"][0]);
			echo "</b>";
		}elseif(count($_SESSION["societes"])>1){
			echo "<select name='societe'>";
			$where="WHERE ";
			foreach($_SESSION["societes"] as $societe) $where.="identifiant=".$societe." OR ";
			$where.=" identifiant=0";
			if(isset($row["idsociete"])) $soc=$row["idsociete"];
			else $soc="";
			$afficher->societes($soc,"menuderoulant", $where);
			echo "</select>";
		}else{
			if(isset($row["idsociete"])) $soc=$row["idsociete"];
			else $soc="";
			echo "<select name='societe'>";
			$afficher->societes($soc);
			echo "</select>";		
		}

		?>
		
        </p>
        <p id="pmandataire">
            <label> Mandataire :</label>
            <select name="" style="font-size:10px;" id="selectmandataire">
            	<option style="font-size:10px;" value="" selected>Sélectionner un mandataire</option>
            </select> <a href="#" id="ajmandataire">+</a> &nbsp;
            <input type="radio" name="payeur" value="1" checked="checked" /> payeur &nbsp;&nbsp;&nbsp;
            <input type="radio" name="payeur" value="0" /> non payeur
        </p>
        <p>
		  <label>Client * :</label>
          <?php if(isset($_GET["id"])){ ?>
          <input type="hidden" id="selectclient" name="client" value="<?php echo $row["idclient"]; ?>" />
          <?php 
		  $afficher->clients($row["idclient"],"texte");
		  }else{ ?>
          <select name="client" style="font-size:10px;" id="selectclient">
		  <option style="font-size:10px;" value="" selected>Sélectionner un client</option>
		  <?php $afficher->clients($row["idclient"]);?></select>
          
		  <!--<input type="text" size="30" name="client" value="" id="inputString" onkeyup="lookup(this.value);" onblur="fill();" autocomplete="off" />-->
		  <a href="#" id="ajouter">+</a>
<div class="suggestionsBox" id="suggestions" style="display: none;">
				<div class="suggestionList" id="autoSuggestionsList" style="z-index:1000">
					&nbsp;
				</div>
		  </div>
          <?php } ?>
		  
          </p>
          <p style="clear:both;">
            <label>Reglement : </label>
          <select name="delai"><?php $afficher->delai_reglement($row["delaiID"]);?></select></p>
          </div>
        <?php
		if(isset($_GET["id"])){
			
			echo "<table id='liste' class='stripeMe sample'><thead><tr><th>Liste des produits :</th><th></th><th></th></tr></thead><tbody>
";
			$row2=$afficher->ordres_mandataire($_GET["id"]);
			echo "<tr><td><i><a href='demande-mandataire.php?id=".$_GET["id"]."&aj=1'>Ajouter un produit</a></i></td><td></td><td></td></tr></tbody>
</table><br/><br/>";
		}		
		?>
        
       
       <?php if( (isset($_GET["aj"])) || (isset($_GET["prod"])) || (!isset($_GET["id"])) ){ ?>
       <?php if(isset($_GET["prod"])){ ?>
        <p><b>Modifier un produit :</b></p>
       <?php }else{ ?>
       <p><b>Ajouter un produit :</b></p>
       <? } ?>
          <div style="padding:5px; padding-top:4px; border:1px dotted #9966CC; margin-bottom:0px;" id="formu">
          <?php if(!isset($_GET["prod"])){ ?>
          <p><label>Produit : </label> 
            <select id="selectproduit" name="produit" style="font-size:10px;"><option value="" selected style="font-size:10px;">Sélectionner un produit</option></select> <a href="#" id="ajproduit">+</a></p>
            <span id="purchase">
            <p><b><label>Purchase order :</label></b> <input type="text" name="purchase_order" value="" /></p>
            <p><b><label>Description :</label> </b><br/><textarea name="description" style="width:300px"></textarea></p>
            </span>
		<?php }else{ ?>
        	<input type="hidden" name="ordre_mandataire" id="ordre_mandataire" value="<?php echo $_GET["prod"]; ?>" />
            <input type="hidden" name="produit" value="<?php echo $row3["produitID"]; ?>" />
            <?php if (($row3["libelle"]=="")&&($row3["purchase_order"]!="")){ ?>
            <p><b><label>Purchase order :</label></b> <input type="text" name="purchase_order" value="<?php echo $row3["purchase_order"]; ?>" /></p>
            <p><b><label>Description :</label> </b><br/><textarea name="description" style="width:300px"><?php echo $row3["description"]; ?></textarea></p>
			<?php }else{ ?>
            <p><b><label>Produit :</label> <?php echo $row3["libelle"]; ?></b></p>
            <?php }
			} ?>
         <p>
           <label>Groupe r&eacute;gies : </label><select style="font-size:10px;" name="groupe" id="selectgroupe"><option value="" selected style="font-size:10px;">Sélectionner un groupe régie</option><?php $afficher->groupe_regies($row3["groupeID"]);?></select> 
           <a href="#" id="ajouter_regie">+</a>
           
           
           </p>
		 <p>
             <label>Supports : <a href="#" id="ajsupport">+</a><br />
             </label>
          <select id="selectsupport" name="supports[]" class="multiselect" multiple="multiple">
          <?php
		  if(isset($_GET["prod"])){
			  $selection = $afficher->supports_demande($_GET["id"], $_GET["prod"]);
			  $afficher->supports($row3["groupeID"], $selection);
		  }
		  
		  ?>
          </select>
          </p>
          
         <p>
            <span style="float:left; padding-right:4px;"> Campagne du </span>
               <input name="date_deb" type="text" id="start-date" class="date-pick dp-applied" value="<?php echo $row3["date_deb"]; ?>" style="float:left" />
<span style="float:left; padding-left:4px; padding-right:4px;">au </span>
  <input name="date_fin" type="text" id="end-date" class="date-pick dp-applied" value="<?php echo $row3["date_fin"]; ?>" style="float:left">
           
         </p>
          
          
          <p style="padding-top:10px">
            <label>Facture r&eacute;gie R&eacute;f <span class="spangroupe"></span>&nbsp;N&deg; </label>
            <input type="text" name="facture" value="<?php echo $row3["facture"]; ?>" />
          </p>
		
	    <table border="0" align="center" cellpadding="5" cellspacing="0" id="liste" class="stripeMe sample Style2">
		<thead>

		<tr class="alt">
			<th colspan="3" align="center"><b>D&Eacute;TAILS FACTURE</b><b></b></th>
		  </tr>
	</thead>
	<tbody>

          <tr>
            <td colspan="3" valign="top" class="moins"><b><u>Facture <span class="spangroupe"></span>&nbsp;/ R&eacute;gie</u></b></td>
          </tr>
          <tr id="1">
            <td valign="top" class="moins">Nombre de spots<br/><input type="text" name="nbspots_infosupp" value="<?php if ($row3["nbspots_infosupp"]!="") echo $row3["nbspots_infosupp"]; else echo "Précisions éventuelles..."; ?>" style="width:200px; font-size:11px" /></td>
			<td align="right" valign="bottom" class="moins"><input name="spot" id="spot" type="text" size="5" style="text-align:right" value="<?php echo $row3["nbspots"]; ?>"/></td>
			<td align="left" valign="bottom" class="moins">spots</td>
          </tr>
          <tr>
            <td valign="top" class="moins">Informations compl&eacute;mentaires :<br /><textarea name="info_supp" style="width:250px; font-size:11px" ><?php if($row3["info_supp"]!="") echo $row3["info_supp"]; else echo "Infos complémentaires facultatives" ?></textarea></td>
            <td align="right" valign="bottom" class="moins">&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" class="moins">Montant Net <span class="spangroupe"></span>&nbsp;</td>
            <td align="right" valign="bottom" class="moins"><input name="net"  style="text-align:right" id="net" type="text" size="5" value="<?php echo $row3["montant_net"]; ?>"/>
              </td>
            <td align="left" valign="bottom" class="moins">&euro; HT</td>
          </tr>
          <tr>
            <td valign="top" class="moins">Montant Net Net <span class="spangroupe"></span>&nbsp;</td>
            <td align="right" valign="bottom" class="moins"><input name="netnet" id="netnet" type="text" size="5"  style="text-align:right" value="<?php echo $row3["montant_netnet"]; ?>"/></td>
            <td align="left" valign="bottom" class="moins">&euro; HT</td>
          </tr>
          <tr id="fraisvis">
            <td valign="top" class="moins">Frais d'antenne (<span id="fraisantenne"><?php echo $row3["frais_antenne"]; ?></span>&nbsp;&euro;/spot)</td>
            <td align="right" valign="bottom" class="moins"><span id="antenne"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; HT</td>
          </tr>
          <tr class="tva">
            <td valign="top" class="moins">TVA 19.6%</td>
            <td align="right" valign="bottom" class="moins"><span id="tvaregie"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; TVA</td>
          </tr>
          <tr class="tva">
            <td valign="top" class="moins">Total TTC (Facture <span class="spangroupe"></span>&nbsp;)</td>
            <td align="right" valign="bottom" class="moins"><span id="ttcregie"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; TTC</td>
          </tr>
          <tr>
            <td colspan="3" valign="top" class="moins">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" class="moins">Honoraires de mandat (
              <input name="honoraires" id="honoraires" type="text" value="<?php echo $row3["honoraires"]; ?>" size="4" maxlength="2"/>
            % du montant Net)</td>
            <td align="right" valign="bottom" class="moins"><span id="hono"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; HT</td>
          </tr>
          <tr>
            <td valign="top" class="moins">Remise de mandat</td>
            <td align="right" valign="bottom" class="moins"><span id="remise"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; HT</td>
          </tr>
          <tr>
            <td colspan="3" valign="top" class="moins">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" class="moins"><b><u>Facture Bipmod / Mandataire</u></b></td>
            <td colspan="2" align="center" valign="bottom" class="moins">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" class="moins">Montant Net Bipmod (HT)</td>
            <td align="right" valign="bottom" class="moins"><span id="netmandataire"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; HT</td>
          </tr>
          <tr class="tva">
            <td valign="top" class="moins">TVA 19.6%</td>
            <td align="right" valign="bottom" class="moins"><span id="tvamandataire"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; TVA</td>
          </tr>
          <tr class="tva">
            <td valign="top" class="moins">Total TTC (Facture Bipmod)</td>
            <td align="right" valign="bottom" class="moins"><span id="ttcmandataire"></span>&nbsp;</td>
            <td align="left" valign="bottom" class="moins">&euro; TTC</td>
          </tr>
		  <input type="hidden" name="lignes" value="1" />
		  
          
        </table><br/>
        </div>
        
        <input type="hidden" name="multi" value="" />
         <p style="margin-top:0px"><a href="#" style="display:block; width:100%; background-color:#382248; color:white; text-align: center; padding-top:5px; padding-bottom:5px; font-weight:bold;" id="validerproduit">
         ENREGISTRER CE PRODUIT
         </a></p>
         <br /> 
         <? } ?>
		<input type="hidden" name="archive" value="0" />
		 <input type="button" value="Enregistrer en Brouillon" id="brouillon" />  <input type="button" value="Enregistrer et envoyer" id="envoi" />
		 
		</p>
        
        <?php if ((isset($_GET["id"])) && (!isset($_GET["prod"])) && (!isset($_GET["aj"]))) { ?>
		<p><br/>
        <table class='sample' style="width:100%" id="recap"><thead><tr>
          <th>R&Eacute;CAPITULATIF :</th></tr></thead>
          <tbody>
          <?php $bid = $afficher->recap_facture($_GET["id"]); ?>
          <tr>
            <td><p>&Agrave; l'attention de : <span style="font-weight:bold; color:black" id="bidclient"> <?php echo $bid["client"]; ?></span> <br />
              Vous devez nous r&eacute;gler <span id="delai" style="text-decoration:underline;"><?php echo $bid["delai"]; ?></span>, la somme de :
              <span style="font-weight:bold; color:black"><span id="montant_total_facture"><?php echo $bid["somme_totale"]; ?></span> &euro; TTC</span><br />
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
			echo " : <span style='font-weight:bold; color:black'>".$bid["somme1"]." &euro; TTC</span><br />";
			?>
             
              
              + la facture Bipmod <?php if($bid["facture_bipmod"]!="") echo "n&deg;".$bid["facture_bipmod"]; else echo"<i>en cours de création</i>"; ?> de : <span style="font-weight:bold; color:black"><?php echo $bid["somme2"]; ?> &euro; TTC</span></p>
            
            <p>En votre aimable r&egrave;glement. </p></td></tr></tbody>
        </table>
        </p>
        <?php } ?>
		</form>
	 <div style="display:none;" id="light" class="white_content">
	 <form name="nouveauclient" class="client" id="nouveauclient">
	 <h2>Ajouter un client</h2>
	 	<input type="hidden" name="identifiant" id="identifiant"/>
		<p class="soul"><label>Enseigne :</label><input type="text" name="libelle" /></p>
		<p><label>Adresse :</label><textarea name="adresse"></textarea></p>
		<p><label>&nbsp;</label><input name="submit" value="Ajouter" type="submit" class="bout" id="aj" /></p>
	   </form>
	 <a href="javascript:void(0)" onClick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
	 </div>
	<div style="display:none;" id="fade" class="black_overlay">&nbsp;</div>	
    
    
    <div style="display:none;" id="light2" class="white_content">
	 <form name="nouvelle_regie" class="client" id="nouvelle_regie">
	 <h2>Ajouter un groupe régies</h2>
	 	<input type="hidden" name="identifiant" id="identifiant"/>
		<p class="soul"><label>Nom du groupe :</label><input type="text" name="libelle" /></p>
		<p><label>Frais d'antenne (au spot) :</label><input type="text" id="frais_regie" name="frais_regie" /></p>
		<p><label>&nbsp;</label><input name="submit" value="Ajouter" type="submit" class="bout" id="ajreg" /></p>
	   </form>
	 <a href="javascript:void(0)" onClick="document.getElementById('light2').style.display='none';document.getElementById('fade2').style.display='none'">Close</a>
	 </div>
	<div style="display:none;" id="fade2" class="black_overlay">&nbsp;</div>	
		
		
		
</div>
	<?php include("inc_footer.php"); ?>
