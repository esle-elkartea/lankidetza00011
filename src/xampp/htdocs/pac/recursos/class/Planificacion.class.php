<?
include_once "POperacion.class.php";
include_once "PComponente.class.php";
class Planificacion {
	
	var $id_planificacion;
	var $id_cliente="";
	var $id_referencia="";
	var $codigo="";
	var $fecha="";
	var $cerrado="0";
	var $prototipo="";
	var $serie="";
	var $preserie="";
	var $equipo="";
	var $fecha_aprobacion="";
	var $nuevo;
	var $actividades=array();
	var $relaciones=array();
	var $estudio=array();
	
	
	/*****************************/
	/* Carga de la planificación */
	/*****************************/
	
	function Planificacion ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_planificacion) is null,1,Max(id_planificacion)+1) FROM planificaciones ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_planificacion=$row[0];
			$this->nuevo=true;		
		}else{		
			$sql="SELECT * FROM planificaciones WHERE id_planificacion=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_planificacion=$id;	
			$this->id_cliente=$row["id_cliente"];
			$this->id_referencia=$row["id_referencia"];
			$this->codigo=$row["codigo"];
			$this->fecha=$row["fecha"];
			$this->cerrado=$row["cerrado"];
			$this->prototipo=$row["prototipo"];
			$this->preserie=$row["preserie"];
			$this->serie=$row["serie"];
			$this->equipo=$row["equipo"];
			$this->fecha_aprobacion=$row["fecha_aprobacion"];
			$this->nuevo=false;
		}
		$this->cargaActividades();
		
	}
	function cargaEstudio(){
		$this->estudio=array();
		$sql="SELECT respuesta,observaciones,orden FROM pl_estudio_preguntas WHERE id_planificacion=".$this->id_planificacion;
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res)){
			$this->estudio["resp"][$row["orden"]]=$row["respuesta"];
			$this->estudio["obs"][$row["orden"]]=$row["observaciones"];
		}
		$sql="SELECT fecha,decision,observaciones FROM pl_estudios WHERE id_planificacion=".$this->id_planificacion;
		$res=mysql_query($sql);
		if($row=mysql_fetch_array($res)){
			$this->estudio["fecha"]=$row["fecha"];
			$this->estudio["decision"]=$row["decision"];
			$this->estudio["observaciones"]=$row["observaciones"];
		}
	}
	function cargaActividades(){
		if($this->nuevo){ 	
			$sql="".
			"SELECT a.id_actividad,a.nombre,a.id_categoria,c.nombre,'','','','0','' FROM ad_actividades a ".
			"LEFT JOIN ad_categorias c ON a.id_categoria=c.id_categoria ".
			"WHERE principal=1 ORDER BY orden ";			
		}else{
			$sql="".
			"SELECT a.id_actividad,a.nombre,a.id_categoria,c.nombre,pa.plazo,pa.id_responsable,pa.observaciones,pa.cerrado,pa.fecha_cerrado ".
			"FROM pl_planificacion_actividad pa ".
			"LEFT JOIN ad_actividades a ON a.id_actividad=pa.id_actividad ".
			"LEFT JOIN ad_categorias c ON c.id_categoria=a.id_categoria ".
			"WHERE id_planificacion=".$this->id_planificacion." ORDER BY pa.orden ";
		}
		$res=mysql_query($sql);
		$i=0;
		while($row=@mysql_fetch_row($res)) {
			$this->actividades[$i]["ida"]=$row[0]."";
			$this->actividades[$i]["anombre"]=convTxt($row[1])."";
			$this->actividades[$i]["idc"]=$row[2]."";
			$this->actividades[$i]["cnombre"]=convTxt($row[3])."";
			$this->actividades[$i]["plazo"]=$row[4]."";	
			$this->actividades[$i]["responsable"]=convTxt($row[5])."";	
			$this->actividades[$i]["observaciones"]=convTxt($row[6])."";	
			$this->actividades[$i]["cerrado"]=$row[7]."";
			$this->actividades[$i++]["fecha_cerrado"]=$row[8]."";				
		}
	}
	
	function cargaRelaciones($id=""){
		$elId=$id==""?$this->id_planificacion:$id;
		$sql="";
		$this->relaciones=array();
		if($this->nuevo || $id!=""){
			if($elId!=""){
				$sql="".
				"SELECT tipo,nombreC,nombreO,maquina,CONCAT(o.id_operacion,'[::]',o.nombre) AS operacionAlternativa FROM ".
				"(".
				"  SELECT o.id_opAlt,CONCAT(m.id_maquina,'[::]',m.nombre) AS maquina, ".
				"  CONCAT(tipo,':',c.id_componente) AS tipo,rr.orden AS orden1,co.orden AS orden2, ".
				"  CONCAT(c.id_componente,'[::]',c.nombre,'[::]',c.codigo)AS nombreC, ".
				"  CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) AS nombreO ".
				"  FROM me_referencia_relacion rr ".
				"  LEFT JOIN me_componentes c ON c.id_componente=rr.id_relacion ".
				"  LEFT JOIN me_componente_operacion co ON co.id_componente=c.id_componente ".
				"  LEFT JOIN me_operaciones o ON o.id_operacion=co.id_operacion ".
				"  LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina ".
				"  WHERE tipo='C' AND rr.id_referencia=".$elId." ".
				"  UNION ALL ".
				"  SELECT o.id_opAlt,CONCAT(m.id_maquina,'[::]',m.nombre) AS maquina, ".
				"  CONCAT(tipo,':',o.id_operacion) AS tipo,rr.orden AS orden1,'','', ".
				"  CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) AS nombreO ".
				"  FROM me_referencia_relacion rr ".
				"  LEFT JOIN me_operaciones o ON rr.id_relacion=o.id_operacion ".
				"  LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina ".
				"  WHERE tipo='O' AND rr.id_referencia=".$elId." ".
				") AS todo ".
				"LEFT JOIN me_operaciones o ON todo.id_opAlt=o.id_operacion ".
				"ORDER BY todo.orden1,todo.orden2 ";
			}
		}else{
			$sql="".
			"SELECT tipo,nombreC,nombreO,maquina,CONCAT(o.id_operacion,'[::]',o.nombre) AS operacionAlternativa FROM ".
			"(".
			"  SELECT o.id_opAlt,CONCAT(m.id_maquina,'[::]',m.nombre) AS maquina, ".
			"  CONCAT(tipo,':',c.id_componente) AS tipo,rr.orden AS orden1,co.orden AS orden2, ".
			"  CONCAT(c.id_componente,'[::]',c.nombre,'[::]',c.codigo)AS nombreC, ".
			"  CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) AS nombreO ".
			"  FROM pl_planificacion_relacion rr ".
			"  LEFT JOIN pl_componentes c ON c.id_componente=rr.id_relacion AND c.id_planificacion=$this->id_planificacion ".
			"  LEFT JOIN pl_componente_operacion co ON co.id_componente=c.id_componente  AND co.id_planificacion=$this->id_planificacion".
			"  LEFT JOIN pl_operaciones o ON o.id_operacion=co.id_operacion  AND o.id_planificacion=$this->id_planificacion  ".
			"  LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina   ".
			"  WHERE tipo='C' AND rr.id_planificacion=".$elId." ".
			"  UNION ALL ".
			"  SELECT o.id_opAlt,CONCAT(m.id_maquina,'[::]',m.nombre) AS maquina, ".
			"  CONCAT(tipo,':',o.id_operacion) AS tipo,rr.orden AS orden1,'','', ".
			"  CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) AS nombreO ".
			"  FROM pl_planificacion_relacion rr ".
			"  LEFT JOIN pl_operaciones o ON rr.id_relacion=o.id_operacion  AND o.id_planificacion=$this->id_planificacion  ".
			"  LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina ".
			"  WHERE tipo='O' AND rr.id_planificacion=".$elId." ".
			") AS todo ".
			"LEFT JOIN pl_operaciones o ON todo.id_opAlt=o.id_operacion AND o.id_planificacion=$this->id_planificacion ".
			" ORDER BY todo.orden1,todo.orden2 ";
		}
		$res=mysql_query($sql);
		$i=0;
		while($row=@mysql_fetch_row($res)){
			$this->relaciones[$i]["idTipo"]=$row[0]."";
			$this->relaciones[$i]["c"]=convTxt($row[1])."";
			$this->relaciones[$i]["o"]=convTxt($row[2])."";
			$this->relaciones[$i]["m"]=convTxt($row[3])."";
			$this->relaciones[$i++]["oAlt"]=convTxt($row[4])."";
		}
	}
	function cargaRelacionesMaestros(){
		$sql="";
		$elId=$this->id_referencia;
		$this->relaciones=array();
		$sql="".
		"SELECT tipo,nombreC,nombreO,maquina,CONCAT(o.id_operacion,'[::]',o.nombre) AS operacionAlternativa FROM ".
		"(".
		"  SELECT o.id_opAlt,CONCAT(m.id_maquina,'[::]',m.nombre) AS maquina, ".
		"  CONCAT(tipo,':',c.id_componente) AS tipo,rr.orden AS orden1,co.orden AS orden2, ".
		"  CONCAT(c.id_componente,'[::]',c.nombre,'[::]',c.codigo)AS nombreC, ".
		"  CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) AS nombreO ".
		"  FROM me_referencia_relacion rr ".
		"  LEFT JOIN me_componentes c ON c.id_componente=rr.id_relacion ".
		"  LEFT JOIN me_componente_operacion co ON co.id_componente=c.id_componente ".
		"  LEFT JOIN me_operaciones o ON o.id_operacion=co.id_operacion ".
		"  LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina ".
		"  WHERE tipo='C' AND rr.id_referencia=".$elId." ".
		"  UNION ALL ".
		"  SELECT o.id_opAlt,CONCAT(m.id_maquina,'[::]',m.nombre) AS maquina, ".
		"  CONCAT(tipo,':',o.id_operacion) AS tipo,rr.orden AS orden1,'','', ".
		"  CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) AS nombreO ".
		"  FROM me_referencia_relacion rr ".
		"  LEFT JOIN me_operaciones o ON rr.id_relacion=o.id_operacion ".
		"  LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina ".
		"  WHERE tipo='O' AND rr.id_referencia=".$elId." ".
		") AS todo ".
		"LEFT JOIN me_operaciones o ON todo.id_opAlt=o.id_operacion ".
		"ORDER BY todo.orden1,todo.orden2 ";
		$res=mysql_query($sql);
		$i=0;
		while($row=@mysql_fetch_row($res)){
			$this->relaciones[$i]["idTipo"]=$row[0]."";
			$this->relaciones[$i]["c"]=$row[1]."";
			$this->relaciones[$i]["o"]=$row[2]."";
			$this->relaciones[$i]["m"]=$row[3]."";
			$this->relaciones[$i++]["oAlt"]=$row[4]."";
		}
	}
	
	
	
	
	/************************************/
	/* Guardar o eliminar planificación */
	/************************************/
	
	function guardar($comprueba=true) {	
		if($comprueba){
			if($this->comprobarCodigo()){
				if($this->nuevo){
					$sql="INSERT INTO planificaciones ".
					"(id_planificacion,id_cliente,id_referencia,codigo,fecha,cerrado,prototipo,preserie,serie,equipo,fecha_aprobacion) VALUES ".
					"('$this->id_planificacion','$this->id_cliente','$this->id_referencia','$this->codigo','$this->fecha','$this->cerrado',".
					"'$this->prototipo','$this->preserie','$this->serie','$this->equipo','".fechaBD($this->fecha_aprobacion)."')";
				}else{
					$sql="UPDATE planificaciones SET id_cliente='$this->id_cliente',id_referencia='$this->id_referencia',fecha='$this->fecha', ".
					"codigo='$this->codigo',cerrado='$this->cerrado',prototipo='$this->prototipo',preserie='$this->preserie',serie='$this->serie' ".
					",equipo='$this->equipo',fecha_aprobacion='".fechaBD($this->fecha_aprobacion)."' ".
					" WHERE id_planificacion=$this->id_planificacion";		
				}
				mysql_query($sql);
				$this->guardarActividades();
				$this->guardarRelaciones();
				if($this->nuevo) $this->copiaEstudio();
				else $this->guardaEstudio();
			}else return "alert('Existe otra planificación con el mismo código');";	
		}else{
			if($this->nuevo){
					$sql="INSERT INTO planificaciones ".
					"(id_planificacion,id_cliente,id_referencia,codigo,fecha,cerrado,prototipo,preserie,serie,equipo,fecha_aprobacion) VALUES ".
					"('$this->id_planificacion','$this->id_cliente','$this->id_referencia','$this->codigo','$this->fecha','$this->cerrado',".
					"'$this->prototipo','$this->preserie','$this->serie','$this->equipo','".fechaBD($this->fecha_aprobacion)."')";
				}else{
					$sql="UPDATE planificaciones SET id_cliente='$this->id_cliente',id_referencia='$this->id_referencia',fecha='$this->fecha', ".
					"codigo='$this->codigo',cerrado='$this->cerrado',prototipo='$this->prototipo',preserie='$this->preserie',serie='$this->serie' ".
					",equipo='$this->equipo',fecha_aprobacion='".fechaBD($this->fecha_aprobacion)."' ".
					" WHERE id_planificacion=$this->id_planificacion";		
				}
				mysql_query($sql);
				$this->guardarActividades();
				$this->guardarRelaciones();
				if($this->nuevo) $this->copiaEstudio();
				else $this->guardaEstudio();
		}
	}
	function comprobarCodigo(){
		$sql="SELECT count(*) FROM planificaciones WHERE codigo='$this->codigo' ".($this->nuevo?"":"AND id_planificacion!=$this->id_planificacion");
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		if($row[0]==0) return true;
		else return false;
	}
	function guardaEstudio(){
		if(count($this->estudio["resp"])>0 && count($this->estudio["resp"])==count($this->estudio["obs"])){
			foreach($this->estudio["resp"] as $key=>$val){
				$respuesta=$val;
				$observaciones=$this->estudio["obs"][$key];
				$sql="UPDATE pl_estudio_preguntas SET respuesta='".$respuesta."',observaciones='".str_replace("'","\\'",$observaciones)."' ".
					 "WHERE id_planificacion=".$this->id_planificacion." AND orden=".$key;
				mysql_query($sql);
			}
		}
		$sql="UPDATE pl_estudios SET fecha='".fechaBD($this->estudio["fecha"])."', decision='".$this->estudio["decision"]."', ".
			 "observaciones='".str_replace("'","\\'",$this->estudio["observaciones"])."' WHERE id_planificacion=".$this->id_planificacion ;
		mysql_query($sql);
	}
	
	function copiaEstudio(){
		$sql="INSERT INTO pl_estudio_preguntas (id_planificacion,pregunta,orden) ".
			 "(SELECT '".$this->id_planificacion."',nombre,orden FROM ad_preguntas ORDER BY orden asc)";
		$res=mysql_query($sql);
		$sql="INSERT INTO pl_estudios (id_planificacion) VALUES (".$this->id_planificacion.")";
		$res=mysql_query($sql);
	}
	function guardarActividades(){
		$this->eliminarActividades();
		$i=0;
		if(count($this->actividades)>0){
			foreach($this->actividades as $act) {
				$sql="".
				"INSERT INTO pl_planificacion_actividad (id_planificacion,id_actividad,id_responsable,plazo,observaciones,orden,cerrado,fecha_cerrado) VALUES ".
				"('".$this->id_planificacion."','".$act["ida"]."','".$act["responsable"]."',".
				"'".$act["plazo"]."','".$act["observaciones"]."','".$i++."','".$act["cerrado"]."','".$act["fecha_cerrado"]."')";
				$res=mysql_query($sql);
			}
		}
	}
	function copiarRelaciones(){
		if(count($this->relaciones)>0){
			$count=0;
			$arRelaciones=array();
			for($i=0;$i<count($this->relaciones);$i++){
				$ss=explode(":",$this->relaciones[$i]["idTipo"]);
				if($ss[0]=="O"){
					$p1=explode("[::]",$this->relaciones[$i]["oAlt"]);
					if($p1[0]!="") $op=new POperacion($this->id_planificacion,$p1[0]);
					$p1=explode("[::]",$this->relaciones[$i]["o"]);
					$op=new POperacion($this->id_planificacion,$p1[0]);					
					$arRelaciones[]=array($this->id_planificacion,$p1[0],"O",$count++);
				}elseif($ss[0]=="C"){
					// compongo el componente con sus operaciones para luego guardarlo					
					$tipoCompActual=$this->relaciones[$i]["idTipo"];
					$p0=explode("[::]",$this->relaciones[$i]["c"]);
					$p1=explode("[::]",$this->relaciones[$i]["o"]);
					if($p1[0]!="") $arRelaciones[]=array($this->id_planificacion,$p0[0],"C",$count++);
					$comp=new PComponente($this->id_planificacion,$p0[0]);
					while($this->relaciones[$i]["idTipo"]==$tipoCompActual){
						$p1=explode("[::]",$this->relaciones[$i]["o"]);
						if($p1[0]!="")	$comp->operaciones[]=$p1[0];
						$i++;
					}
					$i--;
					$comp->guardar();
				}
			}
			if(count($arRelaciones)>0){
				foreach($arRelaciones as $rel){
					$sql="INSERT INTO pl_planificacion_relacion (id_planificacion,id_relacion,tipo,orden) VALUES ".
						 "(".$rel[0].",".$rel[1].",'".$rel[2]."',".$rel[3].")";	
					$res=mysql_query($sql);
				}
			}
		}
	}
	function eliminar() {
		$tablas=array(
		"planificaciones",
		"pl_operaciones",
		"pl_componentes",
		"pl_componente_operacion",
		"pl_modos",
		"pl_modo_efecto",
		"pl_modo_causa",
		"pl_efectos",
		"pl_causas",
		"pl_planificacion_actividad",
		"pl_operacion_caracteristica",
		"pl_operacion_modo",
		"pl_planificacion_relacion",
		"pl_estudios",
		"pl_estudio_preguntas",
		"pl_caracteristicas");
		foreach($tablas as $tbl){
			$sql="DELETE FROM $tbl WHERE id_planificacion=$this->id_planificacion";
			@mysql_query($sql);	
		}
	}
	function eliminarRelaciones(){
		$tablas=array(
		"pl_componente_operacion",
		"pl_planificacion_relacion");
		foreach($tablas as $tbl){
			$sql="DELETE FROM $tbl WHERE id_planificacion=$this->id_planificacion";
			@mysql_query($sql);	
		}
	}
	function guardarRelaciones(){
		$this->eliminarRelaciones();
		if(count($this->relaciones)>0){
			$orden=0;
			for($i=0;$i<count($this->relaciones);$i++){
				if(substr($this->relaciones[$i]["idTipo"],0,1)=="C"){
					$p=explode("[::]",$this->relaciones[$i]["c"]);
					$this->comprobarQueExisteComponente($p[0]);
					$sql="INSERT INTO pl_planificacion_relacion (id_planificacion,id_relacion,tipo,orden) VALUES ".
						 "($this->id_planificacion,".$p[0].",'C',".$orden++.")";
					$res=mysql_query($sql);
					$idTipoActual=$this->relaciones[$i]["idTipo"];
					$j=0;
					do{
						$oa=explode("[::]",$this->relaciones[$i]["oAlt"]);
						$mm=explode("[::]",$this->relaciones[$i]["m"]);
						$po=explode("[::]",$this->relaciones[$i]["o"]);
						$sql="INSERT INTO pl_componente_operacion (id_planificacion,id_componente,id_operacion,orden) VALUES ".
							 "($this->id_planificacion,".$p[0].",".$po[0].",".$j++.")";
						$res=mysql_query($sql);
						$this->comprobarQueExisteOperacion($oa[0]);
						$this->comprobarQueExisteOperacion($po[0]);
						$sql="UPDATE pl_operaciones SET id_opAlt='".$oa[0]."', id_maquina='".$mm[0]."' ".
							 "WHERE id_planificacion=".$this->id_planificacion." AND id_operacion=".$po[0];
						$res=mysql_query($sql);
						$i++;
					}while($this->relaciones[$i]["idTipo"]==$idTipoActual);
					$i--;
				}else{
					$oa=explode("[::]",$this->relaciones[$i]["oAlt"]);
					$mm=explode("[::]",$this->relaciones[$i]["m"]);
					$po=explode("[::]",$this->relaciones[$i]["o"]);
					$sql="INSERT INTO pl_planificacion_relacion (id_planificacion,id_relacion,tipo,orden) VALUES ".
						 "($this->id_planificacion,".$po[0].",'O',".$orden++.")";
					$res=mysql_query($sql);
					$this->comprobarQueExisteOperacion($oa[0]);
					$this->comprobarQueExisteOperacion($po[0]);
					$sql="UPDATE pl_operaciones SET id_opAlt='".$oa[0]."', id_maquina='".$mm[0]."' ".
						 "WHERE id_planificacion=".$this->id_planificacion." AND id_operacion=".$po[0];
					$res=mysql_query($sql);
				}
			}
		}		
	}
	function comprobarQueExisteOperacion($io){
		if($io!=""){
			$sql="SELECT count(*) FROM pl_operaciones WHERE id_planificacion=$this->id_planificacion AND id_operacion=$io";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			if($row[0]==0) $op=new POperacion($this->id_planificacion,$io);	
		}
	}
	function comprobarQueExisteComponente($ic){
		if($ic!=""){
			$sql="SELECT count(*) FROM pl_componentes WHERE id_planificacion=$this->id_planificacion AND id_componente=$ic";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			if($row[0]==0){
				//echo "<br>VAMOS A COPIAR $ic en 'pl_componentes'";
				$sql="SELECT id_operacion FROM me_componente_operacion WHERE id_componente=$ic";
				$res=mysql_query($sql);
				$comp=new PComponente($this->id_planificacion,$ic);
				while($row=@mysql_fetch_row($res)){
					$comp->operaciones[]=$row[0];					
				}			
				$comp->guardar();
			}
		}
	}
	
	
		
	
	/******************************/
	/* Funciones para actividades */
	/******************************/	
	
	function isCerrado(){  //mira cuantas actividades están cerradas y si lo están todas (planificacion cerrada) devuelve -1
		$cuantos=0;
		foreach($this->actividades as $a) if($a["cerrado"]=="1") $cuantos++;	
		if($cuantos==count($this->actividades)) return "-1";
		else return $cuantos;	
	}
	function eliminarActividades(){
		if(!$this->nuevo){
			$sql="DELETE FROM pl_planificacion_actividad WHERE id_planificacion=".$this->id_planificacion;
			$res=mysql_query($sql);	
		}
	}
	function agregarActividades($arActiv){		
		$sql="SELECT a.id_actividad, a.nombre, c.id_categoria, c.nombre FROM ad_actividades a ".
		"LEFT JOIN ad_categorias c ON c.id_categoria=a.id_categoria ".
		"WHERE id_actividad IN (".implode(",",$arActiv).") ";
		$res=mysql_query($sql);
		while($row=@mysql_fetch_row($res)){
			$i=count($this->actividades);
			$this->actividades[$i]["ida"]=$row[0]."";
			$this->actividades[$i]["anombre"]=txtParaInput($row[1])."";
			$this->actividades[$i]["idc"]=$row[2]."";
			$this->actividades[$i]["cnombre"]=txtParaInput($row[3])."";
			$this->actividades[$i]["plazo"]="";	
			$this->actividades[$i]["responsable"]="";	
			$this->actividades[$i]["observaciones"]="";
			$this->actividades[$i]["fecha_cerrado"]="";
			$this->actividades[$i]["cerrado"]="0";				
		}
	}
	function quitarActividades($pos){
		$this->actividades=quitarDeArray($this->actividades,$pos);
	}
	function subirOrdenActividad($pos){
		$this->_subirNormal($pos,"actividades");		
	}
	function bajarOrdenActividad($pos){
		$this->_bajarNormal($pos,"actividades");
	}
	function ponerActividadPrimera($pos){
		$aux=$this->actividades[$pos];
		for($i=$pos;$i>0;$i--)	$this->actividades[$i]=$this->actividades[$i-1];
		$this->actividades[0]=$aux;		
	}
	function ponerActividadUltima($pos){
		$aux=$this->actividades[$pos];
		for($i=$pos;$i<count($this->actividades)-1;$i++) $this->actividades[$i]=$this->actividades[$i+1];
		$this->actividades[count($this->actividades)-1]=$aux;		
	}
	
	
	
	
	
	/**************************************************************/
	/* Funciones para las relaciones de operaciones y componentes */
	/**************************************************************/
	
	function quitarRelacion($pos){
		$this->relaciones=quitarDeArray($this->relaciones,$pos);
	}
	
	
	function agregarOperacion($ido,$idc){		
		
		if($this->_estaEnRelaciones($idc,$ido)==false){
			echo "vamos";
			$sql="".
			"SELECT operacion,maquina,CONCAT(o.id_operacion,'[::]',o.nombre) as opAlt FROM ".
			"(SELECT CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) as operacion, ".
			"CONCAT(m.id_maquina,'[::]',m.nombre,'[::]',m.codigo) as maquina,id_opAlt ".
			"FROM me_operaciones o LEFT JOIN ad_maquinas m ON m.id_maquina=o.id_maquina WHERE o.id_operacion=".$ido.") as todo ".
			"LEFT JOIN me_operaciones o ON o.id_operacion=todo.id_opAlt ";
			$res=mysql_query($sql);
			if($row=@mysql_fetch_row($res)){
				if($idc=="") $pos=count($this->relaciones);
				else{
					$sql="".
					"SELECT CONCAT(id_componente,'[::]',nombre,'[::]',codigo) FROM me_componentes ".
					"WHERE id_componente=".$idc." "; //AND id_planificacion=".$this->id_planificacion;
					$res=mysql_query($sql);
					$row2=mysql_fetch_row($res);
					$j=0;
					while(is_array($this->relaciones[$j]) && $this->relaciones[$j]["c"]!=convTxt($row2[0])) $j++;
					while(is_array($this->relaciones[$j]) && $this->relaciones[$j]["c"]==convTxt($row2[0])) $j++;
					$pos=$j;
					if($this->relaciones[$j-1]["o"]!=""){
						$aux=$this->relaciones[$pos];
						for($i=count($this->relaciones);$i>$pos;$i--)
							$this->relaciones[$i]=$this->relaciones[$i-1];
						$this->relaciones[$pos]=$aux;
					}else $pos--;
				}
				$this->relaciones[$pos]["idTipo"]=$idc!=""?"C:".$idc:"O:".$ido;
				$this->relaciones[$pos]["c"]=convTxt($row2[0])."";
				$this->relaciones[$pos]["o"]=convTxt($row[0])."";
				$this->relaciones[$pos]["m"]=convTxt($row[1])."";
				$this->relaciones[$pos]["oAlt"]=convTxt($row[2])."";
			}
		}
	}
	function agregarComponente($idcs){
		$aIds=explode(",",$idcs);
		$hasta=count($aIds);
		$posisQuitar=array();
		$txtRet="";
		for($i=$hasta-1;$i>=0;$i--) if($this->_estaEnRelaciones($aIds[$i])) $posisQuitar[]=$i;		
		foreach($posisQuitar as $posi) $aIds=quitarDeArray($aIds,$posi);
		if(count($aIds)>0){
			$sql="".
			"SELECT componente, operacion, maquina, CONCAT(o.id_operacion,'[::]',o.nombre) AS opAlt,id_componente,op2 ".
			"FROM ( ".			
				"SELECT CONCAT( c.id_componente, '[::]', c.nombre, '[::]', c.codigo ) AS componente,  ".
				"CONCAT( o.id_operacion, '[::]', o.nombre, '[::]', o.codigo ) AS operacion,  ".
				"CONCAT( m.id_maquina, '[::]', m.nombre, '[::]', m.codigo ) AS maquina, id_opAlt, c.id_componente, o.id_operacion as op2 ".
				"FROM me_componentes c ".
				"LEFT JOIN me_componente_operacion co ON co.id_componente = c.id_componente ".
				"LEFT JOIN me_operaciones o ON o.id_operacion = co.id_operacion ".
				"LEFT JOIN ad_maquinas m ON m.id_maquina = o.id_maquina ".
				"WHERE c.id_componente IN (".implode(",",$aIds).") ORDER BY c.id_componente,co.orden ASC ".
			") AS todo ".
			"LEFT JOIN me_operaciones o ON o.id_operacion = todo.id_opAlt ";
			$res=mysql_query($sql);
			$pos=count($this->relaciones);
			$cAnt="-1";
			while($row=@mysql_fetch_row($res)){
				if(!$this->buscaOpRelaciones($row[5],$this->relaciones)){
					$this->relaciones[$pos]["idTipo"]="C:".$row[4];
					$this->relaciones[$pos]["c"]=convTxt($row[0])."";
					$this->relaciones[$pos]["o"]=convTxt($row[1])."";
					$this->relaciones[$pos]["m"]=convTxt($row[2])."";
					$this->relaciones[$pos++]["oAlt"]=convTxt($row[3])."";
				}else $txtRet="Existen operaciones ya relacionadas entre los componentes seleccionados";
			}
		}
		return $txtRet;
	}
	function buscaOpRelaciones($id,$ar){		
		if(count($ar)>0){
			foreach($ar as $r){
				$p=explode("[::]",$r["o"]);
				if($p[0]==$id) return true;
			}	
		}
		return false;
	}
	function subirOrdenRelacion($pos,$c){
		if($c=="1") $this->subirOrdenOperacion($pos);
		elseif($this->_tieneComponente($this->relaciones[$pos])) $this->subirOrdenComponente($pos);
		else $this->subirOrdenOperacion($pos); 
	}
	function bajarOrdenRelacion($pos,$c){
		if($c=="1") $this->bajarOrdenOperacion($pos);
		elseif($this->_tieneComponente($this->relaciones[$pos])) $this->bajarOrdenComponente($pos);
		else $this->bajarOrdenOperacion($pos); 
	}
	function subirOrdenOperacion($posArray){
		if($posArray>0){
			if($this->_tieneComponente($this->relaciones[$posArray])){
				// la relacion a subir está dentro de un componente	
				$pp=explode("[::]",$this->relaciones[$posArray]["c"]);
				$idComponente=$pp[0];
				$pp=explode("[::]",$this->relaciones[$posArray-1]["c"]);
				$idComponenteAnt=$pp[0];
				if($idComponenteAnt==$idComponente)	$this->_subirNormal($posArray);
			}else{
				// la relacion a subir no tiene componente
				if($this->_tieneComponente($this->relaciones[$posArray-1])){					
					// la relacion de encima pertenece a un componente,
					// contamos el num de operaciones del componente 
					// para saber las posiciones que hay que subir.
					$cuantos=$this->_cuentaOperacionesComp($posArray-1);		
					$aux=$this->relaciones[$posArray];	
					for($i=$posArray;$i>($posArray-$cuantos);$i--) $this->relaciones[$i]=$this->relaciones[$i-1];
					$this->relaciones[$posArray-$cuantos]=$aux;	
				}else $this->_subirNormal($posArray);
			}		
		}
	}
	function bajarOrdenOperacion($posArray){
		if($posArray<count($this->relaciones)-1){
			if($this->_tieneComponente($this->relaciones[$posArray])){
				// la relacion a subir está dentro de un componente	
				$pp=explode("[::]",$this->relaciones[$posArray]["c"]);
				$idComponente=$pp[0];
				$pp=explode("[::]",$this->relaciones[$posArray+1]["c"]);
				$idComponenteDelSig=$pp[0];
				if($idComponenteDelSig==$idComponente) $this->_bajarNormal($posArray);
			}else{
				// la relacion a subir no tiene componente
				if($this->_tieneComponente($this->relaciones[$posArray+1])){					
					$cuantos=$this->_cuentaOperacionesComp($posArray+1);		
					$aux=$this->relaciones[$posArray];	
					for($i=$posArray;$i<($posArray+$cuantos);$i++) $this->relaciones[$i]=$this->relaciones[$i+1];
					$this->relaciones[$posArray+$cuantos]=$aux;
				}else $this->_bajarNormal($posArray);
			}			
		}
	}	
	function subirOrdenComponente($posArray){
		if($posArray>0){
			$nOpsCompActual=$this->_cuentaOperacionesComp($posArray); 
			$nOpsCompAnterior=$this->_cuentaOperacionesComp($posArray-1); 
			$aux=Array();
			for($i=$posArray-$nOpsCompAnterior;$i<$posArray;$i++) $aux[]=$this->relaciones[$i];
			for($i=$posArray-$nOpsCompAnterior;$i<$posArray-$nOpsCompAnterior+$nOpsCompActual;$i++) $this->relaciones[$i]=$this->relaciones[$i+$nOpsCompAnterior];
			for($i=$posArray-$nOpsCompAnterior+$nOpsCompActual,$j=0;$j<count($aux);$j++,$i++) $this->relaciones[$i]=$aux[$j];	
		}
	}	
	function bajarOrdenComponente($posArray){
		$nOpsCompActual=$this->_cuentaOperacionesComp($posArray); 
		if($posArray<count($this->relaciones)-$nOpsCompActual){
			$nOpsCompSiguiente=$this->_cuentaOperacionesComp($posArray+$nOpsCompActual); 
			$aux=Array();
			for($i=$posArray;$i<$posArray+$nOpsCompActual;$i++) $aux[]=$this->relaciones[$i];				
			for($i=$posArray;$i<$posArray+$nOpsCompSiguiente;$i++) $this->relaciones[$i]=$this->relaciones[$i+$nOpsCompActual];
			for($i=$posArray+$nOpsCompSiguiente,$j=0;$j<count($aux);$j++,$i++) $this->relaciones[$i]=$aux[$j];	
		}
	}
	
	
	
	/****************************************************/
	/* Funciones para mostrar AMFEs y Planes de Control */
	/****************************************************/
	
	
	function pintarAMFE(){
		if(count($this->relaciones)>0){
			for($i=0;$i<count($this->relaciones);$i++){
				$p=explode(":",$this->relaciones[$i]["idTipo"]);
				if($p[0]=="C"){
					$cActual=$this->relaciones[$i]["idTipo"];
					$opsComponente=array();
					while($this->relaciones[$i]["idTipo"]==$cActual){
						$oo=explode("[::]",$this->relaciones[$i]["o"]);
						if($oo[0]!="") $opsComponente[]=$oo[0];
						$i++;	
					}
					$todo.=PComponente::pintarFilaAMFE($this->id_planificacion,$p[1],$opsComponente);
					$i--;
				}elseif($p[0]=="O"){
					$todo.=POperacion::pintarFilaAMFE($this->id_planificacion,$p[1],true); //true indica que es operacion suelta y no de un componente
				}
			}			
		}
		return $todo;
	}
	function pintarExportacionAMFE(){
		if(count($this->relaciones)>0){
			for($i=0;$i<count($this->relaciones);$i++){
				$oo=explode("[::]",$this->relaciones[$i]["o"]);
				$todo.=POperacion::pintarFilaExportacionAMFE($this->id_planificacion,$oo[0]);
			}			
		}
		return $todo;
	}
	function generarplandecontrol(){
		$t="";
		if(count($this->relaciones)>0){
			foreach($this->relaciones as $r){
				if($r["o"]!=""){
					$o=explode("[::]",$r["o"]);
					$m=explode("[::]",$r["m"]);
					$idMaquina=$m[0]==""?"0":$m[0];
					$nombreMaquina=$m[1];
					$idOperacion=$o[0];
					$sql="SELECT m.nombre,c.num,c.prod,c.proc,cl.img,c.especificacion,c.evaluacion,c.tam,c.fre,c.metodo,c.plan ".
						 "FROM pl_caracteristicas c ".
						 "LEFT JOIN pl_operacion_caracteristica oc ON c.id_caracteristica=oc.id_caracteristica AND oc.id_planificacion=".$this->id_planificacion." ".
						 "LEFT JOIN ad_clases cl ON cl.id_clase=c.id_clase ".
						 "LEFT JOIN ad_maquinas m ON m.id_maquina=".$idMaquina." ". 
						 "WHERE oc.id_operacion=".$idOperacion." AND c.id_planificacion=".$this->id_planificacion;
					$res=mysql_query($sql);
					$j=0;
					if($row=@mysql_fetch_row($res)){
						$t.="<tr>";
						$t.="<td class=Fila rowspan=".mysql_num_rows($res)." valign=top onMouseOver=\"filaover(this)\" onMouseOut=\"filaout(this)\" ".
						"onClick=\"editarOpPC('".$idOperacion."')\"  style='cursor: pointer'>&nbsp; ".$o[1]."&nbsp;</td>";
						$t.="<td class=Fila1 rowspan=".mysql_num_rows($res)." valign=top>&nbsp;".$m[1]."&nbsp;</td>";
						do{
							$t.="<td class=Fila1 valign=top>".$row[1]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top>".$row[2]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top>".$row[3]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=center align=center>".($row[4]==""?"&nbsp;":"<img src=".$row[4].">")."</td>";
							$t.="<td class=Fila1 valign=top align=left>".$row[5]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top align=left>".$row[6]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top>".$row[7]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top>".$row[8]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top align=left>".$row[9]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top align=left>".$row[10]."&nbsp;</td></tr>";
						}while($row=mysql_fetch_row($res));
					}else{
						$t.="<tr>";
						if($this->_opGuardada($idOperacion)){
							$t.="<td class=Fila valign=top onMouseOver=\"filaover(this)\" onMouseOut=\"filaout(this)\" ".
							"onClick=\"editarOpPC('".$idOperacion."')\">&nbsp; ".$o[1]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top>&nbsp;".$m[1]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top colspan=10 align=left>&nbsp;No hay caracter&iacute;sticas relacionadas con esta operaci&oacute;n</td>";
						}else{
							$t.="<td class=Fila valign=top >&nbsp; ".$o[1]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top>&nbsp;".$m[1]."&nbsp;</td>";
							$t.="<td class=Fila1 valign=top colspan=10 align=left><br>";
							$t.="<b>&nbsp;&nbsp;Guarde la planificaci&oacute;n para mostrar correctamente el Plan de Control</b><br>&nbsp;</td>";
						}
						$t.="</tr>";
					}
				}
			}
		}
		return $t;
	}
	function generarplandecontrolExportacion(){
		global $app_inicioWEB;
		global $app_rutaWEB;		
		$inicioRuta=str_replace($app_inicioWEB,"",$app_rutaWEB);
		$t="";
		
		$bR="border-top:0.5pt solid windowtext;";
		$bB="border-bottom:0.5pt solid windowtext;";
		$bI="border-left:0.5pt solid windowtext;";
		$bD="border-right:0.5pt solid windowtext;";
		$st=" STYLE=\"font-size:7.0pt;".$bR.$bB.$bI.$bD."\" ";
		if(count($this->relaciones)>0){
			foreach($this->relaciones as $r){
				if($r["o"]!=""){
					$o=explode("[::]",$r["o"]);
					$m=explode("[::]",$r["m"]);
					$idMaquina=$m[0]==""?"0":$m[0];
					$nombreMaquina=$m[1];
					$idOperacion=$o[0];
					$sql="SELECT m.nombre,c.num,c.prod,c.proc,cl.img,c.especificacion,c.evaluacion,c.tam,c.fre,c.metodo,c.plan ".
						 "FROM pl_caracteristicas c ".
						 "LEFT JOIN pl_operacion_caracteristica oc ON c.id_caracteristica=oc.id_caracteristica AND oc.id_planificacion=".$this->id_planificacion." ".
						 "LEFT JOIN ad_clases cl ON cl.id_clase=c.id_clase ".
						 "LEFT JOIN ad_maquinas m ON m.id_maquina=".$idMaquina." ". 
						 "WHERE oc.id_operacion=".$idOperacion." AND c.id_planificacion=".$this->id_planificacion;
					$res=mysql_query($sql);
					$j=0;
					if($row=mysql_fetch_row($res)){
						$t.="<tr>";
						$t.="<td rowspan=".mysql_num_rows($res)." valign=top colspan=10 $st>&nbsp; ".$o[1]."&nbsp;</td>";
						$t.="<td class=Fila1 rowspan=".mysql_num_rows($res)." valign=top colspan=8 $st>&nbsp;".$m[1]."&nbsp;</td>";
						do{
							$t.="<td valign=top colspan=5 $st align=center>".$row[1]."</td>";
							$t.="<td valign=top colspan=5 $st align=center>".$row[2]."</td>";
							$t.="<td valign=top colspan=5 $st align=center>".$row[3]."</td>";
							$t.="<td valign=top colspan=6 align=center $st>&nbsp;&nbsp;".($row[4]==""?"&nbsp;":"<img src='".$inicioRuta.$row[4]."'>")."</td>";
							$t.="<td valign=top colspan=11 $st>".$row[5]."</td>";
							$t.="<td valign=top colspan=10 $st>".$row[6]."</td>";
							$t.="<td valign=top colspan=5 $st align=center>".$row[7]."</td>";
							$t.="<td valign=top colspan=5 $st align=center>".$row[8]."</td>";
							$t.="<td valign=top colspan=10 $st>".$row[9]."</td>";
							$t.="<td valign=top colspan=20 $st>".$row[10]."</td></tr>";
						}while($row=mysql_fetch_row($res));
					}else{
						$t.="<tr>";
						$t.="<td valign=top colspan=10 $st>&nbsp; ".$o[1]."&nbsp;</td>";
						$t.="<td valign=top colspan=8 $st>&nbsp;".$m[1]."&nbsp;</td>";
						if($this->_opGuardada($idOperacion)){
							$t.="<td valign=top align=left colspan=82 $st>&nbsp;No hay caracter&iacute;sticas relacionadas con esta operaci&oacute;n</td>";
						}else{
							$t.="<td valign=top align=left colspan=82 $st>&nbsp;Guarde la planificaci&oacute;n para mostrar correctamente el plan de control</td>";
						}
						$t.="</tr>";
					}
				}
			}
		}
		return $t;
	}
	
	
	/**********************************************************************************/
	/* Función que aplica los cambios realizados en la planificacion a la referencia. */
	/* Solamente se modifican los componentes y operaciones de la referencia.         */
	/**********************************************************************************/
	
	function aplicarCambios(){
		
		//guardo las relaciones de la planificacion para la referencia a la que corresponde
		
		mysql_query("DELETE FROM me_referencia_relacion WHERE id_referencia=".$this->id_referencia);
		$sql="INSERT INTO me_referencia_relacion (id_referencia,id_relacion,tipo,orden) ".
			 "(SELECT '".$this->id_referencia."',rr.id_relacion,rr.tipo,rr.orden FROM pl_planificacion_relacion rr ".
			 "WHERE id_planificacion=".$this->id_planificacion.")";		
		mysql_query($sql);
			 
		//modifico la operacion (su nombre, id_maquina y operacion alternativa). 
		//SOLO MODIFICO LAS OPERACIONES QUE ESTÁN RELACIONADAS, LAS DEMÁS SE QUEDAN COMO ESTÁN
		
		$sql="SELECT DISTINCT * FROM (".
		"SELECT pr.id_relacion FROM pl_planificacion_relacion pr ".
		"WHERE pr.tipo='O' AND pr.id_planificacion=$this->id_planificacion ".
		"UNION ALL ".
		"SELECT co.id_operacion FROM pl_planificacion_relacion pr ".
		"LEFT JOIN pl_componente_operacion co ON co.id_planificacion=$this->id_planificacion AND co.id_componente=pr.id_relacion ".
		"WHERE pr.tipo='C' AND pr.id_planificacion=".$this->id_planificacion .")as todo";
		$res=mysql_query($sql);
		$idOps=array();
		while($row=@mysql_fetch_row($res)) $idOps[]=$row[0];
		if(count($idOps)>0){
			mysql_query("DELETE FROM me_operaciones WHERE id_operacion IN (".implode(",",$idOps).")");
			$sql="INSERT INTO me_operaciones (id_operacion,codigo,nombre,id_opAlt,id_maquina) ".
				 "(SELECT id_operacion,codigo,nombre,id_opAlt,id_maquina FROM pl_operaciones ".
				 "WHERE id_operacion IN (".implode(",",$idOps).") AND id_planificacion=".$this->id_planificacion." )";
			$res=mysql_query($sql);
			
		}
		
		//copio las estructuras de los componentes de esta planificacion 
		//SOLO MODIFICO LOS COMPONENTES QUE ESTÁN RELACIONADOS, LOS DEMÁS SE QUEDAN COMO ESTÁN
		
		$sql="DELETE FROM me_componente_operacion WHERE id_componente IN ".
			 "(SELECT id_relacion FROM pl_planificacion_relacion pr WHERE id_planificacion=".$this->id_planificacion." AND tipo='C')";
		$res=mysql_query($sql);
		$sql="INSERT INTO me_componente_operacion (id_componente,id_operacion,orden)  ".
			 "(SELECT id_componente,id_operacion,orden FROM pl_componente_operacion WHERE id_planificacion=".$this->id_planificacion.")";
		$res=mysql_query($sql);
		
		
		//copio los modos de las operaciones copiadas porque están relacionadas que están en el array $idOps
		
		$sql="DELETE FROM me_operacion_modo WHERE id_operacion IN (".implode(",",$idOps).")";
		$res=mysql_query($sql);
		$sql="INSERT INTO me_operacion_modo (id_operacion,id_modo) ".
			 "(SELECT id_operacion,id_modo FROM pl_operacion_modo WHERE id_planificacion=".$this->id_planificacion." AND id_operacion IN (".implode(",",$idOps)."))";
		$res=mysql_query($sql);
		
		//no se modificara nada a partir de aqui (efectos,causas,características...)
		//cambiar esto implicaría cambiar maestros del menu admin y sería mucho rollo.
		
	}
	
	
	
	/**********************/
	/* Funciones privadas */
	/**********************/
	
	function _subirNormal($posArray,$queArray="relaciones"){ // funcion utilizada para actividades y relaciones
		if($posArray>0){
			if($queArray=="relaciones"){
				$aux=$this->relaciones[$posArray-1];
				$this->relaciones[$posArray-1]=$this->relaciones[$posArray];
				$this->relaciones[$posArray]=$aux;
			}elseif($queArray="actividades"){
				$aux=$this->actividades[$posArray-1];
				$this->actividades[$posArray-1]=$this->actividades[$posArray];
				$this->actividades[$posArray]=$aux;
			}
		}
	}
	function _bajarNormal($posArray,$queArray="relaciones"){ // funcion utilizada para actividades y relaciones
		if($queArray=="relaciones"){
			if($posArray<count($this->relaciones)-1){
				$aux=$this->relaciones[$posArray+1];
				$this->relaciones[$posArray+1]=$this->relaciones[$posArray];
				$this->relaciones[$posArray]=$aux;
			}
		}elseif($queArray=="actividades"){
			if($posArray<count($this->actividades)-1){
				$aux=$this->actividades[$posArray+1];
				$this->actividades[$posArray+1]=$this->actividades[$posArray];
				$this->actividades[$posArray]=$aux;
			}
		}
	}
	function _tieneComponente($a){
		$partes=explode("[::]",$a["c"]);
		if($partes[0]!=""&&$partes[0]!="-1"&&$partes[0]!="0") return true;
		else return false;
	}
	function _tieneOperacion($a){
		$partes=explode("[::]",$a["o"]);
		if($partes[0]!=""&&$partes[0]!="-1"&&$partes[0]!="0") return true;
		else return false;
	}
	function _cuentaOperacionesComp($pos){
		$idComponente=explode("[::]",$this->relaciones[$pos]["c"]);
		$idComponente=$idComponente[0];
		$cnt=1;
		if($idComponente!=""&&$idComponente!="-1"){
			$aux=$pos+1;
			$exit=false;
			while(!$exit && $aux<count($this->relaciones)){ //cuento por debajo
				$pp=explode("[::]",$this->relaciones[$aux++]["c"]);
				if($pp[0]==$idComponente) $cnt++;
				else $exit=true;
			}
			$aux=$pos-1;
			$exit=false;
			while(!$exit && $aux>=0){ //cuento por encima
				$pp=explode("[::]",$this->relaciones[$aux--]["c"]);
				if($pp[0]==$idComponente) $cnt++;
				else $exit=true;
			}
		}
		return $cnt;		
	}
	function _estaEnRelaciones($idc,$ido=""){ // mira si ya está en las relaciones una relacion nueva
		if($ido==""){ // agregar componente o no
			foreach($this->relaciones as $r){
				$p=explode("[::]",$r["c"]);
				if($p[0]==$idc) return true;
			}
			return false;
		}else{ // agregar operacion dentro de componente o no
			foreach($this->relaciones as $r){
				$p=explode("[::]",$r["c"]);
				$p2=explode("[::]",$r["o"]);
				if($p[0]==$idc && $p2[0]==$ido) return true;
			}
			return false;
		}	
	}
	function _opGuardada($ido){
		$res=mysql_query("SELECT count(*) FROM pl_operaciones WHERE id_planificacion=".$this->id_planificacion." AND id_operacion=".$ido);
		$row=mysql_fetch_row($res);
		return ($row[0]=="0"?false:true);
	}
	
}
?>