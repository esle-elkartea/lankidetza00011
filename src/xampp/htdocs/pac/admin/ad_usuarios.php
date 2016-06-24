<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
comprobarAcceso(3,$us_rol);
$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));

$miga_pan="Administración >> Usuarios";

$where="";
if($b_nombre!="") 		$where.=" AND nombre LIKE '%".txtParaGuardar($b_nombre)."%'";
if($b_apellidos!="")  $where.=" AND apellidos LIKE '%".txtParaGuardar($b_apellidos)."%'";
if($b_alta!=$b_baja){
	if($b_alta!="")  $where.=" AND baja=0 ";
	if($b_baja!="")  $where.=" AND baja=1 ";
}


//para la paginación
if(!isset($_POST["pag"]) || $_POST["pag"] < 0 ) $pag=0;
else $pag=$_POST["pag"];
$numeroResultadosPorPagina=10;
$limiteDesde=$numeroResultadosPorPagina*$pag;
$limiteHasta=$numeroResultadosPorPagina;
$sql="SELECT count(*) FROM usuarios WHERE 1 $where";

$res=mysql_query($sql);
$row=mysql_fetch_row($res);
$numResultados=$row[0];

$inicio=false;
if(count($_POST)==0) $inicio=true;

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

	<table border=0 width=100% >
		<tr><td align="right"><input type="button" class="Boton" value="  Crear usuario  " onClick="window.location='ad_usuarios_edit.php'"></td></tr>
		<tr><td align="center" class="spacer4">&nbsp;</td></tr>	
	</table>
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
	  <tr><td align="center" class="Tit"><span class="fBlanco">BUSCADOR</span></td></tr>
	  <tr>
	    <td align="center" class="Caja">
		  <form method=POST>
		  <input type="hidden" name="pag" id="pag" value="0">				
	      <table width="90%" border="0" cellspacing="2" cellpadding="4">
    		<tr><td align="left" colspan=2  class="spacer2">&nbsp;</td></tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>&nbsp;&nbsp;Nombre:</td>
	          <td align="left"><input name="b_nombre" type="text" class="input" size="50" VALUE="<?=txtParaInput($b_nombre)?>"></td>
	        </tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>&nbsp;&nbsp;Apellidos:</td>
	          <td align="left"><input name="b_apellidos" type="text" class="input" size="50" VALUE="<?=txtParaInput($b_apellidos)?>"></td>
	          <td align="left"><input type="submit" class="Boton" value="Buscar"></td>
	        </tr>
	        <tr>
	          <td width="100%" align="left" class="TxtBold" nowrap colspan=2>
	          	<table border=0 width=100%>
	          	  <tr>
	          	    <td width=30% align="left" nowrap>
	          			<input type="checkbox" name="b_alta" value="1" <?=$b_alta==1 || $inicio?"checked":""?>>&nbsp;Usuarios de alta
	          		<br>
	          			<input type="checkbox" name="b_baja" value="1" <?=$b_baja==1 || $inicio?"checked":""?>>&nbsp;Usuarios de baja
	          		</td>
	          		<td width=40%>&nbsp;</td>
	          	  </tr>
	          	</table>
	          </td>
	        </tr>
	        <tr><td align="left" colspan=2  class="spacer2">&nbsp;</td></tr>
	      </table>
	    </td>
	  </tr>
	</table>
	<?
	$order=" ORDER BY apellidos";
	$limit=" LIMIT $limiteDesde,$limiteHasta";
	$sql="SELECT * FROM usuarios WHERE 1 $where";
	if($numResultados>0){
		if($res=mysql_query($sql.$where.$order.$limit)){
			?>	
			<br>
			<table width="100%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
		  	<tr>
		  	  <th width="1%" align="left" nowRAP>&nbsp;Baja&nbsp;</th>
			    <th width="60%" align="left" nowRAP>&nbsp;Apellidos </th>
			    <th width="39%" align="left" nowRAP>&nbsp;Nombre</th>
			  </tr>	
			<?	
			while($row=mysql_fetch_array($res)){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" onClick="window.location='ad_usuarios_edit.php?id=<?=$row["id_usuario"]?>'">
		      <td align="center" class="Fila1">&nbsp;<?=($row["baja"]=="0"?"":"<img src='/pac/html/img/x.gif' style='height:10px'>")?>&nbsp;</td>
		      <td align="left" class="Fila1"><a href="#">&nbsp;<?=$row["apellidos"]?>&nbsp;</a> </td>
		      <td align="left" class="Fila1">&nbsp;<?=$row["nombre"]?>&nbsp;</td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<?
			pintarPaginacion($numResultados,$pag,"usuarios");
		}
	}else{
		if($where=="") $txt="No hay usuarios en la base de datos";
		else $txt="No existen usuarios con lo parámetros de búsqueda introducidos";		
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