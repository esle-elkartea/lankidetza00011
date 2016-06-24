<?

include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";

include "../recursos/class/Cliente.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas

comprobarAcceso(2,$us_rol);


/****************************************************************************************
/* guardar cliente */
$JSEjecutar="";
$cli=new Cliente($_GET["id"]);	

if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$cli->nombre=txtParaGuardar($_POST["frm_cliente"]);
	$cli->num=txtParaGuardar($_POST["frm_numero"]);
	$cli->npr=txtParaGuardar($_POST["frm_npr"]);
	$cli->num_proveedor=txtParaGuardar($_POST["frm_num_proveedor"]);	
}
if ($_POST["act_crearPlani"]=="1")					$JSEjecutar=$cli->crearPlanificacion($_POST["ref"]);
if ($_POST["act_guardar"]=="1")						$JSEjecutar=$cli->guardar();
if ($_POST["act_eliminar"]=="1") 					$JSEjecutar=$cli->eliminar();
if ($_POST["act_salir"]=="1" && $JSEjecutar=="") 	Header("Location: ".obtenerRedir($_GET["volver"]));

$miga_pan="Maestros específicos   >>   Clientes   >>   ";
if ($cli->nuevo) $miga_pan.="Nuevo Cliente";
else $miga_pan.=txtParaInput($cli->nombre);


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));
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
function crearPlanificacion (){
	<?=JSventanaSeleccion("referencia","radio=1");?>
}
function copiarReferencia(algo){
	if(confirm("Para crear la planificación los datos del cliente\n serán guardados. ¿Desea continuar?")){
		if(validar(document.forms[0])){
			document.forms[0].act_crearPlani.value="1";
			document.forms[0].ref.value=algo;
			document.forms[0].submit();
		}
	}
}
function validar (f){
	a=new Array();
	a[0]="int::"+f.frm_numero.value+"::Debe rellenar el campo número de cliente::El número de cliente ha de ser un número entero";
	a[1]="text::"+f.frm_cliente.value+"::Introduzca un nombre de Cliente";
	a[2]="int::"+f.frm_npr.value+"::Debe rellenar el campo N.P.R::El valor del campo N.P.R ha de ser un número entero";
	er=JSvFormObligatorios(a);	
	b=new Array();
	b[0]="int::"+f.frm_num_proveedor.value+"::El valor del campo número de proveedor ha de ser un número entero";
	er+=JSvForm(b);	
	if(er=="") return	true;
	else alert (er);
}
function eliminar (){
	if(confirm("¿Está seguro de querer eliminar este cliente?")){
		document.forms[0].act_eliminar.value="1";
		document.forms[0].act_salir.value="1";
		document.forms[0].submit();
	}
}
function guardarSalir(){
	if(validar(document.forms[0])){
		document.forms[0].act_guardar.value=1;
		document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}
}
function verPlani(id){
	if(confirm("Los cambios realizados en el cliente se perderán\n¿Desea continuar?")){
		window.location="../planificacion/pl_planificaciones_edit.php?id="+id+"&volver=cli_<?=$cli->id_cliente?>";	
	}
}
</script>

<!--BOTONES-->
<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Crear planificación  " 	onClick="crearPlanificacion()">
			<input type="button" class="Boton" value="  Eliminar cliente  " 		onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='<?=obtenerRedir($_GET["volver"])?>'">
		</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>
<!--FIN BOTONES-->

<!--DATOS CLIENTE-->
	
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
			<form method=POST>
			<input type="hidden" name="comprobarSubmit" value="1">
			<input type="hidden" name="act_crearPlani" 	value="0">
			<input type="hidden" name="act_eliminar" 	value="0">
			<input type="hidden" name="act_guardar" 	value="0">
			<input type="hidden" name="act_salir" 		value="0">
			<input type="hidden" name="nuevoCliente" 	value="0">
			<input type="hidden" name="ref" 			value="">
			<tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DEL CLIENTE</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
			    	
			    	<tr><td colspan=2 class="spacer2">&nbsp;</td></tr>
			    	
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>N&uacute;mero cliente:</td>
		          <td align="left"><input name="frm_numero" type="text" class="input" size="10" VALUE="<?=$cli->num?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Cliente:</td>
		          <td align="left"><input name="frm_cliente" type="text" class="input" size="60" VALUE="<?=txtParaInput($cli->nombre)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>N.P.R.:</td>
		          <td align="left"><input name="frm_npr" type="text" class="input" size="10" VALUE="<?=($cli->npr==''?'100':$cli->npr)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>N&uacute;mero proveedor:</td>
		          <td align="left"><input name="frm_num_proveedor" type="text" class="input" size="10" VALUE="<?=$cli->num_proveedor?>"></td>
		        </tr>
		        <tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
		  		</table>
		    </td>
		  </tr>
		  <tr><td colspan=2 class="spacer10">&nbsp;</td></tr>
	  </form>
	</table>
	
<!--FIN DATOS CLIENTE-->

<!--LISTADO PANIFICACIONES-->
<?
$p=new Pest($_POST["p"]);
$p->add("Planificaciones","#","");
$p->pintar();
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td class="Caja" align=center valign=top>
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td class="spacer8"><BR>&nbsp;</td>
				<table width="95%" border="0" cellpadding="2" cellspacing="1"  align=center>
					<caption>Planificaciones del cliente</caption>
<?
$sql="SELECT r.num,r.nombre,p.fecha,p.id_planificacion FROM planificaciones p ".
"LEFT JOIN me_referencias r ON r.id_referencia=p.id_referencia ".
"WHERE p.id_cliente=".$cli->id_cliente." ORDER BY r.nombre ";
$res=@mysql_query($sql);
if($row=@mysql_fetch_row($res)){
	?>	
	<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
  	<tr>
	    <th width="15%" align="left" nowRAP>&nbsp;Nº Referencia </th>
	    <th width="75%" align="left" nowRAP>&nbsp;Nombre</th>
	    <th width="10%"  align="center" nowRAP>&nbsp;Fecha</th>
	 </tr>	
	<?	
	do{
		?>
		<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
	      <td align="left" class="Fila1" onClick="verPlani('<?=$row[3]?>')"><?=$row[0]?></a></td>
	      <td align="left" class="Fila1" onClick="verPlani('<?=$row[3]?>')"><?=$row[1]?></td>
	      <td align="center" class="Fila1" onClick="verPlani('<?=$row[3]?>')"><?=muestraFecha($row[2])?></td>
	    </tr>
		<?			
	}while($row=mysql_fetch_row($res));	
	?>
	</table>
	<?
	//pintarPaginacion($numResultados,$pag);
}else{
	?>
		<tr><td class="TxtBold" colspan=3 align=left>No hay planificaciones creadas con este cliente</td></tr>
		<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
	</table>
	<?		
}
?>
<br>
</form>
<script><?=$JSEjecutar?></script>
<!--FIN LISTADO PLANIFICACIONES-->
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