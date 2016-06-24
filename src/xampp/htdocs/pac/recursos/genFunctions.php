<?

// cuando se busca mediante un buscador una 
// cadena con el carácter " o \, luego no se 
// muestra bien en el campo type="text".

function convTxt ($txt) {
	$a=array("Ã", "Ã‰", "Ã", "Ã“", "Ãš", "Ã¡", "Ã©", "Ã­", "Ã³", "Ãº", "Â¿", "Ã±", "Ã¼", "Ãœ");
	$b=array("Á",  "É",  "Í",  "Ó",  "Ú",  "á",  "é",  "í",  "ó",  "ú",  "¿",  "ñ",  "ü",  "Ü");
	return str_replace($a,$b,$txt);
	}

function txtParaInput ($txt) {	
	$txtv2=str_replace("\\'","'",convTxt($txt));
	$txtv2=str_replace("\\","",$txtv2);
	$txtv2=str_replace("\"","''",$txtv2);
	return $txtv2;	
}

function txtParaGuardar ($txt) {	
	$txt=str_replace("\\\"","\\'\\'",convTxt($txt));
	return $txt;	
}

// elimina de una lista de valores ($lista) separados por la cadena $s 
// el valor $valor. Devuelve lista sin el valor que queremos quitar.

function quitarDeLista($lista,$valor,$s=",") {
	$aux="##".str_replace($s,"####",$lista)."##";
	$aux=str_replace("##".$valor."##","",$aux);
	return str_replace("##","",str_replace("####",$s,$aux));
}
function valorEnArray($v,$ar){
	if(array_search($v,$ar)===false) return false;
	else return true;
}
function valorEnLista($lista,$valor,$s=","){
	$aux="##".str_replace($s,"####",$lista)."##";
	if(strpos($aux,"##".$valor."##")===false) return false;
	else return true;
}

function quitarDeArray($ar,$pos,$cuantosQuitar=1) {
	if($pos==0) $a=array_slice($ar,1);
	elseif($pos==count($ar)-1) $a=array_slice($ar,0,-1);
	else $a=array_merge(array_slice($ar,0,$pos),array_slice($ar,$pos+$cuantosQuitar));
	return $a;
	
}



// esta funcion debe utilizarse dentro de un formulario que contenga
// un campo oculto con id="pag" que indica la página a mostrar.

function pintarPaginacion ($_nRes,$_pg,$etiq,$_nResPag=10){
	global $app_rutaWEB;
	if($_nRes>0){
		$_nPags =  floor($_nRes / $_nResPag);
		if($_nRes % $_nResPag!=0) $_nPags++;	
		$x.=	"<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"\">";
		$x.=	"	<tr>";
		$x.=	"  	<td width=\"50%\" align=\"left\" class=\"TxtBold\">N&uacute;mero total de ".$etiq.": ".$_nRes." </td>";
		$x.=	"		<td width=\"50%\" align=\"right\" class=\"TxtAzul\">";
		$x.=	"			<a href=\"#\" onClick=\"document.getElementById('pag').value='0';document.forms[0].submit();\">";
		$x.=	"			<img src=\"".$app_rutaWEB."/html/img/primero.gif\" alt=\"primero\" width=\"13\" height=\"8\" hspace=\"3\" border=\"0\"></a>";	
		$x.=	"			<a href=\"#\" onClick=\"document.getElementById('pag').value='".($_pg==0?0:$_pg-1)."';document.forms[0].submit();\">";
		$x.=	"			<img src=\"".$app_rutaWEB."/html/img/anterior.gif\" alt=\"anterior\" width=\"6\" height=\"8\" hspace=\"3\" border=\"0\"></a>";
		for($_i=0;$_i<$_nPags;$_i++){
			if($_i==$_pg) $_tipo[$_i]="<span class=\"PagActivo\">".($_i+1)."</span>";	//página actual				
			else $_tipo[$_i]="<a href=\"#\" onClick=\"document.getElementById('pag').value='".$_i."';document.forms[0].submit();\"> ".($_i+1)." </a>";
		}
		$x.=  	"|".implode("|",$_tipo)."|";	
		$x.=	"			<a href=\"#\" onClick=\"document.getElementById('pag').value='".($_pg==$_nPags-1?$_nPags-1:$_pg+1)."';document.forms[0].submit();\">";
		$x.=	"			<img src=\"".$app_rutaWEB."/html/img/siguiente.gif\" alt=\"siguiente\" width=\"6\" height=\"8\" hspace=\"3\" border=\"0\"></a>";
		$x.=	"			<a href=\"#\" onClick=\"document.getElementById('pag').value='".($_nPags-1)."';document.forms[0].submit();\">";
		$x.=	"			<img src=\"".$app_rutaWEB."/html/img/ultimo.gif\" alt=\"ultimo\" width=\"13\" height=\"8\" hspace=\"3\" border=\"0\"></a>";
		$x.=	"			</td>";	
		$x.=	"		</td>";
		$x.=	"	</tr>";
		$x.=	"</table>";
		echo $x;
	}
}
function pintaLeyenda(){
	$txt="";
	$txt.="<table><tr>";
	$txt.="<td width=10 class=\"Fila1\">&nbsp;</td><td class=\"txt\">Abierta antes del plazo</td>";
	$txt.="<td width=10 class=\"FilaAmarilla\">&nbsp;</td><td class=\"txt\">Abierta despu&eacute;s del plazo</td>";
	$txt.="<td width=10 class=\"FilaVerde\">&nbsp;</td><td class=\"txt\">Cerrada antes del plazo</td>";
	$txt.="<td width=10 class=\"FilaRoja\">&nbsp;</td><td class=\"txt\">Cerrada despu&eacute;s del plazo</td>";
	$txt.="</tr>";
	$txt.="</table>";
	$txt.="";	
	return $txt;
}
function FlechasOrden($jsSubir,$jsBajar){
	global $app_rutaWEB;
	$txt="<table width=100% heigth=2px><tr heigth=2px><td width=100% align='center'>";
	$txt.="<img src=\"".$app_rutaWEB."/html/img/FlechaAr.gif\" onClick=\"".$jsSubir."\" alt='Subir orden' style=\"cursor: pointer\"><br>";
	$txt.="<img src=\"".$app_rutaWEB."/html/img/FlechaAb.gif\" onClick=\"".$jsBajar."\" alt='Bajar orden' style=\"cursor: pointer\">";
	$txt.="</tr></td></table>";
	return $txt;	
}
function pintarCabeceraAMFE($xls=false){
	$cadena = "";
	$cadena.="<table width='98%' border='0' cellpadding='0' cellspacing='1' class='BordesTabla'>";
	$cadena.="<tr>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Operaci&oacute;n</th>";
	$cadena.="<th width='10%' align='center'  rowspan=2>Modo de fallo</th>";
	$cadena.="<th width='10%' align='center'  rowspan=2>Efecto de fallo</th>";
	$cadena.="<th width='10%' align='center'  rowspan=2>Causa de fallo</th>";
	$cadena.="<th width='14%' align='center' colspan=5>Condiciones Actuales</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Acci&oacute;n recomendada</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Resp. y plazo</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Causa de fallo</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Acci&oacute;n tomada</th>";
	$cadena.="<th width='14%' align='center'  colspan=4>Result. obtenido</th>";			    
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<th width='' align='center'>Controles</th>";
	$cadena.="<th width='' align='center'>OC</th>";
	$cadena.="<th width='' align='center'>GR</th>";
	$cadena.="<th width='' align='center'>DE</th>";
	$cadena.="<th width='' align='center'>N.P.R</th>";
	$cadena.="<th width='0%' align='center'>OC</th>";
	$cadena.="<th width='0%' align='center'>GR</th>";
	$cadena.="<th width='0%' align='center'>DE</th>";
	$cadena.="<th width='0%' align='center'>N.P.R</th>";
	$cadena.="</tr>";
	return $cadena;
}
function pintarCabeceraExportarAMFE($xls=false){
	$st=" STYLE=\"font-size:8.0pt\" ";
	$cadena = "";
	//$cadena.="<TABLE BORDER='1'>";
	$cadena.="<TR>";
	$cadena.="	<TD COLSPAN=9 ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>OPERACI&Oacute;N</b></TD>";
	$cadena.="	<TD COLSPAN=8 ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>MODO DE FALLO</b></TD>";
	$cadena.="	<TD COLSPAN=9 ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>EFECTO DE FALLO</b></TD>";
	$cadena.="	<TD COLSPAN=9 ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>CAUSA DE FALLO</b></TD>";
	$cadena.="	<TD COLSPAN=24 ALIGN=CENTER VALIGN=TO $st><b>CONDICIONES ACTUALES</b></TD>";
	$cadena.="	<TD COLSPAN=11 ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>ACCI&Oacute;N RECOMENDADA</b></TD>";
	$cadena.="	<TD COLSPAN=6  ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>RESP. Y PLAZO</b></TD>";
	$cadena.="	<TD COLSPAN=10 ALIGN=CENTER ROWSPAN=2 VALIGN=TOP $st><b>ACCI&Oacute;N TOMADA</b></TD>";
	$cadena.="	<TD COLSPAN=14 ALIGN=CENTER VALIGN=TOP $st><b>RES. OBTENIDO</b></TD>";			    
	$cadena.="</TR>";	
	$cadena.="<TR>";
	$cadena.="	<TD COLSPAN=10 ALIGN=CENTER $st><b>Controles</b></TD>";
	$cadena.="	<TD COLSPAN=3 ALIGN=CENTER $st><b>OC</b></TD>";
	$cadena.="	<TD COLSPAN=3 ALIGN=CENTER $st><b>GR</b></TD>";
	$cadena.="	<TD COLSPAN=3 ALIGN=CENTER $st><b>DE</b></TD>";
	$cadena.="	<TD COLSPAN=5 ALIGN=CENTER $st><b>N.P.R</b></TD>";
	$cadena.="	<TD COLSPAN=3 ALIGN=CENTER $st><b>OC</b></TD>";
	$cadena.="	<TD COLSPAN=3 ALIGN=CENTER $st><b>GR</b></TD>";
	$cadena.="	<TD COLSPAN=3 ALIGN=CENTER $st><b>DE</b></TD>";
	$cadena.="	<TD COLSPAN=5 ALIGN=CENTER $st><b>N.P.R</b></TD>";
	$cadena.="</TR>";
	return $cadena;
}

function pintarCabeceraAMFE2($xls=false){
	$cadena = "";
	$cadena.="<table width='98%' border='0' cellpadding='0' cellspacing='1' class='BordesTabla'>";
	$cadena.="<tr>";
	$cadena.="<th width='7%' align='center'  rowspan=2>Componente</th>";
	$cadena.="<th width='7%' align='center'  rowspan=2>Operaci&oacute;n</th>";
	$cadena.="<th width='7%' align='center'  rowspan=2>Modo de fallo</th>";
	$cadena.="<th width='7%' align='center'  rowspan=2>Efecto de fallo</th>";
	$cadena.="<th width='7%' align='center'  rowspan=2>Causa de fallo</th>";
	$cadena.="<th width='14%' align='center' colspan=5>Condiciones Actuales</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Acci&oacute;n recomendada</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Resp. y plazo</th>";
	$cadena.="<th width='9%' align='center'  rowspan=2>Acci&oacute;n tomada</th>";
	$cadena.="<th width='7%' align='center'  colspan=4>Result. obtenido</th>";			    
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<th width='' align='center'>Controles</th>";
	$cadena.="<th width='' align='center'>&nbsp;OC&nbsp;</th>";
	$cadena.="<th width='' align='center'>&nbsp;GR&nbsp;</th>";
	$cadena.="<th width='' align='center'>&nbsp;DE&nbsp;</th>";
	$cadena.="<th width='' align='center'>&nbsp;N.P.R&nbsp;</th>";
	$cadena.="<th width='0%' align='center'>&nbsp;OC&nbsp;</th>";
	$cadena.="<th width='0%' align='center'>&nbsp;GR&nbsp;</th>";
	$cadena.="<th width='0%' align='center'>&nbsp;DE&nbsp;</th>";
	$cadena.="<th width='0%' align='center'>&nbsp;N.P.R&nbsp;</th>";
	$cadena.="</tr>";
	return $cadena;
}

function pintarCabeceraAMFEMini($xls=false){
	$cadena = "";
	$cadena.="<table width='95%' border='".($xls?"1":"0")."' cellpadding='0' cellspacing='1' class='BordesTabla'>";
	$cadena.="<tr>";
	$cadena.="<th width='15%' align='center'  rowspan=2>Operaci&oacute;n</th>";
	$cadena.="<th width='15%' align='center'  rowspan=2>Modo de fallo</th>";
	$cadena.="<th width='15%' align='center'  rowspan=2>Efecto de fallo</th>";
	$cadena.="<th width='15%' align='center'  rowspan=2>Causa de fallo</th>";
	$cadena.="<th width='25%' align='center'  colspan=5>Condiciones Actuales</th>";
	$cadena.="<th width='15%' align='center'  rowspan=2>Acci&oacute;n recomendada</th>";
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<th align='center'>Controles</th>";
	$cadena.="<th align='center'>OC</th>";
	$cadena.="<th align='center'>GR</th>";
	$cadena.="<th align='center'>DE</th>";
	$cadena.="<th align='center'>N.P.R</th>";
	$cadena.="</tr>";
	return $cadena;
}
function pintarCabeceraAMFEMini2($xls=false){
	$cadena = "";
	$cadena.="<table width='95%' border='".($xls?"1":"0")."' cellpadding='0' cellspacing='1' class='BordesTabla'>";
	$cadena.="<tr>";
	$cadena.="<th width='11%' align='center'  rowspan=2>Componente</th>";
	$cadena.="<th width='11%' align='center'  rowspan=2>Operaci&oacute;n</th>";
	$cadena.="<th width='11%' align='center'  rowspan=2>Modo de fallo</th>";
	$cadena.="<th width='11%' align='center'  rowspan=2>Efecto de fallo</th>";
	$cadena.="<th width='12%' align='center'  rowspan=2>Causa de fallo</th>";
	$cadena.="<th width='22%' align='center'  colspan=5>Condiciones Actuales</th>";
	$cadena.="<th width='12%' align='center'  rowspan=2>Acci&oacute;n recomendada</th>";
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<th align='center'>Controles</th>";
	$cadena.="<th align='center'>OC</th>";
	$cadena.="<th align='center'>GR</th>";
	$cadena.="<th align='center'>DE</th>";
	$cadena.="<th align='center'>N.P.R</th>";
	$cadena.="</tr>";
	return $cadena;
}


function pintarCabeceraPControl(){
	$cadena = "";
	$cadena.="<table width='95%' border='0' cellpadding='0' cellspacing='1' class='BordesTabla'>";
	$cadena.="<tr>";
	$cadena.="<th width='6%' align='center'  rowspan=3>&nbsp;OPERACIÓN&nbsp;</th>";
	$cadena.="<th width='6%' align='center'  rowspan=3>&nbsp;MÁQUINA&nbsp;</th>";
	$cadena.="<th width='7%' align='center'  colspan=3>&nbsp;CARACTERÍSTICAS</th>";
	$cadena.="<th width='2%' align='center'  rowspan=3>&nbsp;CLASE&nbsp;</th>";
	$cadena.="<th width='20%' align='center'  colspan=5>&nbsp;M&Eacute;TODOS&nbsp;</th>";
	$cadena.="<th width='11%' align='center'  rowspan=3>&nbsp;PLAN DE REACCIÓN&nbsp;</th>";
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<th width='' align='center' rowspan=2>&nbsp;Nº&nbsp;</th>";
	$cadena.="<th width='' align='center' rowspan=2>&nbsp;Prod.&nbsp;</th>";
	$cadena.="<th width='' align='center' rowspan=2>&nbsp;Proc.&nbsp;</th>";
	$cadena.="<th width='' align='center' rowspan=2>&nbsp;Especificaci&oacute;n&nbsp;</th>";
	$cadena.="<th width='' align='center' rowspan=2>&nbsp;Evaluaci&oacute;n&nbsp;</th>";
	$cadena.="<th width='' align='center' colspan=2>&nbsp;Muestra&nbsp;</th>";
	$cadena.="<th width='' align='center' rowspan=2>&nbsp;M&eacute;todo de control&nbsp;</th>";
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<th width='' align='center'>&nbsp;Tam.&nbsp;</th>";
	$cadena.="<th width='' align='center'>&nbsp;Fre.&nbsp;</th>";
	$cadena.="</tr>";
	return $cadena;
}
function pintarCabeceraPControlExportacion(){
	$cadena = "";
	$cadena.="<table border='1'";
	$cadena.="<tr>";
	$cadena.="<th colspan=10 align='center' rowspan=3><b>OPERACIÓN</b></th>";
	$cadena.="<td colspan=8 align='center'  rowspan=3><b>MÁQUINA</b></th>";
	$cadena.="<td colspan=12 align='center'  ><b>CARACTERÍSTICAS</b></th>";
	$cadena.="<td colspan=6 align='center'  rowspan=3><b>CLASE</b></th>";
	$cadena.="<td colspan=40 align='center'  ><b>M&Eacute;TODOS</b></th>";
	$cadena.="<td colspan=24 align='center'  rowspan=3><b>PLAN DE REACCIÓN</b></th>";
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<td colspan=4 align='center' rowspan=2><b>Nº</b></th>";
	$cadena.="<td colspan=4 align='center' rowspan=2><b>Prod.</b></th>";
	$cadena.="<td colspan=4 align='center' rowspan=2><b>Proc.</b></th>";
	$cadena.="<td colspan=10 align='center' rowspan=2><b>Especificaci&oacute;n</b></th>";
	$cadena.="<td colspan=10 align='center' rowspan=2><b>Evaluaci&oacute;n</b></th>";
	$cadena.="<td colspan=10 align='center' ><b>Muestra</b></th>";
	$cadena.="<td colspan=10 align='center' rowspan=2><b>M&eacute;todo de control</b></th>";
	$cadena.="</tr>";	
	$cadena.="<tr>";
	$cadena.="<td colspan=5 align='center'><b>Tam.</b></th>";
	$cadena.="<td colspan=5 align='center'><b>Fre.</b></th>";
	$cadena.="</tr>";
	return $cadena;
}
function pintarPieAMFE(){
	return "</table>";
}
/*
function pintarCabeceraAMFE(){
	$cadena = "";
	$cadena.="<table width='98%' border='0' cellpadding='0' cellspacing='1' class='BordesTabla'>\n";
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='9%' align='center'  rowspan=2>Operaci&oacute;n</th>\n";
	$cadena.="\t\t<th width='10%' align='center'  rowspan=2>Modo de fallo</th>\n";
	$cadena.="\t\t<th width='10%' align='center'  rowspan=2>Efecto de fallo</th>\n";
	$cadena.="\t\t<th width='10%' align='center'  rowspan=2>Causa de fallo</th>\n";
	$cadena.="\t\t<th width='14%' align='center' colspan=5>Condiciones Actuales</th>\n";
	$cadena.="\t\t<th width='9%' align='center'  rowspan=2>Acci&oacute;n recomendada</th>\n";
	$cadena.="\t\t<th width='9%' align='center'  rowspan=2>Resp. y plazo</th>\n";
	$cadena.="\t\t<th width='9%' align='center'  rowspan=2>Causa de fallo</th>\n";
	$cadena.="\t\t<th width='9%' align='center'  rowspan=2>Acci&oacute;n tomada</th>\n";
	$cadena.="\t\t<th width='14%' align='center'  colspan=4>Result. obtenido</th>\n";			    
	$cadena.="\t</tr>\n";	
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='' align='center'>Controles</th>\n";
	$cadena.="\t\t<th width='' align='center'>OC</th>\n";
	$cadena.="\t\t<th width='' align='center'>GR</th>\n";
	$cadena.="\t\t<th width='' align='center'>DE</th>\n";
	$cadena.="\t\t<th width='' align='center'>N.P.R</th>\n";
	$cadena.="\t\t<th width='0%' align='center'>OC</th>\n";
	$cadena.="\t\t<th width='0%' align='center'>GR</th>\n";
	$cadena.="\t\t<th width='0%' align='center'>DE</th>\n";
	$cadena.="\t\t<th width='0%' align='center'>N.P.R</th>\n";
	$cadena.="\t</tr>\n";
	return $cadena;
}
function pintarCabeceraAMFEMini(){
	$cadena = "";
	$cadena.="<table width='95%' border='0' cellpadding='0' cellspacing='1' class='BordesTabla'>\n";
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='15%' align='center'  rowspan=2>Operaci&oacute;n</th>\n";
	$cadena.="\t\t<th width='15%' align='center'  rowspan=2>Modo de fallo</th>\n";
	$cadena.="\t\t<th width='15%' align='center'  rowspan=2>Efecto de fallo</th>\n";
	$cadena.="\t\t<th width='15%' align='center'  rowspan=2>Causa de fallo</th>\n";
	$cadena.="\t\t<th width='25%' align='center'  colspan=5>Condiciones Actuales</th>\n";
	$cadena.="\t\t<th width='15%' align='center'  rowspan=2>Acci&oacute;n recomendada</th>\n";
	$cadena.="\t</tr>\n";	
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='' align='center'>Controles</th>\n";
	$cadena.="\t\t<th width='' align='center'>OC</th>\n";
	$cadena.="\t\t<th width='' align='center'>GR</th>\n";
	$cadena.="\t\t<th width='' align='center'>DE</th>\n";
	$cadena.="\t\t<th width='' align='center'>N.P.R</th>\n";
	$cadena.="\t</tr>\n";
	return $cadena;
}
function pintarCabeceraPControl(){
	$cadena = "";
	$cadena.="<table width='95%' border='0' cellpadding='0' cellspacing='1' class='BordesTabla'>\n";
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='6%' align='center'  rowspan=3>&nbsp;MÁQUINA&nbsp;</th>\n";
	$cadena.="\t\t<th width='8%' align='center'  colspan=3>&nbsp;CARACTERÍSTICAS</th>\n";
	$cadena.="\t\t<th width='7%' align='center'  rowspan=3>&nbsp;CLASE&nbsp;</th>\n";
	$cadena.="\t\t<th width='20%' align='center'  colspan=5>&nbsp;MÉTODOS&nbsp;</th>\n";
	$cadena.="\t\t<th width='11%' align='center'  rowspan=3>&nbsp;PLAN DE REACCIÓN&nbsp;</th>\n";
	$cadena.="\t</tr>\n";	
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='' align='center' rowspan=2>&nbsp;Nº&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center' rowspan=2>&nbsp;Prod.&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center' rowspan=2>&nbsp;Proc.&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center' rowspan=2>&nbsp;Especificaci&oacute;n&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center' rowspan=2>&nbsp;Evaluaci&oacute;n&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center' colspan=2>&nbsp;Muestra&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center' rowspan=2>&nbsp;M&eacute;todo de control&nbsp;</th>\n";
	$cadena.="\t</tr>\n";	
	$cadena.="\t<tr>\n";
	$cadena.="\t\t<th width='' align='center'>&nbsp;Tam.&nbsp;</th>\n";
	$cadena.="\t\t<th width='' align='center'>&nbsp;Fre.&nbsp;</th>\n";
	$cadena.="\t</tr>\n";
	return $cadena;
}
function pintarPieAMFE(){
	return "</table>";
}*/
//imprime x espacios "&nbsp;"
function printEspacios ($cuantos=4){
	for($i=0;$i<$cuantos;$i++) echo "&nbsp;";
}
//devulevle cadena de $n caracteres con el contenido de $v
function printEn($n,$v,$html=false){
	$t=strlen($v);
	if($t>$n) return "NO ENTRA";
	else {
		$c="";
		$d=$html?"&nbsp;":" ";
		for($i=0;$i<($n-$t);$i++) $c.=$d;
		return $v.$c;
	}
}

//
// Funciones realcionadas con fechas
//

function muestraFecha ($fecha){
	$dat="";
	if ($fecha!="0000-00-00" && $fecha!="") {
		$dia=strlen(getDia($fecha))==1?"0".getDia($fecha):getDia($fecha);
		$mes=strlen(getMes($fecha))==1?"0".getMes($fecha):getMes($fecha);
		$dat=$dia."/".$mes."/".getAnio($fecha);		
	}
	return $dat;	
}
function fechaBD($fecha) {
	$vFin=$fecha;
	if ($fecha=="0000-00-00") $vFin="";
	elseif (ereg("([0-9]{2})[-/]([0-9]{2})[-/]([0-9]{4})",$fecha,$res)) $vFin="$res[3]-$res[2]-$res[1]";
	elseif (ereg("([0-9]{4})[-/]([0-9]{2})[-/]([0-9]{2})",$fecha,$res)) $vFin="$res[1]-$res[2]-$res[3]"; 
	return $vFin;	
}
function dateDiff ($f1,$f2,$incluyendo=true) {	
	$days = ( strtotime(fechaBD($f2)) - strtotime(fechaBD($f1)) )  / 86400 + ($incluyendo?1:0);
	return $days;
}
function getAnio ($fecha) {
	$pr=explode("-",fechaBD($fecha));
	return $pr[0];
}
function getMes ($fecha) {
	$pr=explode("-",fechaBD($fecha));
	return $pr[1];
}
function getDia ($fecha) {
	$pr=explode("-",fechaBD($fecha));
	return $pr[2];
}


// Funciones para Base de datos


function obtenerSigId($tabla,$campoId){
	$sql="SELECT if(Max(".$campoId.") is null,1,Max(".$campoId.")+1) FROM ".$tabla;
	$res=mysql_query($sql);
	$row=mysql_fetch_row($res);
	return $row[0];
}

function obtenerTabla($tabla,$demas=""){
	$sql="SELECT * FROM ".$tabla." ".$demas;
	$res=mysql_query($sql);
	$i=0;
	if($row=mysql_fetch_row($res)) do{
		$j=0;
		while(isset($row[$j])) $a[$i][$j]=$row[$j++];
		$i++;
	}while($row=mysql_fetch_row($res));
	return $a;
}
function existeValorTabla($nombreTabla,$nombreCampo,$valor){
	$sql="SELECT count(*) FROM ".$nombreTabla." WHERE ".$nombreCampo."='".$valor."' ";
	$res=mysql_query($sql);
	$row=mysql_fetch_row($res);
	return $row[0];
}


// ****************************************
// Funciones específicas para abrir popUps

function JSventanaSeleccion ($tipo,$demas=""){
	global $app_rutaWEB;
	return "window.open('".$app_rutaWEB."/recursos/vSeleccion.php?tipo=".$tipo."&".$demas."','vSeleccion','height=460,width=500,status=no,toolbar=no,menubar=no,location=no')";
}
function JSventanaCliente ($demas=""){
	global $app_rutaWEB;
	return "window.open('".$app_rutaWEB."/recursos/vSeleccion.php?tipo=cliente&".$demas."','vSeleccion','height=460,width=500,status=no,toolbar=no,menubar=no,location=no')";
}
function JSventanaArchivo ($tipo,$demas=""){
	global $app_rutaWEB;
	return "window.open('".$app_rutaWEB."/recursos/fileUpload.php?tipo=".$tipo."&".$demas."','','height=140,width=460,status=no,toolbar=no,menubar=no,location=no')";
}
function JSventanaActividad ($params=""){
	global $app_rutaWEB;
	return "window.open('".$app_rutaWEB."/planificacion/actividad.php?".$params."','','height=460,width=500,status=no,toolbar=no,menubar=no,location=no')";
}
function JSventanaCalendario($nombreFuncion,$dia,$mes,$anyo){
	global $app_rutaWEB;
	return "window.open('".$app_rutaWEB."/recursos/calendario.php?funcionJS=".$nombreFuncion."&dia=".$dia."&mes=".$mes."&ano=".$anyo."','calendar','height=200,width=230,status=no,toolbar=no,menubar=no,location=no')";
}


/******************************************************************************************************/
/* Funciones para escapar las comillas dobles y simples y poder enviar datos por POST sin preocuparse */

function serialize_esp($o){
	$ob=cambioCaracteresExtranios($o,false);
	return serialize($ob);
}
function unserialize_esp($o){
	$ob=unserialize(str_replace("\\","",$o));
	$obf=cambioCaracteresExtranios($ob,true);
	return $obf;
}
function cambioCaracteresExtranios($mix,$sentido){
	if(is_object($mix)) foreach($mix as $k=>$v) $mix->$k=cambioCaracteresExtranios($v,$sentido);
	elseif(is_array($mix)) foreach($mix as $k=>$v) $mix[$k]=cambioCaracteresExtranios($v,$sentido);
	elseif(!is_bool($mix) && !$mix==null && strlen($mix)>0) $mix=cambioCadena($mix,$sentido);
	return $mix;	
}
function cambioCadena($cad,$i){
	$cadenaParaLasDoblesComillas="[---::DoBlEs::---]";
	$cadenaParaLasSimplesComillas="[---::SiMpLeS::---]";
	if($i){
		$cad2=str_replace($cadenaParaLasDoblesComillas,"''",$cad); 	//ya aprovecho para sustituir " por ''
		$cad2=str_replace($cadenaParaLasSimplesComillas,"'",$cad2);
	}else{
		$cad2=str_replace("\"",$cadenaParaLasDoblesComillas,$cad);
		$cad2=str_replace("'",$cadenaParaLasSimplesComillas,$cad2);
	}
	return $cad2;
}


/******************************************************************************************************/

function obtenerRedir($txt){
	$p=explode("_",$txt);
	switch ($p[0]){
		/*
		case "ref":	 $ret="../maestros/me_referencias_edit.php?id=".$p[1]; 	break;
		case "cli":	 $ret="../maestros/me_clientes_edit.php?id=".$p[1]; 	break;
		case "comp": $ret="../maestros/me_componentes_edit.php?id=".$p[1]; 	break;
		case "op":	 $ret="../maestros/me_operaciones_edit.php?id=".$p[1]; 	break;
		case "md":	 $ret="../maestros/me_modos_edit.php?id=".$p[1]; 		break;
		*/
		default:
			$aux=str_replace("_edit","",$_SERVER['REQUEST_URI']);	 
			$ret=strpos($aux,"?")===false?$aux:substr($aux,0,strpos($aux,"?"));
	}
	return $ret;
}

function guardaScrolls(){ 	// para usar esta función hay que tener un campo hidden con id->scrollPosicion y name->scrollPosicion
							// solo funciona si se mete a pelo, antes del template.
	$todo="".
	"<script>".
	"window.onload=function restoreScroll(){".
	"	window.scrollTo(0,".($_POST["scrollPosicion"]==""?"0":$_POST["scrollPosicion"]).");".
	"	SaveScrollPositions();".
	"};".
	"function SaveScrollPositions() {".
	"	document.getElementById('scrollPosicion').value = document.pageYOffset ? document.pageYOffset : document.body.scrollTop;  ".
	"	setTimeout('SaveScrollPositions()', 10);".
	"}".
	"</script>";
	return $todo;
}



?>