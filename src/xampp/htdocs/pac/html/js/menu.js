

/***********************************************
* AnyLink Drop Down Menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

//Contents for menu 1
/*
var menu1=new Array()
menu1[0]='<a href="/pac/planificacion/pl_datos.php">Datos de la planificación	</a>'
menu1[1]='<a href="/pac/planificacion/pl_etapas.php">Planificación de etapas</a>'
menu1[2]='<a href="/pac/planificacion/pl_estudio.php">Estudio de factibilidad</a>'
menu1[3]='<a href="/pac/planificacion/pl_hoja.php">Hoja de ruta / Sinóptico</a>'
menu1[4]='<a href="/pac/planificacion/pl_amfe.php">AMFE</a>'
menu1[5]='<a href="/pac/planificacion/pl_planes.php">Planes de Control</a>'
*/
//Contents for menu 2, and so on
var menu2=new Array()
menu2[0]='<a href="/pac/maestros/me_clientes.php">Clientes</a>'
menu2[1]='<a href="/pac/maestros/me_referencias.php">Referencias</a>'
menu2[2]='<a href="/pac/maestros/me_componentes.php">Componentes</a>'
menu2[3]='<a href="/pac/maestros/me_operaciones.php">Operaciones</a>'
menu2[4]='<a href="/pac/maestros/me_modos.php">Modos de Fallo</a>'
menu2[5]='<a href="/pac/maestros/me_efectos.php">Efectos de Fallo</a>'
menu2[6]='<a href="/pac/maestros/me_causas.php">Causas de Fallo</a>'
		

var disappeardelay=600  //menu disappear speed onMouseout (in miliseconds)
var hidemenu_onclick="yes" //hide menu when user clicks within menu?


// valida valores OBLIGATORIOS DE INTRODUCIR enviados desde un formulaio de la siguiente forma:
// pasar a la función un array y cada registro del array -> "tipo"::"valor"::"error vacío"::"error mal introducido"



function JSvFormObligatorios(a) {
	erTxt="";
	for(i=0;i<a.length;i++){
		z=a[i].split("::");
		switch(z[0]){
			case "password":
				if (z[1]=="") erTxt+="- Introduzca una contraseña\n";
				else if(z[1]!=z[2]) erTxt+="- Las contraseñas introducidas no coinciden\n";
				break;	
			case "text":
				if (z[1]=="") erTxt+="- "+z[2]+"\n";
				break;				
			case "int":
				if(z[1]=="") erTxt+="- "+z[2]+"\n";
				else { 
					if(isNaN(z[1])) erTxt+="- "+z[3]+"\n";   
					else if((" "+z[1]).indexOf(".")>0) erTxt+="- "+z[3]+"\n"; 
				}
				break;
			case "float":
					if(z[1]=="") erTxt+="- "+z[2]+"\n";
					else if(isNaN(z[1])) erTxt+="- "+z[3]+"\n";
				break;
			default: 
				erTxt+="- [ERROR] validación del campo fallida\n";
		} 
	}
	return erTxt;
}

// valida valores enviados desde un formulaio de la siguiente forma:
// pasar a la función un array y cada registro del array -> "tipo"::"valor"::"error mal introducido"

function JSvForm(a) {
	erTxt="";
	for(i=0;i<a.length;i++){
		z=a[i].split("::");
		switch(z[0]){
			case "int":
				if(isNaN(z[1])) erTxt+="- "+z[2]+"\n";   
				else if((" "+z[1]).indexOf(".")>0) erTxt+="- "+z[2]+"\n"; 
				break;
			case "float":
				if(isNaN(z[1])) erTxt+="- "+z[2]+"\n";
				break;
			case "date":
				
				break;
			default: 
				erTxt+="- [ERROR] validación del campo fallida\n";
		} 
	}
	return erTxt;
}










//document.forms[0].onSubmit=SaveScrollPositions();
/*
function SaveScrollPositions() {   
	document.forms[0].StaticPostBackScrollVerticalPosition.value = (navigator.appName == 'Netscape') ? document.pageYOffset : document.body.scrollTop;  
	setTimeout('SaveScrollPositions()', 10);
}
SaveScrollPositions();
function RestoreScrollPosition(pos) {   
	window.scrollTo(0, pos); // determined during run-time
}
window.onLoad=RestoreScrollPosition(document.forms[0].StaticPostBackScrollVerticalPosition.value);










*/
/*
var __oScrollPos;
window.onload=InitScrollPos;
function InitScrollPos(){	
	__oScrollPos = document.all['__SCROLLPOSITIONS'];
	if (__oScrollPos!=undefined) {
		LoadScrollPos();
		basePostBack = __doPostBack;
		__doPostBack = MyPostBack;
		document.forms[0].onsubmit = MyPostBackFrm();
	}
}
function MyPostBackFrm(){
	SaveScrollPos();
	return false;
	//document.forms[0].submit();
}
function MyPostBack(eventTarget, eventArgument){
	SaveScrollPos();
	basePostBack(eventTarget, eventArgument);
}
function SaveScrollPos(){
	var oNodeList = document.body.getElementsByTagName('DIV');
	var sPos = '';
	for (i=0;i<oNodeList.length;i++){
		oDiv = oNodeList[i];
		if ((oDiv.scrollTop>0)||(oDiv.scrollLeft>0)) {
			if (sPos.length>0) sPos = sPos + ',';
			sPos=sPos + oDiv.id + ':' + oDiv.scrollTop + '#' + oDiv.scrollLeft;
		}
	}
	__oScrollPos.value=sPos;
}
function LoadScrollPos(){
	if (__oScrollPos.value=='') return;
	var sPos = new String();
	sPos = __oScrollPos.value
	sItems = sPos.split(',');
	for (i=0;i<sItems.length;i++){
		var sItem = new String();
		sItem = sItems[i];
		var iSplit = sItem.indexOf(":",0);
		sDiv = sItem.substring(0,iSplit);
		sPos = sItem.substring(iSplit+1,sItem.length); 
		try {
			document.all[sDiv].scrollTop=sPos.substring(0,sPos.indexOf('#'));
			document.all[sDiv].scrollLeft=sPos.substring(sPos.indexOf('#') + 1,sPos.length);
		} catch(e) {  }
	}
}
*/


//--------------------------------------- CHURRO DREAMWEAVER PARA EL MENÚ (no tocar) ---------------------------------------//
/////No further editting needed
var ie4=document.all
var ns6=document.getElementById&&!document.all
if (ie4||ns6)
document.write('<div id="dropmenudiv" style="visibility:hidden;" onMouseover="clearhidemenu()" onMouseout="dynamichide(event)"></div>')
function getposOffset(what, offsettype){
var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
var parentEl=what.offsetParent;
while (parentEl!=null){
totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;}
return totaloffset;}
function showhide(obj, e, visible, hidden, menuwidth){
if (ie4||ns6)
dropmenuobj.style.left=dropmenuobj.style.top="-550px"
if (menuwidth!=""){
dropmenuobj.widthobj=dropmenuobj.style
dropmenuobj.widthobj.width=menuwidth}
if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
obj.visibility=visible
else if (e.type=="click")
obj.visibility=hidden}
function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body}
function clearbrowseredge(obj, whichedge){
var edgeoffset=0
if (whichedge=="rightedge"){
var windowedge=ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15
dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth}else{
var topedge=ie4 && !window.opera? iecompattest().scrollTop : window.pageYOffset
var windowedge=ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure){ //move up?
edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight
if ((dropmenuobj.y-topedge)<dropmenuobj.contentmeasure) //up no good either?
edgeoffset=dropmenuobj.y+obj.offsetHeight-topedge}}
return edgeoffset}
function populatemenu(what){
if (ie4||ns6)
dropmenuobj.innerHTML=what.join("")}
function dropdownmenu(obj, e, menucontents, menuwidth){
if (window.event) event.cancelBubble=true
else if (e.stopPropagation) e.stopPropagation()
clearhidemenu()
dropmenuobj=document.getElementById? document.getElementById("dropmenudiv") : dropmenudiv
populatemenu(menucontents)
if (ie4||ns6){
showhide(dropmenuobj.style, e, "visible", "hidden", menuwidth)
dropmenuobj.x=getposOffset(obj, "left")
dropmenuobj.y=getposOffset(obj, "top")
dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px"
dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px"}
return clickreturnvalue()}
function clickreturnvalue(){
if (ie4||ns6) return false
else return true}
function contains_ns6(a, b) {
while (b.parentNode)
if ((b = b.parentNode) == a)return true;
return false;}
function dynamichide(e){
if (ie4&&!dropmenuobj.contains(e.toElement))
delayhidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
delayhidemenu()}
function hidemenu(e){
if (typeof dropmenuobj!="undefined"){
if (ie4||ns6)
dropmenuobj.style.visibility="hidden"}}
function delayhidemenu(){
if (ie4||ns6)
delayhide=setTimeout("hidemenu()",disappeardelay)}
function clearhidemenu(){
if (typeof delayhide!="undefined")
clearTimeout(delayhide)}
if (hidemenu_onclick=="yes")
document.onclick=hidemenu

