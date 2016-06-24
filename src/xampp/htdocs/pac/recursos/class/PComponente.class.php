<?
include_once "POperacion.class.php";

class PComponente {
	
	var $id_planificacion;
	var $id_componente;
	var $codigo="";
	var $codigoOrig="";
	var $nombre="";
	var $operaciones=Array();
	var $nuevo;
	
	
	/**********************************************************************************************/
	/* Copia del componente y sus operaciones (clase POperacion) 								  */
	/**********************************************************************************************/
	
	function PComponente ($idp,$idc,$nuevo=false){
		//if($nuevo){
			$this->id_planificacion=$idp;
			$sql="SELECT * FROM me_componentes WHERE id_componente=".$idc;
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_componente=$row["id_componente"];
			$this->codigo=$row["codigo"];
			$this->nombre=$row["nombre"];
		//}
	}
	
	/*******************************************************************************************/
	/* Guarda el componente */
		
	function guardar(){	
		$modoDebug=false;
		$sql="INSERT INTO pl_componentes (id_planificacion,id_componente,nombre,codigo) VALUES ".
			 "($this->id_planificacion,$this->id_componente,'".cTxt($this->nombre)."','$this->codigo')";
		mysql_query($sql);
		if($ModoDebug) echo $sql."<br>";
		if(count($this->operaciones>0)){
			$i=0;
			foreach($this->operaciones as $oid){
				$op=new POperacion($this->id_planificacion,$oid);
				$sql="INSERT INTO pl_componente_operacion (id_planificacion,id_componente,id_operacion,orden) VALUES ".
					 "($this->id_planificacion,$this->id_componente,$oid,".$i++.")";
				mysql_query($sql);	
				if($ModoDebug) echo $sql."<br>";
			}
		}
	}
	
	function compruebaPlanis(){
		$sql="SELECT count(*),id_planificacion FROM planificacion_relacion WHERE id_componente=".$this->id_componente." GROUP BY id_planificacion";
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		return $row[0];
	}
	
	/*********************************************************************************************/
	/* Eliminar componente */
	
	function eliminar () {
		if($this->compruebaPlanis()==0){
			$sql="DELETE FROM me_componentes WHERE id_componente=".$this->id_componente;
			@mysql_query($sql);
			$this->eliminaC_O();
		}
	}
	function eliminaC_O(){
		$sql="DELETE FROM me_componente_operacion WHERE id_componente=".$this->id_componente;
		@mysql_query($sql);
		$sql="DELETE FROM me_referencia_relacion WHERE id_relacion=".$this->id_componente." AND tipo='C'";
		@mysql_query($sql);
	}
	
	/********************************************************************************************/
	/* Funciones para el control de las operaciones */
	
	function agregarOperaciones($arOps) {		
		if(is_array($arOps)){
			foreach($arOps as $v){
				if(array_search($v,$this->operaciones)===false) $this->operaciones[]=$v;
			}
		}
		else if(array_search($arOps,$this->operaciones)===false) $this->operaciones[]=$arOps;
	}	
	function quitarOperaciones($arOps) {
		if(is_array($arOps)) foreach ($arOps as $v) $this->operaciones=quitarDeArray($this->operaciones,array_search($v,$this->operaciones));
		else $this->operaciones=quitarDeArray($this->operaciones,array_search($arOps,$this->operaciones));
	}
	function guardarOperaciones(){
		$this->eliminaC_O();
		for($i=0;$i<count($this->operaciones);$i++){
			$sql = "INSERT INTO me_componente_operacion (id_componente,id_operacion,orden) VALUES ".
				   "(".$this->id_componente.",".$this->operaciones[$i].",".$i.")";
			@mysql_query($sql);
		}
	}
	
	
	/***************************************************************************************/
	/* Copiar estructura del componente */
	
	function copiarEstructura($id){
		$sql="SELECT * FROM me_componentes WHERE id_componente=".$id;
		$res=mysql_query($sql);
		if($row=mysql_fetch_array($res)){
			$this->codigo=$row["codigo"];
			$this->nombre=$row["nombre"]." (Copia)";
		}
		$this->operaciones=Array();
		$sql="SELECT id_operacion FROM me_componente_operacion WHERE id_componente=".$id." ORDER BY orden asc";
		$res=mysql_query($sql);
		if($row=mysql_fetch_row($res)) do{
			$this->operaciones[]=$row[0];
		}while($row=mysql_fetch_array($res));		
	}
	
	
	
	function subeOrden($idp,$idc,$pos){
		if($pos>0){
			$sql="SELECT id_operacion WHERE id_planificacion=$idp AND id_componente=$idc orden=$pos";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$idSubir=$row[0];
			$sql="UPDATE pl_componente_operacion SET orden=orden+1 WHERE orden=".($pos-1)." AND id_planificacion=$idp AND id_componente=$idc";
			//mysql_query($sql);
			$sql="UPDATE pl_componente_operacion SET orden=orden-1 WHERE id_planificacion=$idp AND id_componente=$idc AND id_operacion=$idSubir" ;
			//mysql_query($sql);
		}
	}
	function bajaOrden($idp,$idc,$pos){
		$sql="SELECT count(*) FROM pl_componente_operacion WHERE id_planificacion=$idp AND id_componente=$idc AND orden=".($pos+1);
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		$cuantos=$row[0];
		if($pos<$cuantos){
			echo "bajandoooooooo...<br><br>";	
			
		}
		/*
		if($pos<count($this->operaciones)-1){
			$aux=$this->operaciones[$pos+1];
			$this->operaciones[$pos+1]=$this->operaciones[$pos];
			$this->operaciones[$pos]=$aux;
		}
		*/
	}
	
	function pintarFilaAMFE($idp,$idc,$ops){
		$ret="";
		$sql="SELECT count(*) FROM pl_componentes WHERE id_componente=".$idc." AND id_planificacion=".$idp;
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		if($row[0]==0){
			$ret.="<tr onMouseOver\"filaover(this)\" onMouseOut=\"filaout(this)\"><td colspan=17 class=\"Fila1\" align=left>".
			 		"<b><br>&nbsp;&nbsp;Guarde la planificación para mostrar correctamente el AMFE.<br>&nbsp;</b> ".
			 		"</td></tr>";			
		}else {
			if(count($ops)>0){
				$modosTotales=0;
				$sql="SELECT count(*),c.nombre FROM pl_componente_operacion co ".
					 "LEFT JOIN pl_componentes c ON c.id_componente=co.id_componente AND c.id_planificacion=$idp ".
					 "LEFT JOIN pl_operacion_modo op ON op.id_operacion=co.id_operacion AND op.id_planificacion=$idp ".
					 "WHERE co.id_planificacion=$idp AND co.id_operacion IN (".implode(",",$ops).") AND co.id_componente=$idc ".
					 "GROUP BY c.nombre";
				$res=mysql_query($sql);
				if($row=mysql_fetch_row($res)){
					$ret="<tr><td rowspan=\"".$row[0]."\" ".
						 " class=\"Fila1\" align=left>&nbsp;".$row[1]."</td>";
					foreach($ops as $op) $ret.=POperacion::pintarFilaAMFE($idp,$op);
				}
			}
		}
		return $ret;		
	}	
	
}
?>