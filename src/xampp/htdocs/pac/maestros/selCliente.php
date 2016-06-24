<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Planificacion.class.php";
//include "../recursos/class/Actividad.class.php";
global $app_rutaWEB;
if($_POST["yasta"]=="1" && $_POST["id_cliente"]!="") $JSEjecutar="window.close();window.opener.confirmPlanificacion('".$_POST["id_cliente"]."');";	
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
			document.forms[0].yasta.value="1";	
			document.forms[0].submit();			
		}	
	</script>
</head>
<body>
	<form method="POST">
		<input type="hidden" name="comprobarSubmit" value="1">
		<input type="hidden" name="yasta" value="0">
		<input type="hidden" name="datosRelacion" value='<?=serialize_esp($obj)?>'>
		<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
			<tr><td class="spacer6">&nbsp;</td>
		  <tr>
		    <td align="center" class="Tit"><span class="fBlanco">CREAR PLANIFICACIÓN</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
			    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	  				<tr><td class="spacer6">&nbsp;</td>
	  				<tr>
				        <td width="20%" align="left" class="TxtBold" nowrap>Referencia:</td>
				        <td width="80%" align="left" nowrap class="Txt"><?=$_GET["r"]?></td>
				    </tr>
			        <tr>
				        <td width="20%" align="left" class="TxtBold" nowrap>Cliente:</td>
				        <td width="80%" align="left" nowrap class="Txt">
					        <select name="id_cliente" class="input">
					        	<?
					        	$t=explode("[::]",$obj["m"]);
								$res=mysql_query("SELECT id_cliente,nombre FROM me_clientes ORDER BY nombre");
								if($row=mysql_fetch_row($res)) {
									echo "<option value=''>- Seleccione cliente -</option>";
									do echo "<option value='".$row[0]."'>".$row[1]."</option>";	while($row=mysql_fetch_row($res));
								}else echo "<option value=''>- No hay clientes -</option>";
								?>
					        </select>
				      	</td>
				      </tr>
				   <tr>
					 <td colspan=2 rowspan=2 class="TxtBold" nowrap align="right">
					   <input type="button" class="Boton" value="  Crear  " onClick="guardar()">
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