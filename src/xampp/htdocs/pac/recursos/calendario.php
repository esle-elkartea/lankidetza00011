<html>
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Calendario</title>
		<link href="/pac/html/css/asvefat.css" rel="stylesheet" type="text/css">
		<script language="JavaScript1.2" type="text/javascript" src="/pac/html/js/menu.js"></script>
		<script>
		function tratarFecha(funcion,dia,mes,ano){
			document.location = "?funcionJS="+funcion+"&dia="+dia+"&mes="+mes+"&ano="+ano;
		}
		</script>
</head>



<?

$fecha = getdate(time());

if(isset($_GET["dia"]) && $_GET["dia"]!="") $dia=$_GET["dia"];
else $dia=$fecha['mday'];

if(isset($_GET["mes"]) && $_GET["mes"]!="") $mes=$_GET["mes"];
else $mes=$fecha['mon'];

if(isset($_GET["ano"]) && $_GET["ano"]!="") $ano=$_GET["ano"];
else $ano=$fecha['year'];

//para evitar pj 31 de febrero
while(!checkdate($mes,$dia,$ano)) $dia--;

$tdia=strlen($dia)==1?"0".$dia:$dia;
$tmes=strlen($mes)==1?"0".$mes:$mes;


$fecha = mktime(0,0,0,$mes,$dia,$ano);
$fechaInicioMes = mktime(0,0,0,$mes,1,$ano);
$fechaInicioMes = date("w",$fechaInicioMes);
?>


<body>
<form>
	<table border="0" width=100% cellpadding="5" cellspacing="0" class="">
	  <tr>
	    <td width="100%" class="Caja">
				
				<!--***************-->
				<!-- Select de mes -->
				<!--***************-->
				<table width=100%>
					<tr>
						<td align="left" width=50% class="TxtBold">
				    	Mes:&nbsp;&nbsp;&nbsp;<select size="1" name="mes" class="Txt" onchange="document.location = '?funcionJS=<?=$_GET["funcionJS"]?>&dia=<?=$tdia?>&mes=' + document.forms[0].mes.value + '&ano=<?=$ano?>';">
								<?
								$meses = Array ('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
								for($i=1;$i<=12;$i++) echo '<option '.($mes==$i?'selected ':' ').'value="'.$i.'">'.$meses[$i-1]."</option>\n";
								?>
				    	</select>
				    </td>
				    <td align="right" width=50% class="TxtBold">
				    	<!--**************-->   
				    	<!-- Select de año-->	
				    	<!--**************-->
				    	
				    	Año:&nbsp;&nbsp;&nbsp;<select size="1" name="ano" class="Txt" onchange="document.location = '?funcionJS=<?=$_GET["funcionJS"]?>&dia=<?=$tdia?>&mes=<?=$mes?>&ano=' + document.forms[0].ano.value;">
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
		    
		    <!--********************-->
		    <!-- Tabla con los días -->
		    <!--********************-->
		    
		    <table border="0" cellpadding="2" cellspacing="0" width="100%" class="" bgcolor="#FFFFFF" height="100%">
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
					      echo '<a href="#" onclick="tratarFecha(\''.$_GET["funcionJS"].'\','.(++$numMes).','.$mes.','.$ano.')">';
					      if($dia == $numMes)echo '<font color="#FFFFFF">';
					      echo ($numMes).'</a>';
					    }
					    echo "</td>\n";
					  }
					  echo "      </tr>\n";
					}
					?>
					<tr>
						<td colspan=7 align=right>
						<input type="button" class="Boton" value="Aceptar" onClick="window.opener.<?=($_GET["funcionJS"]."('".$tdia."','".$tmes."','".$ano."')")?>;window.close();">
						</td>
					</tr>
		   	</table>
		   	
		   	
		   	
	    </td>
	  </tr>
	</table>
</form>
</body>





</html>