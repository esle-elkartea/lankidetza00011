<?
define("_ERROR1","alert('Existe otro usuario con la misma clave');");

class Usuario {
	
	var $id_usuario;
	var $nombre="";
	var $apellidos="";
	var $clave="";
	var $claveOrig="";
	var $password="";
	var $rol="1";
	var $baja="0";
	var $nuevo;
	
	function Usuario ($id=""){		
		if($id==""){				
			$sql="SELECT if(Max(id_usuario) is null,1,Max(id_usuario)+1) FROM usuarios ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_usuario=$row[0];
			$this->nuevo=true;					
		} else {		
			$sql="SELECT * FROM usuarios WHERE id_usuario=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_usuario=$row["id_usuario"];	
			$this->nombre=$row["nombre"];
			$this->apellidos=$row["apellidos"];
			$this->clave=$row["clave"];
			$this->claveOrig=$this->clave;
			$this->password=$row["password"];
			$this->rol=$row["rol"];
			$this->baja=$row["baja"];
			$this->nuevo=false;			
		}		
	}	
	function guardar (){		
		if($this->nuevo){
			$sql=	"INSERT INTO usuarios (id_usuario,nombre,apellidos,clave,password,rol,baja) VALUES ".
					 	"('$this->id_usuario', '$this->nombre', '$this->apellidos', '$this->clave', '$this->password', '$this->rol', '$this->baja' )";
			if(!existeValorTabla("usuarios","clave",$this->clave)) $res=mysql_query($sql);
			else return (_ERROR1);		 	
					 	
		}else{
			$sql=	"UPDATE usuarios SET nombre='$this->nombre', apellidos='$this->apellidos', clave='$this->clave', ".
						"password='$this->password', baja='$this->baja', rol='$this->rol' WHERE ".
						"id_usuario=$this->id_usuario";		
			if(existeValorTabla("usuarios","clave",$this->clave) && $this->claveOrig!=$this->clave) return (_ERROR1); 
			else $res=mysql_query($sql);						
		}
	}	
	function darDeBaja () {
		$sql="UPDATE usuarios SET baja=1 WHERE id_usuario=".$this->id_usuario;
		mysql_query($sql);
	}	
	function darDeAlta () {
		$sql="UPDATE usuarios SET baja=0 WHERE id_usuario=".$this->id_usuario;
		mysql_query($sql);
	}	
	function eliminar () {
		$sql="DELETE FROM usuarios WHERE id_usuario=".$this->id_usuario;
		mysql_query($sql);
	}	
}
?>