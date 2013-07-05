Array.prototype.in_array = function(p_val) {
    for(var i = 0, l = this.length; i < l; i++) {
        if(this[i] == p_val) {
            rowid = i;
            return true;
        }
    }
    return false;
}
function check(champ, point){
	if(point=="sans") reg = new RegExp('[^0-9\-]+', 'g');
	else reg = new RegExp('[^0-9.\-]+', 'g');
	valeur = champ.value;
	if(point=="sans"){
		if(reg.test(valeur)) champ.value=champ.value.replace(/[^0-9\-]+/, '');
		else return true;
	}else{
		if(reg.test(valeur)) champ.value=champ.value.replace(/[^0-9.\-]+/, '');
		else return true;
	}
	
}
function isValidDate(date)
{
    var matches = /^(\d{2})[\/](\d{2})[\/](\d{4})$/.exec(date);
    if (matches == null) return false;
    var m = matches[2]- 1;
    var d = matches[1] ;
    var y = matches[3];
    var composedDate = new Date(y, m, d);
    return composedDate.getDate() == d &&
            composedDate.getMonth() == m &&
            composedDate.getFullYear() == y;
}
function compareDates(date_debut, date_fin){
	var matches = /^(\d{2})[\/](\d{2})[\/](\d{4})$/.exec(date_debut);
	if (matches == null) return false;
	d1 = new Date();
	d1.setDate(matches[1]) ;
	d1.setMonth(matches[2]- 1);
   	d1.setYear(matches[3]);
   	var matches2 = /^(\d{2})[\/](\d{2})[\/](\d{4})$/.exec(date_fin);
	if (matches2 == null) return false;
	d2 = new Date();
	d2.setDate(matches2[1]) ;
	d2.setMonth(matches2[2]- 1);
   	d2.setYear(matches2[3]);
	if(d2<d1){
		return false;	
	}else return true;
   
}

function verif_formulaire(){
	var mess_error ="";
	if(($("#selectclient").length)&&($("#selectclient").val()=="")){
		mess_error+="- Vous devez sélectionner un client.\r\n";
	}
	if($("#selectproduit").val()==""){
		mess_error +="Vous devez sélectionner un produit\r\n";
	}else if($("#selectproduit").val()==0){
		if(("#purchase_order").val()==""){
			mess_error +="Vous devez saisir un purchase order ou sélectionner un autre produit.\r\n";
		}
	}
	if(($("#start-date").length)&&(isValidDate($("#start-date").val())==false)){
		mess_error +="- Votre date de début de campagne doit etre sous la forme jj/mm/yyyy et etre une date valide.\r\n";
	}
	if(($("#end-date").length)&&(isValidDate($("#end-date").val())==false)){
		mess_error +="- Votre date de fin de campagne doit etre sous la forme jj/mm/yyyy et etre une date valide.\r\n";
	}
	if(($("#start-date").length)&&($("#end-date").length)&&(compareDates($("#start-date").val(), $("#end-date").val()) == false)){
		mess_error +="- Votre date de début de campagne doit etre antérieure a la date de fin de campagne.\r\n";
	}
	if(($("input[name=facture]").length)&&($("input[name=facture]").val()=="")){
		mess_error+="- Vous devez saisir un n° de facture régie.\r\n";
	}
	if(($("#selectgroupe").length)&&($("#selectgroupe").val()=="")){
		mess_error+="- Vous devez sélectionner un groupe de régies.\r\n";
	}
	if(($("#spot").length)&&($("#spot").val()!=parseInt($("#spot").val()))){
		mess_error+="- Vous n\'avez pas sélectionné le nombre de spots diffusés lors de la campagne.\r\n";
	}
	if(($("#net").length)&&($("#net").val()!=parseFloat($("#net").val())||(parseFloat($("#net").val())==0))){
		mess_error+="- Vous n\'avez pas sélectionné le montant net de la régie (ou le montant saisi n'est pas conforme).\r\n";
	}
	if(($("#netnet").length)&&($("#netnet").val()!=parseFloat($("#netnet").val()))||(parseFloat($("#netnet").val())==0)){
		mess_error+="- Vous n\'avez pas sélectionné le montant net net de la régie (ou le montant saisi n'est pas conforme).\r\n";
	}
	if(($("#honoraires").length)&&($("#honoraires").val()!=parseFloat($("#honoraires").val()))){
		mess_error+="- Vous n\'avez pas indiqué les honoraires (ou le pourcentage saisi n'est pas conforme).\r\n";
	}
	if(mess_error !=""){
		alert(mess_error);
		return false;
	}else return true;
}


function reinitialiser(){
	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	$(".stripeMe tr:even").addClass("alt");
	$(".suppr").click( function(){ supprim($(this).attr("id") , $(this).attr("name")) });
}


function format(valeur,decimal,separateur) {
// formate un chiffre avec 'decimal' chiffres apres la virgule et un separateur
	var deci=Math.round( Math.pow(10,decimal)*(Math.abs(valeur)-Math.floor(Math.abs(valeur)))) ; 
	var val=Math.floor(Math.abs(valeur));
	if ((decimal==0)||(deci==Math.pow(10,decimal))) {val=Math.floor(Math.abs(valeur)); deci=0;}
	var val_format=val+"";
	var nb=val_format.length;
	for (var i=1;i<4;i++) {
		if (val>=Math.pow(10,(3*i))) {
			val_format=val_format.substring(0,nb-(3*i))+separateur+val_format.substring(nb-(3*i));
		}
	}
	if (decimal>0) {
		var decim=""; 
		for (var j=0;j<(decimal-deci.toString().length);j++) {decim+="0";}
		deci=decim+deci.toString();
		val_format=val_format+"."+deci;
	}
	if (parseFloat(valeur)<0) {val_format="-"+val_format;}
	return val_format;
}

// MEt a jour le récap ordres mandataire
function majrecap(ordre){
	$.ajax({
		type: "GET",
		processData: true,
		url: "traitements/recap-ordre.php",
		data: "&ordre="+ordre,
		error:function(msg){ alert( "Error !: " + msg ); },
		success:function(data){
			$("table#recap tbody").html(data);
		}
	});
}


// supprime un produit de la liste
function supprim(id, ordre){
	if(confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")){
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/supprim-produit.php",
			data: "id="+id+"&ordre="+ordre,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste tbody").html(data);
				reinitialiser();
				// On met a jour le récapitulatif
				if($("table#recap tbody").length){
					majrecap(ordre);
				}
			}
		});
	}
	return false;
}

function calculregie(spot, netnet, honoraires){
	// CALCUL FRAIS ANTENNE
	frais = $("#fraisantenne").html() * spot;
	if(frais != parseFloat(frais)) frais=0;
	$("#antenne").html(format(frais,2," "));
	// CALCUL TVA REGIE
	netnetplusfrais =  parseFloat($("#netnet").val()) + parseFloat(frais);
	netplusfrais =  parseFloat($("#net").val()) + parseFloat(frais);
	
	if(netnetplusfrais != parseFloat(netnetplusfrais)) netnetplusfrais=0;
	$("#tvaregie").html(format(netnetplusfrais*0.196,2," "));
	// CALCUL TTC REGIE
	$("#ttcregie").html(format(netnetplusfrais*1.196,2," "));
	// CALCUL HONORAIRES MANDATAIRE
	//honoraires = netnetplusfrais * $("#honoraires").val() / 100;
	honoraires = netplusfrais * $("#honoraires").val() / 100;
	$("#hono").html(format(honoraires,2," "));
	// CALCUL REMISE MANDATAIRE
	remise = parseFloat($("#net").val())-parseFloat($("#netnet").val());
	if(remise != parseFloat(remise)) remise=0;
	$("#remise").html(format(remise,2," "));
	// CALCUL MONTANT NET MANDATAIRE
	netmandataire = parseFloat(honoraires)+parseFloat(remise);
	$("#netmandataire").html(format(netmandataire,2," "));
	// CALCUL TVA MANDATAIRE
	$("#tvamandataire").html(format(netmandataire*0.196,2," "));
	// CALCUL TTC MANDATAIRE
	$("#ttcmandataire").html(format(netmandataire*1.196,2," "));
}


$(document).ready(function(){
	 $("#purchase").hide();
	$(".multiselect").multiselect();
	reinitialiser();
	$("#fraisvis").hide();
	calculregie($('#spot').val(), $('#netnet').val(), $('#honoraires').val());
	// Si client déja préselectionné : 
	var idclient = $("#selectclient").val();
	if(idclient!=""){
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/selection-produits.php",
			data: "identifiant="+idclient,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("#selectproduit").html(data);
			}
		});
	}
	if($("#selectgroupe").val()>0){
		$(".spangroupe").html($("#selectgroupe option:selected").text());
		if($("#fraisantenne").html()!="0.00"){
			$("#fraisvis").show();
		}
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
				// Je réinitialise les champs de mon formulaire a zéro :
				document.nouveauclient.libelle.value="";
				document.nouveauclient.adresse.value="";
				document.nouveauclient.style.display="none";
				$("#light").prepend("<p id='confirmation'>Ajout bien effectué.</p><br/>");
				selectclient();
			}
		});
		return false; 
	});	
	
	
	//Ajout d'une régie ;
	$("input#ajreg").click( function(){
		var identifiant=document.nouvelle_regie.identifiant.value;
		var libelle=document.nouvelle_regie.libelle.value;
		var frais=document.nouvelle_regie.frais_regie.value;
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/ajout-grouperegie.php",
			data: "libelle="+libelle+"&frais="+frais+"&identifiant="+identifiant+"&select=1",
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("#selectgroupe").html(data);
				//document.factu.client.value=document.nouvelle_regie.libelle.value;
				// Ci-dessous je selectionne ma nouvelle valeur dans mon menu déroulant :
				for (var i=0; i<document.factu.groupe.options.length; i++) {
					if(document.factu.groupe.options[i].text==libelle){
						document.factu.groupe.options[i].selected = true;
					}
				}
				// Je réinitialise les champs de mon formulaire a zéro :
				document.nouvelle_regie.libelle.value="";
				document.nouvelle_regie.frais_regie.value="";
				document.nouvelle_regie.style.display="none";
				$("#light2").prepend("<p id='confirmation2'>Ajout bien effectué.</p><br/>");
				selectgroupe();
			}
		});
		return false; 
	});	
	
	function selectclient(){
		var idclient = $("select#selectclient").val();
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/selection-produits.php",
			data: "identifiant="+idclient,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("#selectproduit").html(data);
			}
		});
	}
	function selectproduit(){
		var idproduit= $("select#selectproduit").val();
		if(idproduit==0){
			$("#purchase").show();
		}else $("#purchase").hide();
	}
	
	function selectgroupe(){
		var idgroupe = $("select#selectgroupe").val();
		$.ajax({
			type: "POST",
			processData: true,
			url: "traitements/selection-supports.php",
			data: "identifiant="+idgroupe,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("#selectsupport").html(data);
				$(".multiselect").multiselect('destroy');
				$(".multiselect").multiselect();
				$.ajax({
					type: "POST",
					processData: true,
					url: "traitements/selection-frais-antenne.php",
					data: "identifiant="+idgroupe,
					error:function(msg){ alert( "Error !: " + msg ); },
					success:function(data){
						if((data!="0.00") && (data!="")){
							$("#fraisvis").show();
							$("#fraisantenne").html(data);
						}else{
							$("#fraisvis").hide();
							$("#fraisantenne").html("0");
						}
						calculregie($('#spot').val(), $('#netnet').val(), $('#honoraires').val());
					}
				});

			}
		});
		var mongroupe = $("select#selectgroupe option:selected").text();
		if (idgroupe!="") $("span.spangroupe").text(mongroupe);
		else $("span.spangroupe").text("");	
	
	
	}
	
	
	$("select#selectclient").change(function() {
		 selectclient();
	});
	$("select#selectgroupe").change(function() {
		selectgroupe();
	});
	$("select#selectproduit").change(function() {
		selectproduit();
	});
	$("a#ajproduit").click( function(){
		var idclient = $("#selectclient").val();
		if($("#selectclient").val()!=""){
			produit = prompt("Veuillez saisir le nom du nouveau produit (pour le client sélectionné)", "");
			if((produit!="")&&(produit!=null)){
				$.ajax({
					type: "POST",
					processData: true,
					url: "traitements/selection-produits.php",
					data: "identifiant="+idclient+"&libelle="+produit,
					error:function(msg){ alert( "Error !: " + msg ); },
					success:function(data){
						$("#selectproduit").html(data);
						// Ci-dessous je selectionne ma nouvelle valeur dans mon menu déroulant :
						for (var i=0; i<document.getElementById('selectproduit').options.length; i++) {
							if(document.getElementById('selectproduit').options[i].text==produit){
								document.getElementById('selectproduit').options[i].selected = true;
							}
						}
					}
				});	
			}
		}else alert("Veuillez sélectionner un client pour pouvoir lui associer un produit.");
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
	$("a#ajouter_regie").click( function(){
		document.nouvelle_regie.identifiant.value="";
		document.nouvelle_regie.libelle.value="";
		document.nouvelle_regie.frais_regie.value="";
		document.getElementById('light2').style.display='block';
		document.getElementById('fade2').style.display='block';
		$("p#confirmation2").remove();
		document.nouvelle_regie.style.display="block";
	})
	
	
	
	$("a#validerproduit").click( function(){
		if(verif_formulaire()==true){
			if($("#identifiant").length){
				document.factu.multi.value="1";
				document.factu.submit();
			}else{
				document.factu.multi.value="1";
				if(confirm("En ajoutant un produit, votre ordre de facturation sera automatique enregistré en mode brouillon. Confirmez-vous cette demande ?")){
					document.factu.submit();
				}
			}
		}
	});
	
	$("input#envoi").click( function(){
		if(verif_formulaire()==true){
			if(confirm("Êtes-vous sûr de vouloir envoyer votre demande ?")){
				document.factu.archive.value="1";
				document.factu.submit();
			}else return false;
		}
	});
	
	$("input#brouillon").click( function(){
			if(verif_formulaire()==true){
				document.factu.submit();
			}
	});
	
	
	$('.date-pick').datePicker({startDate:'01/01/2010'})
	//$('.date-pick').datePicker({startDate:'01/01/1996'});

	
	$('#start-date').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#end-date').dpSetStartDate(d.addDays(1).asString());
			}
		}
	);
	$('#end-date').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#start-date').dpSetEndDate(d.addDays(-1).asString());
			}
		}
	);
	
	$("a#ajsupport").click( function(){
		var idgroupe = $("select#selectgroupe").val();
		if($("select#selectgroupe").val()!=""){
			support = prompt("Veuillez saisir le nom du nouveau support (pour le groupe de régies sélectionné)", "");
			if((support!="")&&(support!=null)){
				$.ajax({
					type: "POST",
					processData: true,
					url: "traitements/selection-supports.php",
					data: "identifiant="+idgroupe+"&libelle="+support,
					error:function(msg){ alert( "Error !: " + msg ); },
					success:function(data){
						var str = new Array();
						$("#selectsupport option:selected").each(function () {
							str.push($(this).text());
						});
						
						// Ci-dessous je selectionne ma nouvelle valeur dans mon menu déroulant :
						str.push(support);
						
						
						$(".multiselect").multiselect('destroy');
						$("#selectsupport").html(data);
						for (var i=0; i<document.getElementById("selectsupport").options.length; i++) {
							if(str.in_array(document.getElementById("selectsupport").options[i].text)){
								$("#selectsupport option[value='"+document.getElementById("selectsupport").options[i].value+"']").attr("selected","selected");
							}
						}
						$(".multiselect").multiselect();
					}
				});	
			}
		}else alert("Veuillez sélectionner un groupe de régies pour pouvoir lui associer un support.");
	});
	$('#spot').bind('keyup', function() { 
		check(document.factu.spot,"sans");
		calculregie($('#spot').val(), $('#netnet').val(), $('#honoraires').val());
	});
	$('#net').bind('keyup', function() { 
		check(document.factu.net,"");
		calculregie($('#spot').val(), $('#netnet').val(), $('#honoraires').val());
	});
	$('#netnet').bind('keyup', function() { 
		check(document.factu.netnet,"");
		calculregie($('#spot').val(), $('#netnet').val(), $('#honoraires').val());
	});
	$('#honoraires').bind('keyup', function() { 
		check(document.factu.honoraires,"sans");
		calculregie($('#spot').val(), $('#netnet').val(), $('#honoraires').val());
	});
	$('#frais_regie').bind('keyup', function(){
		check(document.nouvelle_regie.frais_regie,"");
	});
});
