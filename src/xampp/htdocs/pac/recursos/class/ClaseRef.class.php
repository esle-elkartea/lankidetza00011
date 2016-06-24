<?
class ClaseRef {
		
	var $id_clase;
	var $nombre="";
	var $nuevo;
	
	function ClaseRef ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_clase) is null,1,Max(id_clase)+1) FROM ad_clases_ref ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_clase=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_clases_ref WHERE id_clase=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_clase=$row["id_clase"];	
			$this->nombre=$row["nombre"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_clases_ref (id_clase,nombre) VALUES ($this->id_clase,'$this->nombre')";
		else $sql="UPDATE ad_clases_ref SET nombre='$this->nombre' WHERE id_clase=$this->id_clase";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_clases_ref WHERE id_clase=".$this->id_clase;
		@mysql_query($sql);
		$sql="UPDATE me_referencias SET id_clase=0 WHERE id_clase=".$this->id_clase;
		@mysql_query($sql);
	}
	
}
?>