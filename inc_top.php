<?php 
session_start();
include("connexion.class.php");
include("affichage.class.php");
include("identification.class.php");
$afficher= new affichage();
$identification= new identification();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Groupe Maniacom : Ordres de facturation</title>
<link href="template.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
</head>

<body>&nbsp;
	<h1>ORDRES DE FACTURATION</h1>
	<div id="conteneur_top">
    <div style="float:left"><?php $identification->verif_identification(); ?></div>
	<div style="float:right"><a href="traitements/deconnexion.php" style="margin-left:80px;">Déconnexion</a></div>
    </div>
	<div id="conteneur">
	  <ul id="menu" align="center">
	    <?php if(in_array("4", $_SESSION["droits"])){ ?>
	  	<li><a href="ajout-client.php" id="ajoutclient">Gestion des clients</a></li>
		<?php 
		} 
		if((in_array("1", $_SESSION["droits"]))||(in_array("7", $_SESSION["droits"]))){
			// Demande classique ET mandataire : 
			if((in_array("1", $_SESSION["droits"]))&&(in_array("7", $_SESSION["droits"]))){
				echo '<li><a href="demande-interm.php" id="facturation">Demande de facturation</a></li>';
			// Demande classique seule :
			}elseif((in_array("1", $_SESSION["droits"]))&&(!in_array("7", $_SESSION["droits"]))){
				echo '<li><a href="demande.php" id="facturation">Demande de facturation</a></li>';
			// Demande mandataire seule :
			}elseif((!in_array("1", $_SESSION["droits"]))&&(in_array("7", $_SESSION["droits"]))){
				echo '<li><a href="demande-mandataire.php" id="facturation">Demande de facturation</a></li>';
			}
		}
		?>

        	

		<li><a href="liste-demandes.php" id="attente">Demandes en attente</a></li>
		<li><a href="liste-demandes-archivees.php" id="archives">Demandes archivées</a></li>
	  </ul>
	  <hr/>