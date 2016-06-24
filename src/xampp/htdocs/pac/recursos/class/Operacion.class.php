<?
define("_ERROR1","alert('Existe otra operación con el mismo código');");
define("_ERROR2","alert('La operación está relacionada con una o varias planificaciones. No será eliminada.');");
class Operacion {
	
	var $id_operacion;
	var $id_opAlt;
	var $id_maquina;
	var $codigo="";
	var $codigoOrig="";
	var $nombre="";
	var $paraPlani=false;
	var $modos=Array();
	var $caracts=Array();
	var $nuevo;
	
	
	/******************************************************************************/
	/* Carga de la operación */
	
	function Operacion ($id="",$paraPlani=false){
		$this->paraPlani=$paraPlani;	
		if($id==""){						
			$sql="SELECT if(Max(id_operacion) is null,1,Max(id_operacion)+1) FROM me_operaciones ";
			$res=mysql_query($sql);
			$row=mysql_fetch_row($res);
			$this->id_operacion=$row[0];
			$this->nuevo=true;					
		} else {		
			$sql="SELECT * FROM ".($paraPlani?"pl":"me")."_operaciones WHERE id_operacion=$id";
			$res=mysql_query($sql);
			$row=mysql_fetch_array($res);
			$this->id_operacion=$row["id_operacion"];
			$this->id_opAlt=$row["id_opAlt"];	
			$this->nombre=$row["nombre"];
			$this->codigo=$row["codigo"];
			$this->id_maquina=$row["id_maquina"];
			$this->codigoOrig=$this->codigo;
			$this->nuevo=false;
			
			$sql="SELECT id_modo FROM me_operacion_modo WHERE id_operacion=$id";
			$res=mysql_query($sql);
			if($row=mysql_fetch_row($res)) do{
				$this->modos[]=$row[0];
			}while($row=mysql_fetch_row($res));
			
			$sql="SELECT id_caracteristica FROM ".($paraPlani?"pl":"me")."_operacion_caracteristica WHERE id_operacion=$id";
			$res=mysql_query($sql);
			if($row=mysql_fetch_row($res)) do{
				$this->caracts[]=$row[0];
			}while($row=mysql_fetch_row($res));
		}		
	}
	
	
	/****************************************************************/
	/* Guardar operacion */
	
	function guardar (){		
		if($this->nuevo){
			$sql=	"INSERT INTO me_operaciones (id_operacion,codigo,nombre,id_opAlt,id_maquina) VALUES ".
					"($this->id_operacion,".
					"'$this->codigo',".
					"'$this->nombre',".
					"'$this->id_opAlt',".
					"'$this->id_maquina')";	
			if(!existeValorTabla("me_operaciones","codigo",$this->codigo)) $res=mysql_query($sql);
			else return (_ERROR1);	
		}else{
			$sql=	"UPDATE me_operaciones SET ".
					"nombre='$this->nombre', ".
					"codigo='$this->codigo', ".
					"id_opAlt='$this->id_opAlt', ".
					"id_maquina='$this->id_maquina' ".
					"WHERE id_operacion=$this->id_operacion";
			if(existeValorTabla("me_operaciones","codigo",$this->codigo) && $this->codigoOrig!=$this->codigo) return (_ERROR1); 
			else $res=mysql_query($sql);	
		}		
		$this->guardarRelaciones();
	}
	
	function guardarRelaciones(){
		$this->guardarModos();
		$this->guardarCaracteristicas();
	}
	
	
	
	
	/************************************************
	/* Eliminar operacion */
	
	function compruebaPlanis(){
		$sql="SELECT SUM(todo.cuantos) as cuantos FROM ".
		"(SELECT count(*) as cuantos FROM pl_planificacion_relacion pr ".
		"WHERE tipo='O' AND id_relacion=$this->id_operacion ".
		"UNION ALL ".
		"SELECT count(*) as cuantos ".
		"FROM pl_planificacion_relacion pr ".
		"INNER JOIN pl_componente_operacion co ON pr.id_relacion=co.id_componente AND co.id_operacion=$this->id_operacion ".
		"WHERE pr.tipo='C')as todo ";
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		return $row[0];	
	}
	function eliminar(){
		if($this->compruebaPlanis()==0){
			$this->eliminarO_M();
			$this->eliminarC_O();
			$this->eliminarO_C();
			$sql="DELETE FROM me_operaciones WHERE id_operacion=".$this->id_operacion;
			@mysql_query($sql);
		}else return (_ERROR2);
	}
	function eliminarO_M(){
		$sql="DELETE FROM me_operacion_modo WHERE id_operacion=".$this->id_operacion;
		@mysql_query($sql);
	}
	function eliminarC_O(){
		$sql="DELETE FROM me_componente_operacion WHERE id_operacion=".$this->id_operacion;
		@mysql_query($sql);
	}
	function eliminarO_C(){
		$sql="DELETE FROM me_operacion_caracteristica WHERE id_operacion=".$this->id_operacion;
		@mysql_query($sql);
	}
	
	
	/*****************************************************************************/
	/* Modos de la operacion */
	
	function agregarModos($arM) {
		if(is_array($arM)) {
			if(count($this->modos)>0) $this->modos = array_merge($this->modos,$arM);
			else $this->modos=$arM;			
		}
		else $this->modos[]=$arM;
	}	
	
	function quitarModos($arM) {
		$ops = "#".implode("##",$this->modos)."#";			
		if(is_array($arM)) foreach($arM as $v)	$ops=str_replace("#".$v."#","",$ops);
		else $ops=str_replace("#".$arM."#","",$ops);
		$aux = str_replace("#","",str_replace("##","||",$ops));
		$this->modos = explode("||",$aux);
	}
	function guardarModos() {
		$this->eliminarO_M();
		foreach($this->modos as $v){
			$sql = "INSERT INTO me_operacion_modo (id_operacion,id_modo) VALUES (".$this->id_operacion.",$v)";
			@mysql_query($sql);
		}		
	}

	/*****************************************************************************/
	/* Características de la operacion */
	
	function agregarCaracteristicas($arC) {
		if(is_array($arC)) {
			if(count($this->caracts)>0) $this->caracts = array_merge($this->caracts,$arC);
			else $this->caracts=$arC;			
		}
		else $this->caracts[]=$arC;
	}
	function quitarCaracteristicas($arC) {
		$ops = "#".implode("##",$this->caracts)."#";			
		if(is_array($arC)) foreach($arC as $v)	$ops=str_replace("#".$v."#","",$ops);
		else $ops=str_replace("#".$arC."#","",$ops);
		$aux = str_replace("#","",str_replace("##","||",$ops));
		$this->caracts = explode("||",$aux);
	}	
	function guardarCaracteristicas() {
		$this->eliminarO_C();
		foreach($this->caracts as $v){
			$sql = "INSERT INTO me_operacion_caracteristica (id_operacion,id_caracteristica) VALUES (".$this->id_operacion.",$v)";
			@mysql_query($sql);
		}	
	}	
	
	
	
	
	
	
	
	
	
	
	// FUNCION PARA EL PLAN DE CONTROL
	
	function generarplandecontrol(){
		// si no existen máquinas en la base de datos esta select no funciona !!
		$datos=array();
		if(count($this->caracts)>0) $campos=",c.num,c.prod,c.proc,cl.img,c.especificacion,c.evaluacion,c.tam,c.fre,c.metodo,c.plan";
		else $campos=",'','','','','','','','','',''";
		$sql="SELECT ".($this->id_maquina!=""?"m.nombre":"''")." ".$campos." ";
		$sql.="FROM ad_maquinas m ";
		if(count($this->caracts)>0){
			$sql.="LEFT JOIN ad_caracteristicas c ON c.id_caracteristica IN (".implode(",",$this->caracts).") ";
			$sql.="LEFT JOIN ad_clases cl ON cl.id_clase=c.id_clase ";
		}
		if($this->id_maquina!="" && $this->id_maquina!="0") $sql.="WHERE m.id_maquina=".$this->id_maquina." ";
		//echo $sql;
		$res=mysql_query($sql);
		$j=0;
		$nombreOperacion="";
		$idOperacion="";
		if($row=@mysql_fetch_row($res)){
			$t ="<tr>";
			$t.="<td class=Fila  rowspan=".mysql_num_rows($res)." valign=top align=left>&nbsp;".txtParaInput($this->nombre)."&nbsp;</td>";
			$t.="<td class=Fila1 rowspan=".mysql_num_rows($res)." valign=top align=left>&nbsp;".$row[0]."&nbsp;</td>";
			if(count($this->caracts)>0){
				do{
					$t.="<td class=Fila1 valign=top>&nbsp;".$row[1]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top>&nbsp;".$row[2]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top>&nbsp;".$row[3]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top>&nbsp;".($row[4]==""?"":"<img src='".$row[4]."'>")."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top align=left>&nbsp;".$row[5]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top align=left>&nbsp;".$row[6]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top>&nbsp;".$row[7]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top>&nbsp;".$row[8]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top align=left>&nbsp;".$row[9]."&nbsp;</td>";
					$t.="<td class=Fila1 valign=top align=left>&nbsp;".$row[10]."&nbsp;</td></tr>";
				}while($row=mysql_fetch_row($res));
			}else $t.='<td class="Fila1" colspan=10 align=left>&nbspNo hay características relacionadas para esta operaci&oacute;n</td></tr>';	
		}
		return $t;
	}
	
	function generarPlanDeControlOperacion(){
		// cargo
		$datos=array();
		$sql="SELECT m.nombre,c.num,c.prod,c.proc,cl.nombre,c.especificacion,c.evaluacion,c.tam,c.fre,c.metodo,c.plan,o.nombre onom,o.id_operacion as oi ".
			 "FROM me_operaciones o ".
			 "LEFT JOIN ad_maquinas m ON m.id_maquina=".$this->id_maquina." ".
			 "LEFT JOIN ad_caracteristicas c ON c.id_caracteristica IN (".implode(",",$this->caracts).") ".
			 "LEFT JOIN ad_clases cl ON cl.id_clase=c.id_clase ".
			 "WHERE o.id_operacion=".$this->id_operacion;
		$res=mysql_query($sql);
		$j=0;
		$nombreOperacion="";
		$idOperacion="";
		if($row=mysql_fetch_row($res)){
			$nombreOperacion=$row[11];
			$idOperacion=$row[12];
			do{
				$datos[$j][]=trim($row[0]);
				$datos[$j][]=trim($row[1]);
				$datos[$j][]=trim($row[2]);
				$datos[$j][]=trim($row[3]);
				$datos[$j][]=trim($row[4]);
				$datos[$j][]=trim($row[5]);
				$datos[$j][]=trim($row[6]);
				$datos[$j][]=trim($row[7]);
				$datos[$j][]=trim($row[8]);
				$datos[$j][]=trim($row[9]);
				$datos[$j++][]=trim($row[10]);
			}while($row=mysql_fetch_row($res));
		}
		
		// escupo
		$rows=count($datos)==0?"1":count($datos);		
		$t ="<tr>";
		$t.="<td class=Fila rowspan=".$rows." valign=top>&nbsp;".$nombreOperacion."&nbsp;</td>";
		$t.="<td class=Fila1 rowspan=".$rows." valign=top>&nbsp;".$datos[0][0]."&nbsp;</td>";
		if(count($datos)>0){
			foreach($datos as $d){
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[1]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[2]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[3]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[4]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[5]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[6]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[7]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[8]."&nbsp;</td>";
				$t.="<td class=Fila1 valign=top>&nbsp;".$d[9]."&nbsp;</td></tr>";	
			}
		}else $t.='<td class="Fila1" colspan=9 align=left>&nbsp;No hay características relacionadas para esta operaci&oacute;n</td></tr>';	
		return $t;
	}
		
	
	
	
	
	
	// FUNCIONES PARA OBTENER EL AMFE
	
	function cargarTablaAMFE ($id=""){
		$cuantos=count($this->modos);
		if($cuantos>0){	
			if($this->paraPlani){
				$sql="".				
				"SELECT  m.id_modo,e.id_efecto,c.id_causa, ".	
				"m.nombre as modo,if(m.o_valor IS NULL,'0',m.o_valor) as valorOcurrencia, ".
				"e.nombre as efecto,if(e.g_valor IS NULL,'0',e.g_valor) as valorGravedad, ".
				"c.nombre as causa,if(c.d_valor IS NULL,'0',c.d_valor) as valorDetectabilidad, ".	
				"c.d_controles, ".
				"c.accion as accion ".							
				"FROM pl_modos m ".
				"LEFT JOIN pl_modo_efecto me ON m.id_modo=me.id_modo ".
				"LEFT JOIN pl_efectos e ON e.id_efecto=me.id_efecto ".
				"LEFT JOIN pl_modo_causa mc ON m.id_modo=mc.id_modo ".
				"LEFT JOIN pl_causas c ON c.id_causa=mc.id_causa ".
				"WHERE m.id_modo IN (".implode(",",$this->modos).") ".
				"ORDER BY m.id_modo asc, m.nombre asc, e.nombre asc, c.nombre asc";
			}else{		
				$sql="".				
				"SELECT  m.id_modo,e.id_efecto,c.id_causa, ".	
				"m.nombre as modo,if(o.valor IS NULL,'0',o.valor) as valorOcurrencia, ".
				"e.nombre as efecto,if(g.valor IS NULL,'0',g.valor) as valorGravedad, ".
				"c.nombre as causa,if(d.valor IS NULL,'0',d.valor) as valorDetectabilidad, ".	
				"d.controles, ".
				"c.accion as accion ".							
				"FROM me_modos m ".
				"LEFT JOIN ad_ocurrencias o ON m.id_ocurrencia=o.id_ocurrencia ".				
				"LEFT JOIN me_modo_efecto me ON m.id_modo=me.id_modo ".
				"LEFT JOIN me_efectos e ON e.id_efecto=me.id_efecto ".
				"LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad ".				
				"LEFT JOIN me_modo_causa mc ON m.id_modo=mc.id_modo ".
				"LEFT JOIN me_causas c ON c.id_causa=mc.id_causa ".
				"LEFT JOIN ad_detectabilidades d ON d.id_detectabilidad=c.id_detectabilidad ".				
				"WHERE m.id_modo IN (".implode(",",$this->modos).") ".
				"ORDER BY m.id_modo asc, m.nombre asc, e.nombre asc, c.nombre asc";	
			}	
			$res=mysql_query($sql);		
			if($row=mysql_fetch_array($res)){
				$modoAnterior="-69";
				$countEfectos=0;
				$countCausas=0;
				do {
					if($modoAnterior!=$row["id_modo"]){
						$listaEfectos="";
						$listaCausas="";
						$a[$row["id_modo"]]["causas"]=array();
						$a[$row["id_modo"]]["efectos"]=array();
					}
					$a[$row["id_modo"]]["nombre"]=$row["modo"];
					$a[$row["id_modo"]]["valorOcurrencia"]=$row["valorOcurrencia"];
					if(strpos($listaEfectos,"#".$row["id_efecto"]."#")===false && $row["id_efecto"]!=""){
						$a[$row["id_modo"]]["efectos"][$countEfectos]["nombre"]=$row["efecto"];
						$a[$row["id_modo"]]["efectos"][$countEfectos++]["gravedad"]=$row["valorGravedad"];
						$listaEfectos.="#".$row["id_efecto"]."#";
					}
					if(strpos($listaCausas,"#".$row["id_causa"]."#")===false && $row["id_causa"]!=""){
						$a[$row["id_modo"]]["causas"][$countCausas]["nombre"]=$row["causa"];
						$a[$row["id_modo"]]["causas"][$countCausas]["accion"]=$row["accion"];
						$a[$row["id_modo"]]["causas"][$countCausas]["detectabilidad"]=$row["valorDetectabilidad"];
						$a[$row["id_modo"]]["causas"][$countCausas++]["controles"]=$row["controles"];
						$listaCausas.="#".$row["id_causa"]."#";
					}
					$modoAnterior=$row["id_modo"];
				}while($row=mysql_fetch_array($res));
			}			
		}else{
			$a[0]["nombre"]="(sin modos)";	
			$a[0]["causas"][0]["nombre"]="";
			$a[0]["efectos"][0]["nombre"]="";
			$a[0]["causas"]=array();
			$a[0]["efectos"]=array();
			$a[0]["valorOcurrencia"]="0";
		}
		return $a;		
	}
	
	function pintarFilaAMFE($comp=false){
		global $app_rutaWEB;
		$imagen="&nbsp;<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;";
		$superMegaArray=$this->cargarTablaAMFE("",false);
		if(is_array($superMegaArray)){
			$clase=$fGris?"FilaGris":"Fila1";
			$ini=true;
			foreach($superMegaArray as $m){
				if($ini){
					$txt=$comp?"<td class=\"$clase\" rowspan='".(count($superMegaArray))."'>(sin componente)</td>":"";
					$txt.="<td class=\"$clase\" rowspan='".(count($superMegaArray))."' valign=center>".txtParaInput($this->nombre)."</td>";
					$ini=false;
				}
				$txt.="<td class=\"$clase\" ALIGN=LEFT valign=\"top\"><img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;".$m["nombre"]."</td>";
				$txt.="<td class=\"$clase\" ALIGN=LEFT valign=\"top\">";
				$gravedades=Array();
				foreach($m["efectos"] as $e){
					$txt.="<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;".txtParaInput($e["nombre"])."<br>";
					$gravedades[]=txtParaInput($e["gravedad"]);
				}
				$txt.="&nbsp;</td>";
				$txt.="<td class=\"$clase\" ALIGN=LEFT valign=\"top\">";
				$detectabilidades=Array();
				$acciones=Array();
				$controles=Array();
				foreach($m["causas"] as $c){
					$txt.=$c["nombre"]!=""?"<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;".txtParaInput($c["nombre"])."<br>":"&nbsp;";
					$detectabilidades[]=$c["detectabilidad"];
					$acciones[]=$c["accion"]==""?"(sin acción)":$c["accion"];
					$controles[]=$c["controles"]==""?"(sin control)":$c["controles"];
				}
				if(count($gravedades)==0) $gravedades=Array("0");
				if(count($detectabilidades)==0) $detectabilidades=Array("0");
				$txt.="&nbsp;</td>";
				$txt.="<td class=\"$clase\" ALIGN=LEFT valign=\"top\">".(count($controles)>0?$imagen.implode("<br>".$imagen,$controles):"&nbsp;")."</td>";
				$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".($m["valorOcurrencia"]==""?"&nbsp;":$m["valorOcurrencia"])."</td>";
				$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".implode("<br>",$gravedades)."</td>";
				$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".implode("<br>",$detectabilidades)."</td>";
				$npr=$this->calcNPR($m["valorOcurrencia"],$gravedades,$detectabilidades);
				$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".implode("<br>",$npr)."</td>";
				$txt.="<td class=\"$clase\" ALIGN=LEFT valign=\"top\">";
				if(count($acciones)>0){
					$txt.=$imagen;
					$txt.=implode("<br>".$imagen,$acciones);
				}else $txt.="&nbsp;";
				$txt.="</td></tr>";
			}
			return $txt;
		}
	}
	function calcNPR($oc,$aG,$aD){
		$aFin=array();
		$gr=max($aG);
		foreach($aD as $de) $aFin[]=$gr*$de*$oc;
		return $aFin;
	}
	function pintarFilaAMFEexportar($comp=false){
		global $app_rutaWEB;
		//$imagen="&nbsp;<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;";
		$imagen="&nbsp;<img src='http://sie.attest.es/pac/html/img/cerrar.JPG'>....&nbsp;";
		$superMegaArray=$this->cargarTablaAMFE("",false);
		if(is_array($superMegaArray)){
			$ini=true;
			foreach($superMegaArray as $m){
				if($ini){
					$txt=$comp?"<TR><TD ROWSPAN='".(count($superMegaArray))."'>(sin componente)</TD>":"";
					$txt.=($comp?"":"")."<TD ROWSPAN='".(count($superMegaArray))."' VALIGN=CENTER>".($comp?"si":"no")."".txtParaInput($this->nombre)."</TD>";
					$ini=false;
				}else $txt.="<TR>";
				$txt.="<TD ALIGN=LEFT VALIGN=TOP>&nbsp;· ".$m["nombre"].$imagen."</td>";
				$txt.="<TD ALIGN=LEFT VALIGN=TOP>";
				$gravedades=Array();
				foreach($m["efectos"] as $e){
					$txt.="&nbsp;· ".txtParaInput($e["nombre"])."<br>";
					$gravedades[]=txtParaInput($e["gravedad"]);
				}
				$txt.="&nbsp;</TD>";
				$txt.="<TD ALIGN=LEFT VALIGN=TOP>";
				$detectabilidades=Array();
				$acciones=Array();
				$controles=Array();
				foreach($m["causas"] as $c){
					$txt.=$c["nombre"]!=""?"&nbsp;· ".txtParaInput($c["nombre"])."<br>":"&nbsp;";
					$detectabilidades[]=$c["detectabilidad"];
					$acciones[]=$c["accion"]==""?"(sin acción)":$c["accion"];
					$controles[]=$c["controles"]==""?"(sin control)":$c["controles"];
				}
				if(count($gravedades)==0) $gravedades=Array("0");
				if(count($detectabilidades)==0) $detectabilidades=Array("0");
				$txt.="&nbsp;</TD>";
				$txt.="<TD ALIGN=LEFT VALIGN=TOP>".(count($controles)>0?"&nbsp;· ".implode("<br>&nbsp;· ",$controles):"&nbsp;")."</TD>";
				$txt.="<TD ALIGN=CENTER VALIGN=TOP>".($m["valorOcurrencia"]==""?"&nbsp;":$m["valorOcurrencia"])."</TD>";
				$txt.="<TD ALIGN=CENTER VALIGN=TOP width=22>".implode("<br>",$gravedades)."</TD>";
				$txt.="<TD ALIGN=CENTER VALIGN=TOP width=44>".implode("<br>",$detectabilidades)."</TD>";
				$npr=$this->calcNPR($m["valorOcurrencia"],$gravedades,$detectabilidades);
				$txt.="<TD align=CENTER VALIGN=TOP>".implode("<br>",$npr)."</TD>";
				$txt.="<TD ALIGN=LEFT valign=TOP>";
				if(count($acciones)>0){
					$txt.="&nbsp;· ";
					$txt.=implode("<br>&nbsp;· ",$acciones);
				}else $txt.="&nbsp;";
				$txt.="</TD></TR>";
			}
			return $txt;
		}
	}
		
	function getNombre($id){
		$res=mysql_query("SELECT nombre FROM me_operaciones WHERE id_operacion=$id");
		if($row=@mysql_fetch_row($res)) return $row[0];			
	}
}
?>