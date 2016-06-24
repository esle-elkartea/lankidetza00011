<?
include_once "../recursos/conex.php";
include_once "../recursos/seguridad.php";
include_once "../recursos/genFunctions.php";
include_once "../recursos/class/Componente.class.php";
include_once "../recursos/class/Operacion.class.php";
include_once "../recursos/class/Pest.class.php"; //clase de pestañas

comprobarAcceso(2,$us_rol);

$comp=new Componente($_GET["id"]);
$JSEjecutar="";
if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$comp->codigo=$_POST["frm_codigo"];
	$comp->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$comp->operaciones = $_POST["todos_operaciones"]==""?Array():explode(",",$_POST["todos_operaciones"]);
}
if ($_POST["act_copiarEstructura"]!="")		$comp->copiarEstructura($_POST["act_copiarEstructura"]);
if ($_POST["act_agregarOperaciones"]) 		$comp->agregarOperaciones(explode(",",$_POST["operaciones"]));
if ($_POST["act_eliminarOperacion"])			$comp->quitarOperaciones($_POST["operaciones"]);
if ($_POST["act_eliminar"]=="1")					$comp->eliminar();
if ($_POST["act_subirOp"]=="1")						$comp->subeOrden($_POST["operaciones"]);
if ($_POST["act_bajarOp"]=="1")						$comp->bajaOrden($_POST["operaciones"]);
if ($_POST["act_guardar"]=="1")						$JSEjecutar=$comp->guardar();

if ($_POST["act_verRef"]=="1" && $JSEjecutar=="")	Header("Location: me_referencias_edit.php?id=".$_POST["operaciones"]."&volver=comp_".$comp->id_componente);
if ($_POST["act_verOp"]=="1" && $JSEjecutar=="")	Header("Location: me_operaciones_edit.php?id=".$_POST["operaciones"]."&volver=comp_".$comp->id_componente);
if ($_POST["act_salir"]=="1" && $JSEjecutar=="")	Header("Location: ".obtenerRedir($_GET["volver"]));


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));


$miga_pan="Maestros específicos   >>   Componentes   >>   ";
if ($comp->nuevo) $miga_pan.="Nuevo Componente";
else $miga_pan.=str_replace("\\","",$comp->nombre);

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
	
}
function validar(f){
	a=new Array();
	a[0]="int::"+f.frm_codigo.value+"::Debe introducir un código de componente::El código de componente ha de ser un número entero";
	a[1]="text::"+f.frm_nombre.value+"::Introduzca un nombre para el nuevo componente";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
<?
$planis=$comp->compruebaPlanis();
if($planis==0){?>
function eliminar (){
	if(confirm("¿Está seguro de querer eliminar este componente?")){
		document.forms[0].act_eliminar.value="1";
		document.forms[0].act_salir.value="1";
		document.forms[0].submit();
	}	
}
<?}else{?>
function eliminar (){
	alert("Existen <?=$planis?> planificacion(es) con este componente relacinado.\nEl componente no puede ser eliminado.");	
}
<?}?>
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
function copiarComponente(sel){
	if(confirm("Todas los datos y relaciones del componente actual serán sustituídos por los del componente seleccionado\n¿Seguro que desea continuar?")){
		document.forms[0].act_copiarEstructura.value=sel;
		document.forms[0].submit();
	}
}
function relOp (){
	 <?=JSventanaSeleccion("operacion")?>
}
function agregarOperaciones(sel){
	document.forms[0].act_agregarOperaciones.value=1;
	document.forms[0].operaciones.value=sel;
	document.forms[0].submit();
}
function opElim (id){
	if(confirm("¿Desea eliminar la relacion con esta operación?")){
		document.forms[0].act_eliminarOperacion.value=1;
		document.forms[0].operaciones.value=id;
		document.forms[0].submit();
	}
}

function copiarEstructura(){
	<?=JSventanaSeleccion("componente","radio=1")?>
}
function sube(p){
	document.forms[0].operaciones.value=p;
	document.forms[0].act_subirOp.value="1";
	document.forms[0].submit();
}
function baja(p){
	document.forms[0].operaciones.value=p;
	document.forms[0].act_bajarOp.value="1";
	document.forms[0].submit();
}
function abreR(id){
	if(confirm("¿Desea guardar los cambios de esta componente antes de ir a editar la referencia?")){
		document.forms[0].act_verRef.value=1;
		document.forms[0].act_guardar.value=1;
		document.forms[0].operaciones.value=id;
		document.forms[0].submit();
	}else window.location="me_componentes_edit.php?id="+id+"&volver=comp_<?=$comp->id_componente?>";
}
function abreO(id){
	if(confirm("¿Desea guardar los cambios de este componente antes de ir a editar la operación?")){
		document.forms[0].act_verOp.value=1;
		document.forms[0].act_guardar.value=1;
		document.forms[0].operaciones.value=id;
		document.forms[0].submit();
	}else window.location="me_operaciones_edit.php?id="+id+"&volver=comp_<?=$comp->id_componente?>";
}
</script>

<!--BOTONES-->
<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Copiar estructura" 			onClick="copiarEstructura()">
			<input type="button" class="Boton" value="  Relacionar operacion  " 	onClick="relOp()">
			<input type="button" class="Boton" value="  Eliminar  " 				onClick="eliminar()">
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
			<input type="hidden" name="act_eliminar" value="0">
			<input type="hidden" name="act_guardar" 	value="0">
			<input type="hidden" name="act_agregarOperaciones" 	value="0">
			<input type="hidden" name="todos_operaciones" value="<?=implode(",",$comp->operaciones)?>">
			<input type="hidden" name="act_eliminarOperacion"	value="0">
			<input type="hidden" name="act_salir" 		value="0">
			<input type="hidden" name="act_subirOp" 	value="0">
			<input type="hidden" name="act_bajarOp" 	value="0">
			<input type="hidden" name="act_verRef" 	value="0">
			<input type="hidden" name="act_verOp" 	value="0">
			<input type="hidden" name="operaciones" 	value="">
			<input type="hidden" name="act_copiarEstructura" value="">
			<input type="hidden" name="p" value="0">
			<tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DEL COMPONENTE</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
		    		<tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo componente:</td>
		          <td align="left"><input name="frm_codigo" type="text" class="input" size="10" VALUE="<?=txtParaInput($comp->codigo)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
		          <td align="left"><input name="frm_nombre" type="text" class="input" size="60" VALUE="<?=txtParaInput($comp->nombre)?>"></td>
		        </tr>
		        <tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
		  		</table>
		    </td>
		  </tr>
		  <tr><td colspan=2 class="spacer10">&nbsp;</td></tr>
	  </form>
	</table>
	

<?
$p=new Pest($_POST["p"]);
$p->add("Operaciones","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Referencias","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->add("AMFE","#","onClick=\"cambioPestanya('".$_POST["p"]."','2')\"");
$p->add("Plan de control","#","onClick=\"cambioPestanya('".$_POST["p"]."','3')\"");
$p->pintar();
?>

	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr><td class="Caja" align=center valign=top>
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr><td class="spacer8"><BR>&nbsp;</td></tr>
		<?
switch($_POST["p"]){
	
	case "1": 
	
		/********************************************************************************************************************************
		/* Referencias */	
		
		$sql="".
		"SELECT r.* FROM me_referencia_relacion rr,me_referencias r ".
		"WHERE rr.tipo='C' AND rr.id_relacion=r.id_referencia AND rr.id_relacion=".$comp->id_componente;
		$res=mysql_query($sql);
		if($row=@mysql_fetch_array($res)) {
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
			  <caption>Referencias</caption>
				<tr>
				    <th width="15%" align="left" nowRAP>&nbsp;Nº Referencia </th>
				    <th width="85%" align="left" nowRAP>&nbsp;Nombre</th>
				  </tr>	
			<?
			$c=0;
			do{
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" onClick="abreR('<?=$row[0]?>')">
			      <td align="left" class="Fila1"><?=$row["num"]?></td>
			      <td align="left" class="Fila1">&nbsp;<?=$row["nombre"]?></td>
			    </tr>
				<?							
				$c++;
			}while($row=mysql_fetch_array($res));
			?></table>
			<table width="95%">
      			<tr><td  align="left" class="TxtBold">N&uacute;mero de referencias con este componente: <?=$c?> </td></tr>
				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Referencias</caption>
				<tr><td class="TxtBold" colspan=3 align=left>No hay referencias con este componente relacionado</td></tr>
				<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?	
		}
		break;
		
		
		
		
	case "2": 
	
		/********************************************************************************************************************************
		/* AMFE */
		echo $comp->pintarFilaAMFE(true);
		break;
	
		
		
	case "3": 
		/********************************************************************************************************************************
		/* Plan de control */
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1">
		<caption>plan de control</caption>
			
		<?
		$todo="";
		if(count($comp->operaciones)>0){
			echo pintarCabeceraPControl();
			foreach($comp->operaciones as $op){
				$o=new Operacion($op);
				echo $o->generarPlanDeControl();	
			}
		}else echo "<tr><td class=TxtBold align=LEft>No hay operaciones relacionadas con el componente.</td></tr>";
		?>
		</table>
		<br><br>
		<?
		break;
			
		
	default: 
	
		/********************************************************************************************************************************
		/* Operaciones */
		
		$ssq=Array();
		// no se otra manera de indicar que recoga los registros en el orden del array -> union all
		for($i=0;$i<count($comp->operaciones);$i++){
			$ssq[]="SELECT * FROM me_operaciones WHERE id_operacion=".$comp->operaciones[$i];
		}
		$sql=implode(" UNION ALL ",$ssq);
		if($res=mysql_query($sql)) $cuantos=mysql_num_rows($res);		
		if($row=@mysql_fetch_row($res)){		
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Operaciones</caption>
				<tr>
					<th width="15px" align="left" nowRAP>&nbsp;</th>
				    <th width="15%" align="left" nowRAP>&nbsp;C&oacute;digo </th>
				    <th width="80%" align="left" nowRAP>&nbsp;Nombre</th>
				    <th width="5%" align="left" nowRAP>&nbsp;</th>
				  </tr>	
			<?	
			$i=0;
			do{
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
			      <td align="left" class="Fila1"><?=FlechasOrden("sube('".$i."')","baja('".$i++."')")?></td>
			      <td align="left" class="Fila1" onClick="abreO('<?=$row[0]?>')"><?=$row[1]?></td>
			      <td align="left" class="Fila1" onClick="abreO('<?=$row[0]?>')">&nbsp;<?=$row[2]?></td>
			      <td align="center" class="Fila1">
			      	<img onClick="opElim('<?=$row[0]?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" 
					width="11" height="11"> 
			      </td>
			    </tr>
				<?			
			}while($row=@mysql_fetch_row($res));
			?>
			</table>
			<table width="95%">
      			<tr><td  align="left" class="TxtBold">N&uacute;mero de operaciones relacionadas: <?=$cuantos?> </td></tr>
				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Operaciones</caption>
				<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones relacionadas</td></tr>
				<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?		 
		}
}
?>

</table>
</td></tr></table>

<script>
<?=$JSEjecutar?>
</script>

</form>
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