<?
define("_ERROR1","alert('Existe otro modo con el mismo código');");

class Modo {
	
	var $id_modo;
	var $nombre="";
	var $codigo="";
	var $codigoOrig="";
	var $id_ocurrencia="";
	var $efectos=Array();
	var $causas=Array();
	var $nuevo;
	
		
	
	// carga del modo 	
	
	function Modo ($id=""){		
		if($id==""){		
			$sql="SELECT if(Max(id_modo) is null,1,Max(id_modo)+1) FROM me_modos ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_modo=$row[0];
			$this->nuevo=true;
		}else{
			$sql="SELECT * FROM me_modos WHERE id_modo=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_modo=$row["id_modo"];	
			$this->nombre=$row["nombre"];
			$this->codigo=$row["codigo"];
			$this->codigoOrig=$this->codigo;
			$this->id_ocurrencia=$row["id_ocurrencia"];
			$this->nuevo=false;
			$this->cargaEfectos();
			$this->cargaCausas();
		}
	}
	function cargaEfectos(){
		$sql="SELECT id_efecto FROM me_modo_efecto WHERE id_modo=".$this->id_modo;
		$res=mysql_query($sql);
		if($row=mysql_fetch_row($res)) do{
			$this->efectos[]=$row[0];
		}while($row=mysql_fetch_row($res));
	}
	function cargaCausas(){
		$sql="SELECT id_causa FROM me_modo_causa WHERE id_modo=".$this->id_modo;
		$res=mysql_query($sql);
		if($row=mysql_fetch_row($res)) do{
			$this->causas[]=$row[0];
		}while($row=mysql_fetch_row($res));
	}
	
	
	
	
	// guardar modo	
	
	function guardar (){		
		if($this->nuevo){
			$sql="INSERT INTO me_modos (id_modo,nombre,codigo,id_ocurrencia) VALUES ($this->id_modo,'$this->nombre','$this->codigo','$this->id_ocurrencia')";
			if(!existeValorTabla("me_modos","codigo",$this->codigo)) $res=mysql_query($sql);
			else return (_ERROR1);	
		}else{
			$sql="UPDATE me_modos SET nombre='$this->nombre', codigo='$this->codigo', id_ocurrencia='$this->id_ocurrencia' WHERE id_modo=$this->id_modo";
			if(existeValorTabla("me_modos","codigo",$this->codigo) && $this->codigoOrig!=$this->codigo) return (_ERROR1); 
			else $res=mysql_query($sql);		
		}
		$this->guardaCausas();
		$this->guardaEfectos();
	}
	
	
	
	
	// eliminar modo
		
	function eliminar () {
		$sql="DELETE FROM me_modos WHERE id_modo=".$this->id_modo;
		@mysql_query($sql);
		$sql="DELETE FROM me_operacion_modo WHERE id_modo=".$this->id_modo;
		@mysql_query($sql);
		$this->eliminaEfectos();
		$this->eliminaCausas();		
	}
	
	
	
	
	// efectos
	
	function agregarEfectos($arE) {
		if(is_array($arE)) {
			if(count($this->efectos)>0) $this->efectos = array_merge($this->efectos,$arE);
			else $this->efectos=$arE;
		}
		else $this->efectos[]=$arE;
	}	
	function quitarEfectos($arE) {
		$ops = "#".implode("##",$this->efectos)."#";			
		if(is_array($arE)) foreach($arE as $v)	$ops=str_replace("#".$v."#","",$ops);
		else $ops=str_replace("#".$arE."#","",$ops);
		$aux = str_replace("#","",str_replace("##","||",$ops));
		$this->efectos = explode("||",$aux);
	}
	function guardaEfectos(){
		$this->eliminaEfectos();
		foreach($this->efectos as $v) mysql_query("INSERT INTO me_modo_efecto (id_modo,id_efecto) VALUES (".$this->id_modo.",$v)");
	}
	function eliminaEfectos(){
		$sql="DELETE FROM me_modo_efecto WHERE id_modo=".$this->id_modo;
		@mysql_query($sql);
	}
	function obtenerEfectos(){
		$sql=	"SELECT e.*,g.nombre as gn FROM me_efectos e LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad ".
				"WHERE e.id_efecto IN (".implode(",",$this->efectos).")";
		$res=mysql_query($sql);
		$i=0;
		$a=Array();
		if($row=@mysql_fetch_array($res)) do{
			$a[$i]["id_efecto"]=$row["id_efecto"];
			$a[$i]["nombre"]=$row["nombre"];
			$a[$i++]["gravedad"]=$row["gn"];
		}while($row=mysql_fetch_array($res));
		return $a;
	}	
	
	
	
	
	
	// causas	
	
	function agregarCausas($arC) {
		if(is_array($arC))  
			if(count($this->causas)>0) $this->causas = array_merge($this->causas,$arC);
			else $this->causas=$arC;
		else $this->causas[]=$arC;
	}	
	function quitarCausas($arC) {
		$ops = "#".implode("##",$this->causas)."#";			
		if(is_array($arC)) foreach($arC as $v)	$ops=str_replace("#".$v."#","",$ops);
		else $ops=str_replace("#".$arC."#","",$ops);
		$aux = str_replace("#","",str_replace("##","||",$ops));
		$this->causas = explode("||",$aux);
	}
	function guardaCausas(){
		$this->eliminaCausas();
		foreach($this->causas as $v) mysql_query("INSERT INTO me_modo_causa (id_modo,id_causa) VALUES (".$this->id_modo.",$v)");
	}	
	function eliminaCausas(){
		$sql="DELETE FROM me_modo_causa WHERE id_modo=".$this->id_modo;
		@mysql_query($sql);
	}		
	function obtenerCausas(){
		$sql=	"SELECT c.*,d.nombre as nn FROM me_causas c LEFT JOIN ad_detectabilidades d ON d.id_detectabilidad=c.id_detectabilidad ".
				"WHERE c.id_causa IN (".implode(",",$this->causas).")";
		$res=@mysql_query($sql);
		$i=0;
		$a=Array();
		if($row=@mysql_fetch_array($res)) do{
			$a[$i]["id_causa"]=$row["id_causa"];
			$a[$i]["nombre"]=$row["nombre"];
			$a[$i++]["detectabilidad"]=$row["nn"];
		}while($row=mysql_fetch_array($res));
		return $a;
	}
}
?>