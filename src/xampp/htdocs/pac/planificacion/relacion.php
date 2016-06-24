<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Planificacion.class.php";
//include "../recursos/class/Actividad.class.php";
global $app_rutaWEB;

$obj=unserialize_esp(str_replace("\\","",$_POST["datosRelacion"]));

if($_POST["guarda"]=="1"){
	if($_POST["id_maquina"]!=""){
		$res=mysql_query("SELECT CONCAT(nombre,'[::]',codigo) FROM ad_maquinas WHERE id_maquina=".$_POST["id_maquina"]);
		$row=mysql_fetch_row($res);
		$obj["m"]=$_POST["id_maquina"].'[::]'.convTxt($row[0]);
	}else $obj["m"]="";
	if($_POST["id_operacion"]!=""){
		$res=mysql_query("SELECT CONCAT(nombre,'[::]',codigo) FROM me_operaciones WHERE id_operacion=".$_POST["id_operacion"]);
		$row=mysql_fetch_row($res);
		$obj["oAlt"]=$_POST["id_operacion"].'[::]'.convTxt($row[0]);
	}else $obj["oAlt"]="";
	$JSEjecutar.="window.opener.agregarDatosRelacion('".$_GET["pos"]."','".serialize_esp($obj)."');window.close();";
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
		<?=$JSEjecutar?>
		function guardar(){
			document.forms[0].guarda.value="1";	
			document.forms[0].submit();			
		}	
	</script>
</head>

<body>
	<form method="POST">
		<input type="hidden" name="comprobarSubmit" value="1">
		<input type="hidden" name="guarda" value="0">
		<input type="hidden" name="datosRelacion" value='<?=serialize_esp($obj)?>'>
		
		<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
			<tr><td class="spacer6">&nbsp;</td>
		  <tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA OPERACIÓN</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
			    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	  				<tr><td class="spacer6">&nbsp;</td>
	  				<tr>
				        <td width="20%" align="left" class="TxtBold" nowrap>Operaci&oacute;n:</td>
				        <td width="80%" align="left" nowrap class="Txt"><?$t=explode("[::]",$obj["o"]);?><?=$t[1]?></td>
				    </tr>
			        <tr>
				        <td width="20%" align="left" class="TxtBold" nowrap>M&aacute;quina:</td>
				        <td width="80%" align="left" nowrap class="Txt">
					        <select name="id_maquina" class="input">
					        	<?
					        	$t=explode("[::]",$obj["m"]);
								$res=mysql_query("SELECT id_maquina,nombre FROM ad_maquinas ORDER BY nombre");
								if($row=mysql_fetch_row($res)) {
									echo "<option value=''>- Seleccione máquina -</option>";
									do echo "<option value='".$row[0]."' ".($row[0]==$t[0]?"selected":"").">".$row[1]."</option>";	while($row=mysql_fetch_row($res));
								}else echo "<option value=''>- No hay máquinas -</option>";
								?>
					        </select>
				      	</td>
				      </tr>
				    <tr>
				        <td width="20%" align="left" class="TxtBold" nowrap>Operaci&oacute;n Alternativa:</td>
				        <td width="80%" align="left" nowrap class="Txt">
					        <select name="id_operacion" class="input">
					        	<?
								$res=mysql_query("SELECT id_operacion,nombre FROM me_operaciones ORDER BY nombre");
								$t=explode("[::]",$obj["oAlt"]);
								if($row=mysql_fetch_row($res)) {
									echo "<option value=''>- Seleccione altenativa -</option>";
									do echo "<option value='".$row[0]."' ".($row[0]==$t[0]?"selected":"").">".$row[1]."</option>";	while($row=mysql_fetch_row($res));
								}else echo "<option value=''>- No hay operaciones -</option>";
								?>
					        </select>
				      	</td>
				    </tr>

					<tr>
						<td colspan=2 rowspan=2 class="TxtBold" nowrap align="right">
							<input type="button" class="Boton" value="Guardar" onClick="guardar()">
						</td>
					</tr>
			      	<tr><td class="spacer6">&nbsp;</td></tr>
			  </table>
		   	</td>
		  </tr>
		</table>
	</form>
</body>


</html>
