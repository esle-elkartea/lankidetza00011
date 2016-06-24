<?


include "../recursos/conex.php";
include "../recursos/genFunctions.php";
include "../recursos/configuracion.php";
include "../recursos/class/Planificacion.class.php";
include "../recursos/class/Responsable.class.php";

global $app_logoEmpresa;
global $app_extensionExportaciones;
header("Content-Disposition: attachment; filename=".date("Ymd")."_Actividades.".$app_extensionExportaciones);



/**********************************************************************************************/
function cabeceraExportacionHorizontal($titulo,$fecha,$rutaLogo,$valsCabecera="",$anchoTotal=900){
	$st=" STYLE=\"font-size:8.0pt\" ";
	$c="";
	$c.="<TABLE BORDER=1 WIDTH=".$anchoTotal.">\n";
	$c.="	<TR HEIGHT=75>\n";
	$c.="		<TD COLSPAN=13 ALIGN=CENTER VALIGN=TOP WIDTH=".round(13*$anchoTotal/100)."><IMG SRC='".$rutaLogo."' BORDER=0></TD>\n";
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


//imprimo datos
$st=" STYLE=\"font-size:7.0pt\" ";

if(count($plani->actividades)>0){
	echo cabeceraExportacionHorizontal("Listado de Actividades",$fecha,$app_logoEmpresa,$vals);
	?>
	<TR>
		<TD COLSPAN=25 ALIGN=CENTER <?=$st?>><B>NOMBRE</B></TD>
		<TD COLSPAN=15 ALIGN=CENTER <?=$st?>><B>RESPONSABLE</B></TD>
		<TD COLSPAN=8  ALIGN=CENTER <?=$st?>><B>PLAZO</B></TD>
		<TD COLSPAN=8  ALIGN=CENTER <?=$st?>><B>FECHA CERRADO</B></TD>
		<TD COLSPAN=25 ALIGN=CENTER <?=$st?>><B>OBSERVACIONES</B></TD>
		<TD COLSPAN=19 ALIGN=CENTER <?=$st?>><B>ESTADO ACTIVIDAD</B></TD>
	</TR>
	<?
	foreach($plani->actividades as $act){
		if(dateDiff($act["plazo"],$fecha)>0){			
			$hoy=str_replace("-","",fechaBd(muestraFecha(date("Y-m-d"))));
			$fCerr=str_replace("-","",fechaBd(muestraFecha($act["fecha_cerrado"])));
			$fPlazo=str_replace("-","",fechaBd(muestraFecha($act["plazo"])));
			$cerrado=$act["cerrado"]=="1"?true:false;
			$clase="Fila1";
			if($cerrado){
				if($fCerr>$fPlazo && $fPlazo!="" && $fCerr!="") $tit="Actividad cerrada con retraso";
				elseif($fCerr<=$fPlazo && $fPlazo!="" && $fCerr!="") $tit="Actividad cerrada dentro del plazo";
				else $tit="Actividad cerrada";
			}else{
				if($fPlazo!="" && $fPlazo<$hoy) $tit="Actividad abierta retrasada";
				else $tit="Actividad abierta";
			}?>
			<TR>
				<TD COLSPAN=25 ALIGN=LEFT VALIGN=TOP <?=$st?>><?=($act["anombre"]==""?"":"· ".$act["anombre"])?></TD>
				<TD COLSPAN=15 ALIGN=LEFT VALIGN=TOP <?=$st?>><?=($act["responsable"]=="0"?"":"· ".(Responsable::getNombre($act["responsable"])))?></TD>
				<TD COLSPAN=8  ALIGN=LEFT VALIGN=TOP <?=$st?>><?=muestraFecha($act["plazo"])?></TD>
				<TD COLSPAN=8  ALIGN=LEFT VALIGN=TOP <?=$st?>><?=muestraFecha($act["fecha_cerrado"])?></TD>
				<TD COLSPAN=25 ALIGN=LEFT VALIGN=TOP <?=$st?>><?=($act["observaciones"]==""?"":"· ".$act["observaciones"])?></TD>
				<TD COLSPAN=19 ALIGN=LEFT VALIGN=TOP <?=$st?>><?=$tit?></TD>
		    </TR><?			
		}
	}
	echo "</TABLE>";
}
?>

