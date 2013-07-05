<?php include("inc_top.php"); ?>
	<script language="javascript" src="js/liste-demandes.js"></script>
	<link href="css/ajout.client.css" rel="stylesheet" type="text/css" />
	<div id="contenu">
		<h2>Liste des demandes archivées</h2>
		<table class="stripeMe sample">

	<thead>

		<tr class="alt">
			<th>Date</th>
			<th>Client</th>
			<th>Demande de</th>
			<th>Date archive</th>
			<th>Facture</th>
			<th>Consulter</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		//On limite l'affichage des ordres de facturation aux societes liées à l'utilisateur (ou à ses propres demandes) :
		if(in_array("3", $_SESSION["droits"])){
			$where="AND (";
			foreach($_SESSION["societes"] as $societe) $where.=" ordres.societe=".$societe." OR";
		}
		else $where = "AND ordres.utilisateur=".$_SESSION["identification"];
		if (substr($where, -2)=="OR") $where=substr($where,0, -3).")";
		$afficher->ordres("2","",$where); ?>
	</tbody>
</table>
	</div>
	<?php include("inc_footer.php"); ?>