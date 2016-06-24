<?
include "../recursos/conex.php";
include "../recursos/genFunctions.php";
include "../recursos/configuracion.php";
include "../recursos/class/Planificacion.class.php";

global $app_extensionExportaciones;
header("Content-Disposition: attachment; filename=".date("Ymd")."_AMFE.".$app_extensionExportaciones);

$_GET["exportar"]=="";

/**********************************************************************************************/
function cabeceraExportacionHorizontal($titulo,$fecha,$valsCabecera="",$anchoTotal=900){
	global $app_logoEmpresa;	
	
	$st=" STYLE=\"font-size:8.0pt\" ";
	$c="";
	$c.="<TABLE BORDER=1 WIDTH=".$anchoTotal.">\n";
	$c.="	<TR HEIGHT=75>\n";
	$c.="		<TD  COLSPAN=13 VALIGN=TOP ALIGN=CENTER WIDTH=".round(13*$anchoTotal/100)."><IMG SRC='".$app_logoEmpresa."' BORDER=0></TD>\n";
	$c.="		<TD COLSPAN=72 VALIGN=TOP WIDTH=".round(72*$anchoTotal/100)." ALIGN=CENTER><H1>".strToUpper($titulo)."</H1></TD>\n";
	$c.="		<TD COLSPAN=15 VALIGN=TOP WIDTH=".round(15*$anchoTotal/100)."><b>Fecha: ".muestraFecha($fecha)."</b></TD>\n";
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
	return $c;
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
$todo=$plani->pintarExportacionAMFE();
echo cabeceraExportacionHorizontal("a.m.f.e de proceso",$fecha,$vals).pintarCabeceraExportarAMFE().$todo;

?>

