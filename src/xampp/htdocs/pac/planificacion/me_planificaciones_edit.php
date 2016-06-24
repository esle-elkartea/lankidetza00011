<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Planificacion.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas
comprobarAcceso(1,$us_rol);

/*

if(isset($_GET["idx"])) 
	$_GET["id"]=$_GET["idx"];
	
if($_POST["guardadoParaRelacion"]) 
{	
		$plani=new Modo();
		$plani->codigo=$_POST["frm_codigo"];
		$plani->nombre=$_POST["frm_nombre"];
		$plani->id_ocurrencia=$_POST["frm_ocurrencia"];
		$plani->guardar();	
		header("Location: ?idx=$plani->id_modo");
		exit;
}
*/

/*******************************************************************************************************************************************/

/*

//efectos
if ($_POST["act_agregarEfectos"] && $_GET["id"]!="") {
	$eIds=explode(",",$_POST["_efectos"]);
	foreach ($eIds as $eId) {
		$sql="INSERT INTO modo_efecto (id_modo,id_efecto) VALUES ('".$_GET["id"]."','".$eId."')";
		@mysql_query($sql);	
	}	
}
if ($_POST["act_eliminarEfecto"] && $_GET["id"]!="") {
	$sql="DELETE FROM modo_efecto WHERE id_modo=".$_GET["id"]." AND id_efecto=".$_POST["_efectos"];
	@mysql_query($sql);		
}	

//causas
if ($_POST["act_agregarCausas"] && $_GET["id"]!="") {
	$cIds=explode(",",$_POST["_causas"]);
	foreach ($cIds as $cId) {
		$sql="INSERT INTO modo_causa (id_modo,id_causa) VALUES ('".$_GET["id"]."','".$cId."')";
		@mysql_query($sql);		
	}	
}
if ($_POST["act_eliminarCausa"] && $_GET["id"]!="") {
	$sql="DELETE FROM modo_causa WHERE id_modo=".$_GET["id"]." AND id_causa=".$_POST["_causas"];
	@mysql_query($sql);		
}	

//modo
if ($_POST["act_guardar"]=="1") {
	$plani=new Modo($_GET["id"]);
	$plani->codigo=$_POST["frm_codigo"];
	$plani->nombre=$_POST["frm_nombre"];
	$plani->id_ocurrencia=$_POST["frm_ocurrencia"];
	$plani->guardar();	
}
if ($_POST["act_eliminar"]=="1" && $_GET["id"]!=""){
	$plani=new Modo($_GET["id"]);
	$plani->eliminar();
	Header("Location: me_modos.php");
}
if ($_POST["act_salir"]=="1") 	
	Header("Location: me_modos.php");
	
*/

/*******************************************************************************************************************************************/




$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));


// cargar detos de la planificación
$plani=new Planificacion($_GET["id"]);
if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	//los valores han podido ser cambiados antes
	$plani->codigo=$_POST["frm_codigo"];
	$plani->nombre=$_POST["frm_nombre"];
	$plani->id_ocurrencia=$_POST["frm_ocurrencia"];
}
$miga_pan="Maestros específicos   >>   Modos   >>   ";
if ($plani->nuevo) $miga_pan.="Nuevo Modo";
else $miga_pan.=$plani->nombre;

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
	if(confirm("¿Está seguro de querer eliminar este modo y todas sus relaciones?")){
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
function cambioPestanya (desde,hasta){
	document.forms[0].p.value=hasta;
	document.forms[0].submit();
}
function relEfecto (){
	idActual='<?=$_GET["id"]?>';
	if(idActual==""){
		if(confirm("El modo será almacenado\n¿Desea continuar?")){
			if(validar(document.forms[0])){
				document.forms[0].guardadoParaRelacion.value=1;
				document.forms[0].submit();
			}
		}		
	}else abrirPopE();
}
function relCausa (){
	idActual='<?=$_GET["id"]?>';
	if(idActual==""){
		if(confirm("El modo será almacenado\n¿Desea continuar?")){
			if(validar(document.forms[0])){
				document.forms[0].guardadoParaRelacion.value=1;
				document.forms[0].submit();
			}
		}		
	}else abrirPopC();
}
function agregarCausas(sel){
	document.forms[0].act_agregarCausas.value=1;
	document.forms[0]._causas.value=sel;
	document.forms[0].p.value=1;
	document.forms[0].submit();
}
function agregarEfectos(sel){
	document.forms[0].act_agregarEfectos.value=1;
	document.forms[0]._efectos.value=sel;
	document.forms[0].p.value=0;
	document.forms[0].submit();
}
function efectoEliminar (id){
	if(confirm("¿Desea eliminar la relacion con este efecto?")){
		document.forms[0].act_eliminarEfecto.value=1;
		document.forms[0]._efectos.value=id;
		document.forms[0].submit();
	}
}
function causaEliminar (id){
	if(confirm("¿Desea eliminar la relacion con esta causa?")){
		document.forms[0].act_eliminarCausa.value=1;
		document.forms[0]._causas.value=id;
		document.forms[0].submit();
	}
}
function validar (f){
	a=new Array();
	a[0]="int::"+f.frm_codigo.value+"::Debe introducir un código para el modo::El código del modo ha de ser un número entero";
	a[1]="text::"+f.frm_nombre.value+"::Introduzca un nombre para el modo";
	er=JSvFormObligatorios(a);	
	if(er=="") return	true;
	else alert (er);
}
function abrirPopE(){
	<?=JSventanaSeleccion("efecto")?>
}
function abrirPopC(){
	<?=JSventanaSeleccion("causa")?>
}

</script>

<!--BOTONES-->
<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="  Relacionar efecto  " 		onClick="relEfecto()">
			<input type="button" class="Boton" value="  Relacionar causa  " 		onClick="relCausa()">
			<input type="button" class="Boton" value="  Eliminar  " 						onClick="eliminar()">
			<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='me_modos.php'">
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
			<input type="hidden" name="act_agregarEfectos" 	value="0">
			<input type="hidden" name="act_agregarCausas" 	value="0">
			<input type="hidden" name="act_eliminarEfecto"	value="0">
			<input type="hidden" name="act_eliminarCausa"	value="0">
			<input type="hidden" name="act_salir" 		value="0">
			<input type="hidden" name="_causas" 	value="">
			<input type="hidden" name="_efectos" 	value="">
			<input type="hidden" name="p" value="<?=$_POST["p"]?>">
			<input type="hidden" name="guardadoParaRelacion" value="0">
			<tr>
		    <td align="center" class="Tit"><span class="fBlanco">DATOS DEL MODO</span></td>
		  </tr>
		  <tr>
		    <td align="center" class="Caja">
		    	<table width="90%" border="0" cellspacing="2" cellpadding="4">
		    		<tr><td colspan=2 class="spacer2">&nbsp;</td></tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
		          <td align="left"><input name="frm_codigo" type="text" class="input" size="10" VALUE="<?=$plani->codigo?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Modo de fallo:</td>
		          <td align="left"><input name="frm_nombre" type="text" class="input" size="60" VALUE="<?=$plani->nombre?>"></td>
		        </tr>
		        <tr>
		          <td width="15%" align="left" class="TxtBold" nowrap>Ocurrencia:</td>
		          <td align="left">		          	
		          	<select name="frm_ocurrencia" class="input">
		          		<?
		          		$res = mysql_query("SELECT * FROM ocurrencias");
		          		if($row=mysql_fetch_array($res)) {
		          			echo "<option value=\"\" selected>- Seleccione ocurrencia -";
		          			do{
		          				$sel = $row["id_ocurrencia"]==$plani->id_ocurrencia ? "selected" : "" ;
		          				echo "<option value='".$row["id_ocurrencia"]."' $sel>".$row["nombre"]."</option>"; 
		          			}while($row=mysql_fetch_array($res));
			          	}
			          	else echo "<option value=\"\">- No hay ocurrencias -</option>";
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
$p=new Pest($_POST["p"]);
$p->add("Efectos del modo de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Causas del modo de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->pintar();
?>

	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td class="Caja" align=center valign=top>
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td class="spacer8"><BR>&nbsp;</td>
							</tr>
		<?
switch($_POST["p"]){
	
	case "1": 
	
		/********************************************************************************************************************************
		/*CAUSAS*/	
		
		$csas=$plani->obtenerCausas();
		if(count($csas)>0){
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Causas</caption>
				<tr>
			    <th width="80%" align="left" nowRAP>&nbsp;Nombre </th>
			    <th width="15%" align="left" nowRAP>&nbsp;Detectabilidad</th>
			    <th width="5%" align="left" nowRAP>&nbsp;</th>
			  </tr>	
			<?	
			for($i=0;$i<count($csas);$i++){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
		      <td align="left" class="Fila1">
		      	<a href="#"><?=$csas[$i]["nombre"]?></a>
		      </td>
		      <td align="left" class="Fila1">&nbsp;
		      	<?=$csas[$i]["detectabilidad"]?>
		      </td>
		      <td align="center" class="Fila1">
		      	<img onClick="causaEliminar('<?=$csas[$i]["id_causa"]?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" width="11" height="11"> 
		      </td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<table width="95%">
      	<tr>
					<td  align="left" class="TxtBold">N&uacute;mero de causas de fallo relacionadas: <?=count($csas)?> </td>
				</tr>
				<tr>
					<td align="left" class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Causas</caption>
				<tr>
					<td class="TxtBold" colspan=3 align=left>No hay causas de fallo relacionadas</td>
				</tr>
				<tr>
					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?		 
		}
		break;
	
			
		
	default: 
	
		/********************************************************************************************************************************
		/*EFECTOS*/
		
		$efcs=$plani->obtenerEfectos();
		if(count($efcs)>0){
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Efectos</caption>
				<tr>
			    <th width="80%" align="left" nowRAP>&nbsp;Nombre </th>
			    <th width="15%" align="left" nowRAP>&nbsp;Gravedad</th>
			    <th width="5%" align="left" nowRAP>&nbsp;</th>
			  </tr>	
			<?	
			for($i=0;$i<count($efcs);$i++){
				?>
				<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
		      <td align="left" class="Fila1">
		      	<a href="#"><?=$efcs[$i]["nombre"]?></a>
		      </td>
		      <td align="left" class="Fila1">&nbsp;
		      	<?=$efcs[$i]["gravedad"]?>
		      </td>
		      <td align="center" class="Fila1">
		      	<img onClick="efectoEliminar('<?=$efcs[$i]["id_efecto"]?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" width="11" height="11"> 
		      </td>
		    </tr>
				<?			
			}	
			?>
			</table>
			<table width="95%">
      	<tr>
					<td  align="left" class="TxtBold">N&uacute;mero de efectos de fallo relacionados: <?=count($efcs)?> </td>
				</tr>
				<tr>
					<td align="left" class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Efectos</caption>
				<tr>
					<td class="TxtBold" colspan=3 align=left>No hay efectos de fallo relacionados</td>
				</tr>
				<tr>
					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?		 
		}
}
?>



</table>
</td></tr></table>

<script>
<?if(isset($_GET["idx"])){ ?>
			document.forms[0].action='?id=<?=$_GET["idx"]?>';
			alert("Guardado");			
<?}?>
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