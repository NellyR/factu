// Scripts utilisés pour les différentes fonctionnalités  de la page liste-demandes.php
// réduit la liste par rapport au champ de recherche

function reinitialiser(){
	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	$(".stripeMe tr:even").addClass("alt");
	$(".suppr").click( function(){ supprim($(this).attr("id")) });
	/*document.getElementById('fade').style.height=document.body.offsetHeight+'px';
	document.getElementById('fade').style.width=document.body.offsetWidth+'px';
	// Pour IE si la page fait moins que la hauteur de la fenêtre :
	if(document.documentElement.clientHeight > document.body.offsetHeight){ 
		document.getElementById('fade').style.height=document.documentElement.clientHeight;
	}*/
}
$(document).ready(function(){
	reinitialiser();
});
// supprime un ordre de facturation de la liste
function supprim(id){
	//var page= $("#nopage").html()-1;
	if(confirm("Êtes-vous sûr de vouloir supprimer cet ordre de facturation ?")){
		$.ajax({
			type: "GET",
			processData: true,
			url: "traitements/supprim-ordre.php",
			data: "id="+id,
			//+"&page="+page,
			error:function(msg){ alert( "Error !: " + msg ); },
			success:function(data){
				$("table#liste1 tbody").html(data);
				reinitialiser();
			}
		});
	}
	return false;
}