<?
@extract($_POST);
@extract($_GET);
@extract($_COOKIES);
@extract($_SESSION);
@extract($_SERVER);

define ("LOCATIONCONEX" , 1 );

switch(LOCATIONCONEX){	
	case 1:
		$CONEX_host =	"localhost";
		$CONEX_user =	"dbroot";
		$CONEX_pass =	"3x4i12";
		$CONEX_db   =	"asvefat";
		break;	
}

$c2 = mysql_connect($CONEX_host,$CONEX_user,$CONEX_pass);
mysql_select_db($CONEX_db,$c2) or die(generarError());

function generarError(){	
	return "No se pudo conectar a la base de datos: ".@mysql_error();
}
