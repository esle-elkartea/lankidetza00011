<?

include "../recursos/conex.php";
include "../recursos/genFunctions.php";
include "../recursos/configuracion.php";
include "../recursos/class/Planificacion.class.php";

global $app_logoEmpresa;
global $app_extensionExportaciones;
header("Content-Disposition: attachment; filename=".date("Ymd")."_PlanControl.".$app_extensionExportaciones);


/**********************************************************************************************/
function cabeceraExportacionHorizontal($titulo,$fecha,$rutaLogo,$valsCabecera="",$vals2="",$vals3="",$anchoTotal=900){
	$sF7="font-size:7.0pt;";
	$sF8="font-size:8.0pt;";
	$bR="border-top:0.5pt solid windowtext;";
	$bB="border-bottom:0.5pt solid windowtext;";
	$bI="border-left:0.5pt solid windowtext;";
	$bD="border-right:0.5pt solid windowtext;";
	$st1=" STYLE=\"".$sF7.$bR.$bB.$bI.$bD."\" ";
	$st2=" STYLE=\"".$sF8.$bR.$bB.$bI.$bD."\" ";
	$bordes=" STYLE=\"".$bR.$bB.$bI.$bD."\" ";
	$c="";
	$c.="<TABLE BORDER=0 WIDTH=".$anchoTotal.">\n";
	$c.="	<TR HEIGHT=75>\n";
	$c.="		<TD  COLSPAN=13 VALIGN=TOP ALIGN=CENTER WIDTH=".round(13*$anchoTotal/100)." $st2><IMG SRC='".$rutaLogo."' BORDER=0></TD>\n";
	$c.="		<TD COLSPAN=72 VALIGN=TOP WIDTH=".round(72*$anchoTotal/100)." ALIGN=CENTER $bordes><H1>".strToUpper($titulo)."</H1></TD>\n";
	$c.="		<TD COLSPAN=15 VALIGN=TOP WIDTH=".round(15*$anchoTotal/100)."$st2><b>Fecha: ".muestraFecha($fecha)."</b></TD>\n";
	$c.="	</TR>\n";
	if(is_array($valsCabecera)){
		$cuantos=count($valsCabecera);
		if($cuantos>0){
			$c.="<TR>\n";
			$c.="	<TD COLSPAN=40 WIDTH=".round(40*$anchoTotal/100)." BORDER=1 STYLE=\"".$sF8.$bI.$bD."\"><BR>\n";
			foreach($valsCabecera as $key=>$val) $c.="&nbsp;&nbsp;<B>".$key.":</B>&nbsp;".$val."<BR>\n";
			$c.="	&nbsp;</TD>\n";
			$c.="	<TD COLSPAN=30 WIDTH=".round(30*$anchoTotal/100)." BORDER=1 STYLE=\"".$sF8.$bI.$bD."\"><BR>\n";
			foreach($vals2 as $key=>$val) $c.="&nbsp;&nbsp;<B>".$key.":</B>&nbsp;".$val."<BR>\n";
			$c.="	&nbsp;</TD>\n";
			$c.="	<TD COLSPAN=30 WIDTH=".round(30*$anchoTotal/100)." BORDER=1 STYLE=\"".$sF8.$bI.$bD."\"><BR>\n";
			foreach($vals3 as $key=>$val) $c.="&nbsp;&nbsp;<B>".$key.":</B>&nbsp;".$val."<BR>\n";
			$c.="	&nbsp;</TD>\n";
			$c.="</TR>\n";
		}
	}
	return $c;
}

/**********************************************************************************************/

$fecha=$_GET["fecha"]==""?date("Y-m-d"):$_GET["fecha"];
$idp=$_GET["idp"];
$vals=array();
$vals2=array();
$vals3=array();

// cargo los datos
$plani=new Planificacion($idp);
$plani->cargaRelaciones();
$res=mysql_query("SELECT * FROM me_clientes c WHERE id_cliente=".$plani->id_cliente);
$row=@mysql_fetch_assoc($res);
$vals=array();
$vals["Cliente"]=$row["nombre"];
$vals["Num. Proveedor"]=$row["num_proveedor"];
$res=mysql_query("SELECT * FROM me_referencias  WHERE id_referencia=".$plani->id_referencia);
$row=@mysql_fetch_assoc($res);
$vals["Referencia"]="(".$row["num"].") ".$row["nombre"];

$vals2["Prototipo"]=$plani->prototipo;
$vals2["Preserie"]=$plani->preserie;
$vals2["Serie"]=$plani->serie;

$vals3["Equipo de  proyecto"]=$plani->equipo;
$vals3["Fecha aprobación"]=muestraFecha($plani->fecha_aprobacion);



		
$todo=$plani->generarPlanDeControlExportacion();
//$st=" STYLE=\"font-size:8.0pt\" ";
$sF7="font-size:7.0pt;";
$sF8="font-size:8.0pt;";
$bR="border-top:0.5pt solid windowtext;";
$bB="border-bottom:0.5pt solid windowtext;";
$bI="border-left:0.5pt solid windowtext;";
$bD="border-right:0.5pt solid windowtext;";
$st1=" STYLE=\"".$sF7.$bR.$bB.$bI.$bD."\" ";
$st2=" STYLE=\"".$sF8.$bR.$bB.$bI.$bD."\" ";

$cabecera.="<tr>";
$cabecera.="<td colspan=10 align='center' valign='middle' rowspan=3 $st2><b>OPERACIÓN</b></td>";
$cabecera.="<td colspan=8 align='center' valign='center' rowspan=3 $st2><b>MÁQUINA</b></td>";
$cabecera.="<td colspan=15 align='center' valign='center' $st2><b>CARACTERÍSTICAS</b></td>";
$cabecera.="<td colspan=6 align='center' valign='center' rowspan=3 $st2><b>CLASE</b></td>";
$cabecera.="<td colspan=41 align='center' valign='center' $st2><b>M&Eacute;TODOS</b></td>";
$cabecera.="<td colspan=20 align='center' valign='center' rowspan=3 $st2><b>PLAN DE REACCIÓN</b></td>";
$cabecera.="</tr>";	
$cabecera.="<tr>";
$cabecera.="<td colspan=5 align='center' valign='center' rowspan=2 $st2><b>Nº</b></td>";
$cabecera.="<td colspan=5 align='center' valign='center' rowspan=2 $st2><b>PROD</b></td>";
$cabecera.="<td colspan=5 align='center' valign='center' rowspan=2 $st2><b>PROC</b></td>";
$cabecera.="<td colspan=11 align='center' valign='center' rowspan=2 $st2><b>ESPECIFICACI&Oacute;N</b></td>";
$cabecera.="<td colspan=10 align='center' valign='center' rowspan=2 $st2><b>EVALUACI&Oacute;N</b></td>";
$cabecera.="<td colspan=10 align='center' valign='center' $st2 ><b>MUESTRA</b></td>";
$cabecera.="<td colspan=10 align='center' valign='center' rowspan=2 $st2><b>M&Eacute;TODO DE CONTROL</b></td>";
$cabecera.="</tr>";	
$cabecera.="<tr>";
$cabecera.="<td colspan=5 align='center' valign='center' $st2><b>TAM.</b></td>";
$cabecera.="<td colspan=5 align='center' valign='center' $st2><b>FRE.</b></td>";
$cabecera.="</tr>";

echo cabeceraExportacionHorizontal("plan de control",muestraFecha($plani->fecha),$app_logoEmpresa,$vals,$vals2,$vals3).$cabecera.$todo."</table>";


?>

