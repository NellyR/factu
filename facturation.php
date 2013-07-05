<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
//Connexion/déconnexion base en local
function connect_bdd($type){
	$lien = mysql_connect("localhost","root","") or die("Echec de connexion à la base.");
	if ($type == "connexion"){
		$db="maniacom";
		$select=mysql_select_db($db,$lien);
	}else mysql_close($lien);
}
connect_bdd("connexion");


$societe=2;//Net Edition
if (isset($_POST["nouveauclient"])) mysql_query("INSERT INTO clients (libelle,adresse) VALUES('".$_POST["nouveauclient"]."','".addslashes(nl2br(htmlentities($_POST["adressenouveauclient"])))."')");
elseif ((isset($_POST["margetotale"]))&&($_POST["plus"]=="")){
	mysql_query("INSERT INTO ordres(date, societe,client) VALUES('".date("Y-m-d")."',".$societe.",".$_POST["client"].")");
	$id=mysql_insert_id();
	for($i=1;$i<=$_POST["lignes"];$i++){
		mysql_query("INSERT INTO lignesordre(produit,montant,frais,ordre) VALUES('".$_POST["produit".$i]."','".$_POST["montant".$i]."','".$_POST["frais".$i]."',".$id.")"); 
	}
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Document sans nom</title>
<style type="text/css">
<!--
.Style1 {font-family: Arial, Helvetica, sans-serif; font-size:16px }
.Style2 {font-family: Arial, Helvetica, sans-serif; font-size:12px }
-->
</style>
<script language="javascript" src="calculs.js"></script>
</head>

<body>

<p align="center" class="Style1"><strong>ORDRE DE FACTURATION<br />
  NOM DE LA SOCIETE (selon personne loggu&eacute;e) </strong><br />
</p>
<form name='client' action="facturation.php" method="post">
<div style="position:absolute; width:200px; border: #000000 solid 1px; padding:2px; visibility:hidden; background-color:#FFFFFF" class="Style2" id="ici">
  <div align="right" class="Style1"><a href='#' onclick='document.getElementById("ici").style.visibility="hidden";'><b>+</b></a></div>
    Nouveau client :<br /> <input type="text" name="nouveauclient" />
    <br />Adresse :<br /><textarea name="adressenouveauclient"></textarea><br /><br />
  <input type="submit" name="Submit" value="Ajouter" />
</div>
</form>
<form name='factu' action="facturation.php" method="post">
<table width="600" border="0" align="center" cellpadding="5" cellspacing="0" class="Style2">
  <tr>
    <td width="74">Client</td>
    <td width="506" colspan="3">
      <select name="client" onchange="adresseclient();">
       <?php
	   $stockage_adresse="";
	   $sql=mysql_query("SELECT * FROM Clients ORDER BY identifiant");
	   while($row=mysql_fetch_assoc($sql)){
	   echo "<option value='".$row["identifiant"]."'>".$row["libelle"]."</option>";
	   $stockage_adresse.=",'".$tab[$row["identifiant"]]=$row["adresse"]."'";
	   }
	   ?>
      </select>
	<script language="javascript">
	function adresseclient(){
		var tab=new Array(''<?php echo $stockage_adresse ?>);
		var num=document.factu.client.value;
		document.factu.adresse.value = tab[num];
	}
	</script>
    <span class="Style1"><b><a href='#' onclick='document.getElementById("ici").style.visibility="visible";'>+</a></b></span></td>
  </tr>
  <tr>
    <td>Adresse</td>
    <td colspan="3"><span class="Style1">
      <input name="adresse" type="text" style="border:none; background-color:#FFFFFF" readonly="readonly" />
    </span></td>
  </tr>
</table>
<div align="center"><br />
    <table width="600" border="0" align="center" cellpadding="5" cellspacing="1" class="Style2">
      <tr>
      	<td width="258" align="center" bgcolor="#66CCFF"><b>Produit factur&eacute;</b></td>
     	<td width="60" align="center" bgcolor="#66CCFF"><b>Montant</b></td>
      	<td width="109" align="center" bgcolor="#66CCFF"><b>Frais fournisseurs</b></td>
      	<td width="109" align="center" bgcolor="#66CCFF"><b>Marge</b></td>
      	<td width="8" align="center">&nbsp;</td>
      </tr>
	  <?php 
	  if(isset($_POST["lignes"]))$lignes=$_POST["lignes"]; 
	  else $lignes=1;
	  for($i=1; $i <= $lignes ; $i++){ 
	  ?>
      <tr>
        <td height="50" align="center" bgcolor="#CCCCCC" class="Style1"><input name="produit<? echo $i ?>" type="text" value="<?php if(isset($_POST["produit$i"])) echo $_POST["produit$i"] ?>" size="43" /></td>
      <td align="center" bgcolor="#CCCCCC"><input name="montant<?php echo $i ?>" type="text" size="10" onkeyup="check(this)"; onchange="calcul('<?php echo $i ?>')" value="<?php if(isset($_POST["montant$i"])) echo $_POST["montant$i"] ?>"/></td>
      <td align="center" bgcolor="#CCCCCC"><input name="frais<?php echo $i ?>" type="text" size="10" onkeyup="check(this);" onchange="calcul('<?php echo $i ?>')" value="<?php if(isset($_POST["frais$i"])) echo $_POST["frais$i"] ?>"/></td>
      <td align="center" bgcolor="#CCCCCC"><input name="marge<?php echo $i ?>" type="text" size="10" readonly="readonly" value="<?php if(isset($_POST["marge$i"])) echo $_POST["marge$i"] ?>"/></td>
      <td align="center" class="Style1"><b><a href='javascript:ajoutligne()'>+</a></b></td>
      </tr>
	  <?php } ?>
      <tr>
        <td align="right"><b>
          <input type="hidden" name="plus" />
          <input type="hidden" name="lignes" value="<?php echo $lignes; ?>" />
        Total &agrave; facturer : </b></td>
      <td ><input name="montanttotal" type="text" size="10" readonly="readonly" value="<?php if(isset($_POST["montanttotal"])) echo $_POST["montanttotal"] ?>"/></td>
      <td align="right"><b>Total marge : </b></td>
      <td align="center"><input name="margetotale" type="text" size="10" readonly="readonly"value="<?php if(isset($_POST["margetotale"])) echo $_POST["margetotale"] ?>"/></td>
      <td>&nbsp;</td>
      </tr>
  </table>
    <div align="center"><br />
        <input type="submit" name="Submit2" value="Envoyer" />
    </div>
</form> 
</body>
</html>
