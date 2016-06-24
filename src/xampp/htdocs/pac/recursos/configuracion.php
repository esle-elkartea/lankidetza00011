<?

//Obtengo los nombres de variable y su valor
$sql="SELECT * FROM configuracion";
$res=mysql_query($sql);
if($row=mysql_fetch_array($res)) do{
	$$row[0]=$row[2];
}while($row=mysql_fetch_array($res));

/*
$app_carpetaArchivos="/files";
$app_carpetaImagenesClases="/clasesImgs";
$app_inicioWEB="/pac";

//WEB
$app_minirutaWEB="/pac";
$app_rutaWEB="http://sie.attest.es/pac";
$app_rutaSERVIDOR="/expert/htwebs/plataforma/docs/pac";
*/

$app_minirutaARCHIVOS=$app_inicioWEB.$app_carpetaArchivos; //para guardar rutas de archivos en BD



//SERVIDOR

$app_rutaARCHIVOS=$app_rutaSERVIDOR.$app_carpetaArchivos;

//rutas completas
$app_rutaImagenesClases=$app_rutaARCHIVOS.$app_carpetaImagenesClases;


?>