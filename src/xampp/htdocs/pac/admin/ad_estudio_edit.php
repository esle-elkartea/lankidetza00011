<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Pregunta.class.php";
comprobarAcceso(2,$us_rol);


//***********************************************************************//
if ($_POST["act_guardar"]=="1"){
	$preg=new Pregunta($_GET["id"]);
	$preg->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$preg->guardar();
}
if ($_POST["act_eliminar"]=="1" && isset($_GET["id"])){
	$preg=new Pregunta($_GET["id"]);
	$preg->eliminar();
	Header("Location: ad_estudio.php");
}
if ($_POST["act_salir"]=="1") 	Header("Location: ad_estudio.php");
//***********************************************************************//


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));

$preg=new Pregunta($_GET["id"]);
if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$preg->nombre=$_POST["frm_nombre"];
}

$miga_pan="Tablas de Mantenimiento   >>   Estudio de factibilidad   >>   ";
if (!isset($_GET["id"])) $miga_pan.="Nueva Pregunta";
else $miga_pan.=$preg->nombre;

flush();
ob_start();
?>

<script>
function validar(f){
	a=new Array();
	a[0]="text::"+f.frm_nombre.value+"::Debe introducir el texto de la pregunta";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
function eliminar (){
	if(confirm("La pregunta será eliminada ¿está seguro?")){
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
			<input type="button" class="Boton" value="  Eliminar  " 				onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_estudio.php'">
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
	    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA PREGUNTA</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
					<tr>
						<td align="left" colspan=2 class="spacer2">&nbsp;</td>
					</tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" valign=top nowrap>Texto:</td>
	          <td align="left">
	          	<textarea name="frm_nombre" class=input rows=4 cols=70><?=txtParaInput($preg->nombre)?></textarea>
	          	<!--<input name="frm_nombre" type="text" class="input" size="50" VALUE="<?=txtParaInput($preg->nombre)?>">-->
	          	
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