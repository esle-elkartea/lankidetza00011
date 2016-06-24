<?
class familia {
		
	var $id_familia;
	var $nombre="";
	var $nuevo;
	
	function familia ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_familia) is null,1,Max(id_familia)+1) FROM ad_familias ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_familia=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_familias WHERE id_familia=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_familia=$row["id_familia"];	
			$this->nombre=$row["nombre"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_familias (id_familia,nombre) VALUES ($this->id_familia,'$this->nombre')";
		else $sql="UPDATE ad_familias SET nombre='$this->nombre' WHERE id_familia=$this->id_familia";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_familias WHERE id_familia=".$this->id_familia;
		@mysql_query($sql);
		$sql="UPDATE me_referencias SET id_familia=0 WHERE id_familia=".$this->id_familia;
		@mysql_query($sql);
	}
	
}
?>