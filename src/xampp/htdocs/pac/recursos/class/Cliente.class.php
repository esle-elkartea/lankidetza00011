<?
include_once "../recursos/class/Planificacion.class.php";
define("_ERROR1","alert('Existe un registro con el mismo número');");
define("_ERROR2","alert('Existen planificaciones relacionadas con el cliente');");

class Cliente {
	
	var $id_cliente;
	var $nombre="";
	var $num="";
	var $numOrig="";
	var $npr="";
	var $num_proveedor="";
	var $nuevo;
	
	function Cliente ($id=""){
		
		if($id==""){
			$sql="SELECT if(Max(id_cliente) is null,1,Max(id_cliente)+1) FROM me_clientes ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_cliente=$row[0];
			$this->nuevo=true;
		}else{
			$sql="SELECT * FROM me_clientes WHERE id_cliente=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_cliente=$row["id_cliente"];	
			$this->nombre=$row["nombre"];
			$this->num=$row["num"];
			$this->numOrig=$this->num;
			$this->npr=$row["npr"];
			$this->num_proveedor=$row["num_proveedor"];
			$this->nuevo=false;
		}
	}
	
	function guardar (){
		if($this->nuevo){
			$sql="INSERT INTO me_clientes (id_cliente,num,nombre,npr,num_proveedor) VALUES ".
			"('$this->id_cliente','$this->num','$this->nombre','$this->npr','$this->num_proveedor')";
			if(!existeValorTabla("me_clientes","num",$this->num)) $res=mysql_query($sql);
			else return (_ERROR1);
		}else{
			$sql="UPDATE me_clientes SET num='$this->num', nombre='$this->nombre', npr='$this->npr', ".
			"num_proveedor='$this->num_proveedor' WHERE id_cliente=$this->id_cliente";
			if(existeValorTabla("me_clientes","num",$this->num) && $this->numOrig!=$this->num) return (_ERROR1); 
			else $res=mysql_query($sql);
		}
	}
	
	function eliminar () {
		$res=mysql_query("SELECT count(*) FROM planificaciones WHERE id_cliente=$this->id_cliente");
		$row=mysql_fetch_row($res);
		if($row[0]=="0"){
			$sql="DELETE FROM me_clientes WHERE id_cliente=".$this->id_cliente;
			$res=mysql_query($sql);
		}else return (_ERROR2);
	}
	function crearPlanificacion($idRef){
		$this->guardar();
		$plani=new Planificacion();
		$plani->fecha=date("Y-m-d");
		$plani->id_referencia=$idRef;
		$plani->id_cliente=$this->id_cliente;
		$plani->codigo=0;
		$plani->cargaRelacionesMaestros();
		$plani->guardar(false);
		return "alert('La planificación ha sido creada');";
	}
}
?>