<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Efecto.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas
comprobarAcceso(2,$us_rol);

$JSEjecutar="";
$efct=new Efecto($_GET["id"]);

if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	//los valores han podido ser cambiados antes
	$efct->codigo=$_POST["frm_codigo"];
	$efct->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$efct->id_gravedad=$_POST["frm_gravedad"];
	$efct->modos=$_POST["todos_modos"]==""?Array():explode(",",$_POST["todos_modos"]);
}

if ($_POST["act_agregarAModos"]) 	$efct->agregarModos(explode(",",$_POST["_modos"]));
if ($_POST["act_eliminarAModos"]) $efct->quitarModos($_POST["_modos"]);
if ($_POST["act_guardar"]=="1"){
	$JSEjecutar=$efct->guardar();
	if($JSEjecutar=="" && $_POST["act_verModo"]!=""){
		header("Location: me_modos_edit.php?id=".$_POST["act_verModo"]);
		exit;	
	}
}
if ($_POST["act_eliminar"]=="1")	$efct->eliminar();
if ($_POST["act_salir"]=="1" && $JSEjecutar=="") Header("Location: me_efectos.php");


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));

$miga_pan="Maestros específicos   >>   Efectos   >>   ";
if ($efct->nuevo) $miga_pan.="Nuevo Efecto";
else $miga_pan.=txtParaInput($efct->nombre);

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
	if(confirm("¿Está seguro de querer eliminar este efecto y todas sus relaciones?")){
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
function cambioPestanya (desde,hasta){
	document.forms[0].p.value=hasta;
	document.forms[0].submit();
}
function agregarModos(sel){
	document.forms[0].act_agregarAModos.value=1;
	document.forms[0]._modos.value=sel;
	document.forms[0].submit();
}
function eliminarModos (id){
	if(confirm("¿Desea eliminar la relacion con este modo?")){
		document.forms[0].act_eliminarAModos.value=1;
		document.forms[0]._modos.value=id;
		document.forms[0].submit();
	}
}
function validar (f){
	a=new Array();
	a[0]="int::"+f.frm_codigo.value+"::Debe introducir un código para el efecto::El código del efecto ha de ser un número entero";
	a[1]="int::"+f.frm_gravedad.value+"::Debe seleccionar una gravedad para el efecto";
	a[2]="text::"+f.frm_nombre.value+"::Introduzca un nombre para el efecto";
	er=JSvFormObligatorios(a);	
	if(er=="") return	true;
	else alert (er);
}
function relModo (){
	<?=JSventanaSeleccion("modo")?>
}
function verModo(id){
	if(confirm("Pulse aceptar para guardar los cambios realizados en el efecto o cancelar para deshacerlos")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_verModo.value=id;
		document.forms[0].submit();
	}else window.location="me_modos_edit.php?id="+id;
}

</script>

<!--BOTONES-->
<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Relacionar modo  " 	onClick="relModo()">
			<input type="button" class="Boton" value="  Eliminar  " 				onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 	onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          	onClick="window.location='me_efectos.php'">
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
			<input type="hidden" name="act_verModo" 	value="">
			<input type="hidden" name="act_agregarAModos" 	value="0">
			<input type="hidden" name="act_eliminarAModos"	value="0">
			<input type="hidden" name="act_salir" 		value="0">
			<input type="hidden" name="_modos" 	value="">
			<input type="hidden" name="todos_modos" 	value="<?=(count($efct->modos)>0?implode(",",$efct->modos):"")?>">
			<input type="hidden" name="guardadoParaRelacion" value="0">
			<!--
			<input type="hidden" name="p" value="<?=$_POST["p"]?>">
			-->
			<tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DEL EFECTO</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
		    		<tr><td colspan=2 class="spacer2">&nbsp;</td></tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
		          <td align="left"><input name="frm_codigo" type="text" class="input" size="10" VALUE="<?=$efct->codigo?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Efecto de fallo:</td>
		          <td align="left"><input name="frm_nombre" type="text" class="input" size="60" VALUE="<?=txtParaInput($efct->nombre)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Gravedad:</td>
		          <td align="left">		          	
		          	<select name="frm_gravedad" class="input">
		          		<?
		          		$res = mysql_query("SELECT * FROM ad_gravedades");
		          		if($row=mysql_fetch_array($res)) {
		          			echo "<option value=\"\" selected>-- Seleccione gravedad --";
		          			do{
		          				$sel = $row["id_gravedad"]==$efct->id_gravedad ? "selected" : "" ;
		          				echo "<option value='".$row["id_gravedad"]."' $sel>".$row["nombre"]."</option>"; 
		          			}while($row=mysql_fetch_array($res));
			          	}
			          	else echo "<option value=\"\">-- No hay gravedades --</option>";
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
/*
$p=new Pest($_POST["p"]);
$p->add("Efectos del modo de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Causas del modo de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->pintar();
*/
?>


	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td class="Caja" align=center valign=top>
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td class="spacer8"><BR>&nbsp;</td>
							</tr>
		<?
		/********************************************************************************************************************************
		/*MODOS*/	
		
		$modos=$efct->obtenerModos();
		if(count($modos)>0){
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Modos de fallo</caption>
				<tr>
			    <th width="15%" align="left" nowRAP>&nbsp;C&oacute;digo </th>
			    <th width="80%" align="left" nowRAP>&nbsp;Nombre</th>
			    <th width="5%" align="left" nowRAP>&nbsp;</th>
			  </tr>	
			<?	
			for($i=0;$i<count($modos);$i++){
				?>
			<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
		      <td align="left" class="Fila1" onClick="verModo('<?=$modos[$i]["id_modo"]?>')">
		      	<a href="#"><?=$modos[$i]["codigo"]?></a>
		      </td>
		      <td align="left" class="Fila1" onClick="verModo('<?=$modos[$i]["id_modo"]?>')">&nbsp;
		      	<?=$modos[$i]["nombre"]?>
		      </td>
		      <td align="center" class="Fila1">
		      	<img onClick="eliminarModos('<?=$modos[$i]["id_modo"]?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" width="11" height="11"> 
		      </td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<table width="95%">
      	<tr>
					<td  align="left" class="TxtBold">N&uacute;mero de modos de fallo relacionados: <?=count($modos)?> </td>
				</tr>
				<tr>
					<td align="left" class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Modos de fallo</caption>
				<tr>
					<td class="TxtBold" colspan=3 align=left>No hay modos de fallo relacionados</td>
				</tr>
				<tr>
					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?		 
		}
?>

</table>
</td></tr></table>

<script>
<?=$JSEjecutar?>
</script>

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