<? 
// Enviamos los encabezados de hoja de calculo 
//header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=".date("Ymd")."_hoja.ods"); 
include "conex.php";
include "genFunctions.php";
include "class/Referencia.class.php";
include "class/Componente.class.php";
include_once "class/Operacion.class.php";
$arrayRelaciones=unserialize_esp(str_replace("\\","",$_POST["texto"]));
$r=new Referencia();
$r->relaciones=$arrayRelaciones;
$todo=$r->pintarAMFEexportar(true); // true indica para el XLS
echo pintarCabeceraAMFEMini2(true).$todo.pintarPieAMFE();
?>