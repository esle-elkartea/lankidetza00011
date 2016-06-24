<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
comprobarAcceso(2,$us_rol);
$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));

$miga_pan="Maestros específicos >> Efectos";

$where="";
if($b_codigo!="") 		$where.=" AND e.codigo LIKE '%".txtParaGuardar($b_codigo)."%'";
if($b_nombre!="") $where.=" AND e.nombre LIKE '%".txtParaGuardar($b_nombre)."%'";
if($b_gravedad!="")  		$where.=" AND g.nombre LIKE '%".txtParaGuardar($b_gravedad)."%'";



//para la paginación
if(!isset($_POST["pag"]) || $_POST["pag"] < 0 ) $pag=0;
else $pag=$_POST["pag"];
$numeroResultadosPorPagina=10;
$limiteDesde=$numeroResultadosPorPagina*$pag;
$limiteHasta=$numeroResultadosPorPagina;
$sql="SELECT count(*) FROM me_efectos e LEFT JOIN ad_gravedades g ON e.id_gravedad=g.id_gravedad WHERE 1 $where";
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
</script>

<!--BOTONES-->
	<table border=0 width=100% >
		<tr>
			<td align="right"><input type="button" class="Boton" value="  Crear efecto  " onClick="window.location='me_efectos_edit.php'"></td>
		</tr>
		<tr>
		    <td align="center" class="spacer4">&nbsp;</td>
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
							<td align="left" colspan=2  class="spacer2">&nbsp;</td>
						</tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
		          <td align="left"><input name="b_codigo" type="text" class="input" size="10" VALUE="<?=txtParaInput($b_codigo)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Efecto:</td>
		          <td align="left"><input name="b_nombre" type="text" class="input" size="60" VALUE="<?=txtParaInput($b_nombre)?>"></td>
		         
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Gravedad:</td>
		          <td align="left"><input name="b_gravedad" type="text" class="input" size="60" VALUE="<?=txtParaInput($b_gravedad)?>"></td>
		          <td align="left"><input type="submit" class="Boton" value="Buscar"></td>
		        </tr>
		        <tr>
							<td align="left" colspan=2  class="spacer2">&nbsp;</td>
						</tr>
		    	</table>
		    </td>
		  </tr>
		</table>
<!--FIN BUSCADOR-->

<!--LISTADO-->
	<?
	$order=" ORDER BY e.nombre";
	$limit=" LIMIT $limiteDesde,$limiteHasta";
	$sql="SELECT e.id_efecto,e.codigo,e.nombre FROM me_efectos e LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad WHERE 1 $where";
	if($numResultados>0){
		if($res=mysql_query($sql.$where.$order.$limit)){
			?>	
			<br>
			<table width="100%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
		  	<tr>
			    <th width="20%" align="left" nowRAP>&nbsp;C&oacute;digo </th>
			    <th width="80%" align="left" nowRAP>&nbsp;Efecto</th>
			  </tr>	
			<?	
			while($row=mysql_fetch_array($res)){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" style="cursor:pointer" onClick="window.location='me_efectos_edit.php?id=<?=$row["id_efecto"]?>'">
		      <td align="left" class="Fila1"><a href="#">&nbsp;<?=$row["codigo"]?>&nbsp;</a> </td>
		      <td align="left" class="Fila1">&nbsp;<?=$row["nombre"]?>&nbsp;</td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<?
			pintarPaginacion($numResultados,$pag,"referencias");
		}
	}else{
		if($where=="") $txt="No hay efectos en la base de datos";
		else $txt="No existen efectos con lo parámetros de búsqueda introducidos";		
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