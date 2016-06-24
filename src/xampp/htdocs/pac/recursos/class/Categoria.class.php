<?
class Categoria {
		
	var $id_categoria;
	var $nombre="";
	var $nuevo;
	
	function Categoria ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_categoria) is null,1,Max(id_categoria)+1) FROM ad_categorias ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_categoria=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_categorias WHERE id_categoria=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_categoria=$id;	
			$this->nombre=$row["nombre"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_categorias (id_categoria,nombre) VALUES ($this->id_categoria,'$this->nombre')";
		else $sql="UPDATE ad_categorias SET nombre='$this->nombre' WHERE id_categoria=$this->id_categoria";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_categorias WHERE id_categoria=".$this->id_categoria;
		@mysql_query($sql);
		$sql="UPDATE ad_actividades SET id_categoria=0 WHERE id_categoria=".$this->id_categoria;
		@mysql_query($sql);
	}
	
	
	
}
?>