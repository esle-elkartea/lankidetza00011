<?
class Detectabilidad {
		
	var $id_detectabilidad;
	var $nombre="";
	var $controles="";
	var $valor=0;
	var $nuevo;
	
	function Detectabilidad ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_detectabilidad) is null,1,Max(id_detectabilidad)+1) FROM ad_detectabilidades ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_detectabilidad=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_detectabilidades WHERE id_detectabilidad=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_detectabilidad=$row["id_detectabilidad"];	
			$this->nombre=$row["nombre"];
			$this->valor=$row["valor"];
			$this->controles=$row["controles"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_detectabilidades (id_detectabilidad,nombre,valor,controles) ".
							  "VALUES ($this->id_detectabilidad,'$this->nombre','$this->valor','$this->controles')";
		else $sql="UPDATE ad_detectabilidades SET nombre='$this->nombre', valor='$this->valor', controles='$this->controles' ".
				  "WHERE id_detectabilidad=$this->id_detectabilidad";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_detectabilidades WHERE id_detectabilidad=".$this->id_detectabilidad;
		@mysql_query($sql);
		$sql="UPDATE me_causas SET id_detectabilidad=0 WHERE id_detectabilidad=".$this->id_detectabilidad;
		@mysql_query($sql);
	}
	
}
?>