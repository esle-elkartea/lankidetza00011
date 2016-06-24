<?
include "conex.php";
include "configuracion.php";
include "class/Fichero.class.php";


if($_POST["guarda"]=="1"){
/******************************************************************************************************************************************/
/* GUARDADO DEL FICHERO Y EJECUCIÓN DE JS DE LA VENTANA PADRE */

	//funcion que crea los directorios de una ruta
	function comprobarYCrearRuta($ruta,$sep){
		$partes=explode($sep,$ruta);
		$cTmp="";
		foreach($partes as $p){
			if ($p!="" && $p!="c:" && $p!="d:" && $p!="e:" && $p!="C:" && $p!="D:" && $p!="E:" ) { 
				if(!is_dir($cTmp.$sep.$p)) mkdir($cTmp.$sep.$p);
				$cTmp.=$sep.$p;
			}			
		}
	}
	global $app_rutaARCHIVOS;
	global $app_minirutaARCHIVOS;
	global $app_carpetaImagenesClases;
	
	$separadorCarpetas=strpos($app_rutaARCHIVOS,"/")===false ? "\\" : "/";
	$separadorCarpetas="\\";
	
	switch ($tipo){
		case "ImagenDeClase":
				$carpetaDestino=$app_rutaARCHIVOS.$app_carpetaImagenesClases.$separadorCarpetas."clase[".$_GET["id"]."]";
				$tmpRuta=str_replace("\\","/",$app_minirutaARCHIVOS.$app_carpetaImagenesClases."\\clase[".$_GET["id"]."]");
			break;
		default: 		
	}
	
	comprobarYCrearRuta($carpetaDestino,$separadorCarpetas);
	
	if($_GET["eliminarAnterior"]!=""){
		$parts=explode("/",$_GET["eliminarAnterior"]);
		$nArch=$parts[count($parts)-1];
		@unlink($carpetaDestino.$separadorCarpetas.$nArch);
	}	
	
	$rutaCompleta = $carpetaDestino.$separadorCarpetas.$_FILES["archivo"]["name"];
	$nombreArchivito=$_FILES["archivo"]["name"];
	if(move_uploaded_file($_FILES["archivo"]["tmp_name"],$rutaCompleta)) $rutaArchivo=$tmpRuta."/".$nombreArchivito;
	
	?>
	<script>
	//alert("<?=$rutaArchivo?>");
	window.opener.ficheroGuardado("<?=$rutaArchivo?>");
	window.close();
	</script>
	<?
/******************************************************************************************************************************************/



}else{

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Selección de archivo</title>
		<link href="/pac/html/css/asvefat.css" rel="stylesheet" type="text/css">
		<script language="JavaScript1.2" type="text/javascript" src="/pac/html/js/menu.js"></script>
		<script>
			function actGuarda(){
				if(document.FileForm.archivo.value=="") alert("Debe seleccionar un archivo");
				else{
					if('<?=$_GET["eliminarAnterior"]?>'!='')	{
						if(confirm("El archivo anterior será eliminado. ¿Desea continuar?")) gg();
						else window.close();
					}else gg();
						
				}				
			}
			function gg(){
				document.FileForm.guarda.value="1";
				document.FileForm.submit();
			}
		</script>
	</head>	
	
	<body>	
		<table width=100% border="0" cellspacing="2" cellpadding="4">
			<tr>
				<td>
					<table border=0 class="Caja" width=98% align="center">
						<tr><td class="spacer8">&nbsp;</td></tr>
						<tr>
							<td>
								<table>
									<tr>
										<td width=10%>&nbsp;</td>
										<td class="TxtBold">Seleccione archivo:</td>
									</tr>
									<tr>
										<td width=10%>&nbsp;</td>
										<td>
											<form name="FileForm" enctype="multipart/form-data" method="POST">
												<input type="hidden" name="guarda" value="0">
												<input type="hidden" name="eliminarAnterior" value="<?=$_GET["eliminarAnterior"]?>">
												<input type="file" size=60 class="input" name="archivo">
											</form>
										</td>
									</tr>
									<tr>
										<td width=10%>&nbsp;</td>
										<td><input type="button" onClick="actGuarda()" class="Boton" value="  Guardar  "></td>
									</tr>
								</table>										
							</td>
						</tr>
						<tr><td class="spacer8">&nbsp;</td></tr>
					</table>
				</td>
			</tr>
		</table>	
	</body>	
	
</html>

<?}?>