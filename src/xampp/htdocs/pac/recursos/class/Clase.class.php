<?
class Clase {
		
	var $id_clase;
	var $nombre="";
	var $img="";
	var $nuevo;
	
	function Clase ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_clase) is null,1,Max(id_clase)+1) FROM ad_clases ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_clase=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_clases WHERE id_clase=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_clase=$row["id_clase"];	
			$this->nombre=$row["nombre"];
			$this->img=$row["img"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) $sql="INSERT INTO ad_clases (id_clase,nombre,img) VALUES ($this->id_clase,'$this->nombre','$this->img')";
		else $sql="UPDATE ad_clases SET nombre='$this->nombre', img='$this->img' WHERE id_clase=$this->id_clase";		
		$res=mysql_query($sql);
	}
	
	function eliminar () {
		$this->eliminarCarpeta();
		$sql="DELETE FROM ad_clases WHERE id_clase=".$this->id_clase;
		@mysql_query($sql);		
	}
	
	function eliminarCarpeta(){		
		global $app_rutaARCHIVOS;
		global $app_carpetaImagenesClases;
		$dirEliminar = $app_rutaARCHIVOS.$app_carpetaImagenesClases."/clase[".$this->id_clase."]";
		$files = glob($dirEliminar."/*.*");
		if ($gd = @opendir($dirEliminar)) {
			while (($archivo = readdir($gd)) !== false) {
				if($archivo!="." && $archivo!="..") unlink($dirEliminar."/".$archivo);
			}
			closedir($gd);
		}
		@rmdir($dirEliminar);		
	}
	
}
?>