<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
comprobarAcceso(3,$us_rol);
$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenu.tpl" ));

$miga_pan="Administración >> Configuración";


if($_POST["guardar"]=="1"){	
	foreach($_POST as $n=>$v) {
		if($n!="guardar") mysql_query("UPDATE configuracion SET valor='$v' WHERE var='$n'");
	}
}

flush();
ob_start();
?>
<script>
function guarda(){
	document.forms[0].guardar.value="1";
	document.forms[0].submit();
}
</script>


<table border=0 width=100% >
	<tr>
		<td align="right"><input type="button" class="Boton" value="  Guardar  " onClick="guarda()"></td>
	</tr>
	<tr>
	    <td align="center" class="spacer4">&nbsp;</td>
	  </tr>	
</table>

<table width="100%" border="0" cellspacing="1" cellpadding="0">
 	<tr>
    <td align="center" class="Tit"><span class="fBlanco">PARÁMETROS DE LA APLICACIÓN</span></td>
  </tr>
  <tr>
    <td align="center" class="Caja">
    	<form method=POST><input type="hidden" name="guardar" value="0">
			<table width="90%" border="0" cellspacing="2" cellpadding="4">
	    		<tr>
					<td align="left" colspan=2  class="spacer2">&nbsp;</td>
				</tr>
		        <?
		        $sql="SELECT * FROM configuracion WHERE oculto=0";
		        $res=mysql_query($sql);
		        while($row=mysql_fetch_row($res)){
			        ?>
			        <tr>
			          <td width="50%" align="left" class="TxtBold"><?=$row[1]?>:</td>
			          <td align="left"><input name="<?=$row[0]?>" type="text" class="input" size="70" VALUE="<?=$row[2]?>"></td>
			        </tr>
			        <?
			    }?>
	    		<tr>
					<td align="left" colspan=2  class="spacer2">&nbsp;</td>
				</tr>
	    	</table>
    	</form>
    	
    </td>		    
  </tr>
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