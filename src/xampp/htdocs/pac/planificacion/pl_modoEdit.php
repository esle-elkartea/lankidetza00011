<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas

global $app_rutaWEB;

function comprobar($cad,$ar){
	$cad="#".str_replace(",","##",$cad)."#";
	foreach($ar as $a) $cad=str_replace("#".$a["id"]."#","",$cad);
	return str_replace("#","",str_replace("##",",",$cad));
}
function validar($nm,$vo,$do,$dg,$dd,$efectos,$causas){
	$erores=array();
	if($nm=="") $errores[]="Introduzca un nombre descriptivo para el modo";
	if($vo=="") $errores[]="Seleccione un valor para la ocurrencia del modo";
	elseif(!is_numeric($vo)) $errores[]="El valor introducido para la ocurrencia ha de ser un valor numérico";
	if($do=="") $errores[]="Introduzca un valor para la ocurrencia del resultado obtenido";
	elseif(!is_numeric($do)) $errores[]="El valor introducido para la ocurrencia del resultado obtenido ha de ser un valor numérico";
	if($dg=="") $errores[]="Introduzca un valor para la gravedad del resultado obtenido";
	elseif(!is_numeric($dg)) $errores[]="El valor introducido para la gravedad del resultado obtenido ha de ser un valor numérico";
	if($dd=="") $errores[]="Introduzca un valor para la detectabilidad del modo";
	elseif(!is_numeric($dd)) $errores[]="El valor introducido para la detectabilidad del resultado obtenido ha de ser un valor numérico";
	foreach($efectos as $e){
		if($e["nombre"]=="") $errores[]="Todos los efectos deben tener un nombre descriptivo.";
		elseif($e["id_gravedad"]=="") $errores[]="El efecto \\\"".str_replace("'","",$e["nombre"])."\\\" no tiene valor de gravedad asignado.";
		elseif(!is_numeric($e["gravedad"])) $errores[]="El efecto \\\"".str_replace("'","",$e["nombre"])."\\\" tiene un valor de gravedad inválido.";			
	}
	foreach($causas as $c){
		if($c["nombre"]=="") $errores[]="Todos los efectos deben tener un nombre descriptivo.";
		else if($c["id_detectabilidad"]=="") $errores[]="La causa \\\"".str_replace("'","",$c["nombre"])."\\\" no tiene valor de gravedad asignado.";
		else if(!is_numeric($c["detectabilidad"])) $errores[]="La causa \\\"".str_replace("'","",$c["nombre"])."\\\" tiene un valor de detectabilidad inválido.";	
	}
	if(count($errores)==0) return true;
	else return ("- ".implode("\\n- ",$errores));
}

$idm=$_GET["idm"];
$ido=$_GET["ido"];
$idp=$_GET["idp"];
$ide=$_POST["ide"];
$idc=$_POST["idc"];


$modos=unserialize_esp($_POST["todos_modos"]);


if($_POST["comprobarSubmit2"]=="1"){
	
	$responsable=txtParaGuardar($_POST["frm_responsable"]);
	$plazo=$_POST["frm_plazo"];
	$efectos=unserialize_esp(str_replace("\\","",$_POST["todos_efectos"]));
	$causas=unserialize_esp(str_replace("\\","",$_POST["todos_causas"]));
	$nombreModo=txtParaGuardar($_POST["frm_nombreModo"]);
	$partes=explode("::",$_POST["frm_valorOcurrencia"]);
	$idOcurrencia=$partes[0];
	$valorOcurrencia=$partes[1];
	$despuesOC=$_POST["frm_despuesOC"];
	$despuesGR=$_POST["frm_despuesGR"];
	$despuesDE=$_POST["frm_despuesDE"];
	$responsable=$_POST["frm_responsable"];
	$plazo=$_POST["frm_plazo"];
	$accionTomada=$_POST["frm_accionTomada"];


}elseif($idm!="" && $idp!=""){
	
	// aqui lo cargo todo porque es la primera vez que entro
	
	$sql="SELECT  m.id_modo,e.id_efecto,c.id_causa, ".	
		 "m.nombre as modo,if(m.o_valor IS NULL,'0',m.o_valor) as valorOcurrencia, ".
		 "e.nombre as efecto,if(e.g_valor IS NULL,'0',e.g_valor) as valorGravedad, ".
		 "c.nombre as causa,if(c.d_valor IS NULL,'0',c.d_valor) as valorDetectabilidad, ".	
		 "c.d_controles, ".
		 "c.accion as accion, ".
		 "om.responsable as responsable, om.plazo as plazo, om.accion_tomada as accionTomada,om.OC,om.GR,om.DE,om.id_operacion,c.d_id,e.g_id,m.o_id ".							
		 "FROM pl_operacion_modo om ".
		 "LEFT JOIN pl_modos m ON om.id_modo=m.id_modo AND om.id_planificacion=".$idp." ".
		 "LEFT JOIN pl_modo_efecto me ON m.id_modo=me.id_modo AND me.id_planificacion=".$idp." ".
		 "LEFT JOIN pl_efectos e ON e.id_efecto=me.id_efecto  AND e.id_planificacion=".$idp."  ".
		 "LEFT JOIN pl_modo_causa mc ON m.id_modo=mc.id_modo  AND mc.id_planificacion=".$idp."  ".
		 "LEFT JOIN pl_causas c ON c.id_causa=mc.id_causa  AND c.id_planificacion=".$idp."  ".
		 "WHERE m.id_modo=".$idm." ".
		 "AND m.id_planificacion=".$idp." ".
		 "ORDER BY m.id_modo asc, m.nombre asc, e.nombre asc, c.nombre asc ";
		 
	$res=mysql_query($sql);
	if($row=mysql_fetch_array($res)){
		$countEfectos=0;
		$countCausas=0;
		$listaEfectos="";
		$listaCausas="";
		$causas=array();
		$efectos=array();
		$nombreModo=$row["modo"];
		$valorOcurrencia=$row["valorOcurrencia"];
		$idOcurrencia=$row["o_id"];
		$despuesOC=0;
		$despuesGR=0;
		$despuesDE=0;
		$responsable="";
		$plazo="";
		$accionTomada="";
		do {
			if($row["id_operacion"]==$ido){
				$despuesOC=$row["OC"];
				$despuesGR=$row["GR"];
				$despuesDE=$row["DE"];
				$responsable=$row["responsable"];
				$plazo=$row["plazo"];
				$accionTomada=$row["accionTomada"];	
			}
			if(strpos($listaEfectos,"#".$row["id_efecto"]."#")===false && $row["id_efecto"]!=""){
				$efectos[$countEfectos]["id"]=$row["id_efecto"];
				$efectos[$countEfectos]["nombre"]=$row["efecto"];
				$efectos[$countEfectos]["id_gravedad"]=$row["g_id"];
				$efectos[$countEfectos++]["gravedad"]=$row["valorGravedad"];
				$listaEfectos.="#".$row["id_efecto"]."#";
			}
			if(strpos($listaCausas,"#".$row["id_causa"]."#")===false && $row["id_causa"]!=""){
				$causas[$countCausas]["id"]=$row["id_causa"];
				$causas[$countCausas]["nombre"]=$row["causa"];
				$causas[$countCausas]["accion"]=$row["accion"];
				$causas[$countCausas]["id_detectabilidad"]=$row["d_id"];
				$causas[$countCausas]["detectabilidad"]=$row["valorDetectabilidad"];
				$causas[$countCausas++]["controles"]=$row["d_controles"];
				$listaCausas.="#".$row["id_causa"]."#";
			}
			$modoAnterior=$row["id_modo"];
		}while($row=mysql_fetch_array($res));
	}
}

if($_POST["pos_efecto_guardar"]!=""){
	$efectos[$_POST["pos_efecto_guardar"]]["nombre"]=txtParaInput($_POST["frm_nombreEfecto"]);
	$partes=explode("::",$_POST["frm_valorGravedad"]);
	$efectos[$_POST["pos_efecto_guardar"]]["id_gravedad"]=$partes[0];
	$efectos[$_POST["pos_efecto_guardar"]]["gravedad"]=$partes[1];	
}
if($_POST["pos_causa_guardar"]!=""){
	$causas[$_POST["pos_causa_guardar"]]["nombre"]=txtParaInput($_POST["frm_nombreCausa"]);
	$partes=explode("::",$_POST["frm_valorDetectabilidad"]);
	$causas[$_POST["pos_causa_guardar"]]["id_detectabilidad"]=txtParaGuardar($partes[0]);
	$causas[$_POST["pos_causa_guardar"]]["detectabilidad"]=txtParaGuardar($partes[1]);
	$causas[$_POST["pos_causa_guardar"]]["accion"]=txtParaInput($_POST["frm_accion"]);
	$causas[$_POST["pos_causa_guardar"]]["controles"]=txtParaInput($_POST["frm_controles"]);
}

//eliminar efecto o causa
	if($_POST["act_eliminarEfecto"]!="") $efectos=quitarDeArray($efectos,$_POST["act_eliminarEfecto"]);
	if($_POST["act_eliminarCausa"]!="") $causas=quitarDeArray($causas,$_POST["act_eliminarCausa"]);

//agregar efectos o causas
	if($_POST["act_agregarEfectos"]!="") {
		$cadenaEfectos=comprobar($_POST["act_agregarEfectos"],$efectos);	
		$sql="SELECT e.id_efecto as id,e.nombre as nombre,g.valor as gravedad,g.id_gravedad  FROM me_efectos e ".
			 "LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad ".
			 "WHERE e.id_efecto IN (".$cadenaEfectos.")";
		$res=mysql_query($sql);
		while($row=@mysql_fetch_assoc($res)) $efectos[]=$row;	
	}
	if($_POST["act_agregarCausas"]!="") {
		$cadenaCausas=comprobar($_POST["act_agregarCausas"],$causas);
		$sql="SELECT c.id_causa as id,c.nombre as nombre,c.accion as accion,d.valor as detectabilidad,d.controles as controles,d.id_detectabilidad ".
			 "FROM me_causas c LEFT JOIN ad_detectabilidades d ON d.id_detectabilidad=c.id_detectabilidad ".
			 "WHERE c.id_causa IN (".$cadenaCausas.")";
		$res=mysql_query($sql);
		while($row=@mysql_fetch_assoc($res)) $causas[]=$row;	
	}

//guardarlo todo
if($_POST["guarda_todo"]=="1"){
	$resultadoValidacion=validar($nombreModo,$valorOcurrencia,$despuesOC,$despuesGR,$despuesDE,$efectos,$causas);
	if($resultadoValidacion===true){ // si no es así no se guarda ni se sale. Se muestra el error que sea en JS
		$sql="UPDATE pl_modos SET nombre='".str_replace("'","\\'",$nombreModo)."',o_valor=".$valorOcurrencia.", ".
			 "o_id=".$idOcurrencia." WHERE id_planificacion=$idp AND id_modo=$idm";
		$res=mysql_query($sql);
		$sql="UPDATE pl_operacion_modo  SET ".
			 "responsable='".txtParaGuardar($responsable)."',".
			 "plazo='".fechaBD($plazo)."',".
			 "accion_tomada='".txtParaGuardar($accionTomada)."',".
			 "OC='".$despuesOC."',GR='".$despuesGR."',DE='".$despuesDE."' ".
			 "WHERE id_planificacion=".$idp." AND id_operacion=".$ido." AND id_modo=".$idm;
		$res=mysql_query($sql);
		
		// guardo efectos
		/**********************/
		
		$arEfcts=array();
		$ids=array();
		
		foreach($efectos as $e) {$arEfcts[$e["id"]]=$e;$ids[]=$e["id"];}
		ksort($arEfcts);
		$efectos=array();
		foreach($arEfcts as $e) $efectos[]=$e;
		
		$sql="SELECT e.id_efecto,e.codigo,e.nombre,e.id_gravedad,g.nombre,g.valor FROM me_efectos e ".
			 "LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad ".
			 "WHERE e.id_efecto IN (".implode(",",$ids).") ORDER BY e.id_efecto asc";
		$res=mysql_query($sql);
		mysql_query("DELETE FROM pl_modo_efecto WHERE id_planificacion=$idp AND id_modo=$idm ");
		if($row=@mysql_fetch_row($res)){
			$res2=mysql_query("SELECT id_efecto FROM pl_efectos WHERE id_planificacion=$idp  ");
			$idsAct=array();
			while($row2=@mysql_fetch_row($res2)) $idsAct[]=$row2[0];
			$j=0;
			do{
				if(array_search($row[0],$idsAct)===false){
					$sql="INSERT INTO pl_efectos (id_planificacion,id_efecto,codigo,nombre,g_id,g_nombre,g_valor) VALUES ".
						 "($idp,".$row[0].",'".$row[1]."','".txtParaGuardar($efectos[$j]["nombre"])."',".
						 " ".($efectos[$j]["id_gravedad"]==""?"0":$efectos[$j]["id_gravedad"]).",'".$row[4]."'".
						 ",".$efectos[$j]["gravedad"].")";
				}else{
					$sql="UPDATE pl_efectos SET nombre='".str_replace("'","\\'",$efectos[$j]["nombre"])."',g_valor=".$efectos[$j]["gravedad"]." ".
						 ",g_id=".$efectos[$j]["id_gravedad"]." WHERE id_planificacion=$idp AND id_efecto=".$efectos[$j]["id"];
				}
				
				mysql_query($sql);
				mysql_query("INSERT INTO pl_modo_efecto (id_planificacion,id_modo,id_efecto) VALUES ($idp,$idm,".$row[0].")");
				$j++;
			}while($row=mysql_fetch_row($res));
		}
		mysql_query("DELETE FROM pl_modo_efecto WHERE id_planificacion=$idp AND id_modo=$idm "); // elimino relaciones anteriores
		foreach($ids as $id) mysql_query("INSERT INTO pl_modo_efecto (id_planificacion,id_modo,id_efecto) VALUES ($idp,$idm,$id)"); //inserto nuevas
				 
				 
		// guardo causas
		/**********************/
		
		$arCausas=array();
		$ids=array();
		
		foreach($causas as $c) {$arCausas[$c["id"]]=$c;$ids[]=$c["id"];}
		ksort($arCausas);
		$causas=array();
		foreach($arCausas as $c) $causas[]=$c;
		
		$sql="SELECT c.id_causa,c.codigo,c.nombre,c.accion,c.id_detectabilidad,d.nombre,d.valor,d.controles FROM me_causas c ".
			 "LEFT JOIN ad_detectabilidades d ON d.id_detectabilidad=c.id_detectabilidad ".
			 "WHERE id_causa IN (".implode(",",$ids).") ORDER BY c.id_causa asc";
		
		mysql_query("DELETE FROM pl_modo_causa WHERE id_planificacion=$idp AND id_modo=$idm ");
		$res=mysql_query($sql);
		if($row=@mysql_fetch_row($res)){
			$res2=mysql_query("SELECT id_causa FROM pl_causas WHERE id_planificacion=$idp ");
			$idsAct=array();
			while($row2=@mysql_fetch_row($res2)) $idsAct[]=$row2[0];
			$j=0;
			do{
				if(array_search($row[0],$idsAct)===false){
					$sql="INSERT INTO pl_causas (id_planificacion,id_causa,codigo,nombre,accion,d_id,d_nombre,d_valor,d_controles) VALUES ".
						 "($idp,".$row[0].",'".$row[1]."','".str_replace("'","\\'",$causas[$j]["nombre"])."',".
						 "'".str_replace("'","\\'",$causas[$j]["accion"])."',".$row[4].",'".str_replace("'","\\'",$row[5])."',".
						 " ".$causas[$j]["detectabilidad"].",'".str_replace("'","\\'",$causas[$j]["controles"])."')";
				}else{
					$sql="UPDATE pl_causas SET ".
						 "nombre='".str_replace("'","\\'",$causas[$j]["nombre"])."',".
						 "accion='".str_replace("'","\\'",$causas[$j]["accion"])."',".
						 "d_id='".$causas[$j]["id_detectabilidad"]."',".
						 "d_valor='".$causas[$j]["detectabilidad"]."',".
						 "d_controles='".str_replace("'","\\'",$causas[$j]["controles"])."' ".
						 "WHERE id_planificacion=$idp AND id_causa=".$causas[$j]["id"];
				}
				
				mysql_query($sql);
				mysql_query("INSERT INTO pl_modo_causa (id_planificacion,id_modo,id_causa) VALUES ($idp,$idm,".$row[0].")");
				$j++;
			}while($row=mysql_fetch_row($res));
		}
	}else $JSEjecutar="alert(\"".$resultadoValidacion."\");"; // se genera la cadena con los errores para lanzar por JS
}

// busco la posicion en el array de la causa o efecto actual

if($ide!=""){$j=0;foreach($efectos AS $e){if($e["id"]==$ide) $posicionE=$j;$j++;}}
if($idc!=""){$j=0;foreach($causas AS $c){if($c["id"]==$idc) $posicionC=$j;$j++;}}


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
		
		<?if($_POST["guarda_todo"]=="1"){?>
			window.opener.functSubmit();
		<?}?>
		function filaover(elemento){
			elemento.style.cursor='hand';
			elemento.className='FilaOver'
		}
		function filaout(elemento){
			elemento.className='Fila'
		}
		function cambioPestanya (desde,hasta){
			document.forms[0].p.value=hasta;
			document.forms[0].submit();
		}
		function verEfecto(id){
			document.forms[0].ide.value=id;
			document.forms[0].submit();
		}
		function verCausa(id){
			document.forms[0].idc.value=id;
			document.forms[0].submit();
		}
		function efectoEliminar(pos){
			posAct='<?=$posicionE?>';
			document.forms[0].act_eliminarEfecto.value=pos;
			if(posAct==pos) document.forms[0].ide.value="";
			document.forms[0].submit();
		}
		function causaEliminar(pos){
			posAct='<?=$posicionC?>';
			document.forms[0].act_eliminarCausa.value=pos;
			if(posAct==pos) document.forms[0].idc.value="";
			document.forms[0].submit();
		}
		function agregarEfectos(ids){
			document.forms[0].act_agregarEfectos.value=ids;
			document.forms[0].p.value=1;
			document.forms[0].submit();
		}
		function agregarCausas(ids){
			document.forms[0].act_agregarCausas.value=ids;
			document.forms[0].p.value=0;
			document.forms[0].submit();
		}
		function guardar(){
			if(confirm("Los cambios serán aplicados.\n¿Desea continuar?")){
				document.forms[0].guarda_todo.value="1";
				document.forms[0].submit();
			}
		}
		function selFecha(dia,mes,ano){
			document.forms[0].frm_plazo_mostrar.value=dia+"/"+mes+"/"+ano;
			document.forms[0].frm_plazo.value=ano+"-"+mes+"-"+dia;
		}
		
	</script>
</head>
<body onLoad='window.resizeTo(800,640);<?=$JSEjecutar?>'>
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
	  <tr><td class="spacer6">&nbsp;</td>
	  <tr>
	    <td align="center" class="Tit"><span class="fBlanco">EDITAR MODO DE FALLO PARA LA PLANIFICACI&Oacute;N</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">	    
			<form method="POST" name="fmodo">
			<input type="hidden" name="comprobarSubmit2" value="1">
			<input type="hidden" name="p" value="<?=$_POST["p"]?>">
			<input type="hidden" name="ide" value="<?=$ide?>">
			<input type="hidden" name="idc" value="<?=$idc?>">
			<?if($ide!="" && $_POST["p"]==1) echo '<input type="hidden" name="pos_efecto_guardar" value="'.$posicionE.'">';?>
			<?if($idc!="" && $_POST["p"]!=1) echo '<input type="hidden" name="pos_causa_guardar" value="'.$posicionC.'">';?>
			<input type="hidden" name="act_eliminarEfecto" value="">
			<input type="hidden" name="act_eliminarCausa" value="">
			<input type="hidden" name="act_agregarEfectos" value="">
			<input type="hidden" name="act_agregarCausas" value="">
			<input type="hidden" name="todos_efectos" value='<?=serialize_esp($efectos)?>'>
			<input type="hidden" name="todos_causas" value='<?=serialize_esp($causas)?>'>
			<input type="hidden" name="frm_nombre" value="<?=txtParaInput($_POST["frm_nombre"])?>">
			<input type="hidden" name="todos_modos" value='<?=serialize_esp($modos)?>'>
			<input type="hidden" name="guarda_todo" value='0'>
			
		    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	 		  	<tr>
	 		  		<td align=right colspan=3>
	 		  			<input type=button class="Boton" onClick="<?=JSventanaSeleccion("causa")?>" value="Agregar causa">
	 		  			<input type=button class="Boton" onClick="<?=JSventanaSeleccion("efecto")?>" value="Agregar efecto">
	 		  			
	 		  			<input type=button class="Boton" Value="<?=($_GET["soloModo"]!=1?"Guardar y Volver":"Guardar y Cerrar")?>" onClick="guardar()">
	 		  			
	 		  			<input type="button" class="Boton" value="<?=($_GET["soloModo"]!=1?"Volver":"Cerrar")?>" 
						onClick="<?=($_GET["soloModo"]!=1?"document.getElementById('fVuelta').submit()":"window.close()")?>">
	 		  		</td>
	 		  	</tr>
		  		<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Operaci&oacute;n:&nbsp;</td>
			        <?
					if($_GET["soloModo"]!=1) $nMostrar=$_POST["frm_nombre"];
					else {
						$res=mysql_query("SELECT nombre FROM pl_operaciones WHERE id_planificacion=$idp AND id_operacion=$ido");
						$row=mysql_fetch_row($res);
						$nMostrar=$row[0];
					}
			        ?>
			        <td width="80%" align="left" nowrap class="Txt" colspan=2>&nbsp;&nbsp;<?=txtParaInput($nMostrar)?></td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Nombre del modo:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2>
			        	&nbsp;<input name="frm_nombreModo" type="text" class="input" size="40" value="<?=txtParaInput($nombreModo)?>">
			        </td>
			        
			    </tr>
			    <tr>
			    	<td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Valor Ocurrencia inicial:</td>
			        <td width="80%" align="left" colspan=2>
			        	<!--&nbsp;<input name="frm_valorOcurrencia" type="text" class="input" size="2" value="<?=txtParaInput($valorOcurrencia)?>">-->
			        	<select name="frm_valorOcurrencia" class=input>
			        		<?
			        		$res=mysql_query("SELECT * FROM ad_ocurrencias ORDER BY valor asc");
			        		if($row=mysql_fetch_assoc($res)){
			        			echo '<option value="">-- seleccione ocurrencia --</option>';
			        			do{
			        				if($row["id_ocurrencia"]==$idOcurrencia) $sel=" selected ";
			        				else $sel="";
			        				echo '<option value="'.$row["id_ocurrencia"].'::'.$row["valor"].'" '.$sel.'>('.$row["valor"].') '.$row["nombre"].'</option>';
			        			}while($row=mysql_fetch_assoc($res));
			        			
			        		}else echo '<option value="">-- no hay ocurrencias --</option>';
			        		?>
			        	</select>
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Responsable:&nbsp;&nbsp;&nbsp;</td>
			        <td width="51%" align="left" nowrap class="TxtBold" >
			        	<select name="frm_responsable" class="input">
			        		<?
			        			$res=mysql_query("SELECT * FROM ad_responsables");
			        			if($row=mysql_fetch_array($res)){
			        				echo "<option value='0'>- Seleccione responsable -</option>";
			        				do{
			        					$sel=$row["id_responsable"]==$responsable?"selected":"";
			        					echo "<option value='".$row["id_responsable"]."' ".$sel.">".$row["nombre"]." ".$row["apellidos"]."</option>";
			        				}while($row=mysql_fetch_array($res));
			        			}else echo "<option value='0'>- No existen responsables -</option>";
			        		?>
			        	</select>
			        	<!--<input name="frm_responsable" type="text" class="input" size="40" value="<?=txtParaInput($responsable)?>">-->
			        </td>
			        <td class="TxtBold" align=left nowRAP>	
			        	Plazo:<?=printEspacios(4)?>
			        	<input name="frm_plazo_mostrar" type="text" class="input" size="8" value="<?=muestraFecha($plazo)?>" disabled>
			        	<a href="#" onClick="<?=JSventanaCalendario("selFecha",getDia($plazo),getMes($plazo),getAnio($plazo))?>">
			        	<img  border=0 src="<?=$app_rutaWEB?>/html/img/calendar.gif"></a>
			        	<input name="frm_plazo" type="hidden" class="input" size="8" value="<?=fechaBD($plazo)?>">
			       </td>
			    </tr>
			     <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Acci&oacute;n tomada:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	&nbsp;<textarea name="frm_accionTomada" class=input rows=3 cols=90><?=txtParaInput($accionTomada)?></textarea>
			        </td>
			        
			    </tr>
			    <tr>
			      <td class=TxtBold align=left width=20% valign=top>Resultado Obtenido:</td>
			      <td width=80% colspan=2>
			      
			      	<table border=0 cellpadding=1 cellspacing=1 align=left>
			        		<tr>
			        			<th width=33%>&nbsp;OC&nbsp;</th>
			        			<th width=33%>&nbsp;GR&nbsp;</th>
			        			<th width=33%>&nbsp;DE&nbsp;</th>
			        		</tr>
			        		<tr>
			        			<td width=33%>
			        				<input type="text" name="frm_despuesOC" class=input value="<?=$despuesOC?>" size=3>
			        			</td>
			        			<td width=33%>
			        				<input type="text" name="frm_despuesGR" class=input value="<?=$despuesGR?>" size=3>
			        			</td>
			        			<td width=33%>
			        				<input type="text" name="frm_despuesDE" class=input value="<?=$despuesDE?>" size=3>
			        			</td>
			        		</tr>
			        	</table>
			      	
			      	<!--
					<input type="text" name="frm_despuesOC" class=input value="<?=$despuesOC?>" size=3>
					<input type="text" name="frm_despuesGR" class=input value="<?=$despuesGR?>" size=3>
					<input type="text" name="frm_despuesDE" class=input value="<?=$despuesDE?>" size=3>
					-->
					
			      </td>
			    </tr>
			   </table>
		</td>
	  </tr>
	  <tr>
	  <tr><td class="spacer8">&nbsp;</td></tr>
	  <tr>
	    <td width=95%>
		  <?
		  $p=new Pest($_POST["p"]);
		  $p->add("Causas","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
		  $p->add("Efectos","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
		  $p->pintar();
		  ?>
		  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td class="Caja" align=center valign=top>
				  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td class="spacer8">
								<BR>&nbsp;
							</td>
						</tr>
						<tr>
							<td width=100% align=center>
									
<?							
switch ($_POST["p"]){
	
	case "1":
		?>
		<table width="98%" border="0" align="center" cellpadding="2" cellspacing="1" class="">
			<tr>
				<td width=42% class="Txt" align=left>
					<span class="claseCaption">&nbsp;lista de Efectos<br>&nbsp;</span>
					<?if(count($efectos)>0){?>	
						<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:170px; overflow: auto;" >
						<table width="263px" border="0" align="left" cellpadding="2" cellspacing="1" class="BordesTabla">
						
						<?
						$j=0;
						foreach($efectos as $e){
							if($e["id"]==$ide) $cls='class=FilaOver';
							else $cls='class=Fila1  onmouseover="filaover(this)" onmouseout="filaout(this)" ';
							?>
							<tr>
								<td width=250px align=left onClick="verEfecto('<?=$e["id"]?>')"  
								<?=$cls?> title="cambiar valores"  style='cursor: pointer'><?=$e["nombre"]?></td>
								<td class=Fila1 width=13px align=center>
									<img  style='cursor: pointer' onClick="efectoEliminar('<?=$j++?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" 
									alt="eliminar" width="11" height="11">
								</td>
							</tr>
							<?}?>
						</table>
						</div><br>&nbsp;
					<?}else echo "<table><tr><td class=Txt align=left>No hay efectos relacionados<br><br></td></table>";?>
		       </td>
		       <td width=2%>&nbsp;</td>
		       <td width=56% class=Txt valign=top align=left>
		        <?if(count($efectos)>0){?>
		       	 <span class="claseCaption">Valores<br>&nbsp;</span>
		         <table width="95%" border="0" align="left" cellpadding="2" cellspacing="1" class="<?=($ide!=""?"":"")?>">
	   			    <?
			        if($ide!=""){
			        	foreach($efectos as $e){
			        		if($e["id"]==$ide){
			        			?>
			        			<tr><td class="spacer8">&nbsp;</td></tr>
			        			<tr>
			        				<td class=TxtBold width=10% nowrap align=left>Nombre del efecto:</td>
			        				<td class=TxtBold align=left>
			        					<input type="text" name="frm_nombreEfecto" value="<?=txtParaInput($e["nombre"])?>" class="input" size=40>
			        				</td>
			        			</tr>
			        			<tr>
			        				<td class=TxtBold width=10% nowrap align=left> Valor gravedad:&nbsp;</td>
			        				<td class=TxtBold align=left>
			        					<!--<input type="text" name="frm_valorGravedad" value="<?=txtParaInput($e["gravedad"])?>" class="input" size=3>-->
			        					<select name="frm_valorGravedad" class=input>
									        	<?
								        		$res=mysql_query("SELECT * FROM ad_gravedades ORDER BY valor asc");
								        		if($row=mysql_fetch_assoc($res)){
								        			echo '<option value="">-- seleccione gravedad --</option>';
								        			do{
								        				if($row["id_gravedad"]==$e["id_gravedad"]) $sel=" selected ";
								        				else $sel="";
								        				echo "<option value=\"".$row["id_gravedad"]."::".$row["valor"]."\" ".$sel.">".
								        					 "(".$row["valor"].") ".$row["nombre"]."</option>";
								        			}while($row=mysql_fetch_assoc($res));
								        			
								        		}else echo '<option value="">-- no hay gravedades --</option>';
								        		?>
				        					</select>
			        				</td>
			        			</tr> 
			        			<tr><td class="spacer8">&nbsp;</td></tr>
			        			<?
			        		}
			        	}
			        }else echo "<tr><td class=Txt align=left>Pulse sobre un efecto para modificar sus valores.</td></tr>";
			        ?>
		        </table> 
		       <?}?>
			  </td>
			</tr>
		</table>	    
		  <?
		
		
		break;
	default:
		// CAUSAS
		?>
		<table width="98%" border="0" align="center" cellpadding="2" cellspacing="1" class="">
			<tr>
				<td width=42% class=Txt align=left>
					<span class="claseCaption">&nbsp;lista de Causas<br>&nbsp;</span>
					<?if(count($causas)>0){?>	
						<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:170px; overflow: auto;" >
						<table width="263px" border="0" align="left" cellpadding="2" cellspacing="1" class="BordesTabla">
						<?
						$j=0;
						foreach($causas as $c){
							if($c["id"]==$idc) $cls='class=FilaOver';
							else $cls='class=Fila1  onmouseover="filaover(this)" onmouseout="filaout(this)" ';
							?>
							<tr>
								<td width=250px align=left onClick="verCausa('<?=$c["id"]?>')" 
								<?=$cls?> title="cambiar valores"  style='cursor: pointer'><?=$c["nombre"]?></td>
								<td class=Fila1 width=13px align=center>
									<img  style='cursor: pointer' onClick="causaEliminar('<?=$j++?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" 
									alt="eliminar" width="11" height="11">
								</td>
							</tr>
							<?}?>
						</table>
						</div><br>&nbsp;
					<?}else echo "<table><tr><td class=Txt align=left>No hay causas relacionadas<br><br></td></table>";?>
		       </td>
		       <td width=2%>&nbsp;</td>
		       <td width=56% class=Txt valign=top align=left>
		       	<?if(count($causas)>0){?>
			       	 <span class="claseCaption">Valores<br>&nbsp;</span>
			       	 <table width="100%" border="0" align="left" cellpadding="2" cellspacing="1" class="<?=($idc!=""?"":"")?>">
		   			    <?
				        if($idc!=""){
				        	foreach($causas as $c){
				        		if($c["id"]==$idc){
				        			?>				        			
				        			<tr>
				        				<td class=TxtBold width=10% nowrap align=left> Nombre de la causa:</td>
				        				<td class=Txt align=left>
				        					<input type="text" name="frm_nombreCausa" value="<?=txtParaInput($c["nombre"])?>" class="input" size=40>
				        				</td>
				        			</tr>
				        			<tr>
				        				<td class=TxtBold width=10% nowrap align=left> Valor detectabilidad:</td>
				        				<td class=Txt align=left>
				        				<!--<input type="text" name="frm_valorDetectabilidad" value="<?=txtParaInput($c["detectabilidad"])?>" class="input" size=3>-->
				        					<select name="frm_valorDetectabilidad" class=input>
									        	<?
								        		$res=mysql_query("SELECT * FROM ad_detectabilidades ORDER BY valor asc");
								        		if($row=mysql_fetch_assoc($res)){
								        			echo '<option value="">-- seleccione detectabilidad --</option>';
								        			do{
								        				if($row["id_detectabilidad"]==$c["id_detectabilidad"]) $sel=" selected ";
								        				else $sel="";
								        				echo "<option value=\"".$row["id_detectabilidad"]."::".$row["valor"]."\" ".$sel.">".
								        					 "(".$row["valor"].") ".$row["nombre"]."</option>";
								        			}while($row=mysql_fetch_assoc($res));
								        			
								        		}else echo '<option value="">-- no hay detectabilidades --</option>';
								        		?>
				        					</select>
				        				</td>
				        			</tr>
				        			<tr>
				        				<td class=TxtBold width=10% nowrap align=left valign=top> Acci&oacute;n recomendada:</td>
				        				<td class=Txt align=left>
				        					<textarea name="frm_accion"class="input" cols=40 rows=4><?=txtParaInput($c["accion"])?></textarea>
				        				</td>
				        			</tr> 
				        			<tr>
				        				<td class=TxtBold width=10% nowrap align=left valign=top> Controles:</td>
				        				<td class=Txt align=left>
				        					<textarea name="frm_controles"class="input" cols=40 rows=4><?=txtParaInput($c["controles"])?></textarea>
				        				</td>
				        			</tr> 
				        			<?
				        		}
				        	}
				        }else echo "<tr><td class=Txt align=left>Pulse sobre una causa para modificar sus valores.</td></tr>";
				        ?>
			        </table> 
			    <?}?>
			  </td>
			</tr>
		</table>	    
		  <?
		
		
}							
							
							
?>							
							</td>
						</tr>
						<tr><td class="spacer8">&nbsp;</td></tr>
				  </table>
		        </td>
		    </tr>
		    
		  </table>
		  </form>
		</td>
	  </tr>
  </table>
							




	<form id="fVuelta" action="pl_operacionEdit.php?idp=<?=$idp?>&ido=<?=$ido?>" method="POST">
	<input type="hidden" name="todos_modos" value='<?=serialize_esp($modos)?>'>
	<input type="hidden" name="frm_nombre" value="<?=txtParaInput($_POST["frm_nombre"])?>">
	<input type="hidden" name="vuelta" value="1">
	</form>

<script>
<?if($_POST["guarda_todo"]=="1" && $JSEjecutar==""){
	if($_GET["soloModo"]!=1) echo "document.getElementById('fVuelta').submit();";
	else echo "window.close();";
}
?>
</script>

</body>
</html>