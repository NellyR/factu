</div>

	<ul id="footer">
	<?php if($page!="index.php"){ ?>
	<?php if(in_array("5", $_SESSION["droits"])){ ?>
		<li><a href="ajout-utilisateur.php">Gestion des utilisateurs</a></li>
        
		<?php 
		}
		if(in_array("6", $_SESSION["droits"])){ 
		?>
		<!--<li>| <a href="">Quelques chiffres indicatifs</a></li>-->
		<?php } ?>
		<?php } ?>
	</ul>
	
</body>
</html>