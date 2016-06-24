<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Caracteristica.class.php";
comprobarAcceso(2,$us_rol);


$car=new Caracteristica($_GET["id"]);

//***********************************************************************//

if ($_POST["act_guardar"]=="1") {
	$car->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$car->num=$_POST["frm_num"];
	$car->prod=$_POST["frm_prod"];
	$car->proc=$_POST["frm_proc"];
	$car->especificacion=$_POST["frm_especificacion"];
	$car->evaluacion=$_POST["frm_evaluacion"];
	$car->metodo=$_POST["frm_metodo"];
	$car->plan=$_POST["frm_plan"];
	$car->id_clase=$_POST["frm_id_clase"];
	$car->tam=$_POST["frm_tam"];
	$car->fre=$_POST["frm_fre"];	
	$car->guardar();
}
if ($_POST["act_eliminar"]=="1" && isset($_GET["id"])){
	$car->eliminar();
	Header("Location: ad_caracteristicas.php");
}
if ($_POST["act_salir"]=="1") 	Header("Location: ad_caracteristicas.php");
//***********************************************************************//


$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));


if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$car->nombre=$_POST["frm_nombre"];
}

$miga_pan="Tablas de Mantenimiento   >>   Familias   >>   ";
if (!isset($_GET["id"])) $miga_pan.="Nueva Familia";
else $miga_pan.=$car->nombre;

flush();
ob_start();
?>

<script>
function validar(f){
	a=new Array();
	a[0]="text::"+f.frm_nombre.value+"::Debe introducir el nombre de la característica";
	a[1]="int::"+f.frm_num.value+"::Debe introducir el número de la característica::El campo número ha de contener un valor entero";
	if(document.getElementById("r1").checked==true) a[2]="int::"+f.frm_prod.value+"::Debe introducir el número del producto::El campo producto ha de contener un valor entero";
	if(document.getElementById("r2").checked==true) a[2]="int::"+f.frm_proc.value+"::Debe introducir el número del proceso::El campo proceso ha de contener un valor entero";		
	er=JSvFormObligatorios(a);
	if(er=="") return	true;
	else alert (er);
}
function eliminar (){
	if(confirm("La característica será eliminada ¿está seguro?")){
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
function prod(ch){
	if(ch){
		document.forms[0].frm_proc.disabled=true;
		document.forms[0].frm_prod.disabled=false;
	}else{
		document.forms[0].frm_prod.disabled=true;
		document.forms[0].frm_proc.disabled=false;
	}
}
function proc(ch){
	if(ch){
		document.forms[0].frm_proc.disabled=false;
		document.forms[0].frm_prod.disabled=true;
	}else{
		document.forms[0].frm_prod.disabled=false;
		document.forms[0].frm_proc.disabled=true;
	}
}
</script>

<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Eliminar  " 						onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_caracteristicas.php'">
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
	    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA CARACTERÍSTICA</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
			<table width="90%" border="0" cellspacing="2" cellpadding="4">
				<tr>
					<td align="left" colspan=2  class="spacer2">&nbsp;</td>
				</tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap><?printEspacios(8)?>Nombre:</td>
		          <td align="left"><input name="frm_nombre" type="text" class="input" size="70" VALUE="<?=txtParaInput($car->nombre)?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap><?printEspacios(8)?>N&uacute;mero:</td>
		          <td align="left"><input name="frm_num" type="text" class="input" size="20" VALUE="<?=$car->num?>"></td>
		        </tr>
		         <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap><?printEspacios(8)?>Clase:</td>
		          <td align="left">
					<select name="frm_id_clase" class=input>
						<?
						$res=mysql_query("SELECT * FROM ad_clases");
						if($row=mysql_fetch_array($res)){
							echo '<option value="">-- Seleccione la clase --</option>';
							do{
								if($row["id_clase"]==$car->id_clase) $sel=" Selected ";
								else $sel="";
								echo '<option value="'.$row["id_clase"].'" '.$sel.'>'.$row["nombre"].'</option>';	
							}while($row=mysql_fetch_array($res));
						}else{
							echo '<option value="">-- no hay clases --</option>';	
						}
						?>
					</select>	
				  </td>
		        </tr>
		         <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>
		          <?$cad=$car->prod!=""||($car->prod=="" && $car->proc=="")?array("checked",""):array("","disabled");?>
		          	<input type="radio" name="pp" id="r1" onClick="prod(this.checked)" <?=$cad[0]?>>&nbsp;Producto:
		          </td>
		          <td align="left"><input name="frm_prod" type="text" class="input" size="20" VALUE="<?=$car->prod?>" <?=$cad[1]?>></td>
		        </tr>
		         <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>
		          <?$cad=$car->proc!=""?array("checked",""):array("","disabled");?>
		          	<input type="radio" name="pp" id="r2" onClick="proc(this.checked)" <?=$cad[0]?>>&nbsp;Proceso:
		          </td>
		          <td align="left"><input name="frm_proc" type="text" class="input" size="20" VALUE="<?=$car->proc?>" <?=$cad[1]?>></td>
		        </tr>
		      	<tr>
		          <td width="15%" align="left" class="TxtBold" nowrap valign=top><?printEspacios(8)?>Especificaci&oacute;n:</td>
		          <td align="left"><textarea name="frm_especificacion" type="text" class="input" cols=70 rows=2 ><?=$car->especificacion?></textarea></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap valign=top><?printEspacios(8)?>Evaluaci&oacute;n:</td>
		          <td align="left"><textarea name="frm_evaluacion" type="text" class="input" cols="70"rows=2><?=$car->evaluacion?></textarea></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap valign=top><?printEspacios(8)?>M&eacute;todo:</td>
		          <td align="left"><textarea name="frm_metodo" type="text" class="input" cols="70"rows=2><?=$car->metodo?></textarea></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap valign=top><?printEspacios(8)?>Plan de reacci&oacute;n:</td>
		          <td align="left"><textarea name="frm_plan" type="text" class="input" cols="70"rows=2><?=$car->plan?></textarea></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap valign=top><?printEspacios(8)?>Muestra:</td>
		          <td align="left">
		          	<table width=30% class=>
		          	  <tr>
		          	    <td class=Txt align=Left width=5%>Tamaño:</td>
		          	    <td class=Txt align=Left width=95%><input type="text" name="frm_tam" value="<?=$car->tam?>" class="input" size=5></td>
		          	  </tr>
		          	  <tr>
		          	    <td class=Txt align=Left width=5%>Frecuencia:</td>
		          	    <td class=Txt align=Left width=95%><input type="text" name="frm_fre" value="<?=$car->fre?>" class="input" size=5></td>
		          	  </tr>
		          	</table>
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