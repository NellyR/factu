// Scripts utilisés pour les différentes fonctionnalités  de la page ajout-client.php
// réduit la liste par rapport au champ de recherche
function lookup(inputString) {
	var where="";
	if(inputString.length == 0) where="";
	else where=inputString;
	$.ajax({
		type: "GET",
		processData: true,
		url: "traitements/affiche-clients.php",
		data: "where="+where+"&page=0",
		dataType: "html",
		error:function(msg){ alert( "Error !: " + msg ); },
		success:function(data){
			$("table#liste tbody").html(data);
			reinitialiser();
		}
	});	
}
// supprime un client de la liste
function supprim(id){
	var page= $("#nopage").html()-1;
	if(confirm("Êtes-vous sûr de vouloir supprimer ce client ?")){
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/supprim-client.php",
			data: "id="+id+"&page="+page,
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
		document.nouveauclient.style.display="block";
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/infos-client.php",
			data: "id="+id,
			dataType: "json",
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				document.nouveauclient.identifiant.value=data.identifiant;
				document.nouveauclient.libelle.value=data.libelle;
				document.nouveauclient.adresse.value=data.adresse;
				document.nouveauclient.tax_id.value=data.tax_id;
		}
	});
	return false;
}

$(document).ready(function(){

	document.getElementById('fade').style.height=document.body.offsetHeight+'px';
	document.getElementById('fade').style.width=document.body.offsetWidth+'px';
	if($("#nopage").html()<2){ document.getElementById('prevp').style.display="none"; }
	$("#recherche").keyup( function(){ lookup(this.value); });
	reinitialiser();
	
	//Ajout d'un client ;
	$("input#aj").click( function(){
		var identifiant=document.nouveauclient.identifiant.value;
		var libelle=document.nouveauclient.libelle.value;
		var adresse=document.nouveauclient.adresse.value;
		var tax_id=document.nouveauclient.tax_id.value;
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/ajout-client.php",
			data: "libelle="+libelle+"&adresse="+adresse+"&tax_id="+tax_id+"&identifiant="+identifiant,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste tbody").html(data);
				document.nouveauclient.libelle.value="";
				document.nouveauclient.adresse.value="";
				document.nouveauclient.tax_id.value="";
				document.nouveauclient.style.display="none";
				$("#light").prepend("<p id='confirmation'>Ajout bien effectué.</p><br/>");
				reinitialiser();
			}
		});
		return false; 
	});
	
	$("a#ajouter").click( function(){
		document.nouveauclient.identifiant.value="";
		document.nouveauclient.libelle.value="";
		document.nouveauclient.adresse.value="";
		document.nouveauclient.tax_id.value="";
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block';
		$("p#confirmation").remove();
		document.nouveauclient.style.display="block";
	});
	
	// Bouton pour voir les clients précédents
	$("a#prevp").click( function(){
		var mapage=$("#nopage").html()-1;
		$("#nopage").html(mapage);
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/affiche-clients.php",
			data: "page="+($("#nopage").html()-1),
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste tbody").html(data);
				if($("#nopage").html()<2){ document.getElementById('prevp').style.display="none"; }
				if($("#nopage").html()>=1){ document.getElementById('nextp').style.display="inline"; }
				reinitialiser();
			}
		});
		return false; 
	});
	
	// Bouton pour voir les clients suivants
	$("a#nextp").click( function(){
		var mapage=$("#nopage").html();
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/affiche-clients.php",
			data: "page="+mapage,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste tbody").html(data);
				$("#nopage").html((mapage*1)+1);
				if($("#nopage").html()>1){ document.getElementById('prevp').style.display="inline"; }
				if(data=="") document.getElementById('nextp').style.display="none";
				reinitialiser();
				// Ici on vérifie si le bouton "suivant" est nécessaire :
				$.ajax({
					type: "GET",
					processData: true,
					url: "traitements/affiche-clients.php",
					data: "page="+ $("#nopage").html(),
					error:function(msg){ alert( "Error !: " + msg ); },
					success:function(data){ if(data=="") document.getElementById('nextp').style.display="none";}
				});
			}
		});
		return false; 
	});
	
});