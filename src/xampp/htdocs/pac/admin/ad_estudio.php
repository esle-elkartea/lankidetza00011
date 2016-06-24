<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Pregunta.class.php";
comprobarAcceso(2,$us_rol);

if($_POST["act_subePregunta"]!="") { $pr=new Pregunta($_POST["act_subePregunta"]); $pr->subirEnLista(); }
if($_POST["act_bajaPregunta"]!="") { $pr=new Pregunta($_POST["act_bajaPregunta"]); $pr->bajarEnLista(); }

$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));



$miga_pan="Tablas de Mantenimiento >> Estudio de factibilidad ";

$where="";
if($b_nombre!="") 		$where.=" AND nombre LIKE '%".txtParaGuardar($b_nombre)."%'";


//para la paginación
if(!isset($_POST["pag"]) || $_POST["pag"] < 0 ) $pag=0;
else $pag=$_POST["pag"];

$numeroResultadosPorPagina=10;
$limiteDesde=$numeroResultadosPorPagina*$pag;
$limiteHasta=$numeroResultadosPorPagina;
$sql="SELECT count(*) FROM ad_preguntas WHERE 1 $where";

$res=mysql_query($sql);
$row=mysql_fetch_row($res);
$numResultados=$row[0];

flush();
ob_start();
?>


<script>
function filaover(elemento){
	//elemento.style.cursor='hand';
	elemento.className='FilaOver'
}
function filaout(elemento){
	elemento.className='Fila'
}
function sube(id){
	document.forms[0].act_subePregunta.value=id;
	document.forms[0].submit();
}function baja(id){
	document.forms[0].act_bajaPregunta.value=id;
	document.forms[0].submit();
}
</script>

	<table border=0 width=100% >
		<tr>
			<td align="right"><input type="button" class="Boton" value="  Agregar pregunta  " onClick="window.location='ad_estudio_edit.php'"></td>
		</tr>
		<tr>
		    <td align="center" class="spacer4">&nbsp;</td>
		  </tr>	
	</table>

		<table width="100%" border="0" cellspacing="1" cellpadding="0">
		 	<tr>
		    <td align="center" class="Tit"><span class="fBlanco">BUSCADOR</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
					<form method=POST>
					<input type="hidden" name="act_subePregunta" value="">
					<input type="hidden" name="act_bajaPregunta" value="">				
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
		    		<tr>
							<td align="left" colspan=2  class="spacer2">&nbsp;</td>
						</tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Nombre:</td>
		          <td align="left"><input name="b_nombre" type="text" class="input" size="50" VALUE="<?=txtParaInput($b_nombre)?>"></td>
		          <td align="left"><input type="submit" class="Boton" value="Buscar"></td>
		        </tr>
		        <tr>
							<td align="left" colspan=2  class="spacer2">&nbsp;</td>
						</tr>
		    	</table>
		    </td>
		  </tr>
		</table>


	<?
	$order=" ORDER BY orden,nombre";
	$limit=" LIMIT $limiteDesde,$limiteHasta";
	$sql="SELECT * FROM ad_preguntas WHERE 1 $where";
	if($numResultados>0){
		if($res=mysql_query($sql.$where.$order.$limit)){
			?>	
			<br>
			<table width="100%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
		  	<tr>
		  		<th width="1%">&nbsp;</th>
			    <th width="85%" align="left" nowRAP>&nbsp;Nombre </th>
			 </tr>	
			<?	
			while($row=mysql_fetch_array($res)){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
					<td width="1%" class="Fila1">
						<?=FlechasOrden("sube('".$row["id_pregunta"]."')","baja('".$row["id_pregunta"]."')")?>
					</td>
					<td style="cursor:pointer" width="99%" align="left" class="Fila1"
					onClick="window.location='ad_estudio_edit.php?id=<?=$row["id_pregunta"]?>'">&nbsp;<?=$row["nombre"]?></td>
				</tr>
						<?			
			}	
			?>
			</table>
			<?
			pintarPaginacion($numResultados,$pag,"preguntas");
		}
	}else{
		if($where=="") $txt="No hay preguntas introducidas para el estudio de factibilidad";
		else $txt="No existen preguntas con lo parámetros de búsqueda introducidos";		
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
<?


$centro=ob_get_contents();
ob_end_clean();
$tpl->assign("{CONTENIDOCENTRAL}",$centro); 
$tpl->assign("{MIGADEPAN}",$miga_pan); 
$tpl->assign("{MENUADMINISTRACION}",mostrarMenuAdmin($us_rol)); 
$tpl->assign("{BOTONESTEMPLATE}",botonesTemplate($us_rol)); 
$tpl->assign("{USUARIO}",$us_nombre." ".$us_apellidos);  
$tpl->parse(CONTENT, main);
$tpl->FastPrint(CONTENT);
?>
