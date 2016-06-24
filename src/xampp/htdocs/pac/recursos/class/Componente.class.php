<?
include_once "Operacion.class.php";
define("_ERROR1","alert('Existe otro componente con el mismo código');");

class Componente {
	
	var $id_componente;
	var $codigo="";
	var $codigoOrig="";
	var $nombre="";
	var $paraPlani=false;
	var $operaciones=Array();
	var $nuevo;
	
	
	/**********************************************************************************************/
	/* Carga del componente */
	
	function Componente ($id="",$paraPlani=false){		
		$this->paraPlani=$paraPlani;
		if($id==""){			
			$sql="SELECT if(Max(id_componente) is null,1,Max(id_componente)+1) FROM me_componentes ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_componente=$row[0];
			$this->nuevo=true;				
		}else{
			$sql="SELECT * FROM ".($paraPlani?"pl":"me")."_componentes WHERE id_componente=".$id;
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_componente=$row["id_componente"];	
			$this->codigo=$row["codigo"];
			$this->codigoOrig=$this->codigo;
			$this->nombre=$row["nombre"];
			$this->nuevo=false;
			$sql="SELECT id_operacion,orden FROM ".($paraPlani?"pl":"me")."_componente_operacion WHERE id_componente=$id ORDER BY orden asc";
			$res=mysql_query($sql);
			$i=0;
			if($row=mysql_fetch_row($res)) do{ 
				$this->operaciones[$i++]=$row[0];
			}while($row=mysql_fetch_row($res));
		}		
	}
	
	/*******************************************************************************************/
	/* Guarda el componente */
		
	function guardar(){		
		if($this->nuevo){
			$sql="INSERT INTO me_componentes (id_componente,codigo,nombre) VALUES ('$this->id_componente','$this->codigo','$this->nombre')";
			if(!existeValorTabla("me_componentes","codigo",$this->codigo)) {
				if($res=mysql_query($sql)) $this->guardarOperaciones();
			}else return (_ERROR1);			
		}else{
			$sql="UPDATE me_componentes SET codigo='$this->codigo', nombre='$this->nombre' WHERE id_componente=$this->id_componente";				
			if(existeValorTabla("me_componentes","codigo",$this->codigo) && $this->codigoOrig!=$this->codigo) return (_ERROR1); 
			elseif($res=mysql_query($sql)) $this->guardarOperaciones();
		}	
	}
	
	function compruebaPlanis(){
		$sql="SELECT count(*),id_planificacion FROM pl_planificacion_relacion WHERE id_relacion=".$this->id_componente." AND tipo='C' GROUP BY id_planificacion";
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		return $row[0];
	}
	
	/*********************************************************************************************/
	/* Eliminar componente */
	
	function eliminar () {
		if($this->compruebaPlanis()==0){
			$sql="DELETE FROM me_componentes WHERE id_componente=".$this->id_componente;
			@mysql_query($sql);
			$this->eliminaC_O();
		}
	}
	function eliminaC_O(){
		$sql="DELETE FROM me_componente_operacion WHERE id_componente=".$this->id_componente;
		@mysql_query($sql);
		$sql="DELETE FROM me_referencia_relacion WHERE id_relacion=".$this->id_componente." AND tipo='C'";
		@mysql_query($sql);
	}
	
	/********************************************************************************************/
	/* Funciones para el control de las operaciones */
	
	function agregarOperaciones($arOps) {
		if(is_array($arOps)) foreach($arOps as $v) if(array_search($v,$this->operaciones)===false) $this->operaciones[]=$v;
		else if(array_search($arOps,$this->operaciones)===false) $this->operaciones[]=$arOps;
	}	
	function quitarOperaciones($arOps) {
		if(is_array($arOps)) foreach ($arOps as $v) $this->operaciones=quitarDeArray($this->operaciones,array_search($v,$this->operaciones));
		else $this->operaciones=quitarDeArray($this->operaciones,array_search($arOps,$this->operaciones));
	}
	function guardarOperaciones() {
		$this->eliminaC_O();
		for($i=0;$i<count($this->operaciones);$i++){
			$sql = "INSERT INTO me_componente_operacion (id_componente,id_operacion,orden) VALUES ".
				   "(".$this->id_componente.",".$this->operaciones[$i].",".$i.")";
			@mysql_query($sql);
		}
	}
	
	
	/***************************************************************************************/
	/* Copiar estructura del componente */
	
	function copiarEstructura($id){
		$sql="SELECT * FROM me_componentes WHERE id_componente=".$id;
		$res=mysql_query($sql);
		if($row=mysql_fetch_array($res)){
			$this->codigo=$row["codigo"];
			$this->nombre=$row["nombre"]." (Copia)";
		}
		$this->operaciones=Array();
		$sql="SELECT id_operacion FROM me_componente_operacion WHERE id_componente=".$id." ORDER BY orden asc";
		$res=mysql_query($sql);
		if($row=mysql_fetch_row($res)) do{
			$this->operaciones[]=$row[0];
		}while($row=mysql_fetch_array($res));		
	}
	
	
	
	function subeOrden($pos){
		if($pos>0){
			$aux=$this->operaciones[$pos-1];
			$this->operaciones[$pos-1]=$this->operaciones[$pos];
			$this->operaciones[$pos]=$aux;
		}
	}
	function bajaOrden($pos){
		if($pos<count($this->operaciones)-1){
			$aux=$this->operaciones[$pos+1];
			$this->operaciones[$pos+1]=$this->operaciones[$pos];
			$this->operaciones[$pos]=$aux;
		}
	}
	
	function pintarFilaAMFE($soloUno=false){
		if(count($this->operaciones)>0){
			$rowspan=0;
			foreach($this->operaciones as $o){
				$op=new Operacion($o);
				$rowspan+=count($op->modos)==0?"1":count($op->modos);
			}
			$todo="<td rowspan=".$rowspan." class='Fila1'>".txtParaInput($this->nombre)."</td>";
			foreach($this->operaciones as $op){
				$o=new Operacion($op,$this->paraPlani);
				$todo.=$o->pintarFilaAMFE();
			}			
		}
		if($soloUno){
			if($todo!="")
				return 	"".
						"<table width='95%' cellpadding='2' cellspacing='1' class=''><caption>AMFE</caption>".
						pintarCabeceraAMFEMini2().$todo.pintarPieAMFE()."<br>";
			else 
				return "".
				"<table width='95%' cellpadding='2' cellspacing='1' class=''><caption>AMFE</caption>".
				"<tr><td class='TxtBold' colspan=3 align=left>No hay operaciones relacionadas</td></tr>".
				"<tr><td align='left' colspan=3  class='spacer8'><br>&nbsp;</td></tr></table><br>";
			
		}else return $todo;
	}
	function pintarFilaAMFEexportar(){
		if(count($this->operaciones)>0){
			$rowspan=0;
			foreach($this->operaciones as $o){
				$op=new Operacion($o);
				$rowspan+=count($op->modos)==0?"1":count($op->modos);
			}
			$todo="<TR><TD ROWSPAN=".$rowspan.">".txtParaInput($this->nombre)."</TD>";
			foreach($this->operaciones as $op){
				$o=new Operacion($op,$this->paraPlani);
				$todo.=$o->pintarFilaAMFEexportar();
			}
			return $todo;		
		}
	}
}
?>