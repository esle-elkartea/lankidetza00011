<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Maquina.class.php";
comprobarAcceso(2,$us_rol);
$maq=new Maquina($_GET["id"]);

//***********************************************************************//

if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$maq->nombre=txtParaGuardar($_POST["frm_nombre"]);
	//$maq->id_clase=$_POST["frm_clase"];
	$maq->codigo=$_POST["frm_codigo"];
}
if ($_POST["act_guardar"]=="1") 						$maq->guardar();
if ($_POST["act_eliminar"]=="1" && isset($_GET["id"])) 	$maq->eliminar();
if ($_POST["act_salir"]=="1" ) 							Header("Location: ad_maquinas.php");


$res=mysql_query("SELECT count(*) FROM pl_operaciones WHERE id_maquina='".$maq->id_maquina."'");
$row=mysql_fetch_row($res);
$planiOps=$row[0];




$miga_pan="Tablas de Mantenimiento   >>   Máquinas   >>   ";
if (!isset($_GET["id"])) $miga_pan.="Nueva Máquina";
else $miga_pan.=$maq->nombre;


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
	a[0]="text::"+f.frm_nombre.value+"::Debe introducir el nombre de la máquina";
	a[1]="int::"+f.frm_codigo.value+"::Debe introducir el código de la máquina::El código ha de ser un valor entero";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
function eliminar (){
	<?if($planiOps==0){?>
	if(confirm("La máquina será eliminada ¿está seguro?")){
		document.forms[0].act_eliminar.value="1";
		document.forms[0].act_salir.value="1";
		document.forms[0].submit();
	}	
	<?}else{?>
		alert("Existen entre las planificaciones creadas \n<?=($planiOps==1?"1 operación relacionada":$planiOps." operaciones relacionadas")?> con esta máquina");
	<?}?>
}
function guardarSalir(){
	if(validar(document.forms[0])){
		document.forms[0].act_guardar.value=1;
		document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}
}
function pulsa(i){
	if(!i) document.forms[0].frm_orden.disabled=true;
	else document.forms[0].frm_orden.disabled=false;
}
</script>

<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Eliminar  " 				onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_maquinas.php'">
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
	    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA MÁQUINA</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
					<tr>
						<td align="left" colspan=2  class="spacer2">&nbsp;</td>
					</tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Código:</td>
	          <td align="left"><input name="frm_codigo" type="text" class="input" size="10" VALUE="<?=$maq->codigo?>"></td>
	        </tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Nombre:</td>
	          <td align="left"><input type="text" class="input" name="frm_nombre" size="50"  value="<?=txtParaInput($maq->nombre)?>"></td>
	        </tr>
	        <!--
	         <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Clase:</td>
	          <td align="left">
	          	<select name="frm_clase" class="input">
		          	<?
					/*$sql="SELECT * FROM ad_clases";
					$res=mysql_query($sql);
					if($row=mysql_fetch_row($res)){
						echo "<option value=\"\">-- Seleccione clase --</option>";
						do{
							if($row[0]==$maq->id_clase) $sel="selected";
							else $sel="";
							echo "<option value=\"".$row[0]."\" ".$sel.">".$row[1]."</option>";
						}while($row=mysql_fetch_row($res));						
					}else echo "<option value=\"\">-- no hay clases --</option>";	          	
		          	*/
					?>          		    	
	          	</select>	          
	          </td>
	        </tr>	-->        
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