<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
global $app_rutaWEB;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Documento sin t&iacute;tulo</title>
	<link href="<?=$app_rutaWEB?>/html/css/asvefatImprimir.css" rel="stylesheet" type="text/css">	
	<script language="JavaScript1.2" type="text/javascript" src="<?=$app_rutaWEB?>/html/js/menu.js"></script>
</head>
<body onLoad="window.print()">
<?

$idp=$_GET["id"];

$sql="SELECT pregunta,respuesta,observaciones FROM pl_estudio_preguntas WHERE id_planificacion=$idp ORDER BY orden asc";
$res=mysql_query($sql);
$sql="SELECT fecha,decision,observaciones FROM pl_estudios WHERE id_planificacion=$idp";
$res2=mysql_query($sql);
$rowEstudio=mysql_fetch_array($res2);
$i=0;
if($row=mysql_fetch_row($res)){
	?>	
	<br>
	<table width="95%" border="0" cellpadding="1" cellspacing="1" class="BordesTabla" align="center">
		<caption>Estudio de factibilidad (<?=muestraFecha($rowEstudio["fecha"])?>)</caption>
		<tr>
			<th align=left>&nbsp;&nbsp;Pregunta</th>
			<th align=left>&nbsp;&nbsp;Respuesta</th>
			<th align=left>&nbsp;&nbsp;Observaciones</th>
		</tr>
		
		
		<?do{?>
		<tr>
	      <td align="left" width=60% class="Fila1"  valign=center>
	      	<span class="TxtBold">&nbsp;<?=$i+1?>.</span>&nbsp;&nbsp;<?=$row[0]?>
	      </td>
	      <td class="Fila" align="center" nowrap width=10% valign=center>
	       	<?
	       	if($row[1]=="0") echo "<b>NO</b>";
	       	elseif($row[1]=="1") echo "<b>S&Iacute;</b>";
	       	elseif($row[1]=="2") echo "<b>NO PROCEDE </b>";
	       	elseif($row[1]=="-1") echo "<b>- sin responder -</b>";
	       	else echo "&nbsp;";
	       	$i++;
	       	?>
	      </td>
	      <td class="Fila" align="left" valign=top width=30%>&nbsp;<?=$row[2]?></td>    
	    </tr>	
		<?}while($row=mysql_fetch_row($res));?>
		
		
	</table>
	<br>
	<table width="95%" border="0" cellpadding="1" cellspacing="1" align=center class=BordesTabla >
		<tr>
			<td align="left" width=15% class="TxtBold" nowrap>Fecha del estudio:</td>
			<td align="left" class="Txt">&nbsp;<?=muestraFecha($rowEstudio["fecha"])?></td>
		</tr>
		<tr>
			<td align="left" width=15% class="TxtBold" nowrap>Decisión final:</td>
			<td align="left" class="TxtBold">&nbsp;	<?=($rowEstudio["decision"]=="0"?"NO FACTIBLE":$rowEstudio["decision"]=="1"?"FACTIBLE":"SIN CONTESTAR")?></td>
		</tr>
		<tr>
			<td align="left" width=15% class="TxtBold" valign="top" nowrap>Observaciones:</td>
			<td align="left" class="Txt">&nbsp;<?=$rowEstudio["observaciones"]?></td>
		</tr>
	</table>
<?}?>
</body>
</html>