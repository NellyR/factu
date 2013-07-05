<?php include("inc_top.php"); ?>
	<script language="javascript" src="js/ajout.client.js"></script>
	<link href="css/ajout.client.css" rel="stylesheet" type="text/css" />
	  <div id="contenu">
	  
	  <form class="client">
	  <p><label>Rechercher un client :</label><input type="name" id="recherche" /></p>
	  </form>
	  	 <h2>Clients disponibles</h2>
<table id="liste" class="stripeMe sample">

	<thead>

		<tr class="alt">
			<th colspan="3">Client</th>
		</tr>
	</thead>
	<tbody>
		<?php $afficher->clients("","tableau"," LIMIT 0, 10"); ?>
	</tbody>
</table>
<div align="right"><a href="#" id="prevp">&lt;&lt; Prev </a> || <a href="#" id="nextp">Next &gt;&gt; </a></div>
<span id="nopage" style="display:none" />1</span>
<br/><p><a href="#" id="ajouter">Ajouter un client</a></p>

	 <div style="display:none;" id="light" class="white_content">
	 <form name="nouveauclient" class="client" id="nouveauclient">
	 <h2>Ajouter un client</h2>
	 	<input type="hidden" name="identifiant"/>
		<p class="soul"><label>Enseigne :</label><input type="text" name="libelle" /></p>
		<p><label>Adresse :</label><textarea name="adresse"></textarea></p>
        <p class="soul"><label>TAX ID* :</label><input type="text" name="tax_id" />
        <br/><span style="font-size:10px">* Clients hors France - TVA non appliquée</span></p>
		<p><label>&nbsp;</label><input name="submit" value="Ajouter" type="submit" class="bout" id="aj" /></p>
		</form>
	 <a href="javascript:void(0)" onClick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
	 </div>
	<div style="display:none;" id="fade" class="black_overlay">&nbsp;</div>
	
	</div>
	<?php include("inc_footer.php"); ?>
