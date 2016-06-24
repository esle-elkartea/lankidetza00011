<?


include "../recursos/conex.php";
include "../recursos/genFunctions.php";
include "../recursos/configuracion.php";
include "../recursos/class/Planificacion.class.php";

global $app_logoEmpresa;
global $app_extensionExportaciones;
header("Content-Disposition: attachment; filename=".date("Ymd")."_Estudio.".$app_extensionExportaciones);



/************************************************************************************************/
function cabeceraExportacionHorizontal($titulo,$fecha,$rutaLogo,$valsCabecera="",$anchoTotal=900){
	$sF="font-size:8.0pt;";
	$bI="border-left:0.5pt solid windowtext;";
	$bD="border-right:0.5pt solid windowtext;";
	$bR="border-top:0.5pt solid windowtext;";
	$bB="border-bottom:0.5pt solid windowtext;";
	$c="";
	$c.="<TABLE BORDER=0 WIDTH=".$anchoTotal.">\n";
	$c.="	<TR HEIGHT=75>\n";
	$c.="		<TD COLSPAN=13 ALIGN=CENTER VALIGN=TOP WIDTH=".round(13*$anchoTotal/100)." STYLE=\"".$sF.$bR.$bB.$bI.$bD."\">\n".
				"<IMG SRC='".$rutaLogo."' BORDER=0></TD>\n";
	$c.="		<TD COLSPAN=72 VALIGN=TOP WIDTH=".round(72*$anchoTotal/100)." ALIGN=CENTER STYLE=\"".$bR.$bB.$bI.$bD."\"><H1>".strToUpper($titulo)."</H1></TD>\n";
	$c.="		<TD COLSPAN=15 VALIGN=TOP WIDTH=".round(15*$anchoTotal/100)." STYLE=\"".$sF.$bR.$bB.$bI.$bD."\"><b>Fecha: ".muestraFecha($fecha)."</b></TD>\n";
	$c.="	</TR>\n";
	if(is_array($valsCabecera)){
		$cuantos=count($valsCabecera);
		if($cuantos>0){
			$c.="<TR>\n";
			$c.="	<TD COLSPAN=100 WIDTH=".$anchoTotal." STYLE=\"".$sF.$bI.$bD."\"><BR>\n";
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

$sF="font-size:7.0pt;";
$sF8="font-size:8.0pt;";
$bR="border-top:0.5pt solid windowtext;";
$bB="border-bottom:0.5pt solid windowtext;";
$bI="border-left:0.5pt solid windowtext;";
$bD="border-right:0.5pt solid windowtext;";
$st=" STYLE=\"".$sF.$bR.$bB.$bI.$bD."\" ";
$st8=" STYLE=\"".$sF8.$bR.$bB.$bI.$bD."\" ";


$sql="SELECT orden,pregunta FROM pl_estudio_preguntas ep ".
	 "WHERE id_planificacion=".$plani->id_planificacion." ORDER BY orden asc";	 
$res=mysql_query($sql);
$cuantas=mysql_num_rows($res);
flush();
ob_start();
if($row=mysql_fetch_row($res)){
	$plani->cargaEstudio();
	$i=0;
	?>
	<tr>
      <td align="left" colspan=50 <?=$st8?>><b>PREGUNTA</td>
      <td colspan=8 align=CENTER <?=$st8?>><b>RESPUESTA</td>
      <td align="left" colspan=42 <?=$st8?>><b>OBSERVACIONES</td>    
    </tr>
	<?	
	do{
		?>
		<tr>
	      <td align="left" colspan=50 valign=top  <?=$st?>><?=$i+1?>.&nbsp;&nbsp;<?=$row[1]?></td>
	      <td colspan=8 align=center valign=top <?=$st?>>
	      	 <?$resp=$plani->estudio["resp"][$row[0]];
	      	 echo ($resp=="1"?"SI":($resp=="0"?"NO":($resp=="2"?"NO PROCEDE":"")));?>
	      </td>
	      <td align="left" colspan=42 <?=$st?> valign=top><?=$plani->estudio["obs"][$row[0]];?></td>    
	    </tr>
		<?
		$i++;			
	}while($row=mysql_fetch_row($res));
	$sql="SELECT * FROM pl_estudios WHERE id_planificacion=".$plani->id_planificacion;
	$res=mysql_query($sql);
	$row=mysql_fetch_array($res);
	?>
	<tr>
		<td align="left" colspan=100> </td>
	</tr>
	<tr>
		<td align="left" colspan=15 STYLE="<?=$sF.$bI.$bR?>" valign=top><b>Fecha del estudio:</td>
		<td align="left" colspan=85 STYLE="<?=$sF.$bD.$bR?>" valign=top><?=muestraFecha($plani->estudio["fecha"])?></td>
	</tr>
	<tr>
		<td align="left" colspan=15 STYLE="<?=$sF.$bI?>" valign=top><b>Decisión final:</td>
		<td align="left" colspan=85 STYLE="<?=$sF.$bD?>" valign=top><B><?=($plani->estudio["decision"]=="1"?"FACTIBLE":"NO FACTIBLE")?></B></td>		
	</tr>
	<tr>
		<td align="left" colspan=15 STYLE="<?=$sF.$bI.$bB?>" valign=top><b>Observaciones:</td>
		<td align="left" colspan=85 STYLE="<?=$sF.$bD.$bB?>"><?=$plani->estudio["observaciones"]?></td>
	</tr>
	
	</table>
<?}

$todo=ob_get_contents();
ob_end_clean();
if($todo!=""){
	echo cabeceraExportacionHorizontal("Estudio de factibilidad",muestraFecha($plani->estudio["fecha"]),$app_logoEmpresa,$vals);
	echo $todo;
}

?>

