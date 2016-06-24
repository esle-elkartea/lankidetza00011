<?
include "../recursos/class/Planificacion.class.php";
include_once "../recursos/class/POperacion.class.php";
include_once "../recursos/class/PComponente.class.php";
define("_ERROR1","alert('Existe un registro con el mismo número de referencia');");

class Referencia {
	
	var $id_referencia;
	var $num="";
	var $numOrig="";
	var $nombre="";
	var $plano="";
	var $nivel;
	var $fecha;
	var $id_familia;
	var $relaciones=Array();
	var $id_clase;
	var $nuevo;
	
	
	/***********************************/
	/* Carga de datos de la referencia */
	/***********************************/
	
	function Referencia ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_referencia) is null,1,Max(id_referencia)+1) FROM me_referencias ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_referencia=$row[0];
			$this->nuevo=true;					
		}else{		
			$sql="SELECT * FROM me_referencias WHERE id_referencia=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_referencia=$row["id_referencia"];	
			$this->nombre=$row["nombre"];
			$this->num=$row["num"];
			$this->numOrig=$this->num;
			$this->plano=$row["plano"];
			$this->nivel=$row["nivel"];
			$this->fecha=$row["fecha"];
			$this->id_familia=$row["id_familia"];
			$this->id_clase=$row["id_clase"];			
			$this->nuevo=false;		
			$this->cargaRelaciones();	
		}		
	}
	function getComponentes(){
		$ret=Array();
		for($i=0;$i<count($this->relaciones);$i++){
			$p=explode(":",$this->relaciones[$i]["idTipo"]);
			if($p[0]=="C") $ret[$i]=$p[1]; 
		}
		return $ret;
	}
	function getOperaciones(){
		$ret=Array();
		for($i=0;$i<count($this->relaciones);$i++){
			$p=explode(":",$this->relaciones[$i]["idTipo"]);
			if($p[0]=="O") $ret[$i]=$p[1]; 
		}
		return $ret;
	}
	function cargaRelaciones($id=""){		
		$elId=$id==""?$this->id_referencia:$id;		
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
		while($row=mysql_fetch_row($res)){
			$relaciones[$i]["idTipo"]=$row[0]."";
			$relaciones[$i]["c"]=convTxt($row[1])."";
			$relaciones[$i]["o"]=convTxt($row[2])."";
			$relaciones[$i]["m"]=convTxt($row[3])."";
			$relaciones[$i++]["oAlt"]=convTxt($row[4])."";
		}
		if($id=="") $this->relaciones=$relaciones;
		else return $relaciones;
	}
	
	
	/*********************************/
	/* Guardar o eliminar referencia */
	/*********************************/
	
	function guardar (){		
		if($this->nuevo) {
			$sql="".
			"INSERT INTO me_referencias (id_referencia,nombre,num,plano,nivel,fecha,id_familia,id_clase) VALUES ".
			"($this->id_referencia,".
			"'$this->nombre',".
			"'$this->num',".
			"'$this->plano',".
			"'$this->nivel',".
			"'".fechaBD($this->fecha)."',".
			"'$this->id_familia','$this->id_clase')";						
			if(!existeValorTabla("me_referencias","num",$this->num)) {
				if($res=mysql_query($sql)) {$this->borrarRelaciones();$this->guardarRelaciones();}
			}else return (_ERROR1);			
		}else{
			$sql="".
			"UPDATE me_referencias SET ".
			"nombre='$this->nombre', ".
			"num='$this->num', ".
			"plano='$this->plano', ".
			"nivel='$this->nivel', ".
			"fecha='".fechaBD($this->fecha)."', ".
			"id_familia='$this->id_familia', ".
			"id_clase='$this->id_clase' ".
			"WHERE id_referencia=$this->id_referencia";						
			if(existeValorTabla("me_referencias","num",$this->num) && $this->numOrig!=$this->num) return (_ERROR1); 
			elseif($res=mysql_query($sql)) {$this->borrarRelaciones();$this->guardarRelaciones();}
		}		
	}	
	function eliminar (){
		if($this->compruebaPlanis()==0){
			$sql="DELETE FROM me_referencias WHERE id_referencia=".$this->id_referencia;
			@mysql_query($sql);
			$this->borrarRelaciones();
		}else return "alert('Existen planificaciones con esta referencia. La referencia no puede ser eliminada');";
	}
	function guardarRelaciones(){
		$i=0;
		foreach($this->relaciones as $r){
			$pt=explode(":",$r["idTipo"]);
			$sql="INSERT INTO me_referencia_relacion (id_referencia,id_relacion,tipo,orden) ".
				 "VALUES ('$this->id_referencia','".$pt[1]."','".$pt[0]."','".($r["idTipo"]!=$ant?$i++:'')."')";	
			if($r["idTipo"]!=$ant)$res=mysql_query($sql);
			$ant=$r["idTipo"];
		}	
	}
	function borrarRelaciones(){
		$sql="DELETE FROM me_referencia_relacion WHERE id_referencia=$this->id_referencia";
		mysql_query($sql);
	}
	function compruebaPlanis(){
		$sql="SELECT count(*) FROM planificaciones p WHERE p.id_referencia=$this->id_referencia";
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		return $row[0];
		
	}
	
	
	/***********************************************/
	/* Copiado de la estructura de otra referencia */	
	/***********************************************/
	
	function copiarEstructura($id){
		$this->copiarReferencia($id);
		$this->relaciones=$this->cargaRelaciones($id);		
	}	
	function copiarReferencia($id){
		$sql="SELECT * FROM me_referencias WHERE id_referencia=".$id;
		$res=mysql_query($sql);
		if($row=mysql_fetch_array($res)){
			$this->num=$row["num"];
			$this->nombre=$row["nombre"]." (Copia)";
			$this->plano=$row["plano"];
			$this->nivel=$row["nivel"];
			$this->fecha=$row["fecha"];
			$this->id_familia=$row["id_familia"];
			$this->id_clase=$row["id_clase"];
		}		
	}	

	
	
	/***********************************************************/
	/* Funciones para las relacionar componentes y operaciones */
	/***********************************************************/
	
	function agregarComponentes($arComps){
		if(is_array($arComps)) $a=$arComps;
		else $a=Array($arComps);
		foreach($a as $v) {
			$i=0;
			$sigue=true;
			while($sigue && $i<count($this->relaciones)) if($this->relaciones[$i++]["idTipo"]=="C:".$v) $sigue=false;
			if($sigue){
				$pos=count($this->relaciones);
				$sql="SELECT CONCAT(c.id_componente,'[::]',c.nombre,'[::]',c.codigo),CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) FROM me_componentes c ".
					 "LEFT JOIN me_componente_operacion co ON co.id_componente=c.id_componente ".
					 "LEFT JOIN me_operaciones o ON co.id_operacion=o.id_operacion ".
					 "WHERE c.id_componente=$v";
				$res=mysql_query($sql);
				while($row=mysql_fetch_row($res)){
					$this->relaciones[$pos]["idTipo"]="C:".$v;
					$this->relaciones[$pos]["c"]=$row[0];
					$this->relaciones[$pos++]["o"]=$row[1];	
				}
			}				
		}
	}
	function agregarOperaciones($arOps){
		if(is_array($arOps)) $a=$arOps;
		else $a=Array($arOps);
		foreach($a as $v) {
			$i=0;
			$sigue=true;
			while($sigue && $i<count($this->relaciones)) if($this->relaciones[$i++]["idTipo"]=="O:".$v) $sigue=false;
			if($sigue){
				$pos=count($this->relaciones);
				$sql="SELECT CONCAT(o.id_operacion,'[::]',o.nombre,'[::]',o.codigo) FROM me_operaciones o ".
					 "WHERE o.id_operacion=$v";
				$res=mysql_query($sql);
				while($row=mysql_fetch_row($res)){
					$this->relaciones[$pos]["idTipo"]="O:".$v;
					$this->relaciones[$pos]["c"]="";
					$this->relaciones[$pos++]["o"]=$row[0];	
				}
			}
		}
	}	
	function quitarComponente($id) {
		$this->quitarRel($id,"C");
	}
	function quitarOperacion ($id) {
		$this->quitarRel($id,"O");
	}
	function quitarRel($id,$cat){
		$i=0;
		while( ($this->relaciones[$i]["idTipo"]!=$cat.":".$id) && $i<count($this->relaciones) ) $i++;
		while($this->relaciones[$i]["idTipo"]==$cat.":".$id) $this->relaciones=quitarDeArray($this->relaciones,$i);			
	}
	function bajarOrdenRelacion($pos){
		$_id=$this->relaciones[$pos]["idTipo"];
		$sePuedeBajar=false;
		for($i=$pos;$i<count($this->relaciones);$i++) if($this->relaciones[$i]["idTipo"]!=$_id) $sePuedeBajar=true;
		if($sePuedeBajar){
			$compAct=Array();
			$tipoPosAct=$this->relaciones[$pos]["idTipo"];
			$i=$pos;
			while($tipoPosAct==$this->relaciones[$i]["idTipo"]) $compAct[]=$this->relaciones[$i++];			
			$compSig=Array();
			$tipoPosSig=$this->relaciones[$pos+count($compAct)]["idTipo"];
			$i=$pos+count($compAct);
			while($tipoPosSig==$this->relaciones[$i]["idTipo"]) $compSig[]=$this->relaciones[$i++];			
			for($i=$pos,$j=0;$j<count($compSig);$j++,$i++) $this->relaciones[$i]=$compSig[$j];	
			for($i=$pos+count($compSig),$j=0;$j<count($compAct);$j++,$i++) $this->relaciones[$i]=$compAct[$j];
		}	
	}
	function subirOrdenRelacion($pos){
		if($pos>0){
			$compAnt=Array();
			$tipoPosAnt=$this->relaciones[$pos-1]["idTipo"];
			$i=$pos-1;
			while($tipoPosAnt==$this->relaciones[$i]["idTipo"]) $compAnt[]=$this->relaciones[$i--];	
			$compAct=Array();
			$tipoPosAct=$this->relaciones[$pos]["idTipo"];
			$i=$pos;
			while($tipoPosAct==$this->relaciones[$i]["idTipo"]) $compAct[]=$this->relaciones[$i++];	
			for($j=0,$i=$pos-count($compAnt);$i<$pos-count($compAnt)+count($compAct);$i++,$j++)	$this->relaciones[$i]=$compAct[$j];	
			for($j=0,$i=$pos-count($compAnt)+count($compAct);$i<$pos+count($compAct);$i++,$j++)	$this->relaciones[$i]=$compAnt[$j];	
		}
	}
		
	
	
	function pintarAMFE(){
		if(count($this->relaciones)>0){
			$cAnt="-";
			foreach($this->relaciones as $r){
				$p=explode(":",$r["idTipo"]);
				if($p[0]=="C" && $cAnt!=$r["idTipo"]){
					$aux=new Componente($p[1]);
					$todo.=$aux->pintarFilaAMFE();	
					$cAnt=$r["idTipo"];		
				}elseif($p[0]=="O"){
					$aux=new Operacion($p[1]);
					$todo.=$aux->pintarFilaAMFE(true); // true indica rellenando el hueco del componente
				}
			}			
		}
		return $todo;
	}
	function pintarAMFEexportar(){
		if(count($this->relaciones)>0){
			$cAnt="-";
			foreach($this->relaciones as $r){
				$p=explode(":",$r["idTipo"]);
				if($p[0]=="C" && $cAnt!=$r["idTipo"]){
					$aux=new Componente($p[1]);
					$todo.=$aux->pintarFilaAMFEexportar();	
					$cAnt=$r["idTipo"];		
				}elseif($p[0]=="O"){
					$aux=new Operacion($p[1]);
					$todo.=$aux->pintarFilaAMFEexportar(true); // true indica rellenando el hueco del componente
				}
			}			
		}
		return $todo;
	}
	
	
	
	
	/**************************************************/
	/*      Funcion para crear una planificación      */
	/**************************************************/
	
	
	function crearPlanificacion($idCliente){
		$plan=new Planificacion();
		$plan->id_referencia=$this->id_referencia;
		$plan->id_cliente=$idCliente;
		$plan->fecha=date("Y-m-d");
		$plan->codigo='0';
		$plan->cargaRelacionesMaestros();
		$plan->guardar(false); //el false indica que no se debe comprobar si el código de la planificacion existe o no ya que no se sabe aún.
	}
	
	
	
	
	
	
	/*
	function subirOrdenOperacion($posArray){
		if($posArray>0){
			if($this->_tieneComponente($this->relaciones[$posArray])){
				// la relacion a subir está dentro de un componente	
				$pp=explode("[::]",$this->relaciones[$posArray]["componente"]);
				$idComponente=$pp[0];
				$pp=explode("[::]",$this->relaciones[$posArray-1]["componente"]);
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
					for($i=$posArray;$i>($posArray-$cuantos);$i--){
						$this->relaciones[$i]=$this->relaciones[$i-1];
						//$this->relaciones[$i]["orden"]++;
					}
					//$aux["orden"]=$posArray-$cuantos;
					$this->relaciones[$posArray-$cuantos]=$aux;	
				}else $this->_subirNormal($posArray);
			}		
		}
	}
	function bajarOrdenOperacion($posArray){
		if($posArray<count($this->relaciones)-1){
			if($this->_tieneComponente($this->relaciones[$posArray])){
				// la relacion a subir está dentro de un componente	
				$pp=explode("[::]",$this->relaciones[$posArray]["componente"]);
				$idComponente=$pp[0];
				$pp=explode("[::]",$this->relaciones[$posArray+1]["componente"]);
				$idComponenteDelSig=$pp[0];
				if($idComponenteDelSig==$idComponente) $this->_bajarNormal($posArray);
			}else{
				// la relacion a subir no tiene componente
				if($this->_tieneComponente($this->relaciones[$posArray+1])){					
					$cuantos=$this->_cuentaOperacionesComp($posArray+1);		
					$aux=$this->relaciones[$posArray];	
					for($i=$posArray;$i<($posArray+$cuantos);$i++){
						$this->relaciones[$i]=$this->relaciones[$i+1];
						//$this->relaciones[$i]["orden"]--;
					}
					$aux["orden"]=$posArray+$cuantos;	
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
			for($i=$posArray-$nOpsCompAnterior;$i<$posArray-$nOpsCompAnterior+$nOpsCompActual;$i++){
				$this->relaciones[$i]=$this->relaciones[$i+$nOpsCompAnterior];
				//$this->relaciones[$i]["orden"]=$i;
			}
			for($i=$posArray-$nOpsCompAnterior+$nOpsCompActual,$j=0;$j<count($aux);$j++,$i++){
				$this->relaciones[$i]=$aux[$j];	
				//$this->relaciones[$i]["orden"]=$i;
			}
		}
	}
	
	
	function bajarOrdenComponente($posArray){
		if($posArray<count($this->relaciones)-1){
			$nOpsCompActual=$this->_cuentaOperacionesComp($posArray); 
			$nOpsCompSiguiente=$this->_cuentaOperacionesComp($posArray+$nOpsCompActual); 
			$aux=Array();
			for($i=$posArray;$i<$posArray+$nOpsCompActual;$i++) $aux[]=$this->relaciones[$i];
			for($i=$posArray;$i<$posArray+$nOpsCompSiguiente;$i++){
				$this->relaciones[$i]=$this->relaciones[$i+$nOpsCompActual];
				//$this->relaciones[$i]["orden"]=$i;
			}
			for($i=$posArray+$nOpsCompSiguiente,$j=0;$j<count($aux);$j++,$i++){
				$this->relaciones[$i]=$aux[$j];	
				//$this->relaciones[$i]["orden"]=$i;
			}
		}
	}	
	function _subirNormal($posArray){
		if($posArray>0){
			//$this->relaciones[$posArray]["orden"]--;
			//$this->relaciones[$posArray-1]["orden"]++;
			$aux=$this->relaciones[$posArray-1];
			$this->relaciones[$posArray-1]=$this->relaciones[$posArray];
			$this->relaciones[$posArray]=$aux;
		}
	}
	function _bajarNormal($posArray){
		if($posArray<count($this->relaciones)-1){
			//$this->relaciones[$posArray]["orden"]++;
			//$this->relaciones[$posArray+1]["orden"]--;
			$aux=$this->relaciones[$posArray+1];
			$this->relaciones[$posArray+1]=$this->relaciones[$posArray];
			$this->relaciones[$posArray]=$aux;
		}		
	}
	function _tieneComponente($a){
		$partes=explode("[::]",$a["componente"]);
		if($partes[0]!="" && $partes[0]!="-1" && $partes[0]!="0") return true;
		else return false;
	}
	function _tieneOperacion($a){
		$partes=explode("[::]",$a["operacion"]);
		if($partes[0]!="" && $partes[0]!="-1" && $partes[0]!="0" ) return true;
		else return false;
	}
	function _cuentaOperacionesComp($pos){
		$idComponente=explode("[::]",$this->relaciones[$pos]["componente"]);
		$idComponente=$idComponente[0];
		$cnt=1;
		if($idComponente!="" && $idComponente!="-1"){
			$aux=$pos+1;
			$exit=false;
			while(!$exit && $aux<count($this->relaciones)){ //cuento por debajo
				$pp=explode("[::]",$this->relaciones[$aux++]["componente"]);
				if($pp[0]==$idComponente) $cnt++;
				else $exit=true;
			}
			$aux=$pos-1;
			$exit=false;
			while(!$exit && $aux>=0){ //cuento por encima
				$pp=explode("[::]",$this->relaciones[$aux--]["componente"]);
				if($pp[0]==$idComponente) $cnt++;
				else $exit=true;
			}
		}
		return $cnt;		
	}
	
	/*
	function cargaComponentes($id="-1"){
		$idRef=$id=="-1"?$this->id_referencia:$id;
		$sql="SELECT c.id_componente,c.codigo,c.nombre FROM referencia_componente rc,componentes c ".
			 "WHERE rc.id_componente=c.id_componente AND id_referencia=$idRef ORDER BY orden";
		$res=mysql_query($sql);
		$j=0;
		if($row=mysql_fetch_row($res)) do{
			$this->id_componentes[$j]=new ReferenciaComponente($this->id_referencia,$row[0]);
			$this->id_componentes[$j]->nombre=$row[2];
			$this->id_componentes[$j++]->codigo=$row[1];
		}while($row=mysql_fetch_row($res));
	}
	function cargaOperaciones($id="-1"){
		$idRef=$id=="-1"?$this->id_referencia:$id;
		$sql="SELECT o.id_operacion,o.codigo,o.nombre FROM referencia_operacion ro,operaciones o ".
			 "WHERE ro.id_operacion=o.id_operacion AND id_referencia=$idRef ORDER BY orden";
		$res=mysql_query($sql);
		$j=0;
		if($row=mysql_fetch_row($res)) do {
			$this->id_operaciones[$j]=new ReferenciaOperacion($this->id_referencia,$row[0]);
			$this->id_operaciones[$j]->codigo=$row[1];
			$this->id_operaciones[$j]->nombre=$row[2];
		}while($row=mysql_fetch_row($res));
	}
	function getListadoOperaciones($id=""){
		$sql=	" SELECT id_operacion FROM referencia_operacion WHERE id_referencia=##id## ".
				" UNION ".
				" SELECT co.id_operacion FROM referencia_componente rc, componente_operacion co".
				" WHERE rc.id_componente=co.id_componente AND rc.id_referencia=##id## ";
				
		if($id!="") $sql=str_replace("##id##",$id,$sql);
		else $sql=str_replace("##id##",$this->id_referencia,$sql);
		
		$res=mysql_query($sql);
		$ops=Array();
		while($row=@mysql_fetch_row($res)) $ops[]=$row[0];
		return $ops;
	}
	
	function getComponentesOperaciones($id=""){
		$componentes_operaciones=Array();
		$sql="".
		" SELECT c.nombre,co.id_operacion".
		" FROM referencia_componente rc".
		" LEFT JOIN componentes c ON c.id_componente=rc.id_componente".
		" LEFT JOIN componente_operacion co ON co.id_componente=rc.id_componente".
		" WHERE rc.id_referencia=##id##".
		" UNION ".
		" SELECT null,id_operacion".
		" FROM referencia_operacion WHERE id_referencia=##id##";
		
		if($id!="") $sql=str_replace("##id##",$id,$sql);
		else $sql=str_replace("##id##",$this->id_referencia,$sql);
		
		$res=mysql_query($sql);
		$c=0;
		if($r=mysql_fetch_row($res)) do{ 
			if($r[0]!=null)	$componentes_operaciones[$r[0]][]=$r[1]; 
			else $componentes_operaciones["####"][$c++]=$r[1]; 
		}while($r=mysql_fetch_row($res));
		return $componentes_operaciones;
	}
	
	function getRelacionesXX ($id=""){
		$idRef=$id==""?$this->id_referencia:$id;
		$a=Array();
		$sql="".
		" SELECT * FROM ".
		"   (".
		"    SELECT c.id_componente,c.nombre as nombreComponente, o.id_operacion,o.nombre as nombreOperacion,rc.orden as orden1,co.orden as orden2".
		"    FROM referencia_componente rc".
		"    LEFT JOIN componentes c ON c.id_componente=rc.id_componente".
		"    LEFT JOIN componente_operacion co ON co.id_componente=rc.id_componente".
		"    LEFT JOIN operaciones o ON o.id_operacion=co.id_operacion".
		"    WHERE rc.id_referencia=$idRef".
		"   UNION ALL ".
		"    SELECT '-1' as id_componente,'' as nombreComponente,ro.orden as orden1,null as orden2,ro.id_operacion,o.nombre as nombreOperacion".
		"    FROM referencia_operacion ro".
		"    LEFT JOIN operaciones o ON o.id_operacion=ro.id_operacion".
		"    WHERE ro.id_referencia=$idRef ".
		"   )as todo ".
		" ORDER BY todo.orden1 asc,todo.orden2 asc ";
		$res=@mysql_query($sql);
		while($row=@mysql_fetch_row($res))
			$a[]=Array(
					"componente" => $row[0]."[::]".$row[1], 
					"operacion"  => $row[2]."[::]".$row[3],
				 );		
		if($id=="") $this->relaciones=$a;
		else return $a;
	}
	
	
	function getRelaciones ($id=""){
		$idRef=$id==""?$this->id_referencia:$id;
		$a=Array();
		$sql="".
		" SELECT * FROM ".
		
		" ORDER BY todo.orden1 asc,todo.orden2 asc ";
		$res=@mysql_query($sql);
		while($row=@mysql_fetch_row($res))
			$a[]=Array(
					"componente" => $row[0]."[::]".$row[1], 
					"operacion"  => $row[2]."[::]".$row[3],
				 );		
		if($id=="") $this->relaciones=$a;
		else return $a;
	}
	*/
	
	
	
	/*
	function ponerOperacionPrimera($pos){
		$aux=$this->relaciones[$pos];
		$aux->orden=0;
		for($i=$pos;$i>0;$i--){
				$aux2=$this->operaciones[$i-1];
				$aux2->orden++;
				$this->operaciones[$i]=$aux2;
		}
		$this->operaciones[0]=$aux;		
	}
	function ponerOperacionUltima($pos){
		$aux=$this->operaciones[$pos];
		$aux->orden=count($this->operaciones)-1;
		for($i=$pos;$i<count($this->operaciones)-1;$i++){
			$aux2=$this->operaciones[$i+1];
			$aux2->orden--;
			$this->operaciones[$i]=$aux2;
		}
		$this->operaciones[count($this->operaciones)-1]=$aux;		
	}
	*/
	/*
	function guardarComponentes() {
		$this->eliminarComponentes();
		foreach ($this->id_componentes as $t=>$v) {
			$sql = "INSERT INTO referencia_componente (id_referencia,id_componente) VALUES ($this->id_referencia,$v)";
			$res = mysql_query($sql);
		}		
	}
	function eliminarComponentes(){
		$sql = "DELETE FROM referencia_componente WHERE id_referencia=".$this->id_referencia;
		@mysql_query($sql);
	}
	*/
	
	
	
	/*************************************************/
	/* Funciones para las relaciones con operaciones */
	/*
	function agregarOperaciones($arOps) {
		if(is_array($arOps)) $this->id_operaciones = array_merge($this->id_operaciones,$arOps);
		else $this->id_operaciones[]=$arOps;
	}	
	function quitarOperaciones($arOps) {
		$ops = "#".implode("##",$this->id_operaciones)."#";			
		if(is_array($arOps)) foreach($arOps as $v)	$ops=str_replace("#".$v."#","",$ops);
		else $ops=str_replace("#".$arOps."#","",$ops);
		$aux = str_replace("#","",str_replace("##","||",$ops));
		$this->id_operaciones = explode("||",$aux);
	}
	function guardarOperaciones() {
		$this->eliminarOperaciones();
		foreach ($this->id_operaciones as $v){
			$sql = "INSERT INTO referencia_operacion (id_referencia,id_operacion) VALUES ($this->id_referencia,$v)";
			@mysql_query($sql);
		}		
	}
	function eliminarOperaciones() {
		$sql = "DELETE FROM referencia_operacion WHERE id_referencia=$this->id_referencia";
		@mysql_query($sql);
	}
	*/
	/*
	function agregarComponentes($arComps){
			$sql=	"SELECT id_componente,id_operacion FROM componente_operacion ".
					"WHERE id_componente IN (".implode(",",$arComps).") ORDER BY id_componente asc,orden asc";
			$res=mysql_query($sql);
			$i=count($this->relaciones);
			while($row=@mysql_fetch_row($res)){
				$this->relaciones[]=Array(
					"componente"=>$row[0],
					"operacion"=>$row[1],
					"maquina"=>"",
					"operacionAlt"=>"",
					"orden"=>$i++);
			}
	}*/
	
	
}




?>