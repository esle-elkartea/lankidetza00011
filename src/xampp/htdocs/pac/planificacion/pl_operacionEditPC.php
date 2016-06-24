<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";

global $app_rutaWEB;

function cTxt($txt){return str_replace("'","\\'",$txt);}
function comprobarCadenaCaracteristicas($cad,$arC){
	$mds=explode(",",$cad);
	$cad="#".str_replace(",","##",$cad)."#";	
	foreach($mds as $m){foreach($arC as $m2) {if($m==$m2["id_caracteristica"]) $cad=str_replace("#".$m."#","",$cad);}}
	return str_replace("#","",str_replace("##",",",$cad));
}

$ido=$_GET["ido"];
$idp=$_GET["idp"];
$posEdit=$_GET["posEdit"];
$operacion=array();
$operacion["id"]=$ido;
$operacion["caracteristicas"]=array();



if($_POST["comprobarSubmit"]=="1" || $_POST["vuelta"]=="1"){
	$operacion["caracteristicas"]=unserialize_esp(str_replace("\\","",$_POST["todos_cars"]));
	$operacion["nombreOp"]=txtParaGuardar($_POST["frm_nombreOp"]);
	if($_POST["act_guardaValores"]!=""){
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["nombre"]=txtParaInput($_POST["frm_nombre"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["id_clase"]=txtParaInput($_POST["frm_clase"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["num"]=txtParaInput($_POST["frm_num"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["prod"]=txtParaInput($_POST["frm_prod"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["proc"]=txtParaInput($_POST["frm_proc"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["metodo"]=txtParaInput($_POST["frm_metodo"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["plan"]=txtParaInput($_POST["frm_plan"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["evaluacion"]=txtParaInput($_POST["frm_evaluacion"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["especificacion"]=txtParaInput($_POST["frm_especificacion"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["tam"]=txtParaInput($_POST["frm_tam"]);
		$operacion["caracteristicas"][$_POST["act_guardaValores"]]["fre"]=txtParaInput($_POST["frm_fre"]);
	}
}else{
	$sql="SELECT o.nombre as onombre,c.* FROM pl_operaciones o ".
	"LEFT JOIN pl_operacion_caracteristica oc ON oc.id_operacion=o.id_operacion AND oc.id_planificacion=$idp AND oc.id_operacion=$ido ".
	"LEFT join pl_caracteristicas c ON c.id_caracteristica=oc.id_caracteristica AND c.id_planificacion=$idp ".
	"WHERE o.id_planificacion=$idp and o.id_operacion=$ido ";
	$res=mysql_query($sql);
	$j=0;
	while($row=@mysql_fetch_assoc($res)){
		if($j==0) $operacion["nombreOp"]=$row["onombre"];
		if(($j==0 && $row["id_caracteristica"]!="")|| $j>0) $operacion["caracteristicas"][$j++]=array_slice($row,1);
	}
}

if($_POST["act_agregarCars"]!=""){
	$cadenaCars="";
	$cadenaCars=comprobarCadenaCaracteristicas($_POST["act_agregarCars"],$operacion["caracteristicas"]);
	if($cadenaCars!=""){
		$sql="SELECT id_caracteristica,c.* FROM ad_caracteristicas c WHERE c.id_caracteristica IN (".$cadenaCars.")";
		$res=mysql_query($sql);
		while($row=@mysql_fetch_assoc($res)) $operacion["caracteristicas"][count($operacion["caracteristicas"])]=$row;
	}	
	
}
if($_POST["act_eliminarCar"]!="")	$operacion["caracteristicas"]=quitarDeArray($operacion["caracteristicas"],$_POST["act_eliminarCar"]);

if($_POST["act_guardar"]!=""){
	mysql_query("UPDATE pl_operaciones SET nombre='".$operacion["nombreOp"]."' WHERE id_planificacion=$idp AND id_operacion=$ido");
	mysql_query("DELETE FROM pl_operacion_caracteristica WHERE id_planificacion=$idp AND id_operacion=$ido");
	$sql="SELECT id_caracteristica FROM pl_caracteristicas  WHERE id_planificacion=".$idp;
	$res=mysql_query($sql);
	while($row=@mysql_fetch_row($res)) $cs[]=$row[0];
	foreach($operacion["caracteristicas"] as $c){
		if(array_search($c["id_caracteristica"],$cs)===false){
			$sql="INSERT INTO pl_caracteristicas (id_planificacion,id_caracteristica,nombre,num,prod,proc,especificacion,evaluacion,metodo,tam,fre,plan,id_clase) ".
				 "VALUES ($idp,".$c["id_caracteristica"].",'".cTxt($c["nombre"])."','".$c["num"]."','".$c["prod"]."','".$c["proc"]."',".
				 "'".cTxt($c["especificacion"])."','".cTxt($c["evaluacion"])."','".cTxt($c["metodo"])."','".cTxt($c["tam"])."','".cTxt($c["fre"])."',".
				 "'".cTxt($c["plan"])."',".($c["id_clase"]==""?0:$c["id_clase"]).")";
		}	
		else {
			$sql="UPDATE pl_caracteristicas c SET ".
				 "nombre='".cTxt($c["nombre"])."',".
				 "num='".cTxt($c["num"])."',".
				 "prod='".cTxt($c["prod"])."',".
				 "proc='".cTxt($c["proc"])."',".
				 "especificacion='".cTxt($c["especificacion"])."',".
				 "evaluacion='".cTxt($c["evaluacion"])."',".
				 "metodo='".cTxt($c["metodo"])."',".
				 "tam='".cTxt($c["tam"])."',".
				 "fre='".cTxt($c["fre"])."',".
				 "plan='".cTxt($c["plan"])."',".
				 "id_clase='".($c["id_clase"]==""?0:$c["id_clase"])."' ".
				 "WHERE c.id_caracteristica=".$c["id_caracteristica"]." AND c.id_planificacion=".$idp;	
		}
		$res=mysql_query($sql);
		$sql="INSERT INTO pl_operacion_caracteristica (id_planificacion,id_operacion,id_caracteristica) VALUES ($idp,$ido,".$c["id_caracteristica"].")";	
		$res=mysql_query($sql);
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Documento sin t&iacute;tulo</title>
	<link href="<?=$app_rutaWEB?>/html/css/asvefat.css" rel="stylesheet" type="text/css">	
	<script language="JavaScript1.2" type="text/javascript" src="<?=$app_rutaWEB?>/html/js/menu.js"></script>	
	<script>
	<?if($_POST["act_guardar"]=="1"){?>
		window.opener.functSubmit();
		window.close();
	<?}?>
	
	function filaover(elemento){
		elemento.style.cursor='hand';
		elemento.className='FilaOver'
	}
 	function filaout(elemento){
		elemento.className='Fila'
	}
	function carEliminar(id){
		document.forms[0].act_eliminarCar.value=id;
		document.forms[0].act_guardaValores.value='';
		document.forms[0].action="?idp=<?=$idp?>&ido=<?=$ido?>";
		document.forms[0].submit();
	}
	function agregarCar(){
		<?=JSventanaSeleccion("caracteristica")?>	
	}
	function agregaCars(vuelta){
		document.forms[0].act_agregarCars.value=vuelta;
		document.forms[0].submit();
	}
	function verCar(pos){
		document.forms[0].action="?idp=<?=$idp?>&ido=<?=$ido?>&posEdit="+pos;
		document.forms[0].submit();
	}
	function guardar(id){
		if(id=="") document.forms[0].act_guardar.value="1";
		else document.forms[0].act_guardarCar.value=id;
		document.forms[0].submit();
	}
	function relacionar(id){
		document.forms[0].act_relacionarCar.value=id;
		document.forms[0].submit();
	}
	window.resizeTo(700,675);
	</script>
</head>

<body onLoad="<?=$JSEjecutar?>">
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
		<tr><td class="spacer6">&nbsp;</td>
	  <tr>
	    <td align="center" class="Tit"><span class="fBlanco">EDITAR OPERACI&Oacute;N PARA LA PLANIFICACI&Oacute;N</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
		    <form method="POST" name="foperacion">
		    <input type="hidden" name="act_guardaValores" VALUE=<?=$posEdit?>>
			<input type="hidden" name="carMostrar" value="">
			<input type="hidden" name="act_eliminarCar" value="">
			<input type="hidden" name="act_agregarCars" value="">
			<input type="hidden" name="act_guardarOperacion" value="">
			<input type="hidden" name="act_guardarCar" value="">
			<input type="hidden" name="act_relacionarCar" value="">
			<input type="hidden" name="act_guardar" value="">
			<input type="hidden" name="comprobarSubmit" value="1">
			<input type="hidden" name="todos_cars" value='<?=serialize_esp($operacion["caracteristicas"])?>'>
	
		    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	 		  	<tr>
	 		  		<td align=right colspan=2>
	 		  			<input type=button class="Boton" onClick="agregarCar()" value="Agregar característica">
	 		  			<input type=button class="Boton" onClick="guardar('')" value="Guardar y cerrar">
	 		  			<input type=button class="Boton" onClick="window.close()" value="  Cerrar  ">
	 		  		</td>
	 		  	</tr>
		  		<tr>
			        <td width="20%" align="center" class="TxtBold" nowrap>Nombre de la operaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap>
			        	&nbsp;<input name="frm_nombreOp" type="text" class="input" size="70" value="<?=txtParaInput($operacion["nombreOp"])?>">
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="center" class="Txt" nowrap valign=top>
			        	<b>Listado de Características:</b><br>(Pulse sobre la característica para editarla)
			        </td>
			        <td width="80%" align="left" nowrap valign="top">
				        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" class="">
			    			<tr>
								<td width=100% class=Txt>								
									<?if(count($operacion["caracteristicas"])>0){?>	
									<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:100px; overflow: auto;">
									<table width="95%" border="0" align="left" cellpadding="2" cellspacing="1" class="BordesTabla">
									<?
									$j=0;
									foreach($operacion["caracteristicas"] as $c){
										if(($j==$posEdit)&&$posEdit!="") $sel=" class=\"FilaOver\" ";
										else $sel= "onmouseover='filaover(this)' onmouseout='filaout(this)'  class=Fila1 ";
										?>
										<tr>
											<td width=100%  style='cursor: pointer' align=left onClick="verCar('<?=$j?>')" <?=$sel?>><?=$c["nombre"]?></td>
											<td class=Fila1 >
												<img  style='cursor: pointer' onClick="carEliminar('<?=$j++?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" 
												alt="eliminar" width="11" height="11">
											</td>
										</tr>
										<?}?>
									</table>
									</div>
									<?}else echo "No hay características relacionadas";?>
						       </td>
				        	</tr>
			        	</table>
			        </td>
			    </tr>	 
			</table>
			
		</td>
	  </tr>
	  <tr><td class="spacer8">&nbsp;</td></tr>
	  <tr>
	  	<td align=center>
	
<?
if($posEdit!=""){
	
	
	
?>
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td align="center" class="Tit"><span class="fBlanco">DATOS DE LA CARACTER&Iacute;STICA</span></td>
		</tr>
		<tr>
			<td align="center" class="Caja">
				<table width="90%" border="0" cellspacing="2" cellpadding="2">
				<tr><td class="spacer8">&nbsp;</td></tr>
	 		  	<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Nombre caracter&iacute;stica:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2>
			        	<input name="frm_nombre" type="text" class="input" size="40" value="<?=txtParaInput($operacion["caracteristicas"][$posEdit]["nombre"])?>">
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Clase:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2>
			        	<select name="frm_clase" class=input>
			        	<?
			        	$sql="SELECT * FROM ad_clases ";
			        	$res=mysql_query($sql);
			        	if($row=mysql_fetch_assoc($res)){
			        		echo '<option value="">-- seleccione una clase --</option>';
			        		do{
			        			$sel=$row["id_clase"]==$operacion["caracteristicas"][$posEdit]["id_clase"]?" selected ":"";
			        			echo '<option value="'.$row["id_clase"].'" '.$sel.'>'.$row["nombre"].'</option>';
			        		}while($row=mysql_fetch_assoc($res));
			        	}else echo '<option value="">-- no hay clases --</option>';
						?>
			        	</select>
			        </td>
			    </tr>
			    <tr>
			    	<td width=20% class=TxtBold align=left valign=top>&nbsp;Caracter&iacute;sticas:</td>
			    	<td width="80%" align="left" class="TxtBold" nowrap colspan=2>
			        	<table border=0 cellpadding=1 cellspacing=1>
			        		<tr>
			        			<th width=33%>&nbsp;N&uacute;mero&nbsp;</th>
			        			<th width=33%>&nbsp;Producto&nbsp;</th>
			        			<th width=33%>&nbsp;Proceso&nbsp;</th>
			        		</tr>
			        		<tr>
			        			<td width=33%>
			        			<input name="frm_num" type="text" class="input" size="20" value="<?=txtParaInput($operacion["caracteristicas"][$posEdit]["num"])?>">
			        			</td>
			        			<td width=33%>
			        			<input name="frm_prod" type="text" class="input" size="20" value="<?=txtParaInput($operacion["caracteristicas"][$posEdit]["prod"])?>">
			        			</td>
			        			<td width=33%>
			        			<input name="frm_proc" type="text" class="input" size="20" value="<?=txtParaInput($operacion["caracteristicas"][$posEdit]["proc"])?>">
			        			</td>
			        		</tr>
			        	</table>
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Especificaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	<?$txtArea=txtParaInput($operacion["caracteristicas"][$posEdit]["especificacion"]);?>
			        	&nbsp;<textarea name="frm_especificacion" class=input rows=3 cols=90><?=$txtArea?></textarea>
			        </td>
			    </tr>
			     <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Evaluaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	<?$txtArea=txtParaInput($operacion["caracteristicas"][$posEdit]["evaluacion"]);?>
			        	&nbsp;<textarea name="frm_evaluacion" class=input rows=3 cols=90><?=$txtArea?></textarea>
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Muestra:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	<table border=0 cellpadding=1 cellspacing=1>
			        		<tr>
			        			<th width=50%>&nbsp;Tamaño&nbsp;</th>
			        			<th width=50%>&nbsp;Frecuencia&nbsp;</th>
			        		</tr>
			        		<tr>
			        			<td width=50%>
			        			<input name="frm_tam" type="text" class="input" size="10" value="<?=txtParaInput($operacion["caracteristicas"][$posEdit]["tam"])?>">
			        			</td>
			        			<td width=50%>
			        			<input name="frm_fre" type="text" class="input" size="10" value="<?=txtParaInput($operacion["caracteristicas"][$posEdit]["fre"])?>">
			        			</td>
			        		</tr>
			        	</table>
			        </td>
			        
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;M&eacute;todo:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        <?$txtArea=txtParaInput($operacion["caracteristicas"][$posEdit]["metodo"]);?>
			        	&nbsp;<textarea name="frm_metodo" class=input rows=3 cols=90><?=$txtArea?></textarea>
			        </td>
			        
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Plan de reacci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        <?$txtArea=txtParaInput($operacion["caracteristicas"][$posEdit]["plan"]);?>
			        	&nbsp;<textarea name="frm_plan" class=input rows=3 cols=90><?=$txtArea?></textarea>
			        </td>
			    </tr>
			    <tr><td class="spacer8">&nbsp;</td></tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="spacer10">&nbsp;</td>
		</tr>
	</table>
	<?
}else {?>
	<tr>
		<td class="TxtBold" colspan=3 align=center><br><br><br><br><br><br><br><br><br><br>Pulse sobre una característica para editarla</td>
	</tr>
<?}?>
	</td></tr>
	</table>
	</form>
</body>
</html>
