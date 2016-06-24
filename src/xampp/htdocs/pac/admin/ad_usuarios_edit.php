<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Usuario.class.php";
comprobarAcceso(3,$us_rol);
$usu=new Usuario($_GET["id"]);

if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$usu->clave=txtParaGuardar($_POST["frm_clave"]);
	$usu->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$usu->apellidos=txtParaGuardar($_POST["frm_apellidos"]);
	$usu->password=txtParaGuardar($_POST["frm_password"]);
	$usu->rol=$_POST["frm_rol"];
	$usu->baja=$_POST["frm_baja"];
}
	
if ($_POST["act_guardar"]=="1")		$usu->guardar();
if ($_POST["act_eliminar"]=="1") 	$usu->baja=true;
if ($_POST["act_revivir"]=="1") 	$usu->baja=false;
if ($_POST["act_salir"]=="1") 		Header("Location: ad_usuarios.php");



$miga_pan="Administraci&oacute;n   >>   Usuarios   >>   ";
if ($usu->nuevo) $miga_pan.="Nuevo Usuario";
else $miga_pan.=$usu->nombre." ".$usu->apellidos;



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
	a[0]="text::"+f.frm_clave.value+"::Debe introducir la clave de acceso a la aplicación (Usuario)";
	a[1]="password::"+f.frm_password.value+"::"+f.frm_password2.value;
	a[2]="text::"+f.frm_nombre.value+"::Debe introducir el nombre del usuario";
	a[3]="text::"+f.frm_apellidos.value+"::Introduzca los apellidos del usuario";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
function eliminar (){
	if(confirm("Este usuario será dado de baja ¿está seguro?")){
		document.forms[0].act_eliminar.value="1";
		//document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}	
}
function guardarSalir(){
	if(validar(document.forms[0])){
		enableTodo();
		document.forms[0].act_guardar.value=1;
		document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}
}
function revivir(){
		enableTodo();
		document.forms[0].act_revivir.value=1;
		document.forms[0].submit();
}
function enableTodo(){
	document.forms[0].frm_clave.disabled=false;
	document.forms[0].frm_password.disabled=false;
	document.forms[0].frm_nombre.disabled=false;
	document.forms[0].frm_password.disabled=false;
	document.forms[0].frm_apellidos.disabled=false;
	document.getElementById("xx0").disabled=false;
	document.getElementById("xx1").disabled=false;
	document.getElementById("xx2").disabled=false;
}
</script>

<?$ds=$usu->baja?"disabled":"";?>
<table border=0 width=100% >
	<tr>
		<td align="right">
		<?if(!$usu->baja){?>
			<input type="button" class="Boton" value="  Dar de baja  " 						onClick="eliminar()">
		<?}else{?>
			<input type="button" class="Boton" value="  Dar de alta  " 						onClick="revivir()">
		<?}?>
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='ad_usuarios.php'">
		</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>

	
	<table width="100%" border="0" cellspacing="1" cellpadding="0">
		<form method=POST>
			<input type="hidden" name="comprobarSubmit" value="1">
			<input type="hidden" name="act_eliminar" value="0">
			<input type="hidden" name="act_revivir" value="0">
			<input type="hidden" name="act_guardar" 	value="0">
			<input type="hidden" name="act_salir" 		value="0">
			<input type="hidden" name="frm_baja" 		value="<?=($usu->baja?'1':'0')?>">
			<tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DEL USUARIO</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4" >
		    		<tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
		    		<tr>
		    			<td width=45%>
		    				<table cellspacing="2" cellpadding="4" width=95% align=left>
					        <tr>
					          <td width="30%" align="left" class="TxtBold" nowrap>Usuario:</td>
					          <td align="left"><input name="frm_clave" type="text" class="input" size="20" VALUE="<?=txtParaInput($usu->clave)?>" <?=$ds?>></td>
					        </tr>
					        <tr>
					          <td width="30%" align="left" class="TxtBold" nowrap>Contraseña:</td>
					          <td align="left"><input name="frm_password" type="text" class="input" size="20" VALUE="<?=txtParaInput($usu->password)?>" <?=$ds?>></td>
					        </tr>
					        <tr>
					          <td width="30%" align="left" class="TxtBold" nowrap >Nombre:</td>
					          <td align="left"><input name="frm_nombre" type="text" class="input" size="40" VALUE="<?=txtParaInput($usu->nombre)?>" <?=$ds?>></td>
					        </tr>
		        		</table>
		        	</td>
		        	<td width=55%>
		        		<table cellspacing="2" cellpadding="4" width=95% align=left>
					        <tr>
					          <td width="30%" align="left" class="TxtBold" nowrap>&nbsp;</td>
					          <td align="left">&nbsp;</td>
					        </tr>
					        <tr>
					          <td width="30%" align="left" class="TxtBold" nowrap >Confirmar contraseña:</td>
					          <td align="left"><input name="frm_password2" type="text" class="input" size="20" VALUE="<?=txtParaInput($usu->password)?>" <?=$ds?>></td>
					        </tr>
					        <tr>
					          <td width="30%" align="left" class="TxtBold" nowrap >Apellidos:</td>
					          <td align="left"><input name="frm_apellidos" type="text" class="input" size="40" VALUE="<?=txtParaInput($usu->apellidos)?>" <?=$ds?>></td>
					        </tr>
		        		</table>
		        	</td>
		        <tr>
		        	<td colspan=2 class="TxtBold" align=left>
		        		<table border=0 width=75%>
		        			<tr>
		        					<td width=15% nowrap>&nbsp;Permisos:</td>
						        	<td class="Txt" width=75% align=left >
						        		<?
						        		$sql="SELECT * FROM roles";
						        		$res=mysql_query($sql);
						        		$xx=0;
						        		if($row=mysql_fetch_row($res)) {
						        			do{
							        			$sel=$usu->rol==$row[0]?"checked":"";
							        			echo '<input type="radio" '.$sel.' name="frm_rol" value="'.$row[0].'" '.$ds.' id="xx'.$xx++.'">'.$row[1].'</option>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							        		}while($row=mysql_fetch_row($res));
							        	}
						        		?>
						        	</td>
						       </tr>
						     </table>
		        	</td>
		        </tr>
		        <tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
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