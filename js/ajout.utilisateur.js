// Scripts utilisés pour les différentes fonctionnalités  de la page ajout-utilisateur.php

// supprime un utilisateur de la liste
function supprim(id){
	if(confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")){
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/supprim-utilisateur.php",
			data: "id="+id,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste tbody").html(data);
				reinitialiser();
			}
		});
	}
	return false;
}
// Rappelle les fonctions de présentation de tableau et d'actions sur les liens de modif et suppression.
function reinitialiser(){
	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	$(".stripeMe tr:even").addClass("alt");
	$(".suppr").click( function(){ supprim($(this).attr("id")) });
	$("a.modifier").click( function(){	modifier($(this));});
}
// Pour modifier un client de la liste :
function modifier(bouton){
	var id= bouton.attr("title");
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
	$("p#confirmation").remove();
	document.nouvelutilisateur.style.display="block";
	$.ajax({
		type: "GET",
		processData: true,
		url: "traitements/infos-utilisateur.php",
		data: "id="+id,
		dataType: "json",
		error:function(msg){ alert( "Error !: " + msg ); },
		success:function(data){
			document.nouvelutilisateur.identifiant.value=data.identifiant;
			document.nouvelutilisateur.nom.value=data.nom;
			document.nouvelutilisateur.login.value=data.login;
			document.nouvelutilisateur.mdp.value=data.motdepasse;
			document.nouvelutilisateur.email.value=data.email;
			for(var i=0; i<document.nouvelutilisateur["societes[]"].length; i++){ document.nouvelutilisateur["societes[]"][i].checked=false; }
			for(var i=0; i<document.nouvelutilisateur["droits[]"].length; i++){ document.nouvelutilisateur["droits[]"][i].checked=false; }
			var chaine=''+data.societe+'';
			var reg=new RegExp("[,]+", "g");
			var tableau=chaine.split(reg);
			for (var i=0; i<tableau.length; i++) { document.nouvelutilisateur["societes[]"][(tableau[i]-1)].checked=true; }
			var chaine=''+data.droit+'';
			var tableau=chaine.split(reg);
			for (var i=0; i<tableau.length; i++) { document.nouvelutilisateur["droits[]"][(tableau[i]-1)].checked=true; }
		}
	});
	return false;
}
$(document).ready(function(){

	document.getElementById('fade').style.height=document.body.offsetHeight+'px';
	document.getElementById('fade').style.width=document.body.offsetWidth+'px';
	reinitialiser();
	
	//Ajout d'un client ;
	$("input#aj").click( function(){
		var identifiant=document.nouvelutilisateur.identifiant.value;
		var nom=document.nouvelutilisateur.nom.value;
		var login=document.nouvelutilisateur.login.value;
		var mdp=document.nouvelutilisateur.mdp.value;
		var email=document.nouvelutilisateur.email.value;
		societes="";
		droits="";
		for(var i=0; i<document.nouvelutilisateur["societes[]"].length; i++){
			if(document.nouvelutilisateur["societes[]"][i].checked==true){ societes=societes+"&societe[]="+document.nouvelutilisateur["societes[]"][i].value; }
		}
		for(var i=0; i<document.nouvelutilisateur["droits[]"].length; i++){
			if(document.nouvelutilisateur["droits[]"][i].checked==true){ droits=droits+"&droit[]="+document.nouvelutilisateur["droits[]"][i].value; }
		}
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/ajout-utilisateur.php",
			data: "nom="+nom+"&login="+login+"&mdp="+mdp+"&email="+email+societes+droits+"&identifiant="+identifiant,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste tbody").html(data);
				document.nouvelutilisateur.nom.value="";
				document.nouvelutilisateur.login.value="";
				document.nouvelutilisateur.mdp.value="";
				document.nouvelutilisateur.email.value="";
				document.nouvelutilisateur.style.display="none";
				$("#light").prepend("<p id='confirmation'>Ajout bien effectué.</p><br/>");
				reinitialiser();
			}
		});
		return false; 
	});	
	$("a#ajouter").click( function(){
		document.nouvelutilisateur.identifiant.value="";
		document.nouvelutilisateur.nom.value="";
		document.nouvelutilisateur.login.value="";
		document.nouvelutilisateur.mdp.value="";
		document.nouvelutilisateur.email.value="";
		for(var i=0; i<document.nouvelutilisateur["societes[]"].length; i++){ document.nouvelutilisateur["societes[]"][i].checked=false; }
		for(var i=0; i<document.nouvelutilisateur["droits[]"].length; i++){ document.nouvelutilisateur["droits[]"][i].checked=false; }
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block';
		$("p#confirmation").remove();
		document.nouvelutilisateur.style.display="block";
	});

});