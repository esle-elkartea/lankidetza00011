<?
$CONEX_host =	"localhost";
$CONEX_user =	"dbroot";
$CONEX_pass =	"3x4i12";
$CONEX_db   =	"asvefat";
$c2 = mysql_connect($CONEX_host,$CONEX_user,$CONEX_pass);
mysql_select_db($CONEX_db,$c2) or die("No se pudo conectar con la base de datos");
$res=mysql_query("SELECT valor FROM configuracion WHERE var='app_rutaWEB'");
$row=mysql_fetch_row($res);
if($row[0]!=""){
	header("Location: /");
	exit;
}

if($_POST["app_rutaWEB"]!=""){
	$ruta=ereg_replace("/+","/",$_POST["app_rutaWEB"]."/pac");
	$ruta="http://".ereg_replace("/pac/pac","/pac",$ruta);
	if (@fclose(@fopen($ruta."/index.php", "r"))) {
		mysql_query("UPDATE configuracion SET valor='".$ruta."' WHERE var='app_rutaWEB'");
		mysql_query("UPDATE configuracion SET valor='".$ruta."/html/img/Asvefat.gif' WHERE var='app_logoEmpresa'");
		header("Location: ".$ruta."/index.php");
	}
}
?>
<html>
<body>
	<form method=POST>
		<table width=60% align=center border=0>
			<?$_POST["app_rutaWEB"]!=""?"<tr><td colspan=3><font color=red>El servidor no existe.</font></td></tr>":"";?>
			<tr>
				<td width=45%>Introduzca el nombre del servidor donde se aloja la carpeta pac. Ej: http://www.nombreservidor.es </td>
				<td width=10%>&nbsp;</td>
				<td width=45%>http://
					<input type="text" name="app_rutaWEB" value="" size=40>
				</td>
			</tr>
			<tr>
				<td colspan=2><br><input type=submit value="Guardar"></td>
			</tr>
		</table>
	</form>
</body>
</html>

	
