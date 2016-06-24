<?


include "../recursos/conex.php";
include "../recursos/genFunctions.php";
include "../recursos/configuracion.php";
include "../recursos/class/Planificacion.class.php";

global $app_logoEmpresa;
global $app_extensionExportaciones;
header("Content-Disposition: attachment; filename=".date("Ymd")."_HojaDeRuta.".$app_extensionExportaciones);



/**********************************************************************************************/
function cabeceraExportacionHorizontal($titulo,$fecha,$rutaLogo,$valsCabecera="",$anchoTotal=900){
	$st=" STYLE=\"font-size:8.0pt\" ";
	$c="";
	$c.="<TABLE BORDER=1 WIDTH=".$anchoTotal.">\n";
	$c.="	<TR HEIGHT=75>\n";
	$c.="		<TD  COLSPAN=13 VALIGN=TOP ALIGN=CENTER WIDTH=".round(13*$anchoTotal/100)."><IMG SRC='".$rutaLogo."' BORDER=0></TD>\n";
	$c.="		<TD COLSPAN=72 VALIGN=TOP WIDTH=".round(72*$anchoTotal/100)." ALIGN=CENTER><H1>".strToUpper($titulo)."</H1></TD>\n";
	$c.="		<TD COLSPAN=15 VALIGN=TOP WIDTH=".round(15*$anchoTotal/100)." $st><b>Fecha: ".muestraFecha($fecha)."</b></TD>\n";
	$c.="	</TR>\n";
	if(is_array($valsCabecera)){
		$cuantos=count($valsCabecera);
		if($cuantos>0){
			$c.="<TR>\n";
			$c.="	<TD COLSPAN=100 WIDTH=".$anchoTotal." BORDER=1 $st><BR>\n";
			foreach($valsCabecera as $key=>$val) $c.="&nbsp;&nbsp;<B>".$key.":</B>&nbsp;".$val."<BR>\n";
			$c.="	&nbsp;</TD>\n";
			$c.="</TR>\n";
		}
	}
	$st=" STYLE=\"font-size:7.0pt\" ";
	$c.="	<TR>\n";
	$c.="		<TD COLSPAN=45 $st><b>COMPONENTE / OPERACI&Oacute;N</b></TD>";
	$c.="		<TD COLSPAN=20 $st><b>M&Aacute;QUINA</b></TD>";
	$c.="		<TD COLSPAN=35 $st><b>OPERACI&Oacute;N ALTERNATIVA</b></TD>";
	$c.="	</TR>";
	return $c;
}
function pintoFila_1($ar,$pos,$fGris=false){
	global $app_rutaWEB;
	$st=" STYLE=\"font-size:7.0pt\" ";
	$t=explode("[::]",($fGris?$ar["c"]:$ar["o"]));
	if($fGris) $todo.="<td colspan=100 align=left $st>".$t[1]."</td></tr>";
	else{
		$m=explode("[::]",$ar["m"]);
		$oa=explode("[::]",$ar["oAlt"]);
		$todo.="<td class='".$cls."' align=left colspan=45  $st>".$t[1]."</td>";
		$todo.="<td class='".$cls."' align=left colspan=20 $st>".($m[1]==""?"-":$m[1])."</td>";
		$todo.="<td class='".$cls."' align=left colspan=35 $st>".($oa[1]==""?"-":$oa[1])."</td>";
		$todo.='</tr>';
	}			
	return $todo;
}
function pintoFila_2($ar,$pos){
	global $app_rutaWEB;
	$x=pintoFila_1($ar,$pos,true);
	if($ar["o"]!="") $x.=pintoFila_3($ar,$pos);
	return $x;
}
function pintoFila_3($ar,$pos){
	global $app_rutaWEB;
	$st=" STYLE=\"font-size:7.0pt\" ";
	$t=explode("[::]",$ar["o"]);
	$t2=explode("[::]",$ar["m"]);
	$t3=explode("[::]",$ar["oAlt"]);
	$todo.="<td class=FilaGris  colspan=5>&nbsp;</td>";
	$todo.="<td colspan=40 align=left $st>&nbsp;".$t[1]."</td>";			
	$todo.="<td colspan=20 align=left $st>".($t2[1]==""?"-":$t2[1])."&nbsp;</td>";
	$todo.="<td colspan=35 align=left $st>".($t3[1]==""?"-":$t3[1])."&nbsp;</td></tr>";
	return $todo;
}
/**********************************************************************************************/

$fecha=$_GET["fecha"]==""?date("Y-m-d"):$_GET["fecha"];
$idp=$_GET["idp"];

// cargo los datos
$plani=new Planificacion($idp);
$res=mysql_query("SELECT * FROM me_clientes c WHERE id_cliente=".$plani->id_cliente);
$row=@mysql_fetch_assoc($res);
$vals=array();
$vals["Cliente"]=$row["nombre"];
$vals["Num. Proveedor"]=$row["num_proveedor"];
$res=mysql_query("SELECT * FROM me_referencias  WHERE id_referencia=".$plani->id_referencia);
$row=@mysql_fetch_assoc($res);
$vals["Referencia"]="(".$row["num"].") ".$row["nombre"];

$plani->cargaRelaciones();

		
$cAnt=-9999;
$i=0;
$compAnt="·%!";
$i=0;
$cnt=0;
$todo="";
foreach($plani->relaciones as $r){
	if(strpos($r["idTipo"],"C:")===false) $todo.= pintoFila_1($r,$i++);
	elseif($compAnt!=$r["c"])  $todo.= pintoFila_2($r,$i++);
	else $todo.= pintoFila_3($r,$i++);
	$compAnt=$r["c"];
	if($r["o"]!="")$cnt++;
}

echo cabeceraExportacionHorizontal("Hoja de ruta",$fecha,$app_logoEmpresa,$vals).$todo."</table>";


?>

