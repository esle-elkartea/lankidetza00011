<?
define("_ERROR1","alert('Existe otro efecto con el mismo código');");

class Efecto {
	
	var $id_efecto;
	var $nombre="";
	var $codigo="";
	var $codigoOrig="";
	var $id_gravedad="";
	var $modos=Array();
	var $nuevo;
	
	
	
	// carga del efecto
	
	function Efecto($id=""){		
		if($id==""){
			$sql="SELECT if(Max(id_efecto) is null,1,Max(id_efecto)+1) FROM me_efectos ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_efecto=$row[0];
			$this->nuevo=true;		
		}else{
			$sql="SELECT * FROM me_efectos WHERE id_efecto=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_efecto=$row["id_efecto"];	
			$this->nombre=$row["nombre"];
			$this->codigo=$row["codigo"];
			$this->codigoOrig=$row["codigo"];
			$this->id_gravedad=$row["id_gravedad"];
			$this->nuevo=false;
			$sql="SELECT id_modo FROM me_modo_efecto WHERE id_efecto=".$this->id_efecto;
			$res=mysql_query($sql);
			while($row=@mysql_fetch_row($res)){
				$this->modos[]=$row[0];
			}			
		}		
	}	
	
	
	
	// guardar el efecto
	
	function guardar (){
		if($this->nuevo){
			$sql="INSERT INTO me_efectos (id_efecto,codigo,nombre,id_gravedad) VALUES ($this->id_efecto,'$this->codigo','$this->nombre','$this->id_gravedad')";
			if(!existeValorTabla("me_efectos","codigo",$this->codigo)) {
				if($res=mysql_query($sql)) $this->guardarModos();
			}else return (_ERROR1);	
		}else{
			$sql="UPDATE me_efectos SET nombre='$this->nombre', codigo='$this->codigo', id_gravedad='$this->id_gravedad' WHERE id_efecto=$this->id_efecto";		
			if(existeValorTabla("me_efectos","codigo",$this->codigo) && $this->codigoOrig!=$this->codigo) return (_ERROR1); 
			elseif($res=mysql_query($sql)) $this->guardarModos();
		}
	}	
	
		
		
	// eliminar efecto
	
	function eliminar () {
		$sql="DELETE FROM me_efectos WHERE id_efecto=".$this->id_efecto;
		@mysql_query($sql);
		$this->eliminarModos();
	}
	
	
	
	// Funciones para los modos
	
	function agregarModos($arM) {
		if(is_array($arM)) {
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
	function guardarModos(){
		$this->eliminarModos();
		foreach($this->modos as $m) mysql_query("INSERT INTO me_modo_efecto (id_modo,id_efecto) VALUES ($m,".$this->id_efecto.")");
	}
	function eliminarModos(){
		$sql="DELETE FROM me_modo_efecto WHERE id_efecto=".$this->id_efecto;
		@mysql_query($sql);
	}	
	function obtenerModos(){
		$sql=	"SELECT m.*,o.nombre as no FROM me_modos m LEFT JOIN ad_ocurrencias o ON m.id_ocurrencia=o.id_ocurrencia ".
					"WHERE m.id_modo IN (".implode(",",$this->modos).")";
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