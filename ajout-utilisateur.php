<?php include("inc_top.php"); ?>
	<script language="javascript" src="js/ajout.utilisateur.js"></script>
	<link href="css/ajout.client.css" rel="stylesheet" type="text/css" />
	  <div id="contenu">
	  
	  	 <h2>Liste des utilisateurs</h2>
<table id="liste" class="stripeMe sample">

	<thead>

		<tr class="alt">
			<th colspan="3">Nom utilisateur</th>
		</tr>
	</thead>
	<tbody>
		<?php $afficher->utilisateurs("","tableau"); ?>
	</tbody>
</table>
<br/><p><a href="#" id="ajouter">Ajouter un utilisateur</a></p>

	 <div style="display:none;" id="light" class="white_content">
	 <form name="nouvelutilisateur" class="nouvelutilisateur" id="nouvelutilisateur">
	 <h2 style="width:300px">Informations utilisateur</h2>
	 	<input type="hidden" name="identifiant"/>
		<p><label>Nom : </label><input type="text" name="nom" /></p>
		<p><label>Login : </label><input type="text" name="login" /></p>
		<p><label>Mot de passe : </label><input type="text" name="mdp" /></p>
        <p><label>Votre e-mail : </label><input type="text" name="email" /></p><br/>
		<p><b>Acc&egrave;s aux sociétés : </b><br/><?php $afficher->societes("", "checkbox"); ?></p>
		<p><b>Autorisations : </b><br/><?php $afficher->droits("", "checkbox"); ?></p>
		<input name="submit" value="Ajouter" type="submit" class="bout" id="aj" />
		</form>
	 <br/><a href="javascript:void(0)" onClick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
	 </div>
	<div style="display:none;" id="fade" class="black_overlay">&nbsp;</div>
	
	</div>
	<?php include("inc_footer.php"); ?>
