<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";

global $app_rutaWEB;
$ido=$_GET["ido"];
$idp=$_GET["idp"];
$operacion=array();
$operacion["id"]=$ido;

function comprobarCadenaModos($cad,$arM){
	$mds=explode(",",$cad);
	$cad="#".str_replace(",","##",$cad)."#";	
	if(count($mds)>0 && count($arM)>0){
		foreach($mds as $m){
			foreach($arM as $m2) {
				if($m==$m2["id"]) $cad=str_replace("#".$m."#","",$cad);
			}
		}
	}
	return str_replace("#","",str_replace("##",",",$cad));
}
function comprobarModoGuardado($m){
	$res=mysql_query("SELECT count(*) FROM pl_modos WHERE id_planificacion=".$_GET["idp"]." AND id_modo=".$m);
	$row=mysql_fetch_row($res);
	return ($row[0]>0?true:false);
}
function guardarModo($modoGuardar,$idp,$ido){
	$debug=false;
	$sql="INSERT INTO pl_modos (id_planificacion,id_modo,nombre,codigo,o_id,o_nombre,o_valor)  ".
		 "(SELECT '$idp',m.id_modo,m.nombre,m.codigo,o.id_ocurrencia as o_id,o.nombre as o_nombre, o.valor as o_valor ".
		 "FROM me_modos m ".
		 "LEFT JOIN ad_ocurrencias o ON o.id_ocurrencia=m.id_ocurrencia ".
		 "WHERE m.id_modo=$modoGuardar)";
	if($debug) echo "<br>".$sql."<br>";
	$res=mysql_query($sql);
	$sql="INSERT INTO pl_operacion_modo (id_planificacion,id_operacion,id_modo) VALUES ($idp,$ido,$modoGuardar)";
	mysql_query($sql);
	if($debug) echo "<br>".$sql."<br>";
	
	//guardo sus efectos (copio solamente los que no están)
	$sql="SELECT e.id_efecto FROM pl_efectos e WHERE e.id_planificacion=$idp";
	$res=mysql_query($sql);
	$efectosActuales=array();
	while($row=@mysql_fetch_row($res)) $efectosActuales[]=$row[0];
	
	$sql="INSERT INTO pl_efectos (id_planificacion,id_efecto,codigo,nombre,g_id,g_nombre,g_valor) ".
		 "(SELECT '$idp',me.id_efecto,e.codigo,e.nombre,g.id_gravedad,g.nombre,g.valor ".
		 "FROM me_modo_efecto me ".
		 "LEFT JOIN me_efectos e ON e.id_efecto=me.id_efecto ".
		 "LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad ".
		 "WHERE me.id_modo=$modoGuardar ".(count($efectosActuales)>0?"AND e.id_efecto NOT IN (".implode(",",$efectosActuales)."))":")");
	$res=mysql_query($sql);
	if($debug) echo "<br>".$sql."<br>";
	$sql="INSERT INTO pl_modo_efecto (id_planificacion,id_modo,id_efecto) ".
		 "(SELECT '$idp',me.id_modo,me.id_efecto FROM me_modo_efecto me WHERE id_modo=$modoGuardar)";
	$res=mysql_query($sql);
	if($debug) echo "<br>".$sql."<br>";
	
	//guardo sus causas
	
	$sql="SELECT c.id_causa FROM pl_causas c WHERE c.id_planificacion=$idp";
	$res=mysql_query($sql);
	$causasActuales=array();
	while($row=@mysql_fetch_row($res)) $causasActuales[]=$row[0];
	
	$sql="INSERT INTO pl_causas (id_planificacion,id_causa,codigo,nombre,accion,d_id,d_nombre,d_valor,d_controles)".
		 "(SELECT ".
		 "'$idp',mc.id_causa,c.codigo,c.nombre,c.accion,d.id_detectabilidad,d.nombre,d.valor,d.controles ".
		 "FROM me_modo_causa mc ".
		 "LEFT JOIN me_causas c ON c.id_causa=mc.id_causa ".
		 "LEFT JOIN ad_detectabilidades d ON d.id_detectabilidad=c.id_detectabilidad ".
		 "WHERE mc.id_modo=$modoGuardar)";
	$res=mysql_query($sql);
	if($debug) echo "<br>".$sql."<br>";
	$sql="INSERT INTO pl_modo_causa (id_planificacion,id_modo,id_causa) ".
		 "(SELECT '$idp',mc.id_modo,mc.id_causa FROM me_modo_causa mc WHERE id_modo=$modoGuardar)";
	$res=mysql_query($sql);
	if($debug) echo "<br>".$sql."<br>";
}


if($_POST["comprobarSubmit"]=="1" || $_POST["vuelta"]=="1"){
	$operacion["nombre"]=txtParaGuardar($_POST["frm_nombre"]);
	//$operacion["codigo"]=$_POST["frm_codigo"];
	$operacion["modos"]=unserialize_esp(str_replace("\\","",$_POST["todos_modos"]));		
	
	
	if($_POST["act_eliminarModo"]!="") $operacion["modos"]=quitarDeArray($operacion["modos"],$_POST["act_eliminarModo"]);
	if($_POST["act_agregarModos"]!=""){
		$cadenaModos="";
		$cadenaModos=comprobarCadenaModos($_POST["act_agregarModos"],$operacion["modos"]);
		if($cadenaModos!=""){
			$sql="SELECT id_modo,nombre FROM me_modos WHERE id_modo IN (".$cadenaModos.")";
			$res=mysql_query($sql);
			if($row=mysql_fetch_array($res)){
				$j=count($operacion["modos"]);
				do{
					$operacion["modos"][$j]["id"]=$row["id_modo"];
					$operacion["modos"][$j++]["nombre"]=$row["nombre"];
				}while($row=mysql_fetch_array($res));				
			}
		}
	}
	if($_POST["act_relacionarModo"]!=""){
		$sql="INSERT INTO pl_operacion_modo (id_planificacion,id_operacion,id_modo) VALUES ($idp,$ido,".$_POST["act_relacionarModo"].")";
		mysql_query($sql);	
		$JSEjecutar="verModo('".$_POST["act_relacionarModo"]."');";
	}
	if($_POST["act_guardarModo"]!=""){		
		// primero guardo el modo (no existe)
		guardarModo($_POST["act_guardarModo"],$idp,$ido);
	}
	if($_POST["act_guardar"]=="1"){
		$sql="UPDATE pl_operaciones SET nombre='".$operacion["nombre"]."' WHERE id_planificacion=".$idp." AND id_operacion=".$ido;
		$res=mysql_query($sql);
		$sql="SELECT id_modo FROM pl_modos WHERE id_planificacion=$idp";
		$res=mysql_query($sql);
		$mds=array();
		while($row=@mysql_fetch_row($res)) $mds[]=$row[0];
		$modosGuardar=array();
		if(count($operacion["modos"])>0){
			foreach($operacion["modos"] AS $m){	
				if(array_search($m["id"],$mds)===false) guardarModo($m["id"],$idp,$ido);
				else mysql_query("INSERT INTO pl_operacion_modo (id_planificacion,id_operacion,id_modo) VALUES ($idp,$ido,".$m["id"].")");
				$modosGuardar[]=$m["id"];
			}
		}else mysql_query("DELETE FROM pl_operacion_modo WHERE id_planificacion=$idp AND id_operacion=$ido");
		$difs=array_diff($mds,$modosGuardar);
		if(count($difs)>0) mysql_query("DELETE FROM pl_operacion_modo WHERE id_planificacion=$idp and id_operacion=$ido AND id_modo IN (".implode(",",$difs).")");
	}	
}else{
	$sql="SELECT o.nombre,m.id_modo as im,m.nombre as mn FROM pl_operaciones o ".
		 "LEFT JOIN pl_operacion_modo om ON om.id_operacion=o.id_operacion AND om.id_planificacion=".$idp." ".
		 "LEFT JOIN pl_modos m ON om.id_modo=m.id_modo  AND m.id_planificacion=".$idp." ".
		 "WHERE o.id_operacion=".$ido." AND o.id_planificacion=".$idp;
	$res=mysql_query($sql);
	if($row=mysql_fetch_array($res)){
		$operacion["nombre"]=$row["nombre"];
		//$operacion["codigo"]=$row["codigo"];
		$j=0;
		do{
			if($row["im"]!=""){
				$operacion["modos"][$j]["id"]=$row["im"];
				$operacion["modos"][$j++]["nombre"]=$row["mn"];
			}
		}while($row=@mysql_fetch_array($res));
	}
}


$sql="SELECT id_modo FROM pl_modos WHERE id_planificacion=".$idp;
$res=mysql_query($sql);
$modosGuardados=array();
while($row=@mysql_fetch_row($res)) $modosGuardados[]=$row[0];
$modosParaJS="#".implode("##",$modosGuardados)."#";

$sql="SELECT id_modo FROM pl_operacion_modo WHERE id_planificacion=".$idp." AND id_operacion=".$ido;
$res=mysql_query($sql);
$modosGuardados=array();
while($row=@mysql_fetch_row($res)) $modosGuardados[]=$row[0];
$modosRelacionados="#".implode("##",$modosGuardados)."#";

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
	
	var listaModos='<?=$modosParaJS?>';
	var listaModosRelacionados='<?=$modosRelacionados?>';
	
	
	
	
	function filaover(elemento){
		elemento.style.cursor='hand';
		elemento.className='FilaOver'
	}
	function filaout(elemento){
		elemento.className='Fila'
	}
	function modoEliminar(id){
		document.forms[0].act_eliminarModo.value=id;
		document.forms[0].submit();
	}
	function agregarModo(){
		<?=JSventanaSeleccion("modo")?>	
	}
	function agregarModos(vuelta){
		document.forms[0].act_agregarModos.value=vuelta;
		document.forms[0].submit();
	}
	function verModo(id){
		if(listaModos.indexOf(id)!=-1 && listaModosRelacionados.indexOf(id)!=-1){
			document.forms[0].action="pl_modoEdit.php?idp=<?=$idp?>&idm="+id+"&ido=<?=$ido?>";
			document.forms[0].submit();
		}else if(listaModos.indexOf(id)==-1){
			if(confirm("El modo y sus relaciones deberán ser guardados.\n¿Desea continuar?")) guardar(id);
		}else if(confirm("El modo debe ser relacionado antes de editarlo.\n¿Desea relacionarlo ahora?")) relacionar(id);
	}
	function guardar(id){
		if(id=="") {
			if(confirm("Los cambios realizados serán guardados\n¿Desea continuar?")){
				document.forms[0].act_guardar.value="1";
				document.forms[0].submit();
			}
		}
		else{
			document.forms[0].act_guardarModo.value=id;
			document.forms[0].submit();
		}
		
	}
	function relacionar(id){
		document.forms[0].act_relacionarModo.value=id;
		document.forms[0].submit();
	}
	</script>
</head>

<body onLoad="window.resizeTo(700,300);<?=$JSEjecutar?>">
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
		<tr><td class="spacer6">&nbsp;</td>
	  <tr>
	    <td align="center" class="Tit"><span class="fBlanco">EDITAR OPERACI&Oacute;N PARA LA PLANIFICACI&Oacute;N</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
	    
			<form method="POST" name="foperacion">
			<input type="hidden" name="modoMostrar" value="">
			<input type="hidden" name="act_eliminarModo" value="">
			<input type="hidden" name="act_agregarModos" value="">
			<input type="hidden" name="act_guardarOperacion" value="">
			<input type="hidden" name="act_guardarModo" value="">
			<input type="hidden" name="act_relacionarModo" value="">
			<input type="hidden" name="act_guardar" value="">
			<input type="hidden" name="comprobarSubmit" value="1">
			<input type="hidden" name="todos_modos" value='<?=serialize_esp($operacion["modos"])?>'>
	
		    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	 		  	<tr>
	 		  		<td align=right colspan=2>
	 		  			<input type=button class="Boton" onClick="agregarModo()" value="Agregar modo">
	 		  			<input type=button class="Boton" onClick="guardar('')" value="Guardar y cerrar">
	 		  			<input type=button class="Boton" onClick="window.close()" value="  Cerrar  ">
	 		  		</td>
	 		  	</tr>
		  		<tr>
			        <td width="20%" align="center" class="TxtBold" nowrap>Nombre de la operaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap>
			        	&nbsp;<input name="frm_nombre" type="text" class="input" size="70" value="<?=txtParaInput($operacion["nombre"])?>">
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="center" class="Txt" nowrap valign=top>
			        	<b>Listado de Modos de Fallo:</b><br>(Pulse sobre el modo para editarlo)
			        	
			        </td>
			        <td width="80%" align="left" nowrap valign="top">
				        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" class="">
			    			<tr>
								<td width=100% class=Txt>								
									<?if(count($operacion["modos"])>0){?>	
									<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:100px; overflow: auto;">
									<table width="95%" border="0" align="left" cellpadding="2" cellspacing="1" class="BordesTabla">
									<?
									$j=0;
									foreach($operacion["modos"] as $m){?>
										<tr>
											<td width=100% class=Fila1 align=left onClick="verModo('<?=$m["id"]?>')" onmouseover="filaover(this)" 
											onmouseout="filaout(this)"><?=$m["nombre"]?></td>
											<td class=Fila1 >
												<img  style='cursor: pointer' onClick="modoEliminar('<?=$j++?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" 
												alt="eliminar" width="11" height="11">
											</td>
										</tr>
										<?}?>
									</table>
									</div>
									<?}else echo "No hay modos relacionados";?>
						       </td>
				        	</tr>
			        	</table>
			        </td>
			    </tr>	 
			</table>
			</form>
		</td>
	  </tr>
	</table>
</body>
</html>
