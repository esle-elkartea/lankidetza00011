<?
class Pregunta {
		
	var $id_pregunta;
	var $nombre="";
	var $orden="";
	var $nuevo;
	
	function Pregunta ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_pregunta) is null,1,Max(id_pregunta)+1) FROM ad_preguntas ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_pregunta=$row[0];
			$this->nuevo=true;	
			$sql="SELECT if(Max(orden) is null,1,Max(orden)+1) FROM ad_preguntas ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->orden=$row[0];			
		} else {		
			$sql="SELECT * FROM ad_preguntas WHERE id_pregunta=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_pregunta=$row["id_pregunta"];	
			$this->nombre=$row["nombre"];
			$this->orden=$row["orden"];
			$this->nuevo=false;
		}		
	}
	
	function guardar ($pos="0"){	
		$sumarUno=false;	
		$restarUno=false;
		if($this->nuevo){		
			if($pos=="1" || $pos=="0"){ // la metemos la última
				$sql="SELECT if(Max(orden) is null,1,Max(orden)+1) FROM ad_preguntas ";
				$res=mysql_query($sql);
				$row=mysql_fetch_row($res);
				$ord=$row[0];
			}elseif($pos=="-1"){ // la introducimios primera
				$sumarUno=true;
				$ord="0";
			}	
			$sql="INSERT INTO ad_preguntas (id_pregunta,nombre,orden) VALUES ($this->id_pregunta,'$this->nombre',$ord)";
			if($res=mysql_query($sql) && $sumarUno){
				mysql_query("UPDATE ad_preguntas SET orden=orden+1 WHERE id_pregunta!=".$this->id_pregunta);	
			}			
		}else{
			if($pos=="1"){ // la colocamos la última
				$sql="SELECT if(Max(orden) is null,1,Max(orden)+1) FROM ad_preguntas ";
				$res=mysql_query($sql);
				$row=mysql_fetch_row($res);
				$ord=$row[0];
				$restarUno=true;
			}elseif($pos=="-1"){ // la colocamos la primera
				$sumarUno=true;
				$ord="0";
			}else $ord=$this->orden; // la dejamos donde está				
			$sql="UPDATE ad_preguntas SET nombre='$this->nombre',orden=$ord WHERE id_pregunta=$this->id_pregunta";
			if($res=mysql_query($sql)){
				if($sumarUno){
					mysql_query("UPDATE ad_preguntas SET orden=orden+1 WHERE id_pregunta!=$this->id_pregunta");
				}elseif($restarUno){
					mysql_query("UPDATE ad_preguntas SET orden=orden-1 WHERE id_pregunta!=$this->id_pregunta AND orden<$this->orden");
				}
			}
		}
		//echo $sql;		
	}
	
	function eliminar(){
		$sql="DELETE FROM ad_preguntas WHERE id_pregunta=".$this->id_pregunta;
		if($res=mysql_query($sql))	mysql_query("UPDATE ad_preguntas SET orden=orden-1 WHERE orden>$this->orden");
	}
	function subirEnLista(){
		if($this->orden>1 && !$this->nuevo){			
			mysql_query("UPDATE ad_preguntas SET orden=orden+1 WHERE orden=".(($this->orden)-1));
			mysql_query("UPDATE ad_preguntas SET orden=orden-1 WHERE id_pregunta=".$this->id_pregunta);
		}
	}
	function bajarEnLista(){
		$res=mysql_query("SELECT MAX(orden) FROM ad_preguntas");
		$row=mysql_fetch_row($res);
		if($row[0]!=$this->orden){
			mysql_query("UPDATE ad_preguntas SET orden=orden-1 WHERE orden=".(($this->orden)+1));
			mysql_query("UPDATE ad_preguntas SET orden=orden+1 WHERE id_pregunta=".$this->id_pregunta);
		}
	}
	
}
?>