<?
include "recursos/conex.php";
include "recursos/seguridad.php";


$tplDir="html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "inicioMain.tpl" ));

//flush();
ob_start();  

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" class="FInicio"><br>
      <br>
    <img src="html/img/Tit.jpg" alt="Asociaci&oacute;n vasca de empresas fabricantes de tornilleria" width="361" height="101"></td>
  </tr>
</table>
<?

$centro=ob_get_contents();

ob_end_clean();


$tpl->assign("{CONTENIDOCENTRAL}",$centro); 
$tpl->assign("{MIGADEPAN}","Inicio"); 
$tpl->assign("{BOTONESTEMPLATE}",botonesTemplate($us_rol)); 
$tpl->assign("{USUARIO}",$us_nombre." ".$us_apellidos);  
$tpl->parse(CONTENT, main);
$tpl->FastPrint(CONTENT);

?>