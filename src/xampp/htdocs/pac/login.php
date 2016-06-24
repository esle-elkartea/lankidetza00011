<?
include "recursos/conex.php";
$tplDir="html";
include "$tplDir/class.FastTemplate.php";


// vamos a comprobar que la ruta WEB está configurada,
// si no lo está habrá que introducirla lo primero.
$sql="SELECT valor FROM configuracion WHERE var='app_rutaWEB' OR var='app_logoEmpresa'";
$res=mysql_query($sql);
$row=mysql_fetch_row($res);
if($row[0]==""){
	$row=mysql_fetch_row($res);
	if($row[0]=="") header("Location: /servConf.php");
}




$tpl = new FastTemplate("$tplDir/default"); 
$tpl->define( array( main => "loginMain.tpl" ));
flush();
ob_start();  
?>


<script>
function validar(f){
	var e="";
	if(f.user.value=="") e="Introduce Nombre\n";
	if(f.pass.value=="") e+="Introduce Password";
	if (e=="") {
		f.logea.value="1";
		f.submit();
	}
	else alert(e);
}
function iSubmitEnter(oEvento, oFormulario){ 
	var iAscii; 
	if (oEvento.keyCode) iAscii = oEvento.keyCode; 
	else if (oEvento.which) iAscii = oEvento.which; 
	else return false; 
	if (iAscii == 13) validar(document.forms[0]); 
	return true; 
} 
</script>
<form action="index.php" method="POST">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" class="FInicio"><input type="hidden" name="logea" value="0">
      <br>
      <table width="406" border="0" cellspacing="2" cellpadding="4">
      <tr>
        <td colspan="4" align="left" class="TxtBold">
        	<img src="html/img/Tit.jpg" alt="Asociaci&oacute;n vasca de empresas fabricantes de tornilleria" width="361" height="101">
        </td>
      </tr>
      <?if($_GET["msg"]!=""){?>
      <tr>
      	<td>&nbsp;</td>
        <td colspan="3" align="left" class="TxtBoldNar">
        	<?=$_GET["msg"]?>
        </td>
      </tr>
	  <?}?>
      <tr>
        <td width="7%" align="left" class="TxtBold">&nbsp;</td>
        <td width="16%" align="left" class="TxtBold">Usuario:</td>
        <td width="45%" align="left"><input name="user" type="text" class="input" size="25"></td>
        <td width="32%" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" class="TxtBold">&nbsp;</td>
        <td align="left" class="TxtBold">Password:</td>
        <td align="left"><input name="pass" type="password" class="input" size="25" onkeypress="iSubmitEnter(event, this.form)"></td>
        <td align="left"><input type="button" class="Boton" onClick="validar(this.form)" value="Aceptar"></td>
      </tr>
    </table></td>
  </tr>
</table>
</form>




<?
$centro=ob_get_contents();
ob_end_clean();
$tpl->assign("{CONTENIDOCENTRAL}",$centro);  
$tpl->parse(CONTENT, main);
$tpl->FastPrint(CONTENT);