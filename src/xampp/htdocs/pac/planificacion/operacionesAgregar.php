<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";

global $app_rutaWEB;

if($_POST["comprobarSubmit"]=="1"){
	$arrayTodo=unserialize_esp(str_replace("\\","",$_POST["todos_relaciones"]));
	$listaComponentes=array();
	$comps=array();
	foreach ($arrayTodo as $o){
		if($o["c"]!="" && strpos($listaComponentes,$o["c"])===false) {
			$comps[]=$o["c"];
			$listaComponentes.="##".$o["c"];
		}
	}
	$pp=explode(",",$_POST["seleccionOperaciones"]);
	$idOperacion=$pp[0];
	$sql="SELECT codigo,nombre FROM me_operaciones WHERE id_operacion=$idOperacion";
	$res=mysql_query($sql);
	$row=mysql_fetch_row($res);
	$CmasN="[".$row[0]."] ".$row[1];
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
	<script language="JavaScript" type="text/JavaScript">
		window.resizeTo(500, 200);
		function agregar(){
			window.opener.agregarOperacion('<?=$idOperacion?>',document.forms[0].componente.value);
			if(document.forms[0].seleccionOperaciones.value.indexOf("<?=$idOperacion?>,")=="-1") window.close();
			else {
				document.forms[0].seleccionOperaciones.value=document.forms[0].seleccionOperaciones.value.replace("<?=$idOperacion?>,","");
				document.forms[0].submit();	
			}
		}
	</script>
</head>

<body>
	<form method="POST">
	<input type="hidden" name="comprobarSubmit" value="1">
	<input type="hidden" name="todos_relaciones"  value='<?=serialize_esp($arrayTodo)?>'>
	<input type="hidden" name="seleccionOperaciones" value="<?=$_POST["seleccionOperaciones"]?>">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
		<tr><td class="spacer6">&nbsp;</td>
		<tr>
			<td align="center" class="Tit"><span class="fBlanco">UBICACI&Oacute;N DE LAS OPERACIONES</span></td>
		</tr>
		<tr>
			<td align="center" class="Caja">
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
					<tr><td class="spacer6">&nbsp;</td></tr>
					<tr>
						<td width="20%" align="left" class="TxtBold" nowrap>Operaci&oacute;n:</td>
						<td width="80%" align="left" nowrap class="Txt"><?=$CmasN?></td>
					</tr>
					<tr>
						<td width="20%" align="left" class="TxtBold" nowrap>Ubicaci&oacute;n:</td>
						<td width="80%" align="left" nowrap class="Txt">
							<select class="input" name="componente">
								<?
								echo "<option value=\"\">-- No introducir en componente --</option>";
								if(count($comps)>0){
									foreach($comps as $c){
										$partes=explode("[::]",$c);
										echo "<option value=\"".$partes[0]."\">".$partes[1]."</option>";
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan=3 align=right><input type="button" class="Boton" value=" Agregar " onClick="agregar()"></td>
					</tr>
					<tr><td class="spacer6">&nbsp;</td>
				</table>
			</td>
		</tr>
	</table>
	</form>
</body>

<script>
<?if($_POST["comprobarSubmit"]==""){?>	
	document.forms[0].todos_relaciones.value=window.opener.getRelaciones();
	document.forms[0].seleccionOperaciones.value='<?=$_GET["ids"]?>';
	document.forms[0].submit();	
<?}?>
</script>
</html>
