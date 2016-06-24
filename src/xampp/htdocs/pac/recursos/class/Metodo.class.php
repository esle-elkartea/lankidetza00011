<?
class Metodo{
	var $id_metodo;
	var $especificacion="";
	var $evaluacion="";
	var $tam;
	var $fre;
	var $metodo;
	var $nuevo;
	//var $muestra=array();

	function Metodo($id=""){
		if($id==""){
			$this->nuevo=true;
			$this->id_metodo=obtenerSigId("ad_metodos","id_metodo");
		}else{
			$this->nuevo=false;
			$sql="SELECT * FROM ad_metodos WHERE id_metodo=$id";
			$res=mysql_query($sql);
			if($row=mysql_fetch_array($res)){
				do{			
					$this->id_metodo=$id;
					$this->especificacion=$row["especificacion"]."";
					$this->evaluacion=$row["evaluacion"]."";
					$this->metodo=$row["metodo_control"]."";
					$this->tam=$row["tam"]."";
					$this->fre=$row["fre"]."";
				}while($row=@mysql_fetch_array($res));
				//$this->cargaRelaciones();
			}
		}
	}
	function guardar(){
		if($this->nuevo){
			$sql="INSERT INTO ad_metodos VALUES (id_metodo,especificacion,evaluacion,metodo_control,tam,fre) VALUES ".
				 "($this->id_metodo,'$this->especificacion','$this->evaluacion','$this->metodo','$this->tam','$this->fre')";
		}else{
			$sql="UPDATE ad_metodos SET especificacion='".$this->especificacion."',evaluacion='".$this->evaluacion."', ".
				 "metodo_control='".$this->metodo."',tam='".$this->tam."',fre='".$this->fre."' ".
				 "WHERE id_metodo=".$this->id_metodo;
		}	
		$res=mysql_query($sql);
		//echo "<br>".$sql;
	}
	function eliminar(){
		$this->eliminarRelaciones();
		$sql="DELETE FROM ad_metodos WHERE id_metodo=$this->id_metodo";
		mysql_query($sql);	
		
	}
	
	
	
	/*
	function cargaRelaciones(){
		$sql="SELECT m.* FROM ad_metodo_muestra mm ".
			 "LEFT JOIN ad_muestras m ON m.id_muestra=mm.id_muestra ".
			 "WHERE id_metodo=$this->id_metodo	";
		$res=mysql_query($sql);
		if($row=mysql_fetch_row($res)){
			$i=0;
			do{
				$this->muestras[$i]["f"]=$row[2];
				$this->muestras[$i++]["t"]=$row[1];
			}while($row=mysql_fetch_row($res));
		}
	}*/
	
		/*
	function guardarRelaciones(){
		$this->eliminarRelaciones();
		if(count($this->muestras)>0){
			$idMuestra=obtenerSigId("ad_metodo_muestra","id_muestra");
			$i=0;
			foreach($this->muestras	as $m){
				$sql="INSERT INTO ad_metodo_muestra (id_muestra,tam,fre) VALUES ($idMuestra,".$m["t"].",".$m["f"].")";
				echo "<br>".$sql;
				$i++;
			}
	}
	*/
		
	/*function eliminarRelaciones(){
		if(!$this->nuevo){
			$sql="DELETE FROM ad_metodo_muestra WHERE id_muestra=".$this->id_muestra;
			$res=mysql_query($sql);
		}
	}*/
}