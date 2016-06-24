<?
include "../recursos/conex.php";
include "../recursos/seguridad.php";
include "../recursos/genFunctions.php";
include "../recursos/class/Referencia.class.php";
include "../recursos/class/Operacion.class.php";
include "../recursos/class/Maquina.class.php";
include "../recursos/class/Componente.class.php";
include "../recursos/class/Responsable.class.php";
include_once "../recursos/class/Planificacion.class.php";
include "../recursos/class/Pest.class.php"; //clase de pestañas

comprobarAcceso(1,$us_rol);

$plani=new Planificacion($_GET["id"]);


if(isset($_POST["comprobarSubmit"]) && $_POST["comprobarSubmit"]=="1") {
	$txtMiga=$_POST["txtMiga"];
	$plani->id_referencia=$_POST["frm_referencia"];
	$plani->id_cliente=$_POST["frm_cliente"];
	$plani->fecha=fechaBD($_POST["frm_fecha2"]);
	$plani->actividades=unserialize_esp(str_replace("\\","",$_POST["todos_actividades"]));
	if($_POST["act_cambioRef"]!="") $plani->cargaRelaciones($_POST["frm_referencia"]);
	else $plani->relaciones=unserialize_esp(str_replace("\\","",$_POST["todos_relaciones"]));
	$plani->estudio=unserialize_esp(str_replace("\\","",$_POST["todos_estudio"]));
	$plani->codigo=$_POST["frm_codigo"];
	$plani->cerrado=$_POST["estado"];
	if($_POST["pAnt"]=="4"){
		$plani->prototipo=$_POST["frm_prototipo"];
		$plani->preserie=$_POST["frm_preserie"];
		$plani->serie=$_POST["frm_serie"];
		$plani->equipo=$_POST["frm_equipo"];
		$plani->fecha_aprobacion=$_POST["frm_fecha_aprobacion"];
	}else{
		$plani->prototipo=$_POST["prototipo_guardar"];
		$plani->preserie=$_POST["preserie_guardar"];
		$plani->serie=$_POST["serie_guardar"];	
		$plani->equipo=$_POST["equipo_guardar"];
		$plani->fecha_aprobacion=$_POST["fecha_aprobacion_guardar"];
	}
}elseif(isset($_GET["id"])){
	$plani->cargaRelaciones();
	$plani->cargaActividades();
	$plani->cargaEstudio();
}


if ($_POST["act_guardarEstudio"]=="1"){
	$estudio=array();
	foreach($_POST as $key=>$valPost){
		if(substr($key,0,8)=="estudio_"){
			$partes=explode("_",$key);
			if($partes[1]=="rad") 				$plani->estudio["resp"][$partes[2]]=($valPost==""?"-1":$valPost);
			elseif($partes[1]=="obs") 			$plani->estudio["obs"][$partes[2]]=txtParaInput($valPost);
			elseif($partes[1]=="fechaEstudio") 	$plani->estudio["fecha"]=$valPost;
			elseif($partes[1]=="decision") 		$plani->estudio["decision"]=$valPost==""?"-1":$valPost;
			elseif($partes[1]=="observaciones") $plani->estudio["observaciones"]=txtParaInput($valPost);
		}
	}
	if($_POST["act_imprimirEstudio"]=="1") $plani->guardaEstudio();
}
//echo $_POST["act_guardar"];

if ($_POST["act_agregarActividades"]=="1") 	$plani->agregarActividades(explode(",",$_POST["_actividades"]));
if ($_POST["act_guardarActividades"]=="1")	{$plani->guardarActividades();$JSEjecutar="window.open('exportarActividades.php?idp=".$plani->id_planificacion."','','')";}
if ($_POST["act_agregarComponente"]=="1") 	$MsgJS=$plani->agregarComponente($_POST["_comp"]);
if ($_POST["act_agregarOperacion"]!="")		$plani->agregarOperacion($_POST["act_agregarOperacion"],$_POST["_comp"]);
if ($_POST["act_eliminarActividad"])  		$plani->quitarActividades($_POST["_actividades"]);
if ($_POST["act_eliminarRelacion"])  		$plani->quitarRelacion($_POST["_relaciones"]);
if ($_POST["act_subirActividad"])  			$plani->subirOrdenActividad($_POST["_actividades"]);
if ($_POST["act_bajarActividad"])  			$plani->bajarOrdenActividad($_POST["_actividades"]);
if ($_POST["act_subirRelacion"])  			$plani->subirOrdenRelacion($_POST["_relaciones"],$_POST["_comp"]);
if ($_POST["act_bajarRelacion"])  			$plani->bajarOrdenRelacion($_POST["_relaciones"],$_POST["_comp"]);
if ($_POST["act_cerrarActividad"])  		{
											$plani->actividades[$_POST["_actividades"]]["cerrado"]="1";
											$plani->actividades[$_POST["_actividades"]]["fecha_cerrado"]=$_POST["fechaCerrado"];
											}
if ($_POST["act_abrirActividad"])  			{
											$plani->actividades[$_POST["_actividades"]]["cerrado"]="0";
											$plani->actividades[$_POST["_actividades"]]["fecha_cerrado"]="";
											}
if ($_POST["act_guardar"]=="1") 			{
											$JSEjecutar=$plani->guardar();
											if($_POST["act_aplicarCambios"]=="1" && $JSEjecutar=="") $plani->aplicarCambios();
											if($_GET["id"]=="" && $JSEjecutar=="") Header("Location: pl_planificaciones_edit.php?id=".$plani->id_planificacion);	
											}
if ($_POST["act_eliminar"]=="1")			$plani->eliminar();
if ($_POST["act_salir"]=="1") 				Header("Location: ".obtenerRedir($_GET["volver"]));	

if ($_POST["_datosActividad"]!=""){
	if($_POST["_actividades"]=="-1"){
		$plani->actividades[count($plani->actividades)]=unserialize_esp(str_replace("\\","",$_POST["_datosActividad"]));
		//echo str_replace("\\","",$_POST["_datosActividad"])."<br><br>" ;
		//print_r(unserialize_esp(str_replace("\\","",$_POST["_datosActividad"])));
		if($_POST["ordenPosicion"]=="inicio") $plani->ponerActividadPrimera(count($plani->actividades)-1);
	}else{
		$plani->actividades[$_POST["_actividades"]]=unserialize_esp(str_replace("\\","",$_POST["_datosActividad"]));
		if($_POST["ordenPosicion"]=="inicio") $plani->ponerActividadPrimera($_POST["_actividades"]);
		elseif($_POST["ordenPosicion"]=="final") $plani->ponerActividadUltima($_POST["_actividades"]);
	}
}
if($_POST["_datosRelacion"]!=""){
	$plani->relaciones[$_POST["_relaciones"]]=unserialize_esp(convTxt(str_replace("\\","",$_POST["_datosRelacion"])));
	if($_POST["ordenPosicion"]=="inicio") $plani->ponerRelacionPrimera($_POST["_relaciones"]);
	elseif($_POST["ordenPosicion"]=="final") $plani->ponerRelacionUltima($_POST["_relaciones"]);	
}
$arrayOps=array();
foreach ($plani->relaciones as $r){
	$ppp=explode("[::]",$r["o"]);
	$arrayOps[]=$ppp[0];
}
$cadenaOps="#".implode("##",$arrayOps)."#";

$estAct=$plani->isCerrado();
if($estAct=="-1") $estadoPlanificacion="CERRADA - N&uacute;mero de actividades completadas: ".count($plani->actividades);
else $estadoPlanificacion="ABIERTA - N&uacute;mero de actividades completadas: ".$estAct." de ".count($plani->actividades);

if($_POST["p"]=="2" || $_POST["p"]=="0" || $_POST["p"]=="") echo guardaScrolls();

$tplDir="../html";
include "$tplDir/class.FastTemplate.php";
$tpl = new FastTemplate("$tplDir/default");
$tpl->define( array( main => "Main.tpl" ));
$miga_pan="Planificación Avanzada   >>  ";
if ($plani->nuevo) $miga_pan.="Nueva Planificación";
elseif($txtMiga==""){
	$res=mysql_query("SELECT c.nombre FROM me_clientes c WHERE c.id_cliente=$plani->id_cliente");
	$row=mysql_fetch_row($res);
	$txtMiga="Cliente: ".$row[0]."";
	$res=mysql_query("SELECT r.num,r.nombre FROM me_referencias r WHERE r.id_referencia=$plani->id_referencia");
	$row=mysql_fetch_row($res);
	$txtMiga.=";   Referencia: [".$row[0]."] ".$row[1];
	$miga_pan.=$txtMiga;
	
	
}

flush();
ob_start();

?>
<script>
<?=$JSEjecutar?>
<?if($_POST["act_imprimirEstudio"]=="1"){?>
	window.open("pl_imprimirEstudio.php?id=<?=$plani->id_planificacion?>","ventanaImprimir","height=600,width=700,status=no,toolbar=no,menubar=no,location=no");
<?}?>

	
	
	
<?if($MsgJS!="") echo "alert('".$MsgJS."');";?>
var JSidPlanificacion='<?=$plani->id_planificacion?>';
function filaover(elemento){
	elemento.style.cursor='hand';
	elemento.className='FilaOver'
}
function filaout(elemento){
	elemento.className='Fila'
}
function selFecha(dia,mes,ano){
	document.forms[0].frm_fecha2.value=ano+"-"+mes+"-"+dia;
	document.forms[0].frm_fecha.value=dia+"/"+mes+"/"+ano;
}
function eliminar (){
	if(confirm("¿Está seguro de querer eliminar esta planificación y todas sus relaciones?")){
		document.forms[0].act_eliminar.value="1";
		document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}	
}
function guardarSalir(){
	if(validar(document.forms[0])){
		document.forms[0].act_guardar.value=1;
		document.forms[0].act_salir.value=1;
		document.forms[0].submit();
	}
}
function cambioPestanya (desde,hasta){
	document.forms[0].p.value=hasta;
	document.forms[0].submit();
}
function agrActiv (){
	<?=JSventanaSeleccion("actividad","idp=".$plani->id_planificacion)?>
}
function agregarActividades(sel){
	document.forms[0].act_agregarActividades.value=1;
	document.forms[0]._actividades.value=sel;
	document.forms[0].p.value=0;
	document.forms[0].submit();
}


function validar (f){
	a=new Array();
	<?
	
		$res=mysql_query("SELECT codigo FROM planificaciones AND id_planificacion!='$plani->id_planificacion'");
		$todosCodigos=array();
		while($r=@mysql_fetch_row($res)) $todosCodigos[]=$r[0];
		$cadenaCodigos=count($todosCodigos)>0?"#".implode("##",$todosCodigos)."#":"";
	?>
	cadenaCodigos='<?=$cadenaCodigos?>';
	a[0]="int::"+f.frm_codigo.value+"::Debe introducir un código para la planificación::El código ha de ser un valor numérico";
	a[1]="int::"+f.frm_cliente.value+"::Debe seleccionar un cliente";
	a[2]="text::"+f.frm_referencia.value+"::Seleccione una referencia";
	er=JSvFormObligatorios(a);
	if(cadenaCodigos.indexOf("#"+parseInt(f.frm_codigo.value)+"#")!=-1) er+="Existe una planificacion con el mismo código";
	if(er=="") return	true;
	else alert (er);
}

function subirOrdenAct(id){
	document.forms[0].act_subirActividad.value="1";
	document.forms[0]._actividades.value=id;
	document.forms[0].submit();
}
function bajarOrdenAct(id){
	document.forms[0].act_bajarActividad.value="1";
	document.forms[0]._actividades.value=id;
	document.forms[0].submit();
}
function subirOrden(id,aux){
	document.forms[0]._comp.value=aux;
	document.forms[0].act_subirRelacion.value="1";
	document.forms[0]._relaciones.value=id;
	document.forms[0].submit();
}
function bajarOrden(id,aux){
	document.forms[0]._comp.value=aux;
	document.forms[0].act_bajarRelacion.value="1";
	document.forms[0]._relaciones.value=id;
	document.forms[0].submit();
}
function ponerComoCerrado(posi,estado,fecha){
	if(estado=="0"){
		document.forms[0].act_cerrarActividad.value="1";
		document.forms[0].fechaCerrado.value=fecha;
	}else document.forms[0].act_abrirActividad.value="1";
	document.forms[0]._actividades.value=posi;
	document.forms[0].submit();	
}
function agregarDatosActividad(posi,datos,donde){
	document.forms[0]._actividades.value=posi;
	document.forms[0]._datosActividad.value=datos;
	document.forms[0].ordenPosicion.value=donde;
	document.forms[0].submit();
	
}
function agregarDatosRelacion(posi,datos){
	document.forms[0]._relaciones.value=posi;
	document.forms[0]._datosRelacion.value=datos;
	document.forms[0].submit();
	
}
function editarDatosActividad(posi,datos){
	window.open('','ventanaActividad','height=460,width=460,status=no,toolbar=no,menubar=no,location=no');
	document.forms[1].action="actividad.php?pos="+posi;
	document.forms[1].target="ventanaActividad";
	document.forms[1].datosActividad.value=datos;
	document.forms[1].submit();	
}
function actividadEliminar (id){
	if(confirm("¿Desea eliminar la actividad de la planificación?")){
		document.forms[0].act_eliminarActividad.value=1;
		document.forms[0]._actividades.value=id;
		document.forms[0].submit();
	}
}
function editarDatosRelacion(posi,datos){
	window.open('','ventanaRelacion','height=170,width=550,status=no,toolbar=no,menubar=no,location=no');
	document.forms[1].action="relacion.php?pos="+posi;
	document.forms[1].target="ventanaRelacion";
	document.forms[1].datosRelacion.value=datos;
	document.forms[1].submit();	
}
function relacionEliminar (id){
	if(confirm("¿Desea eliminar la operación?")){
		document.forms[0].act_eliminarRelacion.value=1;
		document.forms[0]._relaciones.value=id;
		document.forms[0].submit();
	}
}
function cambioRef(v){
	document.forms[0].act_cambioRef.value=v;	
	document.forms[0].submit();
}
function agrOper(){
	<?=JSVentanaSeleccion("operacion","paraPlani=1")?>
	
}
function agregarOperacion(idOperacion,idComponente){
	if(('<?=$cadenaOps?>').indexOf(idOperacion)==-1) {
		document.forms[0].act_agregarOperacion.value=idOperacion;
		document.forms[0]._comp.value=idComponente;
		document.forms[0].submit();
	}
}
function getRelaciones(){
	return document.getElementById("todos_relaciones").value;
}
function agrComps(){
	<?=JSVentanaSeleccion("componente")?>
}
function agregarComponentes(ids){
	document.forms[0].act_agregarComponente.value="1";
	document.forms[0]._comp.value=ids;
	document.forms[0].submit();
}
function infActividades(v,pos){
	if(v=='impr'){
		if(confirm("El listado actual de actividades deberá guardarse. ¿Desea continuar?")){
			document.forms[0].act_guardarActividades.value="1";
			document.forms[0].submit();
		}
	}else if(v=="0"){
		window.open('','informeActividades','height=250,width=250,status=no,toolbar=no,menubar=no,location=no');
		document.forms[2].action="informeActividades.php?pos="+pos;
		document.forms[2].target="informeActividades";
		document.forms[2].submit();
	}else{
		ponerComoCerrado(pos,1);
	}
}
function busca(elque){
	if(elque=="C") <?=JSVentanaSeleccion("cliente","paraPlani=1")?>	
	else  <?=JSVentanaSeleccion("referencia","radio=1")?>
}
function copiarReferencia(id){ //esta funcion (apesar de su nombre) hace submit para seleccionar la nueva referencia. No copia la referencia ni nada asi
	//if(confirm("¿Está seguro de cambiar la referencia de la planificación?\nEl listado de operaciones actual se perderá")){
		document.forms[0].frm_referencia.value=id;	
		document.forms[0].submit();
	//}
}
function confirmPlanificacion(id){ //esta funcion (apesar de su nombre) hace submit para seleccionar el nuevo cliente. No crea plani ni nada asi
	document.forms[0].frm_cliente.value=id;	
}
function guarda(){
	if(validar(document.forms[0])){
		document.forms[0].act_guardar.value="1";
		document.forms[0].submit();
	}
}
function editarOperacion(id){
	window.open('pl_operacionEdit.php?idp=<?=$plani->id_planificacion?>&ido='+id,'','height=250,width=680,status=no,toolbar=no,menubar=no,location=no');	
}
function accionEstudio(f){
	/*
	j=0;
	ojito=false;
	while(document.getElementById("rad_"+j)){
		if((document.getElementById("rad_"+j+"_2").checked)&&(document.getElementById("obs_"+j).value=="")) ojito=true;
		j++;
	}
	if(ojito){
		document.getElementById("MensajeEstudio").innerHTML="OJO! Existen una o varias respuestas \"no\" sin observación introducida";	
	}else{
		document.getElementById("MensajeEstudio").innerHTML="";	
	}*/
	if(confirm("El estudio de factibilidad se guardará antes de imprimirlo\n¿Desea continuar?")){
		document.forms[0].act_imprimirEstudio.value="1";
		document.forms[0].submit();
	}
}
function popFecha(){
	<?=JSventanaCalendario("selFecha2",getDia($plani->fecha),getMes($plani->fecha),getAnio($plani->fecha))?>	
}
function selFecha2(dia,mes,ano){
	document.forms[0].estudio_fechaEstudio.value=ano+"-"+mes+"-"+dia;
	document.forms[0].fechaMostrar.disabled=false;
	document.forms[0].fechaMostrar.value=dia+"/"+mes+"/"+ano;
	document.forms[0].fechaMostrar.disabled=true;
}
function functSubmit(){
	document.forms[0].submit();	
}
function editarOpPC(id){
	window.open('pl_operacionEditPC.php?idp=<?=$plani->id_planificacion?>&ido='+id,'','height=250,width=680,status=no,toolbar=no,menubar=no,location=no');	
}
function expActiv(){
	if(confirm("La planificación deberá guardarse. ¿Desea continuar?")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_exportar.value="exportarActividades";
		document.forms[0].submit();
	}	
}
function expHoja(){
	if(confirm("La planificación deberá guardarse. ¿Desea continuar?")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_exportar.value="exportarHojaDeRuta";
		document.forms[0].submit();
	}
}
function expPlan(){
	if(confirm("La planificación deberá guardarse. ¿Desea continuar?")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_exportar.value="exportarPlanDeControl";
		document.forms[0].submit();
	}
}
function expAMFE(){
	if(confirm("La planificación deberá guardarse. ¿Desea continuar?")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_exportar.value="exportarAMFE";
		document.forms[0].submit();
	}
}
function aplicarCambios(){
	if(confirm("La planificación deberá guardarse. ¿Desea continuar?")){
		document.forms[0].act_aplicarCambios.value="1";	
		document.forms[0].act_guardar.value="1";
		document.forms[0].submit();	
	}
}
function exportarEstudio(){
	if(confirm("La planificación deberá guardarse. ¿Desea continuar?")){
		document.forms[0].act_guardar.value="1";
		document.forms[0].act_exportar.value="exportarEstudio";
		document.forms[0].submit();
	}	
	
}
function selFechaAprob(){
	<?=JSventanaCalendario("selFechaAprob2",getDia($plani->fecha_aprobacion),getMes($plani->fecha_aprobacion),getAnio($plani->fecha_aprobacion))?>	
}
function selFechaAprob2(dia,mes,ano){
	document.forms[0].frm_fecha_aprobacion_ver.disabled=false;
	document.forms[0].frm_fecha_aprobacion_ver.value=dia+"/"+mes+"/"+ano;
	document.forms[0].frm_fecha_aprobacion_ver.disabled=true;	
	document.forms[0].frm_fecha_aprobacion.value=ano+"-"+mes+"-"+dia;
}
function editarModo(p,o,m){
	window.open("pl_modoEdit.php?idp="+p+"&ido="+o+"&idm="+m+"&soloModo=1","","width=1,height=1,status=no,toolbar=no,menubar=no,location=no");
		
}

</script>


<table border=0 width=100%>
	<tr>
		<td align="right">
			<?if($_GET["id"]!=""){?>
				<input type="button" class="Boton" value="  Agregar Componentes  "		onClick="agrComps()">
				<input type="button" class="Boton" value="  Agregar Operaciones  "		onClick="agrOper()">
				<input type="button" class="Boton" value="  Agregar Actividades  "		onClick="agrActiv()">
				<?if($us_rol>=2){?>
				<input type="button" class="Boton" value="  Aplicar cambios a ref.  "		onClick="aplicarCambios()">
				<?}?>
				<input type="button" class="Boton" value="  Eliminar  " 				onClick="eliminar()">
			<?}?>
			<!--<input type="button" class="Boton" value="  Guardar y Salir  " 			onClick="guardarSalir()">-->
			<input type="button" class="Boton" value="  Guardar  " 			onClick="guarda()">
			<input type="button" class="Boton" value="  Salir  "          			onClick="window.location='<?=obtenerRedir($_GET["volver"])?>'">
		</td>
	</tr>
	<tr><td class="spacer4">&nbsp;</td></tr>
</table>


	
<table width="100%" border="0" cellspacing="1" cellpadding="0">
	

	<form method=POST name="formulario" id="fPrincipal">
		<?if($_POST["p"]=="1"){?>
		<input type="hidden" name="act_guardarEstudio"			value="1">
		<?}?>
		<input type="hidden" name="txtMiga" value="<?=$txtMiga?>">
		<input type="hidden" name="comprobarSubmit" 			value="1">
		<input type="hidden" name="p" 							value="<?=$_POST["p"]?>">
		<input type="hidden" name="pAnt" 						value="<?=$_POST["p"]?>">
		<input type="hidden" name="act_eliminar" 				value="0">
		<input type="hidden" name="act_eliminarActividad" 		value="0">
		<input type="hidden" name="act_eliminarRelacion" 		value="0">
		<input type="hidden" name="act_agregarActividades" 		value="0">
		<input type="hidden" name="act_aplicarCambios" 		value="">
		<input type="hidden" name="act_agregarOperacion" 		value="">
		<input type="hidden" name="act_agregarComponente" 		value="">
		<input type="hidden" name="act_subirActividad" 			value="0">
		<input type="hidden" name="act_bajarActividad" 			value="0">
		<input type="hidden" name="act_subirRelacion" 			value="0">
		<input type="hidden" name="act_bajarRelacion" 			value="0">
		<input type="hidden" name="act_cerrarActividad" 		value="0">
		<input type="hidden" name="act_abrirActividad" 			value="0">
		<input type="hidden" name="act_cambioRef" 				value="">
		<input type="hidden" name="act_guardar" 				value="0">
		<input type="hidden" name="act_exportar" 				value="">
		<input type="hidden" name="act_guardarActividades" 		value="0">
		<input type="hidden" name="act_salir" 					value="0">
		<input type="hidden" name="fechaCerrado" 				value="">
		<input type="hidden" name="_actividades" 				value=''>
		<input type="hidden" name="_relaciones" 				value=''>
		<input type="hidden" name="_comp" 						value=''>
		<input type="hidden" name="_datosActividad"				value=''>
		<input type="hidden" name="_datosRelacion"				value=''>
		<input type="hidden" name="ordenPosicion"				value=''>
		<input type="hidden" name="estado" 						value="<?=$plani->cerrado?>">
		<input type="hidden" name="todos_relaciones" id="todos_relaciones"			value='<?=serialize_esp($plani->relaciones)?>'>	
		<input type="hidden" name="todos_estudio" 				value='<?=serialize_esp($plani->estudio)?>'>	
		<input type="hidden" name="todos_actividades" 			value='<?=serialize_esp($plani->actividades)?>'>
		<input type="hidden" name="scrollPosicion" id="scrollPosicion"  value="0">
		<input type="hidden" name="act_imprimirEstudio" 				value="">
		<input type="hidden" name="prototipo_guardar" value="<?=$plani->prototipo?>">
		<input type="hidden" name="preserie_guardar" value="<?=$plani->preserie?>">
		<input type="hidden" name="serie_guardar" value="<?=$plani->serie?>">
		<input type="hidden" name="equipo_guardar" value="<?=$plani->equipo?>">
		<input type="hidden" name="fecha_aprobacion_guardar" value="<?=$plani->fecha_aprobacion?>">
		
		
	  <tr>
	    <td align="center" class="Tit"><span class="fBlanco">DATOS DE LA PLANIFICACIÓN</span></td>
	  </tr>
	  <tr>
	    <td align="center" class="Caja">
	    	<table width="99%" border="0" cellspacing="2" cellpadding="4">
		    	<tr><td colspan=4 class="spacer2">&nbsp;</td></tr>
		    	<tr>
		        	  <td width="5%">&nbsp;</td>
			          <td width="15%" align="left" class="TxtBold" nowrap>C&oacute;digo planificaci&oacute;n:</td>
			          <td align="left" valign=center>	
			          	<input type=text class=input name="frm_codigo" value="<?=$plani->codigo?>" size=20>
			          </td>
			          <td align="right" width="60%" class="TxtBoldNar"><?=$estadoPlanificacion?></tD>
		        </tr>
		        <tr>
		        	<td width="5%">&nbsp;</td>
			        <td width="15%" align="left" class="TxtBold" nowrap>Fecha planificaci&oacute;n:</td>
			        <td align="left" class="TxtAzul">
			        	<?$fechaMostrar=muestraFecha($plani->fecha)==""?date("Y-m-d"):$plani->fecha;?>
			        	<input type="hidden" name="frm_fecha2" value="<?=$fechaMostrar?>">
			          	<input name="frm_fecha"  type="text" class="input" size="8" VALUE="<?=muestraFecha($fechaMostrar)?>" maxlength="10" disabled>
			          	<a href="#" onClick="<?=JSventanaCalendario("selFecha",getDia($plani->fecha),getMes($plani->fecha),getAnio($plani->fecha))?>">
			          	<img  border=0 src="<?=$app_rutaWEB?>/html/img/calendar.gif">
			        </td>
			    </tr>
		        <tr>
		        	<td width="5%">&nbsp;</td>
					<td width="15%" align="left" class="TxtBold" nowrap>Cliente:</td>
					<td align="left" width="0%">
					<select name="frm_cliente" class="input">
						<?
						$res = mysql_query("SELECT id_cliente,nombre FROM me_clientes");
						if($row=mysql_fetch_array($res)) {
							echo "<option value=\"\" selected>- Seleccione cliente -";
							do{
								$sel = $row["id_cliente"]==$plani->id_cliente ? "selected" : "" ;
								echo "<option value='".$row["id_cliente"]."' $sel>";
								echo $row["nombre"];
								echo "</option>"; 
							}while($row=mysql_fetch_array($res));
					  	}
					  	else echo "<option value=\"\">- No hay clientes -</option>";
						?>
					</select>
					</td><td width=60% align=left>
					<img onClick="busca('C');" src="<?=$app_rutaWEB?>/html/img/lupa.gif" alt="buscar cliente" style='cursor: pointer'>
					</td>
		        </tr>
		        <tr>
		        	  <td width="5%">&nbsp;</td>
			          <td width="15%" align="left" class="TxtBold" nowrap>Referencia:</td>
			          <td align="left" valign=center>	
			          	<?
			          	if($p=="2") {}//echo "this.form.submit()";
			          	?>	   
			          	<select name="frm_referencia" class="input" onChange="cambioRef(this.value)" <?=($_GET["id"]!=""?"disabled":"")?>>
			          		<?
			          		$res = mysql_query("SELECT id_referencia,nombre FROM me_referencias");
			          		if($row=mysql_fetch_array($res)) {
			          			echo "<option value=\"\" selected>- Seleccione referencia -";
			          			do{
			          				$sel = $row["id_referencia"]==$plani->id_referencia ? "selected" : "" ;
			          				echo "<option value='".$row["id_referencia"]."' $sel>".$row["nombre"]."</option>"; 
			          			}while($row=mysql_fetch_array($res));
				          	}
				          	else echo "<option value=\"\">- No hay referencias -</option>";
			          		?>
			          	</select>
			          	<?=($_GET["id"]!=""?"<input type='hidden' name='frm_referencia' value='".$plani->id_referencia."'>":"")?>       	
			          	
			          	</td><td align=left>
			          	<?if($_GET["id"]==""){?>
			          	<img onClick="busca('R');" src="<?=$app_rutaWEB?>/html/img/lupa.gif" alt="buscar referencia"  style='cursor: pointer'>
			          	<?}?>
			          </td>
		        </tr>
		        <tr><td colspan=2 class="spacer4">&nbsp;</td></tr>
		  	</table>
	    </td>
	  </tr>
	  <tr><td colspan=2 class="spacer10">&nbsp;</td></tr>
</table>
	

<?
if($_GET["id"]!=""){



$p=new Pest($_POST["p"]);
$p->add("Actividades","#","onClick=\"cambioPestanya('".$_POST["p"]."','0')\"");
$p->add("Estudio de factibilidad","#","onClick=\"cambioPestanya('".$_POST["p"]."','1')\"");
$p->add("Hoja de ruta","#","onClick=\"cambioPestanya('".$_POST["p"]."','2')\"");
$p->add("AMFE","#","onClick=\"cambioPestanya('".$_POST["p"]."','3')\"");
$p->add("Plan de control","#","onClick=\"cambioPestanya('".$_POST["p"]."','4')\"");
$p->pintar();
?>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td class="Caja" align=center valign=top>
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td class="spacer8"><BR>&nbsp;</td>
						</tr>
						<tr><td width=100% align=center>
 
<?
switch($_POST["p"]){
	
	case "1": 
	
		/***************************/
		/* Estudio de factibilidad */	
		/***************************/
		
		$sql="SELECT orden,pregunta FROM pl_estudio_preguntas ep ".
			 "WHERE id_planificacion=".$plani->id_planificacion." ORDER BY orden asc";
			 
		$res=mysql_query($sql);
		$cuantas=mysql_num_rows($res);
		if($row=mysql_fetch_row($res)){
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla"><caption>Estudio de factibilidad</caption>
				<?
				$i=0;	
				do {
					?>
					<tr >
			      <td align="left" width=40% class="Fila1"  valign=center>
			      	<span class="TxtBold">&nbsp;<?=$i+1?>.</span>&nbsp;&nbsp;<?=$row[1]?>
			      </td>
			      <td class="Fila" align="center" nowrap  valign=center>
			      <?
					$resp=$plani->estudio["resp"][$row[0]];
			      ?>
			      	<input type=radio name="estudio_rad_<?=$row[0]?>" id="rad_<?=$i?>_1" value="1" <?=($resp=="1"?"checked":"")?>>Sí<?printEspacios(4)?>	
			      	<input type=radio name="estudio_rad_<?=$row[0]?>" id="rad_<?=$i?>_2" value="0" <?=($resp=="0"?"checked":"")?>>No<?printEspacios(4)?>	
			      	<input type=radio name="estudio_rad_<?=$row[0]?>" id="rad_<?=$i?>_3" value="2" <?=($resp=="2"?"checked":"")?>>No Procede<?printEspacios(2)?>
			      </td>
			      <td class="Fila" align="left" width=35%>
			      	<table  align=center>
			      		<tr>
			      			<td class="TxtBold" valign=top>Observaciones:</td>
					        <td>
					      	  <textarea name="estudio_obs_<?=$row[0]?>" id="obs_<?=$i?>" cols="50" class="input"><?=$plani->estudio["obs"][$row[0]];?></textarea>
					        </td>
					    </tr>
					</table>
			      </td>    
			    </tr>
				<?
				$i++;			
				}while($row=mysql_fetch_row($res));
				?>
				</table>
				<br>
				<?
				$sql="SELECT * FROM pl_estudios WHERE id_planificacion=".$plani->id_planificacion;
				$res=mysql_query($sql);
				$row=mysql_fetch_array($res);
				?>
				<table width="95%" border="0" cellpadding="2" cellspacing="1" >
					<tr>
						<td align="left" width=15% class="TxtBold" nowrap>Fecha del estudio:</td>
						<td align="left" class="TxtBold" colspan=3>
							<?
							$hoy=date("d-m-Y");
							if(muestraFecha($plani->estudio["fecha"])=="") $plani->estudio["fecha"]=$hoy;
							?>
							<input type="text" name="fechaMostrar" value="<?=muestraFecha($plani->estudio["fecha"])?>" class="input" size=8 maxlength="10" disabled>
							<input type="hidden" name="estudio_fechaEstudio" value="<?=fechaBD($plani->estudio["fecha"])?>">
							<a href="#" onClick="<?=JSventanaCalendario("selFecha2",getDia($plani->estudio["fecha"]),getMes($plani->estudio["fecha"]),getAnio($plani->estudio["fecha"]))?>">
							<img  border=0 src="<?=$app_rutaWEB?>/html/img/calendar.gif">
						</td>
					</tr>
					<tr>
						<td align="left" width=15% class="TxtBold" nowrap>Decisión final:</td>
						<td align="left" class="Txt" width=15%>
							<input type="radio" name="estudio_decision" value="1" onClick="//accionEstudio(this.form)" 
							<?=($plani->estudio["decision"]=="1"?"checked":"")?>>Factible
							<input type="radio" name="estudio_decision" value="0" <?=($plani->estudio["decision"]=="0"?"checked":"")?>>No factible
							
						</td>
						<td align="left" class="Txt" colspan=2>
							<div id="MensajeEstudio" class="TxtBoldNar"></div>
						</td>
					</tr>
					<tr>
						<td align="left" width=15% class="TxtBold" valign="top" nowrap>Observaciones:</td>
						<td align="left" class="TxtBold" colspan=2>
							<textarea name="estudio_observaciones" cols=80 rows=6 class="input"><?=$plani->estudio["observaciones"]?></textarea>
						</td>
						<td align=right>
						<!--<input type="button" onClick="accionEstudio(this.form)" value="Imprimir Estudio" class="Boton">-->
						<br><input type="button" onClick="exportarEstudio()" value="Exportar Estudio" class="Boton">
						
						</td>
					</tr>
			</table>
			<br>
		<?
		} else {
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Preguntas</caption>
				<tr>
					<td class="TxtBold" colspan=3 align=left>No hay preguntas</td>
				</tr>
				<tr>
					<td align="left" colspan=3 class="spacer8"><br>&nbsp;</td>
				</tr>
			</table>
			<?		 
		}
		break;
		
		
		
	case "2":
		
		/***************************/
		/*      Hoja de Ruta       */	
		/***************************/
		
		
		function pintoFila_1($ar,$pos,$fGris=false){
			global $app_rutaWEB;
			$st=" style=\"cursor:hand\" ";
			$cls=$fGris?"FilaGris":"Fila1";
			$t=explode("[::]",($fGris?$ar["c"]:$ar["o"]));
			if($pos!=0)$todo="<tr heigth=1px><td colspan=6></td></tr>";
			$todo.="<tr><td width=1% class='".$cls."' >";
			$todo.=FlechasOrden("subirOrden('".$pos."','".($fGris?'0':'1')."')","bajarOrden('".$pos."','".($fGris?'0':'1')."')")."</td>";
			if($fGris) $todo.="<td colspan=5 class='".$cls."' align=left>".$t[1]."</td></tr>";
			else{
				$m=explode("[::]",$ar["m"]);
				$oa=explode("[::]",$ar["oAlt"]);
				$todo.="<td class='".$cls."' align=left colspan=2>".$t[1]."</td>";
				$todo.="<td class='".$cls."' align=left>".($m[1]==""?"-":$m[1])."</td>";
				$todo.="<td class='".$cls."' align=left>".($oa[1]==""?"-":$oa[1])."</td>";
				$todo.='<td align="center" valign="top" class=FilaGris nowrap width=1%>';
			    $todo.=' <img onClick="relacionEliminar(\''.$pos.'\')" src="'.$app_rutaWEB.'/html/img/papelera.gif" alt="eliminar" width="11" height="11" '.$st.'>';
			   	$todo.=' <img onClick=\'editarDatosRelacion("'.$pos.'","'.str_replace("\"","\\\"",serialize_esp($ar)).'");\' ';
			    $todo.='src="'.$app_rutaWEB.'/html/img/editar.gif" alt="modificar" width=12 '.$st.'>';
			    $todo.='</td></tr>';
			}			
			return $todo;
		}
		function pintoFila_2($ar,$pos){
			global $app_rutaWEB;
			$x=pintoFila_1($ar,$pos,true);
			if($ar["o"]!="") $x.=pintoFila_3($ar,$pos);
			return $x;
		}
		function pintoFila_3($ar,$pos){
			global $app_rutaWEB;
			$st=" style=\"cursor:hand\" ";
			$t=explode("[::]",$ar["o"]);
			$t2=explode("[::]",$ar["m"]);
			$t3=explode("[::]",$ar["oAlt"]);
			$todo.="<td class=FilaGris>&nbsp</td>";
			//$todo.="<td class=FilaGris>&nbsp</td>";
			$todo.="<td  align=left width=1% class=FilaGris>";
			$todo.=FlechasOrden("subirOrden('".$pos."','1')","bajarOrden('".$pos."','1')")."</td>";
			$todo.="<td width=40% class=FilaGris align=left><img src=\"".$app_rutaWEB."/html/img/corner.gif\">&nbsp;".$t[1]."</td>";			
			$todo.="<td width=10% class=FilaGris align=left>".($t2[1]==""?"-":$t2[1])."&nbsp;</td>";
			$todo.="<td width=10% class=FilaGris align=left>".($t3[1]==""?"-":$t3[1])."&nbsp;</td>";
			$todo.='<td align="center" valign="top" class=FilaGris nowrap width=1%>';
		    $todo.=' <img onClick="relacionEliminar(\''.$pos.'\')" src="'.$app_rutaWEB.'/html/img/papelera.gif" alt="eliminar" width="11" height="11" '.$st.'>';
		    $todo.=' <img '.$st.' onClick=\'editarDatosRelacion("'.$pos.'","'.str_replace("\"","\\\"",serialize_esp($ar)).'");\' src="'.$app_rutaWEB.'/html/img/editar.gif" alt="modificar" width="12" >';
		    $todo.='</td></tr>';
			return $todo;
		}
		$cAnt=-9999;
		$i=0;
		$compAnt="·%!";
		$i=0;
		$cnt=0;
		$todo="";
		foreach($plani->relaciones as $r){
			if(strpos($r["idTipo"],"C:")===false) $todo.= pintoFila_1($r,$i++);
			elseif($compAnt!=$r["c"])  $todo.= pintoFila_2($r,$i++);
			else $todo.= pintoFila_3($r,$i++);
			$compAnt=$r["c"];
			if($r["o"]!="")$cnt++;
		}
		if ($todo!=""){?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla" align=center>
			<caption>Hoja de Ruta</caption> 
			<tr>
				<th colspan=3 align="left">&nbsp;Componente / Operaci&oacute;n</th>
				<th align="left">&nbsp;M&aacute;quina</th>
				<th align="left">&nbsp;Operaci&oacute;n alternativa</th>
				<th align="left">&nbsp;</th>
			</tr>
			<?echo $todo;?>
			</table>
			<table width="95%">
				<tr>
					<td align="left" class="TxtBold" valign=top>N&uacute;mero de operaciones: <?=$cnt?></td>
					<td align="right" class="TxtBold"><br><input type=button onClick="expHoja()" Value="Exportar Hoja de Ruta" class=Boton></td>
				
				</tr>
				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
			</table>
			
			<?
		}else{
			?>
			<tr><td>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" align=center>
			<caption>Hoja de Ruta</caption>
			<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones asignadas</td></tr>
	        <tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
	        </table>
			<?
		}				
		break;
		
		
		
	case "3":
	
		/***************************/
		/*         AMFE            */	
		/***************************/
		
		?>
		<table width="98%" border="0" cellpadding="2" cellspacing="1" align=left>
		<caption>&nbsp;&nbsp;&nbsp;&nbsp;a.m.f.e de procesos</caption>
		<tr><td align=center>
		<?
		$fGris=true;
		$todo="";
		$todo=$plani->pintarAMFE();
		if($todo!="") {
			echo pintarCabeceraAMFE2().$todo."</table></td></tr>";
			?>
			<tr>
				<td colspan=17 width="98%">
					<table width="100%">
						<tr><td class="TxtBold" colspan=3 align=right><br><input type="button" class="Boton" onClick="expAMFE();" value="Exportar AMFE"></td></tr>
				    </table>
				</td>
			</tr>
			<?
		}else{
			?>
			<tr>
				<td colspan=17 width="98%">
					<table width="98%">
						<tr><td class="TxtBold" colspan=3 align=left>No hay operaciones con modo de fallo asignadas a esta planificaci&oacute;n</td></tr>
				    </table>
				</td>
			</tr>
			<?			
		}
		echo "</td></tr>";
		break;
	case "4":
	
		/***************************/
		/*     Plan de Control     */	
		/***************************/
		
		?>
		<table width="95%" border="0" cellpadding="2" cellspacing="1">
		<tr><td class="claseCaption" align=left>plan de control</td></tr>
		<tr>
			<td width=100% nowrap class="Txt" align=left>
			  <table class="BordesTabla" width=60%>
			    <tr><td class="spacer8">&nbsp;</td></tr>
			    <tr>
			      <td width=40% align=right>
					<b>Prototipo:&nbsp;&nbsp;<input type=text name="frm_prototipo" value="<?=$plani->prototipo?>" class=input size=30><br>
					Preserie:&nbsp;&nbsp;<input type=text name="frm_preserie" value="<?=$plani->preserie?>" class=input size=30><br>
					Serie:&nbsp;&nbsp;<input type=text name="frm_serie" value="<?=$plani->serie?>" class=input size=30><br>
					</td>
					<td width=10%>&nbsp;</td>
					<td width=50% nowrap class="Txt" align=left>
					<b>Equipo de proyecto:&nbsp;&nbsp;&nbsp;<input type=text name="frm_equipo" value="<?=$plani->equipo?>" class=input size=30><br>
					Fecha aprobaci&oacute;n:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type=text name="frm_fecha_aprobacion_ver" value="<?=muestraFecha($plani->fecha_aprobacion)?>" class=input size=8 disabled>
					<img src="<?=$app_rutaWEB?>/html/img/calendar.gif" STYLE="cursor:hand" onClick="selFechaAprob()">
					<input type=hidden name="frm_fecha_aprobacion" value="<?=$plani->fecha_aprobacion?>">
					<br>
				  </td>
				  
				</tr>
				<tr><td class="spacer8">&nbsp;</td></tr>
			  </table><br>
			</td>
		</tr>
		<?
		$todo=$plani->generarPlanDeControl();
		if($todo!=""){
			echo pintarCabeceraPControl().$todo."</table>";
			?>
			<table width=95%>
				<tr><td class="spacer8"></td></tr>
				<tr>
					<td class="Txt" align=right width=90%>
						&nbsp;
					</td>
					<td align=right valign=center>
					<input type=button onClick='expPlan()' class=Boton value='Exportar Plan de Control'>
					</td>
				</tr>
			</table>
			<br>
			<?
		}else echo "<tr><td class=TxtBold align=LEft>No hay operaciones relacionadas en la planificacion.</td></tr>";
		break;
		
	default: 
	
		/*****************************/
		/*        Actividades        */
		/*****************************/
		
		$cu=count($plani->actividades);
		if($cu>0) {			
			?>	
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="BordesTabla">
				<caption>Planificaci&oacute;n de Actividades</caption>
				
				<tr>
				<th width="1%" align="left" nowRAP>&nbsp;</th>
			    <th width="30%" align="left" nowRAP>&nbsp;Nombre </th>
			    <th width="10%" align="left" nowRAP>&nbsp;Responsable </th>
			    <th width="0%" align="center" nowRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plazo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			    <th width="0%" align="center" nowRAP>&nbsp;Fecha cerrado </th>
			    <th width="40%" align="left" >&nbsp;Observaciones </th>
			    <th width="5%" align="left" nowRAP>&nbsp;</th>
			  </tr>	
			<?
			$jj=0;	
			$st=" style=\"cursor:hand\" ";
			foreach($plani->actividades as $act){				
				$hoy=str_replace("-","",fechaBd(muestraFecha(date("Y-m-d"))));
				$fCerr=str_replace("-","",fechaBd(muestraFecha($act["fecha_cerrado"])));
				$fPlazo=str_replace("-","",fechaBd(muestraFecha($act["plazo"])));
				$cerrado=$act["cerrado"]=="1"?true:false;
				$clase="Fila1";
				if($cerrado){
					if($fCerr>$fPlazo && $fPlazo!="" && $fCerr!="") {$clase="FilaRoja";$tit="Actividad cerrada con retraso";}
					elseif($fCerr<=$fPlazo && $fPlazo!="" && $fCerr!="") {$clase="FilaVerde";$tit="Actividad cerrada dentro del plazo";}
					else {$clase="FilaGris";$tit="Actividad cerrada";}
				}else{
					if($fPlazo!="" && $fPlazo<$hoy) {
						$clase="FilaAmarilla";
						$tit="Actividad abierta retrasada";
					}else $tit="Actividad abierta";
				}
				?>
				<tr>
					<td align="left" valign="top" class="Fila1" align="center">
						<?=FlechasOrden("subirOrdenAct('".$jj."')","bajarOrdenAct('".$jj."')")?>
					</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>"><?=$act["anombre"]?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>"><?=Responsable::getNombre($act["responsable"])?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>">&nbsp;<?=muestraFecha($act["plazo"])?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" align="center" 
						title="<?=$tit?>">&nbsp;&nbsp;&nbsp;&nbsp;<?=muestraFecha($act["fecha_cerrado"])?>&nbsp;</td>
					<td align="left" valign="top" class="<?=$clase?>" title="<?=$tit?>"><?=$act["observaciones"]?>&nbsp;</td>
					<td align="center" valign="top" class="Fila1" nowrap>
						<img onClick="actividadEliminar('<?=$jj?>')" src="<?=$app_rutaWEB?>/html/img/papelera.gif" alt="eliminar" width="11" height="11" <?=$st?>>
						<img onClick='editarDatosActividad("<?=$jj?>","<?=str_replace("\"","\\\"",serialize_esp($act))?>");' 
						src="<?=$app_rutaWEB?>/html/img/editar.gif" alt="modificar" width="12" <?=$st?>>
						<img onClick="infActividades('<?=$act["cerrado"]?>','<?=$jj?>')" src="<?=$app_rutaWEB?>/html/img/cerrar.JPG" 
						alt="<?=($act["cerrado"]?"activar":"cerrar")?>" width="12" <?=$st?>>
					</td>
			    </tr>
				<?			
				$jj++;
			}
			?>
			</table>
			<table width="95%">
	      		<tr>
	      			<td nowRap align="left" class="TxtBold">N&uacute;mero de actividades: <?=$cu?> </td>
	      			<td width=100% align=right class="Txt">
	      			<?=pintaLeyenda()?>
	      			</td>
	      		</tr>
	      		<tr><td width=100% align=right colspan=2>
	      		<input type="button" class="Boton" onClick="expActiv();" value="Exportar actividades">
				</td>
				<tr><td align="left" class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?
		}else{
			?>
			<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
				<caption>Actividades</caption>
				<tr><td class="TxtBold" colspan=3 align=left>No hay actividades</td></tr>
				<tr><td align="left" colspan=3  class="spacer8"><br>&nbsp;</td></tr>
			</table>
			<?		 
		}
}

}else{ // en caso de que no esté guardada la planificacion
?>
<table width="95%" border="0" cellpadding="2" cellspacing="1" class="">
	<tr>
		<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
	</tr>
	<tr>
		<td class="TxtBold" colspan=3 align=center>Guarde la planificación antes de editarla</td>
	</tr>
	<tr>
		<td align="left" colspan=3  class="spacer8"><br>&nbsp;</td>
	</tr>
</table>


<?
}
?>


	</td></tr>
</table>
</td></tr></table>

</form>


<br>
<form name="f2" method="POST"  target="ventanaActividad" >							
<input type="hidden" name="datosActividad" value=''>
<input type="hidden" name="nombreActividad" value="">
<input type="hidden" name="datosRelacion" value=''>
</form>

<form name="f3" method="POST"  target="informeActividades" >							
<input type="hidden" name="actividades" value='<?=serialize_esp($plani->actividades)?>'>
</form>
<?
if($_POST["act_exportar"]!="" && $JSEjecutar=="") {
	//$JSEjecutar="window.open('".$_POST["act_exportar"].".php?idp=".$plani->id_planificacion."','','')";
	//Header("Location: pl_planificaciones_edit.php?id=".$plani->id_planificacion."&exportar=".$_POST["act_exportar"]);
	//Header("Redirect: www.google.es");
	//Header("Location: ".$_POST["act_exportar"].".php?idp=".$plani->id_planificacion);
	//echo "<script>document.body.onload=window.location='".$_POST["act_exportar"].".php?idp=".$plani->id_planificacion."';</script>";
	?>
	<script>
	if(navigator.appName=="Netscape") window.open('<?=$_POST["act_exportar"]?>.php?idp=<?=$plani->id_planificacion?>','','');
	else document.body.onload= new Function ("window.location='<?=$_POST["act_exportar"]?>.php?idp=<?=$plani->id_planificacion?>';");
		
	</script>
	<?
}

$centro=ob_get_contents();
ob_end_clean();
$tpl->assign("{CONTENIDOCENTRAL}",$centro); 
$tpl->assign("{MIGADEPAN}",$miga_pan); 
$tpl->assign("{BOTONESTEMPLATE}",botonesTemplate($us_rol)); 
$tpl->assign("{USUARIO}",$us_nombre." ".$us_apellidos);  
$tpl->parse(CONTENT, main);
$tpl->FastPrint(CONTENT);


?>


