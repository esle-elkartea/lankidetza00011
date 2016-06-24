<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Modo.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas
comprobarAcceso(2,$us_rol);

$JSEjecutar="";
$md=new Modo($_GET["id"]);

if($_POST["comprobarSubmit"]=="1"){
	$md->codigo=$_POST["frm_codigo"];
	$md->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$md->id_ocurrencia=$_POST["frm_ocurrencia"];
	$md->efectos=$_POST["todos_efectos"]==""?Array():explode(",",$_POST["todos_efectos"]);
	$md->causas=$_POST["todos_causas"]==""?Array():explode(",",$_POST["todos_causas"]);
}


if ($_POST["act_agregarEfectos"]) $md->agregarEfectos(explode(",",$_POST["_efectos"]));	
if ($_POST["act_eliminarEfecto"]) $md->quitarEfectos(explode(",",$_POST["_efectos"]));	
if ($_POST["act_agregarCausas"])  $md->agregarCausas(explode(",",$_POST["_causas"]));	
if ($_POST["act_eliminarCausa"])  $md->quitarCausas(explode(",",$_POST["_causas"]));

if ($_POST["act_guardar"]=="1"){
	$JSEjecutar=$md->guardar();	
	if($JSEjecutar=="" && $_POST["act_verAlgo"]!=""){
		$p=explode(":",$_POST["act_verAlgo"]);
		header("Location: me_".$p[0]."_edit.php?id=".$p[1]);
		exit;	
	}
}
if ($_POST["act_eliminar"]=="1" && $_GET["id"]!="")	$md->eliminar();
if ($_POST["act_salir"]=="1" && $JSEjecutar=="") 	Header("Location: ".obtenerRedir($_GET["volver"]));

/*******************************************************************************************************************************************/




$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));



$miga_pan="Maestros específicos   >>   Modos   >>   ";
if ($md->nuevo) $miga_pan.="Nuevo Modo";
else $miga_pan.=txtParaInput($md->nombre);

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

function eliminar (){
	if(confirm("¿Está seguro de querer eliminar este modo y todas sus relaciones?")){
		document.forms[0].act_eliminar.value="1";
		document.forms[0].act_salir.value=1;
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
function cambioPestanya (desde,hasta){
	document.forms[0].p.value=hasta;
	document.forms[0].submit();
}
function relEfecto (){
	<?=JSventanaSeleccion("efecto")?>
}
function relCausa (){
	<?=JSventanaSeleccion("causa")?>
}
function agregarCausas(sel){
	document.forms[0].act_agregarCausas.value=1;
	document.forms[0]._causas.value=sel;
	document.forms[0].p.value=1;
	document.forms[0].submit();
}
function agregarEfectos(sel){
	document.forms[0].act_agregarEfectos.value=1;
	document.forms[0]._efectos.value=sel;
	document.forms[0].p.value=0;
	document.forms[0].submit();
}
function efectoEliminar (id){
	if(confirm("¿Desea eliminar la relacion con este efecto?")){
		document.forms[0].act_eliminarEfecto.value=1;
		document.forms[0]._efectos.value=id;
		document.forms[0].submit();
	}
}
function causaEliminar (id){
	if(confirm("¿Desea eliminar la relacion con esta causa?")){
		document.forms[0].act_eliminarCausa.value=1;
		document.forms[0]._causas.value=id;
		document.forms[0].submit();
	}
}
function validar (f){
	a=new Array();
	a[0]="int::"+f.frm_codigo.value+"::Debe introducir un código para el modo::El código del modo ha de ser un número entero";
	a[1]="int::"+f.frm_ocurrencia.value+"::Debe seleccionar una ocurrencia para el modo";
	a[2]="text::"+f.frm_nombre.value+"::Introduzca un nombre para el modo";
	er=JSvFormObligatorios(a);	
	if(er=="") return	true;
	else alert (er);
}
function verOp(id){
	if(confirm("¿Desea guardar los cambios en el modo?")){
		document.forms[0].act_verOp.value=id;
		document.forms[0].act_guardar.value=1;
		document.forms[0].submit();
	}else window.location="me_operaciones_edit.php?id="+id+"&volver=md_<?=$md->id_modo?>";
}
function ver(elq,id){
	if(confirm("Pulse aceptar para guardar los cambios realizados en la operación o cancelar para deshacerlos")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_verAlgo.value=elq+":"+id;
		document.forms[0].submit();
	}else window.location="me_"+elq+"_edit.php?id="+id;	
}
</script>

<!--BOTONES-->
<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Relacionar efecto  " 		onClick="relEfecto()">
			<input type="button" class="Boton" value="  Relacionar causa  " 		onClick="relCausa()">
			<input type="button" class="Boton" value="  Eliminar  " 						onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='me_modos.php'">
		</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>
<!--FIN BOTONES-->

<!--DATOS CLIENTE-->
	
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
		<form method=POST>
			<input type="hidden" name="comprobarSubmit" value="1">
			<input type="hidden" name="act_eliminar" value="0">
			<input type="hidden" name="act_guardar" 	value="0">
			<input type="hidden" name="act_verAlgo" value="">
			<input type="hidden" name="act_agregarEfectos" 	value="0">
			<input type="hidden" name="act_agregarCausas" 	value="0">
			<input type="hidden" name="act_eliminarEfecto"	value="0">
			<input type="hidden" name="act_eliminarCausa"	value="0">
			<input type="hidden" name="act_salir" 		value="0">
			<input type="hidden" name="_causas" 	value="">
			<input type="hidden" name="_efectos" 	value="">
			<input type="hidden" name="act_verOp" 	value="">
			<input type="hidden" name="todos_causas" 	value="<?=(count($md->causas)>0?implode(",",$md->causas):"")?>">
			<input type="hidden" name="todos_efectos" 	value="<?=(count($md->efectos)>0?implode(",",$md->efectos):"")?>">
			<input type="hidden" name="p" value="<?=$_POST["p"]?>">
			<tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DEL MODO</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
		    		<tr><td colspan=2 class="spacer2">&nbsp;</td></tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
		          <td align="left"><input name="frm_codigo" type="text" class="input" size="10" VALUE="<?=$md->codigo?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Modo de fallo:</td>
		          <td align="left"><input name="frm_nombre" type="text" class="input" size="60" VALUE="<?=txtParaInput($md->nombre)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Ocurrencia:</td>
		          <td align="left">		          	
		          	<select name="frm_ocurrencia" class="input">
		          		<?
		          		$res = mysql_query("SELECT * FROM ad_ocurrencias");
		          		if($row=mysql_fetch_array($res)) {
		          			echo "<option value=\"\" selected>- Seleccione ocurrencia -";
		          			do{
		          				$sel = $row["id_ocurrencia"]==$md->id_ocurrencia ? "selected" : "" ;
		          				echo "<option value='".$row["id_ocurrencia"]."' $sel>".$row["nombre"]."</option>"; 
		          			}while($row=mysql_fetch_array($res));
			          	}
			          	else echo "<option value=\"\">- No hay ocurrencias -</option>";
		          		?>
		          	</select>
		          </td>
		        </tr>
		        <tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
		  		</table>
		    </td>
		  </tr>
		  <tr><td colspan=2 class="spacer10">&nbsp;</td></tr>
	  </form>
	</table>
	
<!--FIN DATOS MODO-->



<?
$p=new Pest($_POST["p"]);
$p->add("Efectos del modo de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Causas del modo de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->add("Operaciones","#","onClick=\"cambioPestanya('".$_POST["p"]."','2')\"");
$p->pintar();
?>

	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td class="Caja" align=center valign=top>
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td class="spacer8"><BR>&nbsp;</td>
							</tr>
		<?
switch($_POST["p"]){
	
	case "1": 
	
		/********************************************************************************************************************************
		/*CAUSAS*/	
		
		$csas=$md->obtenerCausas();
		if(count($csas)>0){
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Causas</caption>
				<tr>
			    <th width="80%" align="left" nowRAP>&nbsp;Nombre </th>
			    <th width="15%" align="left" nowRAP>&nbsp;Detectabilidad</th>
			    <th width="5%" align="left" nowRAP>&nbsp;</th>
			  </tr>	
			<?	
			for($i=0;$i<count($csas);$i++){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
		      <td align="left" class="Fila1" onClick="ver('causas','<?=$csas[$i]["id_causa"]?>')">
		      	<a href="#"><?=$csas[$i]["nombre"]?></a>
		      </td>
		      <td align="left" class="Fila1" onClick="ver('causas','<?=$csas[$i]["id_causa"]?>')">&nbsp;
		      	<?=$csas[$i]["detectabilidad"]?>
		      </td>
		      <td align="center" class="Fila1">
		      	<img onClick="causaEliminar('<?=$csas[$i]["id_causa"]?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" width="11" height="11"> 
		      </td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<table width="95%">
      	<tr>
					<td  align="left" class="TxtBold">N&uacute;mero de causas de fallo relacionadas: <?=count($csas)?> </td>
				</tr>
				<tr>
					<td align="left" class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Causas</caption>
				<tr><td class="TxtBold" colspan=3 align=left>No hay causas de fallo relacionadas</td></tr>
				<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?		 
		}
		break;
		
	case "2":
		
		$sql="".
		"SELECT o.id_operacion,o.codigo,o.nombre FROM me_operacion_modo om ".
		"INNER JOIN me_operaciones o ON o.id_operacion=om.id_operacion ".
		"WHERE om.id_modo=".$md->id_modo;
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0){
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
			<caption>Operaciones</caption>
			<tr>
			    <th width="15%" align="left" nowRAP>&nbsp;C&oacute;digo </th>
			    <th width="80%" align="left" nowRAP>&nbsp;Nombre</th>
			</tr>				
			<?
			while($row=mysql_fetch_row($res)){
				?>	
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" >
				<!--onClick="verOp('<?=$row[0]?>')">-->
					<td align="left" class="Fila1" onClick="ver('operaciones','<?=$row[0]?>')"><?=$row[1]?></td>
					<td align="left" class="Fila1" onClick="ver('operaciones','<?=$row[0]?>')"><?=$row[2]?></td>
				</tr>
				<?				
			}
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1">
			<caption>Operaciones</caption>
			<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones con este modo de fallo</td></tr>
			<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
			<?	
		}
		echo "</table><br>";	
	
	break;
			
		
	default: 
	
		/********************************************************************************************************************************
		/*EFECTOS*/
		
		$efcs=$md->obtenerEfectos();
		if(count($efcs)>0){
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Efectos</caption>
				<tr>
			    <th width="80%" align="left" nowRAP>&nbsp;Nombre </th>
			    <th width="15%" align="left" nowRAP>&nbsp;Gravedad</th>
			    <th width="5%" align="left" nowRAP>&nbsp;</th>
			  </tr>	
			<?	
			for($i=0;$i<count($efcs);$i++){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
		      <td align="left" class="Fila1" onClick="ver('efectos','<?=$efcs[$i]["id_efecto"]?>')">
		      	<a href="#"><?=$efcs[$i]["nombre"]?></a>
		      </td>
		      <td align="left" class="Fila1" onClick="ver('efectos','<?=$efcs[$i]["id_efecto"]?>')">&nbsp;
		      	<?=$efcs[$i]["gravedad"]?>
		      </td>
		      <td align="center" class="Fila1">
		      	<img onClick="efectoEliminar('<?=$efcs[$i]["id_efecto"]?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" width="11" height="11"> 
		      </td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<table width="95%">
      	<tr>
					<td  align="left" class="TxtBold">N&uacute;mero de efectos de fallo relacionados: <?=count($efcs)?> </td>
				</tr>
				<tr>
					<td align="left" class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Efectos</caption>
				<tr>
					<td class="TxtBold" colspan=3 align=left>No hay efectos de fallo relacionados</td>
				</tr>
				<tr>
					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?		 
		}
}
?>



</table>
</td></tr></table>

</form>
<script><?=$JSEjecutar?></script>
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