<?
include "../recursos/conex.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Planificacion.class.php";
?>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>Calendario</title>
			<link href="/pac/html/css/asvefat<?=($_POST["generarInforme"]!=""?"Imprimir":"")?>.css" rel="stylesheet" type="text/css">
			<script language="JavaScript1.2" type="text/javascript" src="/pac/html/js/menu.js"></script>
			<script>
			function tratarFecha(dia,mes,ano){
				<?
				$funcionTratarFecha = 'document.location = "?id='.$_GET["id"].'&informe='.$_GET["informe"].'&dia="+dia+"&mes="+mes+"&ano="+ano+"'.($_GET["pos"]!=""?"&pos=".$_GET["pos"]:"").'";';
				echo $funcionTratarFecha;
				?>
			}
			function generar(){
				<?if($_GET["informe"]=="1"){?>
					document.forms[0].generarInforme.value="1";
					document.forms[0].submit();
				<?}else{?>
					document.forms[0].cerrarActividad.value="1";
					document.forms[0].submit();				
				<?}?>
			}
			<?if($_POST["generarInforme"]!=""){?>
				window.resizeTo(700,600);
			<?}?>
			</script>
	</head>

	<?
$actividades=unserialize_esp(str_replace("\\","",$_POST["actividades"]));
$fecha=$_POST["fano"]."-".$_POST["fmes"]."-".$_POST["fdia"];
$fechaMostrar=muestraFecha($fecha);

if($_POST["generarInforme"]!=""){
	$plani=new Planificacion($_GET["id"]);
	$cu=count($plani->actividades);
	if($cu>0){			
		?>
		<body>
		<br>
		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla" align=center>
			<caption>Actividades</caption>
			<tr>
			<th width="30%" align="left" nowRAP>&nbsp;Nombre </th>
		    <th width="10%" align="left" nowRAP>&nbsp;Responsable </th>
		    <th width="0%" align="center" nowRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plazo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
		    <th width="0%" align="center" nowRAP>&nbsp;Fecha cerrado </th>
		    <th width="40%" align="left" >&nbsp;Observaciones </th>
		    <th width="5%" align="left" nowRAP>&nbsp;Estado actividad</th>
		  </tr>	
		<?
		$jj=0;	
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
					if($fPlazo!="" && $fPlazo<$hoy)$tit="Actividad abierta retrasada";
					else $tit="Actividad abierta";
				}
				?>
				<tr>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>"><?=$act["anombre"]?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>"><?=$act["responsable"]?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>">&nbsp;<?=muestraFecha($act["plazo"])?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" align="center" title="<?=$tit?>">
					&nbsp;&nbsp;&nbsp;&nbsp;<?=muestraFecha($act["fecha_cerrado"])?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>"><?=$act["observaciones"]?>&nbsp;</td>
					<td align="center" valign="top" class="Fila1" nowrap><?=$tit?></td>
			    </tr>
				<?			
				$jj++;
			}
		}
		?>
		</table>
		<table width="95%">
      		<tr>
      			<td nowRap align="left" class="TxtBold">N&uacute;mero de actividades: <?=$cu?> </td>
      			<td width=100% align=right class="Txt">
      			<?=pintaLeyenda()?>
      			</td>
      		</tr>
      		<tr><td width=100% align=right colspan=2>
      		<input type="button" class="Boton" onClick="infActividades('impr',0);" value="Imprimir Informe">
			</td>
			<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
		</table>
		<?
	}else{
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
			<caption>Actividades</caption>
			<tr><td class="TxtBold" colspan=3 align=left>No hay actividades</td></tr>
			<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
		</table>
		<?		 
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}elseif($_POST["cerrarActividad"]=="1"){
	echo "ok->".$_GET["pos"]."->$ano - $mes - ".$_POST['fdia'];
	?><script>
	window.opener.ponerComoCerrado("<?=$_GET["pos"]?>",'0','<?=$_POST["fano"]."-".$_POST["fmes"]."-".$_POST["fdia"]?>');
	window.close();
	</script>
	<?	
}else{
	//calendario para elegir fecha
	
	
	$fecha = getdate(time());
	if(isset($_GET["dia"])) $dia=$_GET["dia"];
	else $dia=$fecha['mday'];
	if(isset($_GET["mes"])) $mes=$_GET["mes"];
	else $mes=$fecha['mon'];
	if(isset($_GET["ano"])) $ano=$_GET["ano"];
	else $ano=$fecha['year'];
	
	
	//para evitar pj 31 de febrero
	while(!checkdate($mes,$dia,$ano)) $dia--;
	$fecha = mktime(0,0,0,$mes,$dia,$ano);
	$fechaInicioMes = mktime(0,0,0,$mes,1,$ano);
	$fechaInicioMes = date("w",$fechaInicioMes);
	?>
	<body>
	<form method="POST">
		<input type="hidden" name="actividades" value='<?=serialize_esp($actividades)?>'>
		<input type="hidden" name="generarInforme" value=''>
		<input type="hidden" name="cerrarActividad" value=''>
		<input type="hidden" name="fdia" value="<?=$dia?>">
		<input type="hidden" name="fmes" value="<?=$mes?>">
		<input type="hidden" name="fano" value="<?=$ano?>">
		<table border="0" width=100% cellpadding="5" cellspacing="0" class="">
		  <tr>
		    <td align="center" class="Tit"><span class="fBlanco">SELECCIONE FECHA</span></td>
		  </tr>
		  <tr>
		    <td width="95%" class="Caja">
					<table width=100%>
						<tr>
							<td align="left" width=50% class="TxtBold">
					    	Mes:&nbsp;&nbsp;&nbsp;
					    	<select size="1" name="mes" class="Txt" 
							onchange="document.location = '?id=<?=$_GET["id"]?>&informe=<?=$_GET["informe"]?>&dia=<?=$dia?>&mes=' + document.forms[0].mes.value + '&ano=<?=$ano?><?=$_GET["pos"]!=""?"&pos=".$_GET["pos"]:""?>';">
								<?
								$meses = Array ('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
								for($i=1;$i<=12;$i++) echo '<option '.($mes==$i?'selected ':' ').'value="'.$i.'">'.$meses[$i-1]."</option>\n";
								?>
					    	</select>
					    </td>
					    <td align="right" width=50% class="TxtBold">
					    	Año:&nbsp;&nbsp;&nbsp;
					    	<select size="1" name="ano" class="Txt" 
							onchange="document.location = '?id=<?=$_GET["id"]?>&informe=<?=$_GET["informe"]?>&dia=<?=$dia?>&mes=<?=$mes?>&ano=' + document.forms[0].ano.value+ '<?=$_GET["pos"]!=""?"&pos=".$_GET["pos"]:""?>';">
								<?
								$anoInicial = '1995';
								$anoFinal = '2020';			
								for ($i=$anoInicial;$i<=$anoFinal;$i++) echo '<option '.($ano==$i?'selected ':' ').'value="'.$i.'">'.$i."</option>\n";
								?>
						    </select>
						  </td>
						</tr>
			    </table>
			    <table><tr><td class="spacer2">&nbsp;</td></tr></table>
			   	<table border="0" cellpadding="2" cellspacing="0" width="95%" class="" bgcolor="#FFFFFF" height="100%">
					<?
					$diasSem = Array ('L','M','M','J','V','S','D');
					$ultimoDia = date('t',$fecha);
					$numMes = 0;
					for ($fila = 0; $fila < 7; $fila++){
					  echo "      <tr>\n";
					  for ($coln = 0; $coln < 7; $coln++){
					    $posicion = Array (1,2,3,4,5,6,0);
					    echo '        <td width="14%" height="19" class="TxtAzul" ';
					    if($fila == 0)echo ' bgcolor="#808080"';
					    if(($dia-1 == $numMes)&&(($numMes && $numMes < $ultimoDia) || (!$numMes && $posicion[$coln] == $fechaInicioMes)))
					    	echo ' bgcolor="#FFBA00"';
					    echo " align=\"center\">\n";
					    echo '        ';
					    if ($fila == 0) echo '<font color="#FFBA00"><b>'.$diasSem[$coln];
					    elseif (($numMes && $numMes < $ultimoDia) || (!$numMes && $posicion[$coln] == $fechaInicioMes)) {
					      echo '<a href="#" onclick="tratarFecha('.(++$numMes).','.$mes.','.$ano.')">';
					      if($dia == $numMes)echo '<font color="#FFFFFF">';
					      echo ($numMes).'</a>';
					    }
					    echo "</td>\n";
					  }
					  echo "      </tr>\n";
					}
					?>
			   	</table>
		    </td>
		  </tr>
		  <tr><td align=center><input type="button" class="Boton" value="<?=($_GET["pos"]!=""?"Seleccionar fecha":"Imprimir informe")?>" onClick="generar()">
		</table>
	</form>
	</body>
	</html>
<?}