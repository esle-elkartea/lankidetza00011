<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Actividad.class.php";
comprobarAcceso(2,$us_rol);
$act=new Actividad($_GET["id"]);

//***********************************************************************//
if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$act->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$act->principal=$_POST["frm_principal"];
	$act->principalAntes=$_POST["frm_principalAntes"];
	//$act->orden=$_POST["frm_orden"];
	$act->id_categoria=$_POST["frm_id_categoria"];
}
if ($_POST["act_guardar"]=="1") 						$act->guardar($_POST["frm_posicion"]);
if ($_POST["act_eliminar"]=="1" && isset($_GET["id"])) 	$act->eliminar();
if ($_POST["act_salir"]=="1") 							Header("Location: ad_actividades.php");


//***********************************************************************//



$miga_pan="Tablas de Mantenimiento   >>   Actividades   >>   ";
if (!isset($_GET["id"])) $miga_pan.="Nueva Actividad";
else $miga_pan.=txtParaGuardar($act->nombre);


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));


flush();
ob_start();
?>

<script>
function validar(f){
	a=new Array();
	a[0]="text::"+f.frm_nombre.value+"::Debe introducir el nombre de la actividad";
	//if(f.frm_principal.value=="1") a[1]="int::"+f.frm_orden.value+"::Debe introducir el orden de aparición de la actividad::El orden debe ser rellenado con un valor entero";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
<?
$planis=$act->compruebaPlanis();
if($planis==0){?>
function eliminar (){
	if(confirm("La actividad será eliminada ¿está seguro?")){
		document.forms[0].act_eliminar.value="1";
		document.forms[0].act_salir.value="1";
		document.forms[0].submit();
	}	
}
<?}else{?>
function eliminar (){
	alert("Existen <?=$planis?> planificaciones con esta actividad relacionada.");
}
<?}?>
function guardarSalir(){
	if(validar(document.forms[0])){
		document.forms[0].act_guardar.value=1;
		document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}
}
function pulsa(i){
	if(!i) {
		//document.forms[0].frm_orden.disabled=true;
		document.getElementById("radio1").disabled=true;
		if(document.getElementById("radio2"))document.getElementById("radio2").disabled=true;
		document.getElementById("radio3").disabled=true;
		document.forms[0].frm_principal.value="0";
	}else{
		//document.forms[0].frm_orden.disabled=false;
		document.getElementById("radio1").disabled=false;
		if(document.getElementById("radio2"))document.getElementById("radio2").disabled=false;
		document.getElementById("radio3").disabled=false;
		document.forms[0].frm_principal.value="1";
	}
}
function subir(pos){
	document.forms[0].act_subir.value=1;
	document.forms[0].posicion.value=pos;
	document.forms[0].submit();
}
function bajar(pos){
	document.forms[0].act_bajar.value=1;
	document.forms[0].posicion.value=pos;
	document.forms[0].submit();
}
</script>

<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Eliminar  " 				onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_actividades.php'">
		</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>
	
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	<form method=POST>
		<input type="hidden" name="comprobarSubmit" value="1">
		<input type="hidden" name="act_eliminar" value="0">
		<input type="hidden" name="act_subir" 	value="0">
		<input type="hidden" name="act_bajar" 	value="0">
		<input type="hidden" name="posicion" 	value="">
		<input type="hidden" name="act_guardar" 	value="0">
		<input type="hidden" name="act_salir" 		value="0">
		<input type="hidden" name="frm_principalAntes" 		value="<?=$act->principalAntes?>">
		<tr>
	    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA ACTIVIDAD</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
					<tr>
						<td align="left" colspan=2  class="spacer2">&nbsp;</td>
					</tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Nombre:</td>
	          <td align="left">&nbsp;<input name="frm_nombre" type="text" class="input" size="50" VALUE="<?=txtParaInput($act->nombre)?>"></td>
	        </tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Principal:</td>
	          <td align="left"><input name="frm_ch" onClick="pulsa(this.checked)" type="checkbox" <?=($act->principal=="1"?"checked":"")?>></td>
	          <input type="hidden" name="frm_principal" value="<?=$act->principal?>">
	        </tr>
	        <!--
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Orden:</td>
	          <td align="left">&nbsp;
	          	<input name="frm_orden" type="text" class="input" size="2" VALUE="<?=$act->orden?>" <?=($act->principal!="1"?"disabled":"")?>>
	          </td>
	        </tr>
	        -->
	         <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Posici&oacute;n:</td>
	          <td align="left" class="Txt">&nbsp;
	          
	          	<!--<input name="frm_orden" type="text" class="input" size="2" VALUE="<?=$act->orden?>" <?=($act->principal!="1"?"disabled":"")?>>-->
	          	<input type="radio" name="frm_posicion" id="radio1" value="-1"<?=($act->principal!="1"?"disabled":"")?> >Primera&nbsp;
	          	<?if($act->principal=="1"){?>
	          	<input type="radio" name="frm_posicion" id="radio2" value="0" checked>Dejar donde est&aacute;
	          	<?}?>
	          	<input type="radio" name="frm_posicion" id="radio3" value="1" <?=($act->principal=="0"?"disabled":"")?> <?if($act->nuevo || $act->principal=="0")echo"checked";?>>&Uacute;ltima
	          	
	          </td>
	        </tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Categor&iacute;a:</td>
	          <td align="left">
	          	&nbsp;<select name="frm_id_categoria" class="input">
	          		<?
	          		$res=mysql_query("SELECT * FROM ad_categorias");
	          		if($row=mysql_fetch_row($res)){
	          			echo '<option value="0">-- Seleccione categoría --</option>';
	          			do{
	          				$ss=$act->id_categoria==$row[0]?"selected":"";
	          				echo '<option value="'.$row[0].'" '.$ss.'>'.$row[1].'</option>';
	          			}while($row=mysql_fetch_row($res));
	          		}else echo '<option value="0">-- No hay categorías --</option>';
	          		
	          		?>
	          	</select>
	          </td>
	        </tr>
	        <tr>
						<td align="left" colspan=2  class="spacer2">&nbsp;</td>
					</tr>
	      </table>
	    </td>
	  </tr>
	  <tr><td colspan=2 class="spacer10">&nbsp;</td></tr>
  </form>
</table>
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