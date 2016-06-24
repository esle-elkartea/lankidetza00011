<?
/*****************************************
* Attest ITC (c)
* Marzo 2007
* PROYECTO: PAC 
* Página de seguridad
******************************************/

include_once "configuracion.php";

$pagvuelta = $app_rutaWEB."/login.php";

session_start();

if($logea) {

	$consulta="select id_usuario,password,nombre,apellidos,rol from usuarios where clave='".$_POST["user"]."' and baja=0";
	$res=mysql_query($consulta);		
	if($row=mysql_fetch_array($res)) {		
		if($_POST["pass"]==$row[1]) {		
			$us_id=$row["id_usuario"];
			$us_nombre=$row["nombre"];
			$us_apellidos=$row["apellidos"];
			$us_rol=$row["rol"];					
			session_register("us_id");
			session_register("us_nombre");
			session_register("us_apellidos");
			session_register("us_rol");
		}else{
			header("Location:$pagvuelta?msg=La contraseña introducida es incorrecta.");
			exit;
		}
	}else{
		header("Location:$pagvuelta?msg=El usuario introducido no existe.");		
		exit;
	}	
} else {
	if(!$us_id && $_SESSION["us_id"]==""){
		header("Location:$pagvuelta");
		exit;
	}else if(!$us_id){
			// hay que hacer la conversión de las variables que estan en 
			// $_SESSION a sus nombres normales (linux vs windows)
			$us_id=$_SESSION["us_id"];
			$us_nombre=$_SESSION["us_nombre"];
			$us_apellidos=$_SESSION["us_apellidos"];
			$us_rol=$_SESSION["us_rol"];
	}
}

		



/*****************************************/
/* FUNCIONES DE SEGURIDAD
/*****************************************/


function comprobarAcceso ($rol,$urol){
	global $app_rutaWEB;
	if($rol > $urol){
		header("Location: ".$app_rutaWEB."/login.php");
	}
}


function botonesTemplate($rol) {
	global $app_rutaWEB;
	if($rol=="3" || $rol=="2") {		
		$botones=	"<td width='21%' class='fMenu'>".
					"<a href='".$app_rutaWEB."/planificacion/pl_planificaciones.php'> ".
					"PLANIFICACI&Oacute;N AVANZADA</a> </td>".
					"<td width='19%' class='fMenu'>".
					"<a href='default.htm' onClick='return clickreturnvalue()' ".
					"onMouseover='dropdownmenu(this, event, menu2, \"210px\")' onMouseout='delayhidemenu()'>".
					"MAESTROS ESPEC&Iacute;FICOS</a> </td>".
					"<td width='21%' class='fMenu'><a href='".$app_rutaWEB."/admin/ad_inicio.php'>MANTENIMIENTO</a></td>";
	}else{
		$botones=	"<td width='21%' class='fMenu'>".
							"<a href='".$app_rutaWEB."/planificacion/pl_planificaciones.php'> ".
							"PLANIFICACI&Oacute;N AVANZADA</a> </td>".
							"<td width='40%' colspan=2 class='fMenu'>&nbsp;</td>";
	}
	return $botones;
}


function mostrarMenuAdmin($rol){
	global $app_rutaWEB;
	$txt="";
	if($rol=="3"){
		$txt= '<tr><td align="left" class="TitMaestros">ADMINISTRACIÓN</a></td></tr>'.
		      '<tr><td class="MenuMaestros" nowrap><a href="'.$app_rutaWEB.'/admin/ad_usuarios.php">Usuarios</a></td></tr>'.
		      '<tr><td class="MenuMaestros" nowrap><a href="'.$app_rutaWEB.'/admin/ad_responsables.php">Responsables</a></td></tr>'.
		      '<tr><td class="MenuMaestros" nowrap><a href="'.$app_rutaWEB.'/admin/ad_config.php">Configuraci&oacute;n </a></td></tr>';
	}
	return $txt;
}






?>
