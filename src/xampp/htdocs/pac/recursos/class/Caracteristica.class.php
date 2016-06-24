<?
class Caracteristica {
		
	var $id_caracteristica;
	var $nombre="";
	var $num="";
	var $prod="";
	var $proc="";
	
	var $especificacion="";
	var $evaluacion="";
	var $metodo="";
	var $tam="";
	var $fre="";
	var $plan="";
	var $id_clase=0;
	
	var $nuevo;
	
	function Caracteristica ($id=""){		
		if($id==""){						
			$sql="SELECT if(Max(id_caracteristica) is null,1,Max(id_caracteristica)+1) FROM ad_caracteristicas ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_caracteristica=$row[0];
			$this->nuevo=true;				
		} else {		
			$sql="SELECT * FROM ad_caracteristicas WHERE id_caracteristica=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_caracteristica=$row["id_caracteristica"];	
			$this->nombre=$row["nombre"];
			$this->num=$row["num"];
			$this->proc=$row["proc"];
			$this->prod=$row["prod"];
			$this->especificacion=$row["especificacion"];
			$this->evaluacion=$row["evaluacion"];
			$this->metodo=$row["metodo"];
			$this->tam=$row["tam"];
			$this->fre=$row["fre"];
			$this->plan=$row["plan"];
			$this->id_clase=$row["id_clase"];
			$this->nuevo=false;
		}		
	}
	
	function guardar (){		
		if($this->nuevo) 
		$sql="INSERT INTO ad_caracteristicas (id_caracteristica,nombre,num,prod,proc,especificacion,evaluacion,metodo,tam,fre,plan,id_clase) ".
			 "VALUES ($this->id_caracteristica,'$this->nombre','$this->num','$this->prod','$this->proc','$this->especificacion',".
			 "'$this->evaluacion','$this->metodo','$this->tam','$this->fre','$this->plan','$this->id_clase')";
		else $sql="UPDATE ad_caracteristicas SET nombre='$this->nombre',prod='$this->prod',proc='$this->proc',num='$this->num',".
			  	  "especificacion='$this->especificacion',evaluacion='$this->evaluacion',".
			  	  "metodo='$this->metodo',tam='$this->tam',fre='$this->fre',plan='$this->plan',id_clase='$this->id_clase' ".
				  "WHERE id_caracteristica=$this->id_caracteristica";		
		$res=mysql_query($sql);
	}
	
	function eliminar (){
		$sql="DELETE FROM ad_caracteristicas WHERE id_caracteristica=".$this->id_caracteristica;
		@mysql_query($sql);
		$sql="DELETE FROM me_operacion_caracteristica WHERE id_caracteristica=".$this->id_caracteristica;
		@mysql_query($sql);
	}
	
}
?>