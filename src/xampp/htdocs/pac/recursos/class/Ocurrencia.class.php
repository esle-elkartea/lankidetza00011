<?
class ocurrencia {
		
	var $id_ocurrencia;
	var $nombre="";
	var $valor="";
	var $nuevo;
	
	function ocurrencia ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_ocurrencia) is null,1,Max(id_ocurrencia)+1) FROM ad_ocurrencias ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_ocurrencia=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_ocurrencias WHERE id_ocurrencia=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_ocurrencia=$row["id_ocurrencia"];	
			$this->nombre=$row["nombre"];
			$this->valor=$row["valor"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_ocurrencias (id_ocurrencia,nombre,valor) VALUES ($this->id_ocurrencia,'$this->nombre','$this->valor')";
		else $sql="UPDATE ad_ocurrencias SET nombre='$this->nombre', valor='$this->valor' WHERE id_ocurrencia=$this->id_ocurrencia";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_ocurrencias WHERE id_ocurrencia=".$this->id_ocurrencia;
		@mysql_query($sql);
		$sql="UPDATE me_modos SET id_ocurrencia=0 WHERE id_ocurrencia=".$this->id_ocurrencia;
		@mysql_query($sql);
	}
	
}
?>