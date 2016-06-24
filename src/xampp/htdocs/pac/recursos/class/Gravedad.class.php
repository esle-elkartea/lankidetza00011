<?
class Gravedad {
		
	var $id_gravedad;
	var $nombre="";
	var $valor=0;
	var $nuevo;
	
	function Gravedad ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_gravedad) is null,1,Max(id_gravedad)+1) FROM ad_gravedades ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_gravedad=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_gravedades WHERE id_gravedad=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_gravedad=$row["id_gravedad"];	
			$this->nombre=$row["nombre"];
			$this->valor=$row["valor"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_gravedades (id_gravedad,nombre,valor) VALUES ($this->id_gravedad,'$this->nombre','$this->valor')";
		else $sql="UPDATE ad_gravedades SET nombre='$this->nombre', valor='$this->valor' WHERE id_gravedad=$this->id_gravedad";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_gravedades WHERE id_gravedad=".$this->id_gravedad;
		@mysql_query($sql);
		$sql="UPDATE me_efectos SET id_gravedad=0 WHERE id_gravedad=".$this->id_gravedad;
		@mysql_query($sql);
	}
	
}
?>