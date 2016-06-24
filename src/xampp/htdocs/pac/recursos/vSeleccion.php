<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";

global $app_rutaWEB;

$tipo=$_GET["tipo"];
$buscando=false;
$radio=isset($_GET["radio"]) ? true : false;
//if(formatSeleccion()){
//cargamos las funciones de JS dependiende del tipo
switch ($tipo){
	case "referencia":
		if($radio) {
			$eventoJS="onClick=\"if(formatSeleccion()){window.opener.copiarReferencia(seleccionados);window.close();}\"";
			$tipoInput="radio";
			$var2="selected";
		}else{
			$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarReferencias(seleccionados);window.close();}\"";
			$tipoInput="checkbox";
			$var2="checked";
		}
		if(isset($_POST["frm_num"]) || isset($_POST["frm_nombre"]) || isset($_POST["frm_familia"])) $buscando=true; 
		break;
	case "operacion":
		if(isset($_GET["paraPlani"])){
			$eventoJS="onClick='if(formatSeleccion()){window.location=\"../planificacion/operacionesAgregar.php?ids=\"+seleccionados;}'";
		}else{	
			$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarOperaciones(seleccionados);window.close();}\"";
		}
		if(isset($_POST["frm_cod"]) || isset($_POST["frm_nombre"])) $buscando=true;
		break;
	case "actividad":
		$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarActividades(seleccionados);window.close();}\"";
		$eventoJS2="onClick=\"window.location='".$app_rutaWEB."/planificacion/actividad.php?nuevo=1&idp=".$_GET["idp"]."'\"";
		if(isset($_POST["frm_nombre"])) $buscando=true; 
		break;
	case "modo":
		$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarModos(seleccionados);window.close();}\"";
		if(isset($_POST["frm_nombre"])) $buscando=true; 
		break;
	case "causa":
		$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarCausas(seleccionados);window.close();}\"";
		if(isset($_POST["frm_nombre"])) $buscando=true; 
		break;
	case "efecto":
		$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarEfectos(seleccionados);window.close();}\"";
		if(isset($_POST["frm_nombre"])) $buscando=true; 
		break;
	case "caracteristica":
		$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregaCars(seleccionados);window.close();}\"";
		if(isset($_POST["frm_nombre"])) $buscando=true; 
		break;
	case "componente":
		if($radio) {
			$eventoJS="onClick=\"if(formatSeleccion()){window.opener.copiarComponente(seleccionados);window.close();}\"";
			$tipoInput="radio";
			$var2="selected";
		}else{
			$eventoJS="onClick=\"if(formatSeleccion()){window.opener.agregarComponentes(seleccionados);window.close();}\"";
			$tipoInput="checkbox";
			$var2="checked";
		}		
		if(isset($_POST["frm_cod"]) || isset($_POST["frm_nombre"])) $buscando=true; 
		break;
	case "cliente":
		$eventoJS="onClick=\"if(formatSeleccion()){window.opener.confirmPlanificacion(seleccionados);window.close();}\"";
		if(isset($_POST["frm_nombre"]) || isset($_POST["frm_cod"])) $buscando=true; 
		break;
	default:
	
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Documento sin t&iacute;tulo</title>
	<link href="<?=$app_rutaWEB?>/html/css/asvefat.css" rel="stylesheet" type="text/css">	
	<script language="JavaScript1.2" type="text/javascript" src="<?=$app_rutaWEB?>/html/js/menu.js"></script>	
	<script language="JavaScript" type="text/JavaScript">
		var seleccionados="";
		function filaover(elemento){
			elemento.style.cursor='hand';
			elemento.className='FilaOver'
		}
		function filaout(elemento){
			elemento.className='Fila'
		}
		function formatSeleccion (){		
			if(seleccionados!=""){
				seleccionados=(seleccionados.replace(/##/g,",")).replace(/#/g,"");
				return true;
			}else{
				alert("Debe seleccionar algún registro");
				return false;
			}
			
		}
		function agSel(id)	{	seleccionados+="#"+id+"#"; }
		function qSel (id) 	{ seleccionados=seleccionados.replace("#"+id+"#",""); }
		function Pulsa(boo,id) {
			if(boo) agSel(id);
			else 		qSel(id);	
		}
	</script>
</head>

<body>
	<form method="POST">
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
		<tr><td class="spacer6">&nbsp;</td>
	  <tr>
	    <td align="center" class="Tit"><span class="fBlanco">BUSCADOR</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
		    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	 <?
	  switch ($tipo) {
	  	case "referencia":

/******************************************************************************************************************************************/
/*REFERENCIAS*/
/******************************************************************************************************************************************/

		  			?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>N&ordm; Referencia:</td>
			        <td width="80%" align="left" nowrap><input name="frm_num" type="text" class="input" size="10" value="<?=txtParaInput($_POST["frm_num"])?>"></td>
			        <td rowspan=3 align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="40" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>
			      </tr>	 
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Familia:</td>
			        <td width="80%" align="left" nowrap><input name="frm_familia" type="text" class="input" size="40" value="<?=txtParaInput($_POST["frm_familia"])?>"></td>
			      </tr>	
			      <tr><td class="spacer6">&nbsp;</td>
		      </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_num"]!="") $where.=" AND r.num LIKE '%".txtParaGuardar($_POST["frm_num"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND r.nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      if($_POST["frm_familia"]!="") $where.=" AND f.nombre LIKE '%".txtParaGuardar($_POST["frm_familia"])."%'";
		      $sql="SELECT r.* FROM me_referencias r LEFT JOIN ad_familias f ON r.id_familia=f.id_familia WHERE 1 ".$where." ORDER BY r.nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Referencias
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							    <th width="85px" align="left" nowrap>&nbsp;N&ordm; Referencia </th>
							    <th width="310px" align="left" >&nbsp;Denominaci&oacute;n</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1"><input type="<?=$tipoInput?>" name="aupa" onClick="<?=($radio ? "seleccionados='".$row["id_referencia"]."'" : "Pulsa(this.checked,'".$row["id_referencia"]."')")?>"  class="Txt"></td>
											    <td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["num"]?></a> </td>
											    <td width="280px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	Numero de referencias encontradas: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen referencias con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay referencias en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Referencias
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
		   break;



	  		
	  		
	  	case "operacion":
/******************************************************************************************************************************************/
/*OPERACIONES*/
/******************************************************************************************************************************************/

		  			?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
			        <td width="80%" align="left" nowrap><input name="frm_cod" type="text" class="input" size="10" value="<?=txtParaInput($_POST["frm_cod"])?>"></td>
			        <td rowspan=3 align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="40" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>
			      </tr>	
			      <tr><td class="spacer6">&nbsp;</td> 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_cod"]!="") $where.=" AND codigo LIKE '%".txtParaGuardar($_POST["frm_cod"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM me_operaciones WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Operaciones
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							    <th width="85px" align="left" nowrap>&nbsp;C&oacute;digo </th>
							    <th width="310px" align="left" >&nbsp;Denominaci&oacute;n</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1">
												    <input type="checkbox" id="ch<?=$cnt?>" onClick="Pulsa(this.checked,'<?=$row["id_operacion"]?>')" 
									      			value="<?=$row["id_operacion"]?>" class="Txt"></td>
											    <td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["codigo"]?></a> </td>
											    <td width="280px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	Numero de operaciones encontradas: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen operaciones con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay operaciones en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Operaciones
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
		   break;
	  	case "modo":
/******************************************************************************************************************************************/
/*MODOS*/
/******************************************************************************************************************************************/  
?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>
			        <td align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			    	<tr><td class="spacer6">&nbsp;</td> 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM me_modos WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>MODOs
							  </caption>
							  <tr>
							    <th width="30px" align="left">&nbsp;</th>
							    <th width="390px" align="left" nowrap>&nbsp;Denominaci&oacute;n </th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1"><input type="checkbox" id="ch<?=$cnt?>" onClick="Pulsa(this.checked,'<?=$row["id_modo"]?>')" value="<?=$row["id_modo"]?>" class="Txt"></td>
											    <td width="370px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	N&uacute;mero de modos encontrados: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen modos con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay modos en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Modos
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
	  		break;

	  	case "actividad":
/******************************************************************************************************************************************/
/*ACTIVIDADES*/
/******************************************************************************************************************************************/  
?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Nombre:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>
			        <td align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			    	<tr><td class="spacer6">&nbsp;</td> 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM ad_actividades WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Actividades
							  </caption>
							  <tr>
							    <th width="30px" align="left">&nbsp;</th>
							    <th width="390px" align="left" nowrap>&nbsp;Nombre </th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1"><input type="checkbox" id="ch<?=$cnt?>" onClick="Pulsa(this.checked,'<?=$row["id_actividad"]?>')" value="<?=$row["id_actividad"]?>" class="Txt"></td>
											    <td width="370px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="40%" align="left" class="TxtBold" valign="bottom">
							    	N&uacute;mero de actividades: <?=$cnt?> <br><br>
							    </td>
							    <td width="60%" align="right" class="TxtBold">
							    	<input type="button" class="Boton" value="Crear Nueva" <?=$eventoJS2?>>  	
							    	<input type="button" class="Boton" value="Relacionar" <?=$eventoJS?>>
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen actividades con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay actividades en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Actividades
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
	  		break;
	  		
	  		case "causa":
/******************************************************************************************************************************************/
/*CAUSAS*/
/******************************************************************************************************************************************/  
?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
			        <td width="80%" align="left" nowrap><input name="frm_codigo" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_codigo"])?>"></td>
			      </tr>
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Causa:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>  
			        <td align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			    	<tr><td class="spacer6">&nbsp;</td> 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_codigo"]!="") $where.=" AND codigo LIKE '%".txtParaGuardar($_POST["frm_codigo"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM me_causas WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Causas
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							    <th width="85px" align="left" nowrap>&nbsp;C&oacute;digo </th>
							    <th width="310px" align="left" >&nbsp;Causa</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1"><input type="checkbox" id="ch<?=$cnt?>" onClick="Pulsa(this.checked,'<?=$row["id_causa"]?>')" value="<?=$row["id_operacion"]?>" class="Txt"></td>
											    <td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["codigo"]?></a> </td>
											    <td width="280px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	Numero de causas encontradas: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen causas con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay causas en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Causas
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
	  		break;

	  		
	  		
	  			  		case "caracteristica":
/******************************************************************************************************************************************/
/* CARACTERISTICAS */
/******************************************************************************************************************************************/  
?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<!--<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
			        <td width="80%" align="left" nowrap><input name="frm_codigo" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_codigo"])?>"></td>
			      </tr>-->
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Característica:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>  
			        <td align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			    	<tr><td class="spacer6">&nbsp;</td> 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      //if($_POST["frm_codigo"]!="") $where.=" AND codigo LIKE '%".txtParaGuardar($_POST["frm_codigo"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM ad_caracteristicas WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Caracter&iacute;sticas
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							  <!-- <th width="85px" align="left" nowrap>&nbsp;C&oacute;digo </th>-->
							    <th width="395px" align="left" >&nbsp;Característica</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1">
											    <input type="checkbox" id="ch<?=$cnt?>" onClick="Pulsa(this.checked,'<?=$row["id_caracteristica"]?>')" 
								      	value="<?=$row["id_caracteristica"]?>" class="Txt"></td>
											    <!--<td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["codigo"]?></a> </td>-->
											    <td width="370px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	Numero de características encontradas: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen características con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay características en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Caracter&iacute;sticas
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
	  		break;
	  		
	  		
	  		
	  		
  		case "efecto":
/************************************************************************************************/
/*EFECTOS*/
/************************************************************************************************/  
			?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
			        <td width="80%" align="left" nowrap><input name="frm_codigo" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_codigo"])?>"></td>
			      </tr>
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Efecto:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="30" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>  
			        <td align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			    	<tr><td class="spacer6">&nbsp;</td> 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_codigo"]!="") $where.=" AND codigo LIKE '%".txtParaGuardar($_POST["frm_codigo"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM me_efectos WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Efectos
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							    <th width="85px" align="left" nowrap>&nbsp;C&oacute;digo </th>
							    <th width="310px" align="left" >&nbsp;Efecto</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1"><input type="checkbox" id="ch<?=$cnt?>" onClick="Pulsa(this.checked,'<?=$row["id_efecto"]?>')" value="<?=$row["id_operacion"]?>" class="Txt"></td>
											    <td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["codigo"]?></a> </td>
											    <td width="280px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	Numero de efectos encontradas: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen efectos con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay efectos en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Efectos
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
	  		break;
	  	case "componente":
/******************************************************************************************************************************************/
/*COMPONENTES*/
/******************************************************************************************************************************************/	  	

		  			?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
			        <td width="80%" align="left" nowrap><input name="frm_cod" type="text" class="input" size="10" value="<?=txtParaInput($_POST["frm_cod"])?>"></td>
			        <td rowspan=3 align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="40" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>
			      </tr>	 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_cod"]!="") $where.=" AND codigo LIKE '%".txtParaGuardar($_POST["frm_cod"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaGuardar($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM me_componentes WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Componentes
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							    <th width="85px" align="left" nowrap>&nbsp;C&oacute;digo </th>
							    <th width="310px" align="left" >&nbsp;Denominaci&oacute;n</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1">
											    <input type="<?=$tipoInput?>" name="foo" onClick="<?=($radio ? "seleccionados='".$row["id_componente"]."'" : "Pulsa(this.checked,'".$row["id_componente"]."')")?>" value="<?=$row["id_componente"]?>" class="Txt"></td>
											    <td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["codigo"]?></a> </td>
											    <td width="280px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	Numero de componentes encontrados: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen componentes con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay componentes en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Componentes
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
		  	break;
		case "cliente":
/******************************************************************************************************************************************/
/*CLIENTEs*/
/******************************************************************************************************************************************/  
	?>
		  			<tr><td class="spacer6">&nbsp;</td>
		  			<tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>C&oacute;digo:</td>
			        <td width="80%" align="left" nowrap><input name="frm_cod" type="text" class="input" size="10" value="<?=txtParaInput($_POST["frm_cod"])?>"></td>
			        <td rowspan=3 align="right"> <input type="button" class="Boton" value="Buscar" onClick="this.form.submit()"> </td>
			      </tr>	 
			      <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>Denominaci&oacute;n:</td>
			        <td width="80%" align="left" nowrap><input name="frm_nombre" type="text" class="input" size="40" value="<?=txtParaInput($_POST["frm_nombre"])?>"></td>
			      </tr>	 
			    </table>
		    </td>
		  </tr>
			<tr><td class="spacer6">&nbsp;</td></tr>
			<tr>
		  	<td align="center" class="Caja">
		      <?
		      $where="";
		      if($_POST["frm_cod"]!="") $where.=" AND num LIKE '%".txtParaInput($_POST["frm_cod"])."%'";
		      if($_POST["frm_nombre"]!="") $where.=" AND nombre LIKE '%".txtParaInput($_POST["frm_nombre"])."%'";
		      $sql="SELECT * FROM me_clientes WHERE 1 ".$where." ORDER BY nombre";
		      if($res=mysql_query($sql)) {
		      	if($row=mysql_fetch_array($res)){
		      		//si hay resultados
				      $cnt=0;
				      ?>
				      <table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="BordesTabla">
				        <caption>
							    <br>Clientes
							  </caption>
							  <tr>
							    <th width="25px" align="left">&nbsp;</th>
							    <th width="85px" align="left" nowrap>&nbsp;N&uacute;mero </th>
							    <th width="310px" align="left" >&nbsp;Nombre del Cliente</th>
							  </tr>			 
								<tr>
									<td colspan=3>
										<div id="Layer1" style="width:100%; position:relative; top:0; left:0; height:200px; overflow: auto;">     
											<table width="390" border="0" align="left" cellpadding="0" cellspacing="1" class="">							  
								      <?
								      do{
								      	$cnt++;
								      	?>
								      	<tr onMouseOver="filaover(this)" onMouseOut="filaout(this)">
											    <td width="20px" class="Fila1">
											    <input type="radio" name="foo" onClick="seleccionados='<?=$row["id_cliente"]?>'" value="<?=$row["id_componente"]?>" class="Txt"></td>
											    <td width="90px" align="left" class="Fila1"><a href="#">&nbsp;<?=$row["num"]?></a> </td>
											    <td width="280px" align="left" class="Fila1">&nbsp;<?=$row["nombre"]?> </td>									    
											  </tr>
								      	<?
							      	}while($row=mysql_fetch_array($res))
							      	?>
				      				</table>
			      				</div>
			      			</td>
			      		</tr>
			      	</table>			      	
			      	<table width="420px" border="0" align="center" cellpadding="0" cellspacing="0">
							  <tr><td colspan="2" align="left" class="spacer6">&nbsp;</td></tr>
							  <tr>
							    <td width="70%" align="left" class="TxtBold" valign="bottom">
							    	N&uacute;mero de clientes encontrados: <?=$cnt?> <br><br>
							    </td>
							    <td width="30%" align="right" class="TxtBold">
							    	<input type="button" valign=top class="Boton" value="Relacionar" <?=$eventoJS?>>  	
							    </td>
							  </tr>
							  <tr><td colspan="2" align="left" class="spacer8">&nbsp;</td></tr>
							</table>
			      	<?
			      } else {
			      	//no hay resultados
			      	if($buscando) $txt="No existen clientes con lo parámetros de búsqueda introducidos";
			      	else $txt="No hay clientes en la Base de Datos";
			      	?>
			      	<table width="420px" border="0" align="center" cellpadding="2" cellspacing="1" class="">							      
							  <caption>
							    <br>Clientes
							  </caption>							  
							  <tr><td width=100% class="TxtBold" align="left"> <?=$txt?> </td></tr>
							  <tr><td class="spacer6">&nbsp;</td>
							</table>
		      		<?		      
		    		}
		      }
		  	break;
	  	default:
	  		echo "<script>window.close()</script>";
	  }
	  
	  ?>
	  
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		    </table>
	   	</td>
	  </tr>
	</table>
</form>


</body>
</html>
