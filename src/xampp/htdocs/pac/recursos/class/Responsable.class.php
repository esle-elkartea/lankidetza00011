<?

class Responsable {
		
	var $id_maquina;
	var $nombre='';
	var $apellidos='';
	
	function Responsable ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_responsable) is null,1,Max(id_responsable)+1) FROM ad_responsables ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_responsable=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_responsables WHERE id_responsable=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_responsable=$row["id_responsable"];	
			$this->nombre=$row["nombre"];
			$this->apellidos=$row["apellidos"];
			$this->nuevo=false;
		}		
	}
	
	function guardar(){		
		if($this->nuevo) {
			$sql="INSERT INTO ad_responsables (id_responsable,nombre,apellidos) VALUES ".
				 "($this->id_responsable,'$this->nombre','$this->apellidos')";
			$res=mysql_query($sql);
		}else {
			$sql="UPDATE ad_responsables SET nombre='".txtParaGuardar($this->nombre)."', apellidos='".txtParaGuardar($this->apellidos)."' ".
				 "WHERE id_responsable=$this->id_responsable";
			$res=mysql_query($sql);		
		}			
	}
	
	function eliminar () {
		$sql="DELETE FROM ad_responsables WHERE id_responsable=".$this->id_responsable;
		@mysql_query($sql);
	}
	function getNombre($id){
		$res=mysql_query("SELECT * FROM ad_responsables WHERE id_responsable = $id ");
		if($id!=""){
			$row=mysql_fetch_row($res);
			return $row[1]." ".$row[2];
		}
	}
}
?>