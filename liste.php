<?php

//Connexion/déconnexion base en local
$host = "localhost"; 
$user = "root"; 
$password = "";  
$database = "bipmod"; 

$dblink=mysql_connect($host, $user, $password);
mysql_select_db($database) or die("err".mysql_error());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Document sans nom</title>
</head>

<body>

<h1>LISTE DES ORDRES DE FACTURATION EN ATTENTE</h1>

<table width="600" border="0" cellspacing="0" cellpadding="3">
<?php
$mem_societe="";
$sql=mysql_query("SELECT ordres.date, clients.libelle AS client, societes.libelle AS societe FROM ordres INNER JOIN societes ON ordres.societe=societes.identifiant INNER JOIN clients ON ordres.client=clients.identifiant WHERE archive=0 ORDER BY societes.libelle ASC, date DESC") or die("erreur ".mysql_error()); 
while($row=mysql_fetch_assoc($sql)){
	if ($row["societe"]!=$mem_societe){
		echo "<tr><td colspan='2'><b>".$row["societe"]."</b></td></tr>";
		$mem_societe=$row["societe"];
	}
	echo "<tr><td>".$row["date"]."</td><td>".$row["client"]."</td></tr>";
}
mysql_close($dblink);
?>
</table>
</body>
</html>
