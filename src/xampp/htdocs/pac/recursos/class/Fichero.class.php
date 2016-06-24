<?

 
class Fichero {
	
	var $fichero;
	var $tipo="";
	var $carpetaDestino="";
	var $rutaArchivo="";
	
	//estas vars se asignan desde fuera
	var $nombre="";
	
	function Fichero ($f,$t="ImagenDeClase"){	
		global $app_rutaARCHIVOS;
		global $app_carpetaImagenesClases;
		
		
		$this->fichero=$f;	
		$this->tipo=$t;		
		
		//compongo la ruta donde será guardado el archivo según el tipo
		$this->carpetaDestino=$app_rutaARCHIVOS;
		if($t=="ImagenDeClase") $this->carpetaDestino.=$app_carpetaImagenesClases;
	}
	
	function guardar ($carpeta=""){
		global $app_rutaARCHIVOS;
		global $app_carpetaImagenesClases;
		global $app_minirutaARCHIVOS;
		
		
		//creo las carpetas necesarias
		if (!is_dir($app_rutaARCHIVOS)) 
			mkdir($app_rutaARCHIVOS, 0777);		
			
		if (!is_dir($this->carpetaDestino)) 
			mkdir($this->carpetaDestino, 0777);
			
		if($carpeta!="" && !is_dir($this->carpetaDestino."/".$carpeta))
			mkdir($this->carpetaDestino."/".$carpeta); //si hay carpeta adicional
				
				
		//copio y si ok se establece la ruta definitiva para posteriormente ser guardada en la clase o en lo que sea	
		$rutaCompleta = $this->carpetaDestino."/".($carpeta==""?"":$carpeta."/").$this->nombre;
		if(copy($this->fichero,$rutaCompleta)){
			$this->rutaArchivo=$app_minirutaARCHIVOS."/".($carpeta==""?"":$carpeta."/").$this->nombre;
		}
	
	
		
	}
	
	function eliminar () {
		
	}
	
	function setNombre($n) { $this->nombre=$n; }
	
	
	
}
?>