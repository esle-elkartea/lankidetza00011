<?
define("_ERROR1","alert('Existe otra causa con el mismo código');");

class Causa {
	
	var $id_causa;
	var $nombre="";
	var $codigo="";
	var $codigoOrig="";
	var $modos=Array();
	var $id_detectabilidad="";
	var $accion="";
	var $nuevo;
	
	
	
	
	// carga de la causa
	
	function Causa ($id=""){
		if($id==""){
			$sql="SELECT if(Max(id_causa) is null,1,Max(id_causa)+1) FROM me_causas ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_causa=$row[0];
			$this->nuevo=true;	
		}else{
			$sql="SELECT * FROM me_causas WHERE id_causa=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_causa=$row["id_causa"];	
			$this->nombre=$row["nombre"];
			$this->codigo=$row["codigo"];
			$this->codigoOrig=$this->codigo;
			$this->id_detectabilidad=$row["id_detectabilidad"];
			$this->accion=$row["accion"];
			$this->nuevo=false;
			$sql="SELECT id_modo FROM me_modo_causa WHERE id_causa=".$this->id_causa;
			$res=mysql_query($sql);
			while($row=@mysql_fetch_row($res)){
				$this->modos[]=$row[0];
			}
		}
	}
	
	
	
	
	// guardar
	
	function guardar(){
		if($this->nuevo){
			$sql="INSERT INTO me_causas (id_causa,codigo,nombre,id_detectabilidad,accion) VALUES ($this->id_causa,'$this->codigo','$this->nombre','$this->id_detectabilidad','$this->accion')";
			if(!existeValorTabla("me_causas","codigo",$this->codigo)) {
				if($res=mysql_query($sql)) $this->guardarModos();
			}else return (_ERROR1);	
		}else{
			$sql="UPDATE me_causas SET nombre='$this->nombre', codigo='$this->codigo', id_detectabilidad='$this->id_detectabilidad', accion='$this->accion' WHERE id_causa=$this->id_causa";
			if(existeValorTabla("me_causas","codigo",$this->codigo) && $this->codigoOrig!=$this->codigo) return (_ERROR1); 
			elseif($res=mysql_query($sql)) $this->guardarModos();
		}
		
	}	
	function guardarModos(){
		$this->eliminarModos();
		foreach($this->modos as $m) mysql_query("INSERT INTO me_modo_causa (id_modo,id_causa) VALUES ($m,".$this->id_causa.")");
	}
	
	
	
	
	
	
	// eliminar
	
	function eliminar() {
		$sql="DELETE FROM me_causas WHERE id_causa=".$this->id_causa;
		@mysql_query($sql);
		$this->eliminarModos();
	}
	function eliminarModos(){
		$sql="DELETE FROM me_modo_causa WHERE id_causa=".$this->id_causa;
		@mysql_query($sql);
	}
	
	
	
	
	
	
	// Funciones para los modos
	
	function agregarModos($arM){
		if(is_array($arM)){
			if(count($this->modos)>0) $this->modos = array_merge($this->modos,$arM);
			else $this->modos=$arM;			
		}
		else $this->modos[]=$arM;
	}
	function quitarModos($arM) {
		$ops = "#".implode("##",$this->modos)."#";			
		if(is_array($arM)) foreach($arM as $v)	$ops=str_replace("#".$v."#","",$ops);
		else $ops=str_replace("#".$arM."#","",$ops);
		$aux = str_replace("#","",str_replace("##","||",$ops));
		$this->modos = explode("||",$aux);
	}	
	function obtenerModos(){
		$sql=	"SELECT m.*,o.nombre as no FROM me_modos m LEFT JOIN ad_ocurrencias o ON m.id_ocurrencia=o.id_ocurrencia ".
					"WHERE m.id_modo IN (".implode(",",$this->modos).") ORDER BY m.nombre";
		$res=mysql_query($sql);
		$i=0;
		$a=Array();
		if($row=@mysql_fetch_array($res)) do{
			$a[$i]["id_modo"]=$row["id_modo"];
			$a[$i]["nombre"]=$row["nombre"];
			$a[$i]["ocurrencia"]=$row["no"];			
			$a[$i++]["codigo"]=$row["codigo"];
		}while($row=mysql_fetch_array($res));
		return $a;
	}
	
	
}
?>