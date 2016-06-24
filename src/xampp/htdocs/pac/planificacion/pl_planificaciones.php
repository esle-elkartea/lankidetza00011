<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
comprobarAcceso(1,$us_rol);
$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));

$miga_pan="Planificación avanzada >> Listado de planificaciones";


$busca=false;
if($b_referencia!="" || $b_cliente!="" || $b_desde!="" || $b_hasta!="" ){
	$busca=true;
	$where="";
	if($b_referencia!="") $where=" AND r.num LIKE '%".$b_referencia."%'";
	if($b_cliente!="") $where.=" AND c.nombre LIKE '%".$b_cliente."%'";
	if($b_desde!="") $where.=" AND p.fecha >= '".$b_desde."'";
	if($b_hasta!="") $where.=" AND p.fecha <= '".$b_hasta."'";
	
}


//para la paginación
if(!isset($_POST["pag"]) || $_POST["pag"] < 0 ) $pag=0;
else $pag=$_POST["pag"];
$numeroResultadosPorPagina=10;
$limiteDesde=$numeroResultadosPorPagina*$pag;
$limiteHasta=$numeroResultadosPorPagina;
$sql = 	"SELECT count(*) FROM planificaciones p LEFT JOIN me_referencias r ON r.id_referencia=p.id_referencia ".
				"LEFT JOIN me_clientes c ON c.id_cliente=p.id_cliente".
				" WHERE 1 $where";
$res=mysql_query($sql);
$row=mysql_fetch_row($res);
$numResultados=$row[0];

flush();
ob_start();
?>


<script>
function filaover(elemento){
	elemento.style.cursor='hand';
	elemento.className='FilaOver'
}
function filaout(elemento){
	elemento.className='Fila'
}
function selDesde(dia,mes,ano){
	document.forms[0].b_desde.value=ano+"-"+mes+"-"+dia;
	document.forms[0].b_fechaDesde.disabled=false;
	document.forms[0].b_fechaDesde.value=dia+"/"+mes+"/"+ano;
	document.forms[0].b_fechaDesde.disabled=true;
}
function selHasta(dia,mes,ano){
	document.forms[0].b_hasta.value=ano+"-"+mes+"-"+dia;
	document.forms[0].b_fechaHasta.disabled=false;
	document.forms[0].b_fechaHasta.value=dia+"/"+mes+"/"+ano;
	document.forms[0].b_fechaHasta.disabled=true;
}

</script>

<!--BOTONES-->
	<table border=0 width=100% >
		<tr>
			<td align="right"><input type="button" class="Boton" value="  Crear planificaci&oacute;n  " onClick="window.location='pl_planificaciones_edit.php'"></td>
		</tr>
		<tr>
		  <td valign="top" class="spacer4">&nbsp;</td>
		</tr>
	</table>
<!--FIN BOTONES-->


<!--BUSCADOR-->
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
		  <tr>
		    <td align="center" class="Tit"><span class="fBlanco">BUSCADOR</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<form method=POST>
					<input type="hidden" name="pag" id="pag" value="0">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
						<tr>
							<td class="spacer4" colspan=5>&nbsp;</td>
							<td rowspan=5 valign=bottom align=left>
							<input type="button" onClick="this.form.reset();" value="Vaciar campos " class="Boton">
							<input type="submit" value=" Buscar " class="Boton"><br>&nbsp;</td>
						</tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Referencia:</td>
		          <td align="left" colspan=4><input name="b_referencia" type="text" class="input" size="30" VALUE="<?=txtParaInput($b_referencia)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Cliente:</td>
		          <td align="left" colspan=4><input name="b_cliente" type="text" class="input" size="85" VALUE="<?=txtParaInput($b_cliente)?>"></td>
		        </tr>
		        <tr>
      				<td width="15%" align="left" class="TxtBold" nowrap>Fecha desde:</td>
      				<td width="25%" align="left"  class="TxtAzul" nowrap>
      					<input name="b_fechaDesde" type="text" class="input" size="8" maxlength="10" VALUE="<?=muestraFecha($b_desde)?>" disabled>
		          	<a href="#" onClick="<?=JSventanaCalendario("selDesde",getDia($b_desde),getMes($b_desde),getAnio($b_desde))?>">
		          		<img  border=0 src="<?=$app_rutaWEB?>/html/img/calendar.gif">
		          	</a>
		          	<input type="hidden" name="b_desde" value="<?=$b_desde?>">
      				</td>
		          <td width="1%"><?printEspacios(12)?></td>
		          <td width="1%" align="right" class="TxtBold" nowrap>Fecha hasta: <?printEspacios(3)?></td>
		          <td align="left" class="TxtAzul">
		          	<input name="b_fechaHasta" type="text" class="input" size="8" maxlength="10" VALUE="<?=muestraFecha($b_hasta)?>" disabled>
		          	<a href="#" onClick="<?=JSventanaCalendario("selHasta",getDia($b_hasta),getMes($b_hasta),getAnio($b_hasta))?>">
		          		<img  border=0 src="<?=$app_rutaWEB?>/html/img/calendar.gif">
		          	</a>
		          	<input type="hidden" name="b_hasta" value="<?=$b_hasta?>">
		          </td>
		        </tr>
						<tr>
							<td class="spacer4" colspan=5>&nbsp;</td>
						</tr>		        
		        </table>
		    </td>
		  </tr>
		  <tr>
				<td align="left" colspan=2  class="spacer8">&nbsp;</td>
			</tr>
		</table>
<!--FIN BUSCADOR-->

<!--LISTADO-->
	<?
	$order=" ORDER BY c.nombre";
	$limit=" LIMIT $limiteDesde,$limiteHasta";
	$sql = 	"SELECT p.id_planificacion,p.fecha,r.num,c.nombre as n2 FROM planificaciones p LEFT JOIN me_referencias r ON r.id_referencia=p.id_referencia ".
			"LEFT JOIN me_clientes c ON c.id_cliente=p.id_cliente ".
			"WHERE 1 ";
	if($numResultados>0){
		if($res=mysql_query($sql.$where.$order.$limit)){
			?>	
			<table width="100%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<tr>
			    <th width="15%" align="left" nowRAP>&nbsp;C&oacute;digo Referencia </th>
			    <th width="85%" align="left" nowRAP>&nbsp;Cliente</th>
			    <th width="85%" align="left" nowRAP>&nbsp;Fecha de planificaci&oacute;n</th>
			  </tr>	
			<?	
			while($row=mysql_fetch_array($res)){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" onClick="window.location='pl_planificaciones_edit.php?id=<?=$row["id_planificacion"]?>'">
		      <td align="left" class="Fila1"><a href="#">&nbsp;<?=$row["num"]?></a> </td>
		      <td align="left" class="Fila1">&nbsp;<?=$row["n2"]?></td>
		      <td align="center" class="Fila1">&nbsp;<?=muestraFecha($row["fecha"])?></td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<?
			pintarPaginacion($numResultados,$pag,"planificaciones");
		}
	}else{
		if($where=="") $txt="No hay planificaciones creadas la base de datos";
		else $txt="No hay planificaciones con lo parámetros de búsqueda introducidos";
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<tr>
					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
				</tr>
		  	<tr>
					<td class="TxtBold" colspan=3 align=center><?=$txt?></td>
				</tr>
				<tr>
					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
		<?
	
	}
	?>

	</form>
<!--FIN LISTADO-->
<?





$centro=ob_get_contents();
ob_end_clean();
$tpl->assign("{CONTENIDOCENTRAL}",$centro); 
$tpl->assign("{MIGADEPAN}",$miga_pan); 
$tpl->assign("{BOTONESTEMPLATE}",botonesTemplate($us_rol)); 
$tpl->assign("{USUARIO}",$us_nombre." ".$us_apellidos);  
$tpl->parse(CONTENT, main);
$tpl->FastPrint(CONTENT);
?>