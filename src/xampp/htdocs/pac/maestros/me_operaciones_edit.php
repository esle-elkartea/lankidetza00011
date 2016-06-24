<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Operacion.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas
comprobarAcceso(2,$us_rol);

$JSEjecutar="";
$oprc=new Operacion($_GET["id"]);

if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1"){
	$oprc->codigo=$_POST["frm_codigo"];
	$oprc->nombre=txtParaGuardar($_POST["frm_nombre"]);
	$oprc->id_opAlt=$_POST["frm_opAlt"];
	$oprc->id_maquina=$_POST["frm_maquina"];
	$oprc->modos=$_POST["todos_modos"]!=""?explode(",",$_POST["todos_modos"]):array();
	$oprc->caracts=$_POST["todos_caracts"]!=""?explode(",",$_POST["todos_caracts"]):array();
}

if ($_POST["act_agregarModo"]=="1")  		$oprc->agregarModos(explode(",",$_POST["_modos"]));
if ($_POST["act_agCaracteristicas"]!="")  	$oprc->agregarCaracteristicas(explode(",",$_POST["act_agCaracteristicas"]));
if ($_POST["act_eliminarModo"]=="1")		$oprc->quitarModos($_POST["_modos"]);
if ($_POST["act_eliminarCarac"]!="")		$oprc->quitarCaracteristicas($_POST["act_eliminarCarac"]);
if ($_POST["elimina"]=="1")					$JSEjecutar=$oprc->eliminar();
if ($_POST["act_guardar"]=="1")				$JSEjecutar=$oprc->guardar();	

if ($_POST["act_salir"]=="1" && $JSEjecutar=="") 	Header("Location: ".obtenerRedir($_GET["volver"]));
if ($_POST["act_verRef"]!="" && $JSEjecutar=="") 	Header("Location: me_referencias_edit.php?id=".$_POST["act_verRef"]."&volver=op_".$oprc->id_operacion);
if ($_POST["act_verComp"]!="" && $JSEjecutar=="") 	Header("Location: me_componentes_edit.php?id=".$_POST["act_verComp"]."&volver=op_".$oprc->id_operacion);
if ($_POST["act_verModo"]!="" && $JSEjecutar=="") 	Header("Location: me_modos_edit.php?id=".$_POST["act_verModo"]."&volver=op_".$oprc->id_operacion);
if ($_POST["act_verCaract"]!="" && $JSEjecutar=="")	Header("Location: ../admin/ad_caracteristicas_edit.php?id=".$_POST["act_verCaract"]."&volver=op_".$oprc->id_operacion);

$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));

$miga_pan="Maestros específicos >> Operaciones >> ";
if ($oprc->nuevo) $miga_pan.="Nueva Operación";
else $miga_pan.=txtParaInput($oprc->nombre);

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
	document.forms[0].act_agregarModo.value=1;
	document.forms[0]._modos.value=sel;
	document.forms[0].submit();
}
function nuevoModo(){
	<?=JSventanaSeleccion("modo")?>;
}
function nuevaCar(){
	<?=JSventanaSeleccion("caracteristica")?>;
}
<?
//$planis=$oprc->compruebaPlanis();
$planis=0;
if($planis=="0"){?>
function eliminar(){
	if(confirm("¿Seguro que desea eliminar esta operación y todas sus relaciones?")){
		document.forms[0].elimina.value="1";
		document.forms[0].act_salir.value="1";
		document.forms[0].submit();
	}
}
<?}else{?>
function eliminar(){
	alert("Existen <?=$planis?> planificaciones relacionadas con esta operación");
}
<?}?>
function eliminaM (c) {
	if(confirm("¿Desea eliminar la relacion con este modo?")){
		document.forms[0].act_eliminarModo.value=1;
		document.forms[0]._modos.value=c;
		document.forms[0].submit();	
	}
}
function eliminaC (c) {
	if(confirm("¿Desea eliminar la relacion con esta característica?")){
		document.forms[0].act_eliminarCarac.value=c;
		document.forms[0].submit();	
	}
}
function validar(f){
	a=new Array();
	a[0]="int::"+f.frm_codigo.value+"::Debe introducir un código de operación::El código de operación ha de ser un número entero";
	a[1]="text::"+f.frm_nombre.value+"::Introduzca un nombre para la operación";
	er=JSvFormObligatorios(a);		
	if(er=="") return	true;
	else alert (er);
}
function verRef(id){
	if(confirm("¿Desea guardar los cambios realizados en la operación?")){
		document.forms[0].act_verRef.value=id;
		document.forms[0].act_guardar.value=1;
		document.forms[0].submit();
	}else window.location="me_referencias_edit.php?id="+id+"&volver=op_<?=$oprc->id_operacion?>";
}
function verComp(id){
	if(confirm("¿Desea guardar los cambios realizados en la operación?")){
		document.forms[0].act_verComp.value=id;
		document.forms[0].act_guardar.value=1;
		document.forms[0].submit();
	}else window.location="me_componentes_edit.php?id="+id+"&volver=op_<?=$oprc->id_operacion?>";
}
function verModo(id){
	if(confirm("¿Desea guardar los cambios realizados en la operación?")){
		document.forms[0].act_verModo.value=id;
		document.forms[0].act_guardar.value=1;
		document.forms[0].submit();
	}else window.location="me_modos_edit.php?id="+id+"&volver=op_<?=$oprc->id_operacion?>";
}
function agregaCars(ids){
	//alert(ids);
	document.forms[0].act_agCaracteristicas.value=ids;
	document.forms[0].submit();
}
<?if($us_rol>=2){?>
	function verCaract(id){
		if(confirm("¿Desea guardar los cambios realizados en la operación?")){
			document.forms[0].act_verCaract.value=id;
			document.forms[0].act_guardar.value=1;
			document.forms[0].submit();
		}else window.location="../admin/ad_caracteristicas_edit.php?id="+id+"&volver=op_<?=$oprc->id_operacion?>";
	}
<?}?>
</script>


<table border=0 width=100% >
	<tr>
		<td align="right">
			<input type="button" class="Boton" value="Relacionar modo de fallo" 	onClick="nuevoModo()">
			<input type="button" class="Boton" value="Relacionar característica" 	onClick="nuevaCar()">
			<input type="button" class="Boton" value="Eliminar" 					onClick="eliminar()">
			<input type="button" class="Boton" value="Guardar y Salir" 						onClick="guardarSalir()">
			<input type="button" class="Boton" value="Salir"          						onClick="window.location='<?=obtenerRedir($_GET["volver"])?>'">
		</td>
	</tr>
	<tr>
		<td class="spacer2">&nbsp;</td>
	</tr>
</table>


<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA OPERACIÓN</span></td>
  </tr>
  <tr>
    <td align="center" class="Caja">    
	    <form method=POST name="f_datos">		    
				<input type="hidden" name="comprobarSubmit" value="1">				
				<input type="hidden" name="p" value="<?=$_POST["p"]?>">				
				<input type="hidden" name="elimina" value="0">				
				<input type="hidden" name="act_agregarModo" 	value="0">
				<input type="hidden" name="act_eliminarModo" 	value="0">
				<input type="hidden" name="act_eliminarCarac" 	value="">
				<input type="hidden" name="_modos" 	value="">
				<input type="hidden" name="todos_modos" 	value="<?=(count($oprc->modos)>0?implode(",",$oprc->modos):"")?>">
				<input type="hidden" name="todos_caracts" 	value="<?=(count($oprc->caracts)>0?implode(",",$oprc->caracts):"")?>">
				<input type="hidden" name="act_guardar" 	value="0">
				<input type="hidden" name="act_salir" 		value="0">
				<input type="hidden" name="act_verRef" 		value="">
				<input type="hidden" name="act_verComp" 	value="">
				<input type="hidden" name="act_verModo" 	value="">	
				<input type="hidden" name="act_agCaracteristicas" 		value="">
				<input type="hidden" name="act_verCaract" 		value="">					
				<input type="hidden" name="guardadoParaRelacion" value="0">	
				<table width="90%" border="0" cellspacing="2" cellpadding="4">
					<tr>
						<td class="spacer4" colspan=5>&nbsp;</td>
					</tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
	          <td align="left" colspan=4><input name="frm_codigo" type="text" class="input" size="10" VALUE="<?=txtParaInput($oprc->codigo)?>"></td>
	        </tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
	          <td align="left" colspan=4><input name="frm_nombre" type="text" class="input" size="100" VALUE="<?=txtParaInput($oprc->nombre)?>"></td>
	        </tr>
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>M&aacute;quina:</td>
	          <td align="left" colspan=4>
	          	<select name="frm_maquina" class="input" onChange="<?=($_POST["p"]=="3"?"this.form.submit()":"")?>">
	          		<?
					$sql="SELECT id_maquina,nombre FROM ad_maquinas ORDER BY nombre";
	          		$res=mysql_query($sql);
	          		if($row=mysql_fetch_row($res)){
	          			echo "<option value=\"\">-- Seleccione --</option>";
	          			do{
	          				if($oprc->id_maquina==$row[0]) $sl="selected";
	          				else $sl="";
	          				echo "<option value=\"".$row[0]."\" $sl>".$row[1]."</option>";
	          			}while($row=mysql_fetch_row($res));
	          		}else echo "<option value=\"\">-- no hay máquinas disponibles --</option>";
	          		?>
	          	</select>
	          </td>
	        </tr> 
	        <tr>
	          <td width="15%" align="left" class="TxtBold" nowrap>Operaci&oacute;n alt.:</td>
	          <td align="left" colspan=4>
	          	<select name="frm_opAlt" class="input">
	          		<?
					$sql="SELECT id_operacion,nombre FROM me_operaciones ORDER BY nombre";
	          		$res=mysql_query($sql);
	          		if($row=mysql_fetch_row($res)){
	          			echo "<option value=\"\">-- Seleccione --</option>";
	          			do{
	          				if($oprc->id_opAlt==$row[0]) $sl="selected";
	          				else $sl="";
	          				echo "<option value=\"".$row[0]."\" $sl>".$row[1]."</option>";
	          			}while($row=mysql_fetch_row($res));
	          		}else echo "<option value=\"\">-- no hay operaciones disponibles --</option>";
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



<?
$p=new Pest($_POST["p"]);
$p->add("Modos de fallo","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Características","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->add("AMFE","#","onClick=\"cambioPestanya('".$_POST["p"]."','2')\"");
$p->add("Plan de control","#","onClick=\"cambioPestanya('".$_POST["p"]."','3')\"");
$p->add("Referencias y componentes","#","onClick=\"cambioPestanya('".$_POST["p"]."','4')\"");
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
switch($_POST["p"]) {	
			
	case "4":
		//*****************************************************************************************************************************/
       	// COMPONENTES Y OPERACIONES RELACIONADAS
       	//*****************************************************************************************************************************/
		?>		
		<tr>
			<td>   			
	      	<?		  
      		/* REFERENCIAS */
    		$sql="".
			"SELECT r.id_referencia,r.num,r.nombre FROM me_referencia_relacion rr ".
			"LEFT JOIN me_referencias r ON rr.id_referencia=r.id_referencia ".
			"WHERE rr.id_relacion=".$oprc->id_operacion." AND rr.tipo='O' ";
			$cnt=0;
			$res=@mysql_query($sql);
			if ($row=@mysql_fetch_array($res)){
				?>
				<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>
		          Referencias
		        </caption>
				<tr>
					<th width="20%" align=left>Nº referencia</th>
		          	<th width="80%" align="left">Denominaci&oacute;n</th>
		        </tr>	
				<?
				do{
		  			$cnt++;
		  			?>
		  			<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
			          <td align="left" class="Fila1" onClick="verRef('<?=$row["id_referencia"]?>')"><?=$row["num"]?></td>
			          <td align="left" class="Fila1" onClick="verRef('<?=$row["id_referencia"]?>')"><?=$row["nombre"]?></td>
			          <!--<td align="left" class="Fila1" onClick="verRef('<?=$row["id_referencia"]?>')">
			          <img src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar relación" width="11" height="11">
			          </td>-->
			        </tr>
		  			<?
      			}while($row=mysql_fetch_array($res)); 
	      		?>
	      		</table>
	      		<table width="95%">
	        		<tr>
    					<td  align="left" class="TxtBold">N&uacute;mero de referencias relacionadas: <?=$cnt?> </td>
    				</tr>
    				<tr>
    					<td align="left" class="spacer8"><br>&nbsp;</td>
    				</tr>
    			</table>
	      		<?
    		}else{
      		?>
      		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>
		        Referencias
		        </caption>
				<tr><td class="TxtBold" colspan=3 align=left>No hay referencias con esta operación</td></tr>
				<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
      		</table>
      		<?		        	
      		}
      	
      	/* COMPONENTES */
      	      	
      	$sql="".
      	"SELECT c.id_componente,c.nombre,c.codigo FROM me_operaciones o ".
		"JOIN me_componente_operacion co ON o.id_operacion=co.id_operacion ".
		"JOIN me_componentes c ON co.id_componente=c.id_componente ".
		"WHERE o.id_operacion=".$oprc->id_operacion;
		$cnt=0;
		$res=@mysql_query($sql);
		if ($row=@mysql_fetch_array($res)){
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
			<caption>
	        Componentes
	        </caption>
			<tr>
				<th width="20%" align=left>C&oacute;digo</th>
	          	<th width="80%" align="left">Denominaci&oacute;n</th>
	        </tr>	
			<?
			do{
	  			$cnt++;
	  			?>
	  			<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
		          <td align="left" class="Fila1" onClick="verComp('<?=$row["id_componente"]?>')"><?=$row["codigo"]?></td>
		          <td align="left" class="Fila1" onClick="verComp('<?=$row["id_componente"]?>')"><?=$row["nombre"]?></td>
		        </tr>
	  			<?
      		}while($row=mysql_fetch_array($res)); 
      		?>
      		</table>
      		<table width="95%">
        		<tr>
					<td  align="left" class="TxtBold">N&uacute;mero de componentes relacionados: <?=$cnt?> </td>
				</tr>
				<tr>
					<td align="left" class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
      		<?
    	} else {
      		//no hay refs relaccionadas
      		?>
      		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
		  	<caption>
	        Componentes
	        </caption>
  			<tr>
  				<td class="TxtBold" colspan=3 align=left>No hay componentes con esta operación</td>
  			</tr>
  			<tr>
				<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
			</tr>
      		</table>
      		<?		        	
      	}?>
      </td>
    </tr>
    <?	        	
		break;
		
	case "2":
		//*****************************************************************************************************************************/
       	// AMFE DE PROCESOS
       	//*****************************************************************************************************************************/
       	
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1">
		<caption>amfe</caption>		
		<?
		echo pintarCabeceraAMFEMini();
		echo $oprc->pintarFilaAMFE();
		echo pintarPieAMFE();
		echo"<br>";
		echo "</table>";
		break;
		
	
	case "1":
		?>		
		<tr>
			<td>   			
	        <?
	        //*****************************************************************************************************************************/
	       	// CARACTERÍSTICAS RELACIONADAS
	       	//*****************************************************************************************************************************/
	           
        	$sql="SELECT * FROM ad_caracteristicas WHERE id_caracteristica IN (".implode(",",$oprc->caracts).")";	
        	$res=mysql_query($sql);        		
			if ($row=@mysql_fetch_array($res)){
				?>
				<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				  <caption>
		          Características
		        </caption>
				<tr>
		          <th width="75%" align="left">Denominaci&oacute;n</th>
		          <th width="5%" align="left">&nbsp;</th>
		        </tr>	
				<?
				do{
					if($us_rol>=2) $eventoOnClick=" onClick=\"verCaract('".$row["id_caracteristica"]."')\" ";
					else $eventoOnClick="";
					$cnt++;
        			?>
        			<tr <?=($us_rol>=2?"onMouseOver=\"filaover(this)\" onMouseOut=\"filaout(this)\"":"")?> >
        			   <td align="left" class="Fila1" <?=$eventoOnClick?> ><?=$row["nombre"]?></td>
			          <td align="center" class="Fila1" onClick="eliminaC('<?echo $row["id_caracteristica"]?>')">
			          <img src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar relación" width="11" height="11"> </td>
			        </tr>
        			<?
        		}while($row=mysql_fetch_array($res)); 
        		?>
        		</table>
        		<table width="95%">
	        		<tr>
      					<td  align="left" class="TxtBold">N&uacute;mero de características relacionadas: <?=$cnt?> </td>
      				</tr>
      				<tr>
      					<td align="left" class="spacer8"><br>&nbsp;</td>
      				</tr>
      			</table>
        		<?
      		}else{
        		?>
        		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
					  <caption>
		          Características
		        </caption>
        			<tr>
        				<td class="TxtBold" colspan=3 align=left>No hay características asignadas a esta operaci&oacute;n</td>
        			</tr>
        			<tr>
      					<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
      				</tr>
        		</table>
        		<?		        	
        	}	        
	        ?>		        	
			</td>
		</tr>
			
		
		
		<?
		break;
		
			
		
	case "3":
	
		//*****************************************************************************************************************************/
       	// PLAN DE CONTROL
       	//*****************************************************************************************************************************/
		
       	?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1">
		<caption>plan de control</caption>
		<?
		echo pintarCabeceraPControl();
		$plan=$oprc->generarPlanDeControl();
		echo $plan;
		?>
		</table>
		<br><br>
		<?
		break;
	
		
	default:
		?>		
		<tr>
			<td>   			
	        <?
	       //*****************************************************************************************************************************/
	       // MODOS DE FALLO
	       //*****************************************************************************************************************************/
	           
        	$sql="SELECT * FROM me_modos WHERE id_modo IN (".implode(",",$oprc->modos).")";	
        	$res=mysql_query($sql);        		
			if ($row=@mysql_fetch_array($res)){
				?>
				<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				  <caption>
		          Modos
		        </caption>
				<tr>
		          <th width="75%" align="left">Denominaci&oacute;n</th>
		          <th width="5%" align="left">&nbsp;</th>
		        </tr>	
				<?
				do{
	    			$cnt++;
	    			?>
	    			<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
			          <td align="left" class="Fila1" onClick="verModo('<?=$row["id_modo"]?>')"><?=$row["nombre"]?></td>
			          <td align="center" class="Fila1" onClick="eliminaM('<?echo $row["id_modo"]?>')"><img src="<?=$app_rutaWEB?>/html/img/papelera.gif" 
	    			  alt="eliminar relación" width="11" height="11"> </td>
			        </tr>
	    			<?
        		}while($row=mysql_fetch_array($res)); 
        		?>
        		</table>
        		<table width="95%">
	        		<tr><td  align="left" class="TxtBold">N&uacute;mero de modos relacionados: <?=$cnt?> </td></tr>
      				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
      			</table>
        		<?
      		}else{
        		?>
        		<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
					<caption>Modos</caption>
	    			<tr><td class="TxtBold" colspan=3 align=left>No hay modos de fallo asignados a esta operaci&oacute;n</td></tr>
	    			<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
        		</table>
        	<?}?>		        	
			</td>
		</tr>
<?}?>
			</table>
		</td>
	</tr>		
</table>
<script><?=$JSEjecutar?></script>
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