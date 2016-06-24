<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
comprobarAcceso(2,$us_rol);
$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "MainMenuInicio.tpl" ));

$miga_pan="Administración >> Inicio";

flush();
ob_start();
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" class="FInicio"><br>
      <br>
    <img src="/pac/html/img/Tit.jpg" alt="Asociaci&oacute;n vasca de empresas fabricantes de tornilleria" width="361" height="101"></td>
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