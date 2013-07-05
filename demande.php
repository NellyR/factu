<?php include("inc_top.php");
// Préremplissage du formulaire pour un ordre existant : 
if(isset($_GET["id"])) $row= $afficher->ordres("0", "", $where="AND ordres.identifiant=".$_GET["id"], "array");
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
		<form class="demande" name='factu' action="traitements/demande.php" method="post" onsubmit="alert(document.factu.societe.value==""); return false;">
		<h2>Créer un nouvel ordre de facturation</h2>
		<div style="padding:5px; padding-top:8px; border:1px dotted #9966CC; margin-bottom:15px;">
		<p><label>Ordre de facturation de :</label>
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
		<p>
		  <label>Client * :</label>
		  <select name="client" style="font-size:10px;" id="selectclient">
		  <option style="font-size:10px;" value="" selected>Sélectionner un client</option>
		  <?php $afficher->clients($row["idclient"]);?></select>
          
		  <!--<input type="text" size="30" name="client" value="" id="inputString" onkeyup="lookup(this.value);" onblur="fill();" autocomplete="off" />-->
		  <a href="#" id="ajouter">Ajouter un client</a>
		  <div class="suggestionsBox" id="suggestions" style="display: none;">
				<div class="suggestionList" id="autoSuggestionsList" style="z-index:1000">
					&nbsp;
				</div>
		  </div>
		  Remarques &eacute;ventuelles: <br/><textarea name="remarques" style="width:300px" rows="3" ><?php echo stripslashes($row["remarques"]); ?></textarea>
		  </p>
		 
		</div>
	    <table border="0" align="center" cellpadding="5" cellspacing="0" id="liste" class="stripeMe sample Style2">
		<thead>

		<tr class="alt">
			<th><b>Produit factur&eacute;</b></th>
			<th><b>Cplt d'information  <br />
			(facult.)</b></th>
            <th><b>Montant</b></th>
          <th><b>Frais<br />
              fourniss.</b></th>
            <th><b>Marge</b></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(isset($row)){
		$tab=$afficher->lignesordre($_GET["id"]);
		$i=0;
		foreach($tab as $t){
			$i++;
			print '
			<tr id="'.$i.'">
            <td height="50" align="center" valign="top" class="moins"><textarea name="produit'.$i.'" cols="30" rows="4" type="text" >'.stripslashes($t["produit"]).'</textarea><br/>';
			
			 if(in_array(2, $_SESSION["societes"])){
				$row2=$afficher->societes("", "array", "WHERE identifiant=2");
				if($row2["nomenclature"]==1){
					echo "<select name='nomenc".$i."' style='font-size:10px; width: 180px;'><option style='font-size:10px;' value=''>Objet (nomenclature Bipmod)</option>";
					$afficher->nomenclature($t["nomenc"],"menuderoulant","WHERE societe=".$_SESSION["societes"][0]);
					echo "</select></p>";
					$texte = $afficher->nomenclature($t["nomenc"],"liste","WHERE societe=".$_SESSION["societes"][0]); 
					echo "<input type='hidden' name='liste_nomenc".$i."' value='".str_replace("selected", "",str_replace('_','"',$texte))."' />";
				}	
			}
		  
			
			
			print '</td>
			<td align="center" valign="bottom" class="moins"><textarea name="cplt'.$i.'" cols="13" rows="4">'.stripslashes($t["cplt"]).'</textarea></td>
            <td align="center" valign="bottom" class="moins"><input name="montant'.$i.'" type="text" size="5" onkeyup="check(this)"; onchange="calcul('.$i.')" value="'.$t["montant"].'"/></td>
            <td align="center" valign="bottom" class="moins"><input name="frais'.$i.'" type="text" size="5" onkeyup="check(this);" onchange="calcul('.$i.')" value="'.$t["frais"].'"/></td>
            <td align="center" valign="bottom" class="moins"><input name="marge'.$i.'" type="text" size="5" readonly="readonly" value="'.($t["montant"]-$t["frais"]).'"/></td>
          </tr>
			'; 
		}
		print '<input type="hidden" name="lignes" value="'.$i.'" />';
		//$row2= $afficher->ordres("0", "", $where="AND ordres.identifiant=".$_GET["id"], "array");
	}else{
	?>
          <tr id="1">
            <td height="50" align="center" valign="top" class="moins"><textarea name="produit1" style="width:180px;" rows="4" type="text" ></textarea><br/>
            <?php
            if(in_array(2, $_SESSION["societes"])){	
				$row2=$afficher->societes("", "array", "WHERE identifiant=2");
				if($row2["nomenclature"]==1){
					echo "<select style='font-size:10px; width: 180px;' name='nomenc1'><option style='font-size:10px;' value=''>Objet (nomenclature Bipmod)</option>";
					$afficher->nomenclature("","menuderoulant","WHERE societe=".$_SESSION["societes"][0]);
					echo "</select></p>";
					$texte = $afficher->nomenclature("","liste","WHERE societe=".$_SESSION["societes"][0]); 
					echo "<input type='hidden' name='liste_nomenc1' value='".str_replace('_','"',$texte)."' />";
				}	
			}
			?>
            
            
            
            </td>
			<td align="center" valign="bottom" class="moins"><textarea name="cplt1" rows="4" style="width:110px;"></textarea></td>
            <td align="center" valign="bottom" class="moins"><input name="montant1" type="text" size="5" onkeyup="check(this)"; onchange="calcul('1')" value="0"/></td>
            <td align="center" valign="bottom" class="moins"><input name="frais1" type="text" size="5" onkeyup="check(this);" onchange="calcul('1')" value="0"/></td>
            <td align="center" valign="bottom" class="moins"><input name="marge1" type="text" size="5" readonly="readonly" value="0"/></td>
          </tr>
		  <input type="hidden" name="lignes" value="1" />
		  
          <?php } ?>
		  </tbody>
		  <tr id="derligne">
            <td colspan="2" align="right"><b>
			  <input type="hidden" name="identifiant" value="<?php if(isset($_GET["id"])) echo $_GET["id"]; else echo '0';  ?>"/>
			 
              Total &agrave; facturer : </b>              <input name="montanttotal" type="text" size="6" readonly="readonly" value=""/> 
              &euro; HT </td>
            <td colspan="3" align="right"><b>Total marge : </b>
			<input name="margetotale" type="text" size="6" readonly="readonly"value=""/>
            &euro; HT </td>
          </tr>
        </table>
		<p>&nbsp;</p>
		<p>
		<input type="hidden" name="archive" value="0" />
		 <input type="submit" value="Enregistrer en Brouillon" />  <input type="button" value="Enregistrer et envoyer" id="envoi" />
		  <input name="ajoutligne" type="button" value="Ajout ligne" id="ajoutligne" />
		</p>
		</form>
	 <div style="display:none;" id="light" class="white_content">
	 <form name="nouveauclient" class="client" id="nouveauclient">
	 <h2>Ajouter un client</h2>
	 	<input type="hidden" name="identifiant"/>
		<p class="soul"><label>Enseigne :</label><input type="text" name="libelle" /></p>
		<p><label>Adresse :</label><textarea name="adresse"></textarea></p>
		<p><label>&nbsp;</label><input name="submit" value="Ajouter" type="submit" class="bout" id="aj" /></p>
	   </form>
	 <a href="javascript:void(0)" onClick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
	 </div>
	<div style="display:none;" id="fade" class="black_overlay">&nbsp;</div>	
		
		
		
</div>
	<?php include("inc_footer.php"); ?>
