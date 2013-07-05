<?php
	session_start();
	unset($_SESSION["identification"]);
	unset($_SESSION["droits"]);
	unset($_SESSION["societes"]);
	header("Location: ../index.php");
?>
