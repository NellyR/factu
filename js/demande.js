// Scripts utilisés pour les différentes fonctionnalités  de la page demande.php
// réduit la liste par rapport au champ de recherche
function lookup(inputString) {
	if(inputString.length == 0) $('#suggestions').hide();
	else {
		$.post("traitements/autocompletion-clients.php", {where: ""+inputString+"" }, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
}
	
function fill(thisValue) {
	$('#inputString').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}


function check(champ){
	reg = new RegExp('[^0-9.\-]+', 'g');
	valeur = champ.value;
	if(reg.test(valeur)) champ.value=champ.value.replace(/[^0-9.\-]+/, '');
	else return true;
}

function calcul(numligne){
	marge="marge"+numligne; montant="montant"+numligne; frais="frais"+numligne;
	document.factu.elements[marge].value= Math.round (((document.factu.elements[montant].value)-(document.factu.elements[frais].value))*100)/100;
	document.factu.montanttotal.value="";
	document.factu.margetotale.value="";
	for(i=1; i<=document.factu.lignes.value; i++){
		marge="marge"+i; montant="montant"+i; frais="frais"+i;
		if(document.factu.montanttotal.value=="") document.factu.montanttotal.value=0;
		document.factu.montanttotal.value=Number(document.factu.montanttotal.value)+Number(document.factu.elements[montant].value);
		document.factu.margetotale.value=Number(document.factu.margetotale.value)+Number(document.factu.elements[marge].value);
	}
}
function reinitialiser(){
	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	$(".stripeMe tr#derligne").mouseover(function(){$(this).removeClass("over");}).mouseout(function(){$(this).removeClass("over");});
	$(".stripeMe tr:even").addClass("alt");
	$(".stripeMe tr#derligne:even").removeClass("alt");
	document.getElementById('fade').style.height=document.body.offsetHeight+'px';
	document.getElementById('fade').style.width=document.body.offsetWidth+'px';
	// Pour IE si la page fait moins que la hauteur de la fenêtre :
	if(document.documentElement.clientHeight > document.body.offsetHeight){ 
		document.getElementById('fade').style.height=document.documentElement.clientHeight;
	}
}

$(document).ready(function(){

	reinitialiser();
	document.factu.montanttotal.value=0;
	document.factu.margetotale.value=0;
	for(i=1; i<=document.factu.lignes.value; i++){
		document.factu.montanttotal.value = (document.factu.montanttotal.value*1) + (document.factu.elements['montant'+i].value*1);
		document.factu.margetotale.value = (document.factu.margetotale.value*1) + (document.factu.elements['marge'+i].value*1);
	}
	//Ajout d'un client ;
	$("input#aj").click( function(){
		var identifiant=document.nouveauclient.identifiant.value;
		var libelle=document.nouveauclient.libelle.value;
		var adresse=document.nouveauclient.adresse.value;
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/ajout-client.php",
			data: "libelle="+libelle+"&adresse="+adresse+"&identifiant="+identifiant+"&select=1",
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("#selectclient").html(data);
				document.factu.client.value=document.nouveauclient.libelle.value;
				// Ci-dessous je selectionne ma nouvelle valeur dans mon menu déroulant :
				for (var i=0; i<document.factu.client.options.length; i++) {
					if(document.factu.client.options[i].text==libelle){
						document.factu.client.options[i].selected = true;
					}
				}
				document.nouveauclient.libelle.value="";
				document.nouveauclient.adresse.value="";
				document.nouveauclient.style.display="none";
				$("#light").prepend("<p id='confirmation'>Ajout bien effectué.</p><br/>");
			}
		});
		return false; 
	});	
	$("a#ajouter").click( function(){
		document.nouveauclient.identifiant.value="";
		document.nouveauclient.libelle.value="";
		document.nouveauclient.adresse.value="";
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block';
		$("p#confirmation").remove();
		document.nouveauclient.style.display="block";
	});
	$("input#ajoutligne").click( function(){
		var nblignes=(document.factu.lignes.value*1)+1;
		if(document.factu.liste_nomenc1) var sel = document.factu.liste_nomenc1.value;
		var ligne="<tr id='"+nblignes+"'><td align='center' valign='top' class='moins'><textarea name='produit"+nblignes+"' cols='30' rows='2' type='text' style='width:180px' /></textarea>";
		if(sel) ligne+="<br/><select name='nomenc"+nblignes+"' style='font-size:10px; width:180px'><option value=''>Objet (nomenclature Bipmod)</option>"+sel+"</select>";
		ligne =ligne+"</td><td align='center' valign='bottom' class='moins'><textarea name='cplt"+nblignes+"' cols='13' value='' /></textarea></td><td align='center' valign='bottom' class='moins'><input name='montant"+nblignes+"' type='text' size='5' onkeyup='check(this)'; onchange='calcul(\""+nblignes+"\")' value=''/></td><td align='center' valign='bottom' class='moins'><input name='frais"+nblignes+"' type='text' size='5' onkeyup='check(this);' onchange='calcul(\""+nblignes+"\")' value=''/></td><td align='center' valign='bottom' class='moins'><input name='marge"+nblignes+"' type='text' size='5' readonly='readonly' value=''/></td></tr>";
		$("table#liste").append(ligne);
		document.factu.lignes.value=nblignes;
		reinitialiser();
	});
	$("input#envoi").click( function(){
		if(confirm("Êtes-vous sûr de vouloir envoyer votre demande ?")){
			document.factu.archive.value="1";
			document.factu.submit();
		}else return false;
	});
});