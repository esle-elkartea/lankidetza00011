<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas

global $app_rutaWEB;


$ido=$_GET["ido"];
$idp=$_GET["idp"];
$idc=$_GET["idc"];

$cars=unserialize_esp($_POST["todos_cars"]);


if($_POST["comprobarSubmit2"]!="1" &&($idc!="" && $idp!="")){
	// aqui lo cargo todo porque es la primera vez que entro
	$sql="SELECT  * FROM pl_caracteristicas c WHERE id_caracteristica=$idc and id_planificacion=$idp";
	$res=mysql_query($sql);
	if($row=@mysql_fetch_assoc($res)){
		$_POST["frm_nombre"]=$row["nombre"];
		$_POST["frm_evaluacion"]=$row["evaluacion"];
		$_POST["frm_num"]=$row["num"];
		$_POST["frm_tam"]=$row["tam"];
		$_POST["frm_fre"]=$row["fre"];
		$_POST["frm_metodo"]=$row["metodo"];
		$_POST["frm_plan"]=$row["plan"];
		$_POST["frm_proc"]=$row["proc"];
		$_POST["frm_prod"]=$row["prod"];
		$_POST["frm_clase"]=$row["id_clase"];
	}
}


//guardarlo todo
if($_POST["act_guardar"]=="1"){
	$sql="UPDATE pl_caracteristicas SET ".
	"nombre='".str_replace("'","\\'",txtParaInput($_POST["frm_nombre"]))."', ".
	"num='".$_POST["frm_num"]."', ".
	"prod='".$_POST["frm_prod"]."', ".
	"proc='".$_POST["frm_proc"]."', ".
	"id_clase='".$_POST["frm_clase"]."', ".
	"evaluacion='".str_replace("'","\\'",txtParaInput($_POST["frm_evaluacion"]))."', ".
	"tam='".$_POST["frm_tam"]."', ".
	"fre='".$_POST["frm_fre"]."', ".
	"metodo='".str_replace("'","\\'",txtParaInput($_POST["frm_metodo"]))."', ".
	"plan='".str_replace("'","\\'",txtParaInput($_POST["frm_plan"]))."' ".
	"WHERE id_caracteristica=$idc AND id_planificacion=$idp";
	mysql_query($sql);
	echo $sql;
}

// busco la posicion en el array de la causa o efecto actual




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Documento sin t&iacute;tulo</title>
	<link href="<?=$app_rutaWEB?>/html/css/asvefat.css" rel="stylesheet" type="text/css">	
	<script language="JavaScript1.2" type="text/javascript" src="<?=$app_rutaWEB?>/html/js/menu.js"></script>
	<script>
		
		function filaover(elemento){
			elemento.style.cursor='hand';
			elemento.className='FilaOver'
		}
		function filaout(elemento){
			elemento.className='Fila'
		}
		function validar(f){
			return true;
			/*a=new Array();
			a[0]="int::"+f.frm_num.value+"::Rellene el número de la característica::El número de la característica ha de ser un valor entero";
			a[0]="int::"+f.frm_prod.value+"::Rellene el número de la característica::El número de la característica ha de ser un valor entero";
			a[0]="int::"+f.frm_proc.value+"::Rellene el número de la característica::El número de la característica ha de ser un valor entero";
			a[1]="text::"+f.frm_referencia.value+"::Seleccione una referencia";
			er=JSvFormObligatorios(a);	
			if(er=="") return	true;
			else alert (er);*/
		}
		function guardar(){
			if(validar(document.forms[0])){
				document.forms[0].act_guardar.value="1";
				document.forms[0].submit();
			}
		}
		
		
	</script>
</head>
<body onLoad='window.resizeTo(700,500);<?=$JSEjecutar?>'>
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
	  <tr><td class="spacer6">&nbsp;</td>
	  <tr>
	    <td align="center" class="Tit"><span class="fBlanco">EDITAR CARACTER&Iacute;STICA PARA LA PLANIFICACI&Oacute;N</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">	    
			<form method="POST" name="fmodo">
			<input type="hidden" name="comprobarSubmit2" value="1">
			<input type="hidden" name="act_guardar" value='0'>
			<input type="hidden" name="frm_nombreOp" value="<?=txtParaInput($_POST["frm_nombreOp"])?>">
			<input type="hidden" name="p" value="<?=$_POST["p"]?>">
			<input type="hidden" name="todos_cars" value='<?=str_replace("\\","",$_POST["todos_cars"])?>'>
			<input type="hidden" name="valores_car" value='<?=serialize_esp($car)?>'>
			
		    <table width="90%" border="0" cellspacing="2" cellpadding="4">
	 		  	<tr>
	 		  		<td align=right colspan=3>
	 		  			<input type=button class="Boton" Value="Guardar y volver" onClick="guardar()">
	 		  			<input type="button" class="Boton" value="Volver" onClick="document.getElementById('fVuelta').submit()">
	 		  		</td>
	 		  	</tr>
		  		
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Nombre caracter&iacute;stica:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2>
			        	&nbsp;<input name="frm_nombre" type="text" class="input" size="40" value="<?=txtParaInput($_POST["frm_nombre"])?>">
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap>&nbsp;Clase:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2>
			        	<select name="frm_clase" class=input>
			        	<?
			        	$sql="SELECT * FROM ad_clases ";
			        	$res=mysql_query($sql);
			        	if($row=mysql_fetch_assoc($res)){
			        		echo '<option value="">-- seleccione una clase --</option>';
			        		do{
			        			$sel=$row["id_clase"]==$_POST["frm_clase"]?" selected ":"";
			        			echo '<option value="'.$row["id_clase"].'" '.$sel.'>'.$row["nombre"].'</option>';
			        		}while($row=mysql_fetch_assoc($res));
			        	}else echo '<option value="">-- no hay clases --</option>';

			        	
			        	?>
			        	</select>
			        </td>
			    </tr>
			    <tr>
			    	<td width=20% class=TxtBold align=left valign=top>&nbsp;Caracter&iacute;sticas:</td>
			    	<td width="80%" align="left" class="TxtBold" nowrap colspan=2>
			        	<table border=0 cellpadding=1 cellspacing=1>
			        		<tr>
			        			<th width=33%>&nbsp;N&uacute;mero&nbsp;</th>
			        			<th width=33%>&nbsp;Producto&nbsp;</th>
			        			<th width=33%>&nbsp;Proceso&nbsp;</th>
			        		</tr>
			        		<tr>
			        			<td width=33%>
			        				<input name="frm_num" type="text" class="input" size="20" value="<?=txtParaInput($_POST["frm_num"])?>">
			        			</td>
			        			<td width=33%>
			        				<input name="frm_prod" type="text" class="input" size="20" value="<?=txtParaInput($_POST["frm_prod"])?>">
			        			</td>
			        			<td width=33%>
			        				<input name="frm_proc" type="text" class="input" size="20" value="<?=txtParaInput($_POST["frm_proc"])?>">
			        			</td>
			        		</tr>
			        	</table>
			        </td>
			    </tr>
			    
			     <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Evaluaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	&nbsp;<textarea name="frm_evaluacion" class=input rows=3 cols=90><?=txtParaInput($_POST["frm_evaluacion"])?></textarea>
			        </td>
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Muestra:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	<table border=0 cellpadding=1 cellspacing=1>
			        		<tr>
			        			<th width=50%>&nbsp;Tamaño&nbsp;</th>
			        			<th width=50%>&nbsp;Frecuencia&nbsp;</th>
			        		</tr>
			        		<tr>
			        			<td width=50%>
			        				<input name="frm_tam" type="text" class="input" size="10" value="<?=txtParaInput($_POST["frm_tam"])?>">
			        			</td>
			        			<td width=50%>
			        				<input name="frm_fre" type="text" class="input" size="10" value="<?=txtParaInput($_POST["frm_fre"])?>">
			        			</td>
			        		</tr>
			        	</table>
			        </td>
			        
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;M&eacute;todo:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	&nbsp;<textarea name="frm_metodo" class=input rows=3 cols=90><?=txtParaInput($_POST["frm_metodo"])?></textarea>
			        </td>
			        
			    </tr>
			    <tr>
			        <td width="20%" align="left" class="TxtBold" nowrap valign=top>&nbsp;Plan de reacci&oacute;n:&nbsp;&nbsp;&nbsp;</td>
			        <td width="80%" align="left" nowrap class="TxtBold" colspan=2 >
			        	&nbsp;<textarea name="frm_plan" class=input rows=3 cols=90><?=txtParaInput($_POST["frm_plan"])?></textarea>
			        </td>
			    </tr>
			</table>
		  </form>
		</td>
	  </tr>
  </table>
							




	<form id="fVuelta" action="pl_operacionEditPC.php?idp=<?=$idp?>&ido=<?=$ido?>" method="POST">
	<input type="hidden" name="todos_cars" value='<?=serialize_esp($cars)?>'>
	<input type="hidden" name="frm_nombreOp" value="<?=txtParaInput($_POST["frm_nombreOp"])?>">
	<input type="hidden" name="vuelta" value="1">
	</form>

<script>
<?if($_POST["act_guardar"]=="1"){?>
	window.opener.functSubmit();
	document.getElementById('fVuelta').submit();
<?}?>
</script>
</body>
</html>