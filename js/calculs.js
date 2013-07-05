// JavaScript Document

document.factu.plus.value="";
function check(champ){
	reg = new RegExp('^[\-]{0,1}+[0-9.]+', 'g');
	valeur = champ.value;
	if(reg.test(valeur)){
		//champ.value=champ.value.replace(/[^0-9.]+/, '');
		champ.value=champ.value.replace(/[\-]{0,1}+[0-9.]+/, '');
		alert("bou");
	}else return true;
}
function calcul(numligne){
	marge="marge"+numligne; montant="montant"+numligne; frais="frais"+numligne;
	document.factu.elements[marge].value= Math.round (((document.factu.elements[montant].value)-(document.factu.elements[frais].value))*100)/100;
	document.factu.montanttotal.value="";
	document.factu.margetotale.value="";
	for(i=1; i<=document.factu.lignes.value; i++){
		marge="marge"+i; montant="montant"+i; frais="frais"+i;
		document.factu.montanttotal.value=Number(document.factu.montanttotal.value)+Number(document.factu.elements[montant].value);
		document.factu.margetotale.value=Number(document.factu.margetotale.value)+Number(document.factu.elements[marge].value);
	}
}
function ajoutligne(){
	document.factu.lignes.value=Number(document.factu.lignes.value)+1;
	document.factu.plus.value="oui";
	document.factu.subsmit();
}