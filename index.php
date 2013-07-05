<?php 
$page="index.php";
session_start();
include("connexion.class.php");
include("affichage.class.php");
include("identification.class.php");
$afficher= new affichage();
$identification= new identification();
if(isset($_POST["login"])) $identification->rechercher_identite($_POST["login"], $_POST["pass"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Groupe Maniacom : Ordres de facturation</title>
<link href="template.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
</head>

<body>
	<h1>ORDRES DE FACTURATION</h1>
	<div id="conteneur_top" style="height:1px">
    </div>
	<div id="conteneur">
	<div id="contenu">
		<form class="client" name='identification' action="index.php" method="post">
		<h2>Veuillez vous identifier</h2>
        <p><label>Votre login :</label><input name="login" type="text" /></p>
        <p><label>Votre mot de passe :</label><input name="pass" type="password" /></p>
	    <input type="submit" value="S'identifier" />
		</form>
	 </div>
	<?php include("inc_footer.php"); ?>