<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Planificacion.class.php";
include "../recursos/class/Actividad.class.php";
global $app_rutaWEB;

$act=unserialize_esp(str_replace("\\","",$_POST["datosActividad"]));
$idPlanificacion=$_GET["idp"];

if($_POST["comprobarSubmit"]=="1"){
	if(isset($_GET["nuevo"])){
		$act["cid"]=$_POST["frm_categoria"];
		$act["anombre"]=str_replace("\"","''",str_replace("\\","",$_POST["frm_nombre"]));
	}
	$act["responsable"]=txtParaGuardar($_POST["frm_responsable"]);	
	$act["observaciones"]=txtParaInput($_POST["frm_observaciones"]);
	$act["plazo"]=$_POST["ano"]."-".$_POST["mes"]."-".$_POST["dia"];
}
if($_POST["act_guardarDatosActividad"]=="1"){
	if(isset($_GET["nuevo"])){
		if($act["cid"]!=""){
			$res=mysql_query("SELECT nombre FROM ad_categorias WHERE id_categoria=".$act["cid"]);
			if($row=mysql_fetch_row($res)) $act["cnombre"]=convTxt($row[0]);
		}else $act["cnombre"]="";
		$a=new Actividad();		
		$a->id_categoria=$act["cid"];
		$a->nombre=str_replace("'","\\'",$act["anombre"]);
		$a->cerrado=0;
		$act["ida"]=$a->id_actividad;
		$a->guardar();
	}
	if(muestraFecha($act["plazo"])=="") $act["plazo"]="";//date("Y-m-d");	
	$pos=$_GET["pos"]==""?"-1":$pos;
	if(isset($_GET["nuevo"])) $act["cerrado"]="0";
	$cadena=serialize_esp($act);
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
		<?if($_POST["act_guardarDatosActividad"]=="1"){?>
			window.opener.agregarDatosActividad("<?=$pos?>",'<?=$cadena?>',"<?=$_POST["frm_orden"]?>");
			window.close();
			//alert ('<?=$cadena?>\n<?=$pos?>\n<?=$_POST["frm_orden"]?>');
		<?}?>
		function validar(f){
			a=new Array();
			a[0]="text::"+f.frm_nombre.value+"::Introduzca el nombre de la actividad";
			a[1]="text::"+f.frm_categoria.value+"::Seleccione una categoría para la actividad";
			er=JSvFormObligatorios(a);	
			if(er=="") return	true;
			else alert (er);
		}
		function guardar(){
			<?if($_GET["nuevo"]){?>
				if(confirm("La actividad será guardada. ¿Desea continuar?")){
					if(validar(document.forms[0])){
						document.forms[0].act_guardarDatosActividad.value="1";
						document.forms[0].submit();
					}
				}
			<?}else{?>
				document.forms[0].act_guardarDatosActividad.value="1";
				document.forms[0].submit();			
			<?}?>					
		}	
	</script>
</head>
<?
/* datos del calendario */
$ano=$_POST["ano"]==""?getAnio($act["plazo"]):$_POST["ano"];
$mes=$_POST["mes"]==""?getMes($act["plazo"]):$_POST["mes"];
$dia=$_POST["dia"]==""?getDia($act["plazo"]):$_POST["dia"];
if($ano=="") $ano=date("Y");
if($mes=="") $mes=date("n");
if($dia=="") $dia=date("d");
if(strlen($dia)==1) $dia="0".$dia;
if(strlen($mes)==1) $mes="0".$mes;
while(!checkdate($mes,$dia,$ano)) $dia--;
$tdia=strlen($dia)==1?"0".$dia:$dia;
$tmes=strlen($mes)==1?"0".$mes:$mes;
$fecha = mktime(0,0,0,$mes,$dia,$ano);
$fechaInicioMes = mktime(0,0,0,$mes,1,$ano);
$fechaInicioMes = date("w",$fechaInicioMes);
$fechaCompleta="$ano-$mes-$dia";
$anoInicial = '1995';
$anoFinal = '2020';
/* fin datos calendario */
?>
<body>
	<form method="POST">
		<input type="hidden" name="dia" id="dddd" value="<?=$dia?>">
		<input type="hidden" name="datosActividad" value='<?=serialize_esp($act)?>'>
		<input type="hidden" name="comprobarSubmit" value="1">
		<input type="hidden" name="act_guardarDatosActividad" value="0">
		<input type="hidden" name="act_guardarYAgregarNuevo" value="0">
		
		<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
			<tr><td class="spacer6">&nbsp;</td>
		  <tr>
		    <td align="center" class="Tit"><span class="fBlanco"><?=$nuevo?"NUEVA":"DATOS DE LA"?> ACTIVIDAD</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
			    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	  				<tr><td class="spacer6">&nbsp;</td>
	  				
	  				
	<!-- Nombre actividad -->
	  				
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Actividad:</td>
			        <td width="80%" align="left" nowrap class="Txt">
				        <?
				        if($nuevo) echo	'<input name="frm_nombre" type="text" size="60" class="input" size="30" value="'.txtParaInput($act["anombre"]).'">';
				        else echo $act["anombre"];
				        ?>			        
			      	</td>
			      </tr>
			      
			      
	<!-- Categoría de la actividad -->	
	      
			      <?
			      if($nuevo){?>
			      	<tr>
				        <td width="20%" align="left" class="TxtBold" nowrap>Categoria:</td>
				        <td width="80%" align="left" nowrap>
				        	<select name="frm_categoria" class="input">
				        		<?
				        		$res=mysql_query("SELECT * FROM ad_categorias ORDER BY nombre");
				        		if($row=mysql_fetch_row($res)){
				        			echo "\n<option value=\"\">- Seleccione categoría -</option>\n";
				        			do{
				        				$sel=$row[0]==$act["cid"]?"selected":"";
				        				echo "<option value=\"".$row[0]."\" ".$sel.">".$row[1]."</option>\n";
				        			}while($row=mysql_fetch_row($res));			        			
				        		}
				        		?>
				        	</select>
				        </td>
				      </tr>			      
			      <?}?> 
	
	<!-- Responsable -->	
			      
			      
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Responsable:</td>
			        <td width="80%" align="left" nowrap class="Txt">
			        	<select name="frm_responsable" class=input>
				        	<?
							$sql="SELECT * FROM ad_responsables ORDER BY nombre";
							$res=mysql_query($sql);
							if($row=mysql_fetch_array($res)){
								echo "<option value=''>- Seleccione responsable -</option>";
								do{
									if($row["id_responsable"]==$act["responsable"]) $sel=" selected ";
									else $sel="";
									echo "<option value='".$row["id_responsable"]."' $sel>".$row["nombre"]." ".$row["apellidos"]."</option>";
								}while($row=mysql_fetch_array($res));
								
							}
				        	?>
				      	</select>
				      </td>
			      </tr>
			      
	<!-- Observaciones -->	
	
		      
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign="top">Observaciones:</td>
			        <td width="80%" align="left" nowrap class="Txt">
				      	<textarea name="frm_observaciones" cols="60" rows="4" class="input"><?=txtParaInput($act["observaciones"])?></textarea>
				      </td>
			      </tr>	
			      
			      
			      	      
	<!-- Inicio calendario -->
	
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>Plazo:</td>
			        <td width="80%" align="left" nowrap class="Txt">
				       	<table border="0" width=100% cellpadding="1" cellspacing="0" class="">
								  <tr>
								    <td width="220" class="">
								    	<table border=0 width="220" class="BordesTabla">
										    <tr>
											    <td>
											    	<select size="1" name="ano" class="input" onchange="this.form.submit()">
															<?
															for ($i=$anoInicial;$i<=$anoFinal;$i++) echo '<option '.($ano==$i?'selected ':' ').'value="'.$i.'">'.$i."</option>\n";
															?>
												    </select>
												  </td>											
													<td align="right" width=50% class="Txt">
											    	<select size="1" name="mes" class="input" onchange="this.form.submit()">
															<?
															$meses = Array ('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
															for($i=1;$i<=12;$i++) echo '<option '.($mes==$i?'selected ':' ').'value="'.$i.'">'.$meses[$i-1]."</option>\n";
															?>
											    	</select>
											    </td>
											  </tr>
									    </table>									    
											<table border="0" cellpadding="2" cellspacing="0" width="220" class="BordesTabla" bgcolor="#FFFFFF" height="100%">
												<?
												$diasSem = Array ('L','M','M','J','V','S','D');
												$ultimoDia = date('t',$fecha);
												$numMes = 0;
												for ($fila = 0; $fila < 7; $fila++){
												  echo "      <tr>\n";
												  for ($coln = 0; $coln < 7; $coln++){
												    $posicion = Array (1,2,3,4,5,6,0);
												    echo '        <td width="14%" height="17" class="TxtAzul" ';
												    if($fila == 0)echo ' bgcolor="#808080"';
												    if(($dia-1 == $numMes)&&(($numMes && $numMes < $ultimoDia) || (!$numMes && $posicion[$coln] == $fechaInicioMes)))
												    	echo ' bgcolor="#FFBA00"';
												    echo " align=\"center\">\n";
												    echo '        ';
												    if ($fila == 0) echo '<font color="#FFBA00"><b>'.$diasSem[$coln];
												    elseif (($numMes && $numMes < $ultimoDia) || (!$numMes && $posicion[$coln] == $fechaInicioMes)) {
												      echo '<a href="#" onclick="document.getElementById(\'dddd\').value=\''.++$numMes.'\';document.forms[0].submit()">';
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
								</table>
							</td>
			      </tr>
			      
			      
<!-- Orden -->
	
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign="top">Orden:</td>
			        <td width="80%" align="left" nowrap class="Txt">		  
			        <?
			        $v1=$_POST["frm_orden"]=="inicio"?"checked":"";
			        $v2=$_POST["frm_orden"]==""?"checked":"";
			        $v3=$_POST["frm_orden"]=="final" || ($_POST["frm_orden"]=="" && isset($_GET["nuevo"]) ) ?"checked":"";
			        ?>    	
				      	<input type="radio" name="frm_orden" value="inicio" class="input" style="border:0px" <?=$v1?>>Poner primera<br>
				      	<?if(!isset($_GET["nuevo"])){?>
				      	<input type="radio" name="frm_orden" value="" class="input" style="border:0px" <?=$v2?>>Dejar donde est&aacute;<br>
				      	<?}?>
				      	<input type="radio" name="frm_orden" value="final" class="input" style="border:0px" <?=$v3?>>Poner al final
				      </td>
			      </tr>
			      
			      
<!-- Botón guardar -->			      
			      
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
