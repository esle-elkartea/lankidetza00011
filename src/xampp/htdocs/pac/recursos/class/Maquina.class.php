<?
define("_ERROR1","alert('Existe otra máquina con el mismo código');");

class Maquina {
		
	var $id_maquina;
	var $codigo=0;
	var $codigoOrig=0;
	var $nombre="";
	var $id_clase="0";
	var $nuevo;
	
	function Maquina ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_maquina) is null,1,Max(id_maquina)+1) FROM ad_maquinas ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_maquina=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_maquinas WHERE id_maquina=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_maquina=$row["id_maquina"];	
			$this->codigo=$row["codigo"];
			$this->codigoOrig=$this->codigo;
			$this->nombre=$row["nombre"];
			$this->id_clase=$row["id_clase"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) {
			$sql="INSERT INTO ad_maquinas (id_maquina,codigo,nombre,id_clase) VALUES ".
				 "($this->id_maquina,'$this->codigo','$this->nombre','$this->id_clase')";
			if(!existeValorTabla("ad_maquinas","codigo",$this->codigo)) $res=mysql_query($sql);
			else return (_ERROR1);	
		}else {
			$sql="UPDATE ad_maquinas SET nombre='$this->nombre', codigo='$this->codigo', id_clase='$this->id_clase' ".
				 "WHERE id_maquina=$this->id_maquina";
			if(existeValorTabla("ad_maquinas","codigo",$this->codigo) && $this->codigoOrig!=$this->codigo) return (_ERROR1); 
			else $res=mysql_query($sql);		
		}			
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_maquinas WHERE id_maquina=".$this->id_maquina;
		@mysql_query($sql);
		$sql="UPDATE me_operaciones SET id_maquina=0 WHERE id_maquina=".$this->id_maquina;
		@mysql_query($sql);
	}
	function getNombre($id){
		$res=mysql_query("SELECT nombre FROM ad_maquinas WHERE id_maquina=$id");
		if($row=@mysql_fetch_row($res)) return $row[0];
	}
	
	
}
?>