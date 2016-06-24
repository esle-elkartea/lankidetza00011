<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Referencia.class.php";
include "../recursos/class/Operacion.class.php";
include "../recursos/class/Componente.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas
comprobarAcceso(2,$us_rol);

$JSEjecutar="";
$ref=new Referencia($_GET["id"]);

if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	//los valores han podido ser cambiados antes
	$ref->num=$_POST["frm_numero"];
	$ref->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$ref->plano=txtParaGuardar($_POST["frm_plano"]);
	$ref->nivel=txtParaGuardar($_POST["frm_nivel"]);
	$ref->fecha=$_POST["frm_fecha"];
	$ref->id_familia=$_POST["frm_id_familia"];
	$ref->id_clase=$_POST["frm_id_clase"];
	$ref->relaciones = $_POST["todos_relaciones"]==""?Array():unserialize_esp($_POST["todos_relaciones"]);
}


if ($_POST["act_copiarEstructura"]!="") 		$ref->copiarEstructura($_POST["act_copiarEstructura"]);	
if ($_POST["act_agregarComponente"]=="1")		$ref->agregarComponentes(explode(",",$_POST["_componentes"]));	
if ($_POST["act_agregarOperacion"]=="1")		$ref->agregarOperaciones(explode(",",$_POST["_operaciones"]));	
if ($_POST["act_subeRel"]=="1") 				$ref->subirOrdenRelacion($_POST["_posicion"]);
if ($_POST["act_bajaRel"]=="1") 				$ref->bajarOrdenRelacion($_POST["_posicion"]);
if ($_POST["eliminaRef"]=="1")					$JSEjecutar=$ref->eliminar();
if ($_POST["act_eliminarOperacion"]=="1" )		$ref->quitarOperacion($_POST["_operaciones"]);
if ($_POST["act_eliminarComponente"]=="1") 		$ref->quitarComponente($_POST["_componentes"]);
if ($_POST["act_crearPlanificacion"]!="") 		{$ref->crearPlanificacion($_POST["act_crearPlanificacion"]);$bonload="alert('La planificación ha sido creada.');";}

if ($_POST["act_guardar"]=="1")	$JSEjecutar=$ref->guardar();
if ($_POST["act_salir"]=="1"  && $JSEjecutar=="") header("Location: ".obtenerRedir($_GET["volver"]));
if ($_POST["act_verCom"]=="1" && $JSEjecutar=="") header("Location: me_componentes_edit.php?id=".$_POST["_componentes"]."&volver=ref_".$ref->id_referencia);
if ($_POST["act_verCli"]=="1" && $JSEjecutar=="") header("Location: ../planificacion/pl_planificaciones_edit.php?id=".$_POST["_componentes"]."&volver=ref_".$ref->id_referencia);
if ($_POST["act_verOp"]=="1"  && $JSEjecutar=="") header("Location: me_operaciones_edit.php?id=".$_POST["_operaciones"]."&volver=ref_".$ref->id_referencia);

if($_POST["p"]=="1") echo guardaScrolls();

$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));


$miga_pan="Maestros específicos >> Referencias >> ";
if ($ref->nuevo) $miga_pan.="Nueva Referencia";
else $miga_pan.=str_replace("\\","",$ref->nombre);

flush();
ob_start();


?>
<script>
function filaout(elemento){
	elemento.className='Fila'
}
function filaover(elemento){
	elemento.style.cursor='hand';
	elemento.className='FilaOver'
}
function validar(f){
	a=new Array();
	a[0]="int::"+f.frm_numero.value+"::Debe rellenar el campo número de referencia::El número de referencia ha de ser un número entero";
	a[1]="text::"+f.frm_nombre.value+"::Introduzca un nombre para la referencia";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
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
function agregarComponentes(sel){
	document.forms[0].act_agregarComponente.value=1;
	document.forms[0]._componentes.value=sel;
	document.forms[0].submit();
}
function agregarOperaciones(sel){
	document.forms[0].act_agregarOperacion.value=1;
	document.forms[0]._operaciones.value=sel;
	document.forms[0].submit();
}
function nuevoComp(){
	<?=JSventanaSeleccion("componente")?>
}
function nuevoOper(){
	<?=JSventanaSeleccion("operacion")?>
}
function copiarEstructura(){
	<?=JSventanaSeleccion("referencia","radio=1")?>
}
function copiarReferencia(id){
	if(confirm("Todas los datos y relaciones de la referencia actual serán sustituídos por los de la referencia seleccionada\n¿Seguro que desea continuar?")){
		document.forms[0].act_copiarEstructura.value=id;
		document.forms[0].submit();
	}
}
function eliminarRef(){
	if(confirm("¿Está seguro de que desea eliminar esta referencia y todas sus relaciones?")){
		document.forms[0].eliminaRef.value="1";
		document.forms[0].act_salir.value="1";
		document.forms[0].submit();
	}
}
function abreC(id){
	if(confirm("¿Desea guardar los cambios de esta referencia antes de ir a editar el componente?")){
		document.forms[0].act_verCom.value=1;
		document.forms[0].act_guardar.value=1;
		document.forms[0]._componentes.value=id;
		document.forms[0].submit();
	}else window.location="me_componentes_edit.php?id="+id+"&volver=ref_<?=$ref->id_referencia?>";
}
function abreO(id){
	if(confirm("¿Desea guardar los cambios de esta referencia antes de ir a editar la operación?")){
		document.forms[0].act_verOp.value=1;
		document.forms[0].act_guardar.value=1;
		document.forms[0]._operaciones.value=id;
		document.forms[0].submit();
	}else window.location="me_operaciones_edit.php?id="+id+"&volver=ref_<?=$ref->id_referencia?>";
}
function verCli(id){
	if(confirm("¿Desea guardar los cambios de esta referencia antes de ir a editar el cliente?")){
		document.forms[0].act_verCli.value=1;
		document.forms[0].act_guardar.value=1;
		document.forms[0]._componentes.value=id;
		document.forms[0].submit();
	}else window.location="../planificacion/pl_planificaciones_edit.php?id="+id+"&volver=ref_<?=$ref->id_referencia?>";
}
function eliminaC (c) {
	if(confirm("¿Desea eliminar la relacion con este componente? "+c)){
		document.forms[0].act_eliminarComponente.value=1;
		document.forms[0]._componentes.value=c;
		document.forms[0].submit();	
	}
}
function eliminaO (o) {
	if(confirm("¿Desea eliminar la relacion con esta operación? "+o)){
		document.forms[0].act_eliminarOperacion.value=1;
		document.forms[0]._operaciones.value=o;
		document.forms[0].submit();	
	}
}
function fechaSeleccionada (dia,mes,ano){
	tdMostrar=dia+"/"+mes+"/"+ano;
	tdBD=ano+"-"+mes+"-"+dia;
	document.forms[0].frm_fechaMostrar.value=tdMostrar;
	document.forms[0].frm_fecha.value=tdBD;
	document.forms[0].submit();
}
function subirOrden(i,a){
	document.forms[0].act_subeRel.value='1';
	document.forms[0]._posicion.value=i;
	document.forms[0].submit();
}
function bajarOrden(i,a){
	document.forms[0].act_bajaRel.value='1';
	document.forms[0]._posicion.value=i;
	document.forms[0].submit();
}
function confirmPlanificacion(idCliente){
	if(confirm("Para crear una planificación la referencia deberá guardarse.\n¿Desea continuar?")) {
		if(validar(document.forms[0])){
			document.forms[0].act_guardar.value="1";
			document.forms[0].act_crearPlanificacion.value=idCliente;
			document.forms[0].submit();	
		}
	}
}
function plani(){
	<?=JSVentanaSeleccion("cliente","r=".str_replace("'","\\'",str_replace("\\","",$ref->nombre)))?>
}
function genRTF(todo){
	document.forms[1].submit();		
}

</script>


<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="Crear planificación" 		onClick="plani()">
			<input type="button" class="Boton" value="Copiar estructura" 		onClick="copiarEstructura()">
			<input type="button" class="Boton" value="Relacionar componente" 	onClick="nuevoComp()">
			<input type="button" class="Boton" value="Relacionar operación" 	onClick="nuevoOper()">
			<input type="button" class="Boton" value="Eliminar" 				onClick="eliminarRef()">
			<input type="button" class="Boton" value="Guardar y Salir" 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="Salir"          			onClick="window.location='<?=obtenerRedir($_GET["volver"])?>'">			
		</td>
	</tr>
	<tr>
		<td class="spacer2">&nbsp;</td>
	</tr>
</table>


	<table width="100%" border="0" cellspacing="1" cellpadding="0">
		  <tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA REFERENCIA</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    <form method=POST name="f_datos">		    
				<input type="hidden" name="comprobarSubmit" value="1">
				<input type="hidden" name="act_copiarEstructura" value="">				
				<input type="hidden" name="p" value="<?=$_POST["p"]?>">				
				<input type="hidden" name="eliminaRef" value="0">				
				<input type="hidden" name="act_agregarComponente" 	value="0">
				<input type="hidden" name="act_eliminarComponente" 	value="0">
				<input type="hidden" name="act_agregarOperacion" 	value="0">
				<input type="hidden" name="act_eliminarOperacion" 	value="0">		
				<input type="hidden" name="act_crearPlanificacion" 	value="">				
				<input type="hidden" name="_componentes" 	value="">
				<input type="hidden" name="_operaciones" 	value="">			
				<input type="hidden" name="idsC" 	value="<?=implode(",",$ref->idsC)?>">
				<input type="hidden" name="idsO" 	value="<?=implode(",",$ref->idsO)?>">
				<input type="hidden" name="todos_relaciones" 	value='<?=(count($ref->relaciones)>0?serialize_esp($ref->relaciones):"")?>'>
				<input type="hidden" name="act_bajaRel" 	value="">
				<input type="hidden" name="act_subeRel" 	value="">
				<input type="hidden" name="_posicion" 		value="">
				<input type="hidden" name="act_guardar" 	value="0">
				<input type="hidden" name="act_verCom" 	value="0">
				<input type="hidden" name="act_verCli" 	value="0">
				<input type="hidden" name="act_verOp" 	value="0">
				<input type="hidden" name="act_salir" 		value="0">		
				<input type="hidden" name="guardadoParaRelacion" value="0">	
				<input type="hidden" name="scrollPosicion" id="scrollPosicion" value="0">
				
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
				<tr>
					<td class="spacer4" colspan=5>&nbsp;</td>
				</tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>N&uacute;mero referencia:</td>
		          <td align="left" colspan=4><input name="frm_numero" type="text" class="input" size="10" VALUE="<?=txtParaInput($ref->num)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Denominación:</td>
		          <td align="left" colspan=4><input name="frm_nombre" type="text" class="input" size="100" VALUE="<?=txtParaInput($ref->nombre)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Plano:</td>
		          <td align="left" colspan=4><input name="frm_plano" type="text" class="input" size="100" VALUE="<?=txtParaInput($ref->plano)?>"></td>
		        </tr>		      
		        <tr>
      				<td width="15%" align="left" class="TxtBold" nowrap>Nivel de ingernier&iacute;a:</td>
      				<td width="10%" align="left"><input name="frm_nivel" type="text" class="input" size="30" VALUE="<?=txtParaInput($ref->nivel)?>"></td>
		          <td width="1%"><?printEspacios(20)?></td>
		          <td width="1%" align="left" class="TxtBold" nowrap>Fecha: <?printEspacios(8)?></td>
		          <td align="left" class="TxtAzul">
		          	<?$fechaMostrar=$ref->fecha=="" ? date("Y-m-d") : $ref->fecha;?>
		          	<input name="frm_fechaMostrar" type="text" class="input" size="8" maxlength="10" VALUE="<?=muestraFecha($fechaMostrar)?>" disabled>
		          	<a href="#" onClick="<?=JSventanaCalendario("fechaSeleccionada",getDia($ref->fecha),getMes($ref->fecha),getAnio($ref->fecha))?>">
		          		<img  border=0 src="<?=$app_rutaWEB?>/html/img/calendar.gif">
		          		
		          		<!--<img src="<?=$app_rutaWEB?>/html/img/ico_calendar.gif" border="0" alt="seleccionar fecha" height="20">-->
		          	</a>
		          	<input type="hidden" name="frm_fecha" value="<?=$fechaMostrar?>">
		          </td>
		        </tr>
		        <tr>
      				<td width="15%" align="left" class="TxtBold" nowrap>Familia:</td>
      				<td width="10%" align="left">
        				<select name="frm_id_familia" class="Txt">					          
			          <?
			          	$sql="SELECT * FROM ad_familias";
			          	$res=mysql_query($sql);
			          	if($row=mysql_fetch_array($res))
			          	{
			          		echo "<option value=\"\">-- Seleccione familia --</option>"; 
			          		do
			          		{
			          			$sel="";
			          			if($ref->id_familia==$row["id_familia"]) $sel="selected";
			          			echo "<option value=\"".$row["id_familia"]."\" $sel>".$row["nombre"]."</option>"; 
			          		}while($row=mysql_fetch_array($res));
			          	}
			          	else echo "<option value=\"\">-- No existen familias --</option>"; 					          	
			          ?>
			          </select>
		          <td width="1%"><?printEspacios(20)?></td>
		          <td width="1%" align="left" class="TxtBold" nowrap>Clase: <?printEspacios(8)?></td>
		          <td align="left" >
			          <select name="frm_id_clase" class="Txt">					          
			          <?
			          	$sql="SELECT * FROM ad_clases_ref";
			          	$res=mysql_query($sql);
			          	if($row=mysql_fetch_array($res))
			          	{
			          		echo "<option value=''>-- Seleccione clase --</option>"; 
			          		do{
			          			$sel="";
			          			if($ref->id_clase==$row["id_clase"]) $sel="selected";
			          			echo "<option value=\"".$row["id_clase"]."\" ".$sel.">".$row["nombre"]."</option>"; 
			          		}while($row=mysql_fetch_array($res));
			          	}
			          	else echo "<option value=\"\">-- No existen clases --</option>"; 		
			          ?>
			          </select>	
			        </td>
		        </tr>
		        </table>
		    	</form>
		    </td>
		  </tr>
		  <tr>
				<td class="spacer6">&nbsp;</td>
			</tr>
		</table>
<!--FIN DATOS CLIENTE-->




<!--CREAR PESTAÑAS-->
<?
$p=new Pest($_POST["p"]);
$p->add("Componentes y operaciones","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Hoja de ruta","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->add("AMFE","#","onClick=\"cambioPestanya('".$_POST["p"]."','2')\"");
$p->add("Plan de control","#","onClick=\"cambioPestanya('".$_POST["p"]."','3')\"");
$p->add("Clientes","#","onClick=\"cambioPestanya('".$_POST["p"]."','4')\"");
$p->pintar();
?>
<!--FIN CREAR PESTAÑAS-->


<!--MOSTRAR PESTAÑA ACTUAL-->
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td class="Caja" align=center valign=top>
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td class="spacer8"><BR>&nbsp;</td>
						</tr>
						
<?
switch($_POST["p"]) {	
			
	case "1":
	
		// HOJA DE RUTA
		
		function pintoFila_1($ar,$pos,$fGris=false){
			global $app_rutaWEB;
			$cls=$fGris?"FilaGris":"Fila1";
			$t=explode("[::]",($fGris?$ar["c"]:$ar["o"]));
			if($pos!=0)$todo="<tr heigth=1px><td colspan=6 ></td></tr>";
			$todo.="<tr><td width=1% class='".$cls."' >";
			$todo.=FlechasOrden("subirOrden('".$pos."','".($fGris?'0':'1')."')","bajarOrden('".$pos."','".($fGris?'0':'1')."')")."</td>";
			if($fGris) $todo.="<td colspan=3 class='".$cls."' align=left>".$t[1]."</td></tr>";
			else{
				$m=explode("[::]",$ar["m"]);
				$oa=explode("[::]",$ar["oAlt"]);
				$todo.="<td class='".$cls."' align=left>".$t[1]."</td>";
				$todo.="<td class='".$cls."' align=left>".($m[1]==""?"-":$m[1])."</td>";
				$todo.="<td class='".$cls."' align=left>".($oa[1]==""?"-":$oa[1])."</td>";
				$todo.='</tr>';
			}			
			return $todo;
		}
		function pintoFila_2($ar,$pos){
			global $app_rutaWEB;
			$x=pintoFila_1($ar,$pos,true);
			if($ar["o"]!="") $x.=pintoFila_3($ar,$pos);
			return $x;
		}
		function pintoFila_3($ar,$pos){
			global $app_rutaWEB;
			$t=explode("[::]",$ar["o"]);
			$t2=explode("[::]",$ar["m"]);
			$t3=explode("[::]",$ar["oAlt"]);
			$todo.="<td class=FilaGris>&nbsp</td>";
			$todo.="<td width=40%  class=FilaGris align=left><img src=\"".$app_rutaWEB."/html/img/corner.gif\">&nbsp;".$t[1]."</td>";
			$todo.="<td width=10% class=FilaGris align=left>".($t2[1]==""?"-":$t2[1])."&nbsp;</td>";
			$todo.="<td width=10% class=FilaGris align=left>".($t3[1]==""?"-":$t3[1])."&nbsp;</td>";
			$todo.='</tr>';
			return $todo;
		}		
		$cAnt=-9999;
		$i=0;
		$compAnt="·%!";
		$i=0;
		$cnt=0;
		$todo="";
		foreach($ref->relaciones as $r){
			if(strpos($r["idTipo"],"C:")===false) $todo.= pintoFila_1($r,$i++);
			elseif($compAnt!=$r["c"])  $todo.= pintoFila_2($r,$i++);
			else $todo.= pintoFila_3($r,$i++);
			$compAnt=$r["c"];
			if($r["o"]!="")$cnt++;
		}
		if ($todo!=""){?>
		<tr><td align=center width=100%>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
			<caption>Hoja de Ruta</caption> 
			<tr>
				<th colspan=2 align="left">&nbsp;Componente / Operaci&oacute;n</th>
				<th align="left">&nbsp;M&aacute;quina</th>
				<th align="left">&nbsp;Operaci&oacute;n alternativa</th>
			</tr>
			<?echo $todo;?>
			</table>
			<table width="95%">
				<tr><td align="left" class="TxtBold">N&uacute;mero de operaciones: <?=$cnt?></td></tr>
				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
			</table>
		</td></tr>
			<?
		}else{
			?>
			<tr><td>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" align=center>
			<caption>Hoja de Ruta</caption>
			<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones asignadas</td></tr>
	        <tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
	        </table>
	        </td></tr>
			<?
		}				
		break;
		
	case "2":
	
		// AMFE
		
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1">
		<caption>a.m.f.e DE PROCESOS</caption>
		<?
		$fGris=true;
		$todo="";
		$todo=$ref->pintarAMFE();
		if($todo!="") {
			echo pintarCabeceraAMFEMini2().$todo.pintarPieAMFE();
			echo "<br><br>";
		}else{
			?>
			<table width="95%">
			<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones con modo de fallo asignadas a esta referencia</td></tr>
	        <tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?			
		}
		break;
		
	case "3":
	
		// PLAN DE CONTROL
		
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1">
		<caption>plan de control</caption>
		<?
		$todo="";
		foreach($ref->relaciones as $r){
			$p=explode("[::]",$r["o"]);
			if($p[0]!=""){
				$o=new Operacion($p[0]);
				$todo.=$o->generarplandecontrol();	
			}
		}
		if($todo!="") echo pintarCabeceraPControl().$todo;
		else echo "<tr><td class=TxtBold align=LEft>No hay operaciones relacionadas con el componente.</td></tr>";
		?></table><br><?
		break;
		
	case "4":
		// CLIENTES
		
		$sql="".
		"SELECT c.id_cliente,c.num,c.nombre,p.id_planificacion,p.codigo,p.fecha FROM planificaciones p ".
		"LEFT JOIN me_clientes c ON c.id_cliente=p.id_cliente ".
		"WHERE p.id_referencia=".$ref->id_referencia;
		$res=mysql_query($sql);
		if($row=@mysql_fetch_row($res)){
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
			<caption>clientes con planificaciones de la referencia</caption>
			<tr>
				<th align="left">&nbsp;Código plan.</th>
				<th align="left">&nbsp;Fecha plan.</th>
				<th align="left">&nbsp;Nombre cliente</th>
				<th align="left">&nbsp;Num. cliente</th>
			</tr>
			<?
			do{
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" style="cursor:pointer" onClick="verCli('<?=$row[3]?>');">
					<td class="Fila" align="left" width=15% nowrap><?=$row[4]?></td>
					<td class="Fila" align="left" width=15% nowrap><?=muestraFecha($row[5])?>&nbsp;</td>
					<td class="Fila" align="left"><?=$row[2]?></td>
					<td class="Fila" align="left" nowrap width=15%><?=$row[1]?></td>
				</tr>
				<?	
			}while($row=mysql_fetch_row($res));
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1">
			<caption>clientes con planificaciones de la referencia</caption>
			<tr><td width=100% align=left>
			<table width="95%" align=left border=0>
			<tr><td class="TxtBold" colspan=3 align=left>No hay planificaciones con esta referencia asignada</td></tr>
	        <tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
	    </td></tr>
			<?	
		}
		echo "</table><br>";
		
		break;
		
	default:
		// COMPONENTES Y OPERACIONES
		?>		
		<tr><td>
		<?					
			// separo cmoponentes de operaciones			
			
			$arComps=Array();
			if(count($ref->relaciones)>0){
				foreach($ref->relaciones as $r){
					$p=explode(":",$r["idTipo"]);
					$id=$p[1];
					if(strpos($r["idTipo"],"C:")!==false){
						$p=explode("[::]",$r["c"]);	
						if($cAnt!=$r["idTipo"]) $arComps[$id]=$p[2]."[#?#]".$p[1];
						$cAnt=$r["idTipo"];						
					}else{						
						$p=explode("[::]",$r["o"]);
						$arOps[$id]=$p[2]."[#?#]".$p[1];
						$oAnt=$r["idTipo"];
					}
				}
			}
			if(count($arComps)>0){
				//while($row=@mysql_fetch_row($res)){$arComps[$row[0]]=$row[1]."[#?#]".$row[2];}
				?>
				<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla"  align=center>
				 	<caption>Componentes</caption>
					<tr>
			          	<th width="20%" align="left">C&oacute;digo</th>
			          	<th width="75%" align="left">Denominaci&oacute;n</th>
			        	<th width="5%" align="left">&nbsp;</th>
			        </tr>	
					<?
					$cnt=0;
					foreach($arComps as $idC=>$n){
						$cnt++;
						$prt=explode("[#?#]",$n);
	        			?>
	        			<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" style="cursor:pointer">
				          <td align="left"   class="Fila" onClick="abreC(<?=$idC?>)"><?=$prt[0]?></td>
				          <td align="left"   class="Fila" onClick="abreC(<?=$idC?>)"><?=$prt[1]?></td>
				          <td align="center" class="Fila" onClick="eliminaC('<?=$idC?>')">
				          	<img src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar relación" width="11" height="11"> 
				          </td>
				        </tr>
	        		<?}?>	        		
        		</table>
        		<table width="95%" align=center>
	        		<tr><td align="left" class="TxtBold">N&uacute;mero de componentes: <?=$cnt?></td></tr>
      				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
      			</table>
        		<?						
			} else {
				?>
        		<table width="95%" border="0" cellpadding="2" cellspacing="1"  align=center>
					<caption>Componentes</caption>
		        	<tr><td class="TxtBold" colspan=3 align=left>No hay componentes asignados a esta referencia</td></tr>
        			<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
        		</table>
        		<?	
			}

			// operaciones
			
			if(count($arOps)>0){
				?>
				<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla" align=center>
				 	<caption>Operaciones</caption>
					<tr>
			          	<th width="20%" align="left">C&oacute;digo</th>
			          	<th width="75%" align="left">Denominaci&oacute;n</th>
			        	<th width="5%" align="left">&nbsp;</th>
			        </tr>	
					<?
					$cnt=0;
					foreach($arOps as $idO=>$n){
						$cnt++;
						$prt=explode("[#?#]",$n);
	        			?>
	        			<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)" style="cursor:pointer">
				          <td align="left"   class="Fila" onClick="abreO(<?=$idO?>)"><?=$prt[0]?></td>
				          <td align="left"   class="Fila" onClick="abreO(<?=$idO?>)"><?=$prt[1]?></td>
				          <td align="center" class="Fila" onClick="eliminaO('<?=$idO?>')">
				          	<img src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar relación" width="11" height="11"> 
				          </td>
				        </tr>
	        		<?}?>	        		
        		</table>
        		<table width="95%" align=center>
	        		<tr><td align="left" class="TxtBold">N&uacute;mero de operaciones: <?=$cnt?></td></tr>
      				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
      			</table>
        		<?						
			} else {
				?>
        		<table width="95%" border="0" cellpadding="2" cellspacing="1"  align=center>
					<caption>Operaciones</caption>
		        	<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones asignadas a esta referencia</td></tr>
        			<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
        		</table>
        		<?	
			}
	    ?>		
		</td></tr>
		<?
}
?>
					</table>
				</td>
			</tr>		
		</table>

<script>
<?=$JSEjecutar?>
<?=$bonload?>
</script>
<form action="../recursos/XLSGen.php?mini=true" method=POST>
<input type=hidden value='<?=serialize_esp($ref->relaciones)?>' name="texto">
</form>

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