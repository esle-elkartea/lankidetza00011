<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/ClaseRef.class.php";
comprobarAcceso(2,$us_rol);


//***********************************************************************//
if ($_POST["act_guardar"]=="1") {
	$cls=new claseRef($_GET["id"]);
	$cls->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$cls->guardar();
}
if ($_POST["act_eliminar"]=="1" && isset($_GET["id"])){
	$cls=new claseRef($_GET["id"]);
	$cls->eliminar();
	Header("Location: ad_clasesRef.php");
}
if ($_POST["act_salir"]=="1") 	Header("Location: ad_clasesRef.php");
//***********************************************************************//


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));

$cls=new claseRef($_GET["id"]);
if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$cls->nombre=$_POST["frm_nombre"];
}

$miga_pan="Tablas de Mantenimiento   >>   Clases de referencia   >>   ";
if (!isset($_GET["id"])) $miga_pan.="Nueva clase";
else $miga_pan.=$cls->nombre;

flush();
ob_start();
?>

<script>
function validar(f){
	a=new Array();
	a[0]="text::"+f.frm_nombre.value+"::Debe introducir el nombre de la clase";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
function eliminar (){
	if(confirm("La clase ser� eliminada �est� seguro?")){
		document.forms[0].act_eliminar.value="1";
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
</script>

<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Eliminar  " 						onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_clasesRef.php'">
		</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>
	
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	<form method=POST>
		<input type="hidden" name="comprobarSubmit" value="1">
		<input type="hidden" name="act_eliminar" value="0">
		<input type="hidden" name="act_guardar" 	value="0">
		<input type="hidden" name="act_salir" 		value="0">
		<tr>
	    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA CLASE</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
					<tr>
						<td align="left" colspan=2  class="spacer2">&nbsp;</td>
					</tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Nombre:</td>
	          <td align="left"><input name="frm_nombre" type="text" class="input" size="50" VALUE="<?=txtParaInput($cls->nombre)?>"></td>
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