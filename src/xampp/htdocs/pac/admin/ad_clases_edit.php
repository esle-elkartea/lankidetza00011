<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Clase.class.php";
comprobarAcceso(2,$us_rol);


//***********************************************************************//
if ($_POST["act_guardar"]=="1") {
	$cls=new Clase($_GET["id"]);
	$cls->nombre=$_POST["frm_nombre"];
	$cls->img=$_POST["imag"];
	$cls->guardar();
	if($_POST["act_paraImagen"]=="1") {
		header("Location: ?id=".$cls->id_clase);
		exit();
	}
}
if ($_POST["act_eliminar"]=="1" && isset($_GET["id"])){
	$cls=new Clase($_GET["id"]);
	$cls->eliminar();
	Header("Location: ad_clases.php");
}
if ($_POST["act_salir"]=="1") 	Header("Location: ad_clases.php");
//***********************************************************************//


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));

$cls=new Clase($_GET["id"]);
if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$cls->nombre=$_POST["frm_nombre"];
}

$miga_pan="Tablas de Mantenimiento   >>   Clases   >>   ";
if (!isset($_GET["id"])) $miga_pan.="Nueva Clase";
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
	if(confirm("La clase será eliminada ¿está seguro?")){
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
function guardarParaImagen(){
	if(validar(document.forms[0])){
		document.forms[0].act_guardar.value=1;
		document.forms[0].act_paraImagen.value=1;
		document.forms[0].submit();
	}
}
function ficheroGuardado(ruta){
	document.forms[0].imag.value=ruta;
	document.forms[0].act_guardar.value="1";
	document.forms[0].submit();
}
function selArch(n){
	if('<?=$_GET["id"]?>'==''){
		if(confirm("Para asignar una imagen a la clase, la clase debe ser guardada")){
			guardarParaImagen();
		}	
	}
	else{
		if(n==1) <?=JSventanaArchivo("ImagenDeClase","id=".$cls->id_clase)?>;
		if(n==2) <?=JSventanaArchivo("ImagenDeClase","id=".$cls->id_clase."&eliminarAnterior=".$cls->img)?>;
	}
}
</script>

<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Eliminar  " 						onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_clases.php'">
	</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>
	
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	<form method=POST>
		<input type="hidden" name="comprobarSubmit" value="1">
		<input type="hidden" name="act_eliminar" value="0">
		<input type="hidden" name="act_guardar" 	value="0">
		<input type="hidden" name="act_paraImagen" 	value="0">
		<input type="hidden" name="act_salir" 		value="0">
		<input type="hidden" name="act_guardaImagen" value="0">
		<input type="hidden" name="imag" value="<?=$cls->img?>"
	          
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
	          <td width="15%" align="left" class="TxtBold" nowrap valign=top>Imagen:</td>
	          <td align="left" class="TxtBold">
	          	<?
	          	if($cls->img!="") 
	          		echo '<img src="'.$cls->img.'"><br><br><input type="button" class="Boton" value="cambiar imagen" onClick="selArch(2)">';     
	          	else 
	          		echo '<input type="button" value="selecci&oacute;n de imagen" class="Boton" onClick="selArch(1)">';
	          		
	          	?>
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