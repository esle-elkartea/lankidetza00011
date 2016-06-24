<?
class Actividad {
		
	var $id_actividad;
	var $nombre="";
	var $principal=0;
	var $principalAntes;
	var $orden=0;
	var $id_categoria=0;
	var $nuevo;
	
	function Actividad ($id="",$pos=""){
		if($pos!=""){
			$sql="SELECT * FROM ad_actividades WHERE principal=1 AND orden=".$pos;
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_actividad=$row["id_actividad"];	
			$this->nombre=$row["nombre"];
			$this->principal=$row["principal"];
			$this->principalAntes=$row["principal"];
			$this->orden=$row["orden"];
			$this->id_categoria=$row["id_categoria"];
			$this->nuevo=false;			
		}else{		
			if($id==""){						
				$sql="SELECT if(Max(id_actividad) is null,1,Max(id_actividad)+1) FROM ad_actividades ";
				$res=mysql_query($sql);
				$row=mysql_fetch_row($res);
				$this->id_actividad=$row[0];
				$this->nuevo=true;				
			} else {		
				$sql="SELECT * FROM ad_actividades WHERE id_actividad=$id";
				$res=mysql_query($sql);			
				$row=mysql_fetch_array($res);
				$this->id_actividad=$row["id_actividad"];	
				$this->nombre=$row["nombre"];
				$this->principal=$row["principal"];
				$this->principalAntes=$row["principal"];
				$this->orden=$row["orden"];
				$this->id_categoria=$row["id_categoria"];
				$this->nuevo=false;
			}
		}		
	}
	function guardar($pos=""){
		$sumoUno=false;
		$restoUno=false;		
		if($this->nuevo){
			// NUEVA ACTIVIDAD
			if($this->principal){				
				if($pos=="1"){ // última
					$res=mysql_query("SELECT if(Max(orden) is null,1,Max(orden)+1) FROM ad_actividades WHERE principal=1");
					$row=mysql_fetch_row($res);
					$ord=$row[0];				
				}else{ // primera
					$ord="0";
					$sumoUno=true;
				}
			}else $ord="0";	
			$sql="INSERT INTO ad_actividades (id_actividad,nombre,principal,orden,id_categoria) VALUES ".
				 "($this->id_actividad,'$this->nombre','$this->principal','$ord','$this->id_categoria')";
			if($res=mysql_query($sql) && $sumoUno){
				$sql="UPDATE ad_actividades SET orden=orden+1 WHERE principal=1 AND id_actividad!=".$this->id_actividad;
				$res=mysql_query($sql);
			}
		}else{
			// ACTIVIDAD EXISTENTE
			if($this->principalAntes=="0"){
				if($this->principal=="1"){
					if($pos=="1"){ // última
						$res=mysql_query("SELECT if(Max(orden) is null,1,Max(orden)+1) FROM ad_actividades WHERE principal=1");
						$row=mysql_fetch_row($res);	
						$ord=$row[0];
					}else{ // primera
						$ord="0";
						$sumoUno=true;
					}					
				}else $ord="0";
			}elseif($this->principalAntes=="1"){
				if($this->principal=="1"){
					if($pos=="1"){ // última
						$res=mysql_query("SELECT if(Max(orden) is null,1,Max(orden)+1) FROM ad_actividades WHERE principal=1");
						$row=mysql_fetch_row($res);	
						$ord=$row[0];
						$restoUno=true;
						$desdePos=$this->orden;
					}elseif($pos=="0") $ord=$this->orden;
					else{ // primera
						$ord="0";
						$sumoUno=true;
					}	
				}else{
					$ord=0;
					$restoUno=true;
					$desdePos=$this->orden;
				}
					
			}
			$sql="UPDATE ad_actividades SET nombre='$this->nombre', principal='$this->principal', orden='$ord', id_categoria='$this->id_categoria' ".
				 "WHERE id_actividad=$this->id_actividad";	
			if($res=mysql_query($sql)){
				if($sumoUno) mysql_query("UPDATE ad_actividades SET orden=orden+1 WHERE id_actividad!=".$this->id_actividad);
				if($restoUno) mysql_query("UPDATE ad_actividades SET orden=orden-1 WHERE orden>".$desdePos);				 	
			}
		}
	}
	function compruebaPlanis(){
		$sql="SELECT count(*) FROM pl_planificacion_actividad WHERE id_actividad=".$this->id_actividad;
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		return $row[0];	
	}
	function eliminar (){
		if($this->compruebaPlanis==0){
			$sql="DELETE FROM ad_actividades WHERE id_actividad=".$this->id_actividad;
			if($res=mysql_query($sql) && $this->principalAntes=="1"){
				$sql="UPDATE ad_actividades SET orden=orden-1 WHERE principal=1 AND orden>".$this->orden;
				$res=mysql_query($sql);	
			}
		}
	}
	function subirOrden($pos){
		if($pos>0){
			$sql="SELECT id_actividad,orden FROM ad_actividades WHERE principal=1 AND orden<".$pos." ORDER BY orden DESC LIMIT 1";
			$res=mysql_query($sql);
			$row=@mysql_fetch_row($res);
			$id=$row[0];
			$ord=$row[1];
			$sql="UPDATE ad_actividades SET orden=".$ord." WHERE principal=1 AND orden=".$pos;
			$res=mysql_query($sql);
			$sql="UPDATE ad_actividades SET orden=$pos WHERE id_actividad=".$id;
			$res=mysql_query($sql);
		}
	}
	function bajarOrden($pos){
		$sql="SELECT id_actividad FROM ad_actividades WHERE principal=1 AND orden=".($pos+1);
		$res=mysql_query($sql);
		if($row=@mysql_fetch_row($res)){
			$id=$row[0];
			$sql="UPDATE ad_actividades SET orden=orden+1 WHERE principal=1 AND orden=".$pos;
			$res=mysql_query($sql);
			$sql="UPDATE ad_actividades SET orden=$pos WHERE id_actividad=".$id;
			$res=mysql_query($sql);
		}
	}	
}
?>