<?php include("inc_top.php"); ?>
	<script language="javascript" src="js/liste-demandes.js"></script>
	<link href="css/ajout.client.css" rel="stylesheet" type="text/css" />
	<div id="contenu">
	<?php
	if((in_array("1", $_SESSION["droits"]))||(in_array("7", $_SESSION["droits"]))){ ?>
		<h2>Mes brouillons</h2>
		<table id="liste1" class="stripeMe sample">
	<thead>
		<tr class="alt">
			<th>Date (provisoire)</th>
			<th>Client</th>
			<th>Demande de</th>
			<th>Modifier</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$where = "AND ordres.utilisateur=".$_SESSION["identification"];
		$afficher->ordres("0", "", $where); 
		?>
	</tbody>
</table>
<p>&nbsp;</p>
<?php } ?>
		<h2>Ordres de facturation en attente de traitement</h2>
		<table id="liste2" class="stripeMe sample">
		<thead>
		<tr class="alt">
			<th>N°</th>
			<th>Date de la demande</th>
			<th>Client</th>
			<th>Demande de</th>
			<th>Consulter</th>
		</tr>
	</thead>
	<tbody>
		<?php
		//On limite l'affichage des ordres de facturation aux societes liées r l'utilisateur (ou a ses propres demandes) :
		if(in_array("3", $_SESSION["droits"])){
			$where="AND (";
			foreach($_SESSION["societes"] as $societe) $where.=" ordres.societe=".$societe." OR";
		}
		if (substr($where, -2)=="OR") $where=substr($where,0, -3).")";
		$afficher->ordres("1","",$where); ?>
	</tbody>
</table>
	</div>
	<?php include("inc_footer.php"); ?>
