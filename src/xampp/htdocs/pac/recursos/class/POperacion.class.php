<?
function cTxt($txt){
	
	return str_replace("'","\\'",convTxt($txt));	
}
class POperacion {
	
	var $id_planificacion="";
	var $id_operacion;
	var $id_opAlt;
	var $id_maquina;
	var $codigo="";
	var $codigoOrig="";
	var $nombre="";
	//var $id_operacion_maestros;
	//var $modos=Array();
	//var $caracts=Array();
	
	
	/******************************************************************************/
	/* Copia de la operación */
	
	function POperacion ($idp,$ido){
		$sql="SELECT * FROM me_operaciones WHERE id_operacion=$ido";
		$res=mysql_query($sql);
		$row=mysql_fetch_array($res);		
		$this->id_planificacion=$idp;		
		$this->id_operacion=$row["id_operacion"];
		$this->id_opAlt=$row["id_opAlt"];	
		$this->nombre=convTxt($row["nombre"]);
		$this->codigo=$row["codigo"];
		$this->id_maquina=$row["id_maquina"];		
				
		$this->guardaOperacion();	
		
		$this->copiaModos($ido);
		$this->copiaEfectos($ido);
		$this->copiaCausas($ido);
		$this->copiaCaracteristicas($ido);
	}
	
	function guardaOperacion(){
		$sql="INSERT INTO pl_operaciones (id_planificacion,id_operacion,codigo,nombre,id_opAlt,id_maquina) VALUES ".
			 "(".
			 "".$this->id_planificacion.", ".
			 "".$this->id_operacion.", ".
			 "'".cTxt($this->codigo)."', ".
			 "'".cTxt($this->nombre)."', ".
			 "'".$this->id_opAlt."', ".
			 "'".$this->id_maquina."') ";
		mysql_query($sql);
	}
	
	
	function copiaModos($ido){
		//************** modos y ocurrencias
		$ModoDebug=false;	
		$this->modos=array();
		$sql="SELECT m.*,o.nombre as no,o.valor FROM me_operacion_modo om  ".
			 "LEFT JOIN me_modos m ON m.id_modo=om.id_modo ".
			 "LEFT JOIN ad_ocurrencias o ON o.id_ocurrencia=m.id_ocurrencia ".
			 "WHERE om.id_operacion=$ido";
		if($ModoDebug) echo "BUSCO MODOS -> ".$sql."<br>";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res)){
			$sql="INSERT INTO pl_modos(id_planificacion,id_modo,nombre,codigo,o_id,o_nombre,o_valor) VALUES ".
				 "(".
				 "'".$this->id_planificacion."',".
				 "'".$row["id_modo"]."',".
				 "'".cTxt($row["nombre"])."',".
				 "'".cTxt($row["codigo"])."',".
				 "'".$row["id_ocurrencia"]."',".
				 "'".cTxt($row["no"])."',".
				 "'".$row["valor"]."'".
				 ")";
			if($ModoDebug) echo "INSERTO MODO -> ".$sql."<br>";
			mysql_query($sql);
			// guardo la relacion
			$sql="INSERT INTO pl_operacion_modo (id_planificacion,id_operacion,id_modo) VALUES ".
				 "(".$this->id_planificacion.",".$this->id_operacion.",".$row["id_modo"].")";
			mysql_query($sql);
			if($ModoDebug) echo "GUARDO RELACION -> ".$sql."<br>";
		}						
	}
	
	
	function copiaEfectos($ido){		
		//************** efectos y gravedades		
		$sql="SELECT e.id_efecto,e.nombre,e.codigo,g.nombre as ng,g.valor,g.id_gravedad,om.id_modo as idm FROM me_operacion_modo om  ".
			 "INNER JOIN me_modo_efecto me ON me.id_modo=om.id_modo ".
			 "INNER JOIN me_efectos e ON e.id_efecto=me.id_efecto ".
			 "LEFT JOIN ad_gravedades g ON g.id_gravedad=e.id_gravedad ".
			 "WHERE om.id_operacion=$ido";
		if($ModoDebug) echo "BUSCO EFECTOS -> ".$sql."<br>";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res)){
			$sql="INSERT INTO pl_efectos(id_planificacion,id_efecto,codigo,nombre,g_id,g_nombre,g_valor) VALUES ".
				 "(".
				 "'".$this->id_planificacion."',".
				 "'".$row["id_efecto"]."',".
				 "'".$row["codigo"]."',".
				 "'".cTxt($row["nombre"])."',".
				 "'".$row["id_gravedad"]."',".
				 "'".cTxt($row["ng"])."',".
				 "'".$row["valor"]."'".
				 ")";
			if($ModoDebug) echo "INSERTO EFECTOS -> ".$sql."<br>";
			mysql_query($sql);
			$sql="INSERT INTO pl_modo_efecto (id_planificacion,id_modo,id_efecto) VALUES ".
				 "(".$this->id_planificacion.",".$row["idm"].",".$row["id_efecto"].")";
			mysql_query($sql);
			if($ModoDebug) echo "GUARDO RELACION -> ".$sql."<br>";
		}		
	}
	
	
	
	function copiaCausas($ido){		
		//************** causas y detectabilidades		
		$sql="SELECT c.id_causa,c.nombre,c.codigo,c.accion,d.nombre as nd,d.valor,c.id_detectabilidad,d.controles,mc.id_modo as idm  FROM me_operacion_modo om  ".
			 "INNER JOIN me_modo_causa mc ON mc.id_modo=om.id_modo ".
			 "LEFT JOIN me_causas c ON c.id_causa=mc.id_causa ".
			 "LEFT JOIN ad_detectabilidades d ON d.id_detectabilidad=c.id_detectabilidad ".
			 "WHERE om.id_operacion=$ido";
		if($ModoDebug) echo "BUSCO CAUSAS -> ".$sql."<br>";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res)){
			/*
			$sql="SELECT if(Max(id_causa) is null,1,Max(id_causa)+1) FROM pl_causas WHERE id_planificacion=".$this->id_planificacion;
			$re=mysql_query($sql);
			$ro=mysql_fetch_row($re);*/
			$sql="INSERT INTO pl_causas(id_planificacion,id_causa,codigo,nombre,accion,d_id,d_nombre,d_valor,d_controles) VALUES ".
				 "(".
				 "'".$this->id_planificacion."',".
				 "'".$row["id_causa"]."',".
				 "'".$row["codigo"]."',".
				 "'".cTxt($row["nombre"])."',".
				 "'".cTxt($row["accion"])."',".
				 "'".$row["id_detectabilidad"]."',".
				 "'".cTxt($row["nd"])."',".
				 "'".$row["valor"]."',".
				 "'".cTxt($row["controles"])."'".
				 ")";
			if($ModoDebug) echo "INSERTO CAUSAS -> ".$sql."<br>";
			mysql_query($sql);
			$sql="INSERT INTO pl_modo_causa (id_planificacion,id_modo,id_causa) VALUES ".
				 "(".$this->id_planificacion.",".$row["idm"].",".$row["id_causa"].")";
			if($ModoDebug) echo "GUARDO RELACION CAUSAS -> ".$sql."<br>";
			mysql_query($sql);
		}		
	}	
	
	
	function copiaCaracteristicas($ido){
		$sql="SELECT c.* FROM ad_caracteristicas c ".
			 "LEFT JOIN me_operacion_caracteristica oc ON oc.id_caracteristica=c.id_caracteristica ".
			 "WHERE oc.id_operacion=".$ido;
		//echo "<br>".$sql."<br>";
		$res=mysql_query($sql);
		while($row=mysql_fetch_array($res)){
			$sql="INSERT INTO pl_caracteristicas (id_planificacion,id_caracteristica,nombre,num,prod,proc,especificacion,evaluacion,metodo,tam,fre,plan,id_clase)".
				 " VALUES ".
				 "($this->id_planificacion,".$row["id_caracteristica"].",'".cTxt($row["nombre"])."','".$row["num"]."','".cTxt($row["prod"])."'".
				 ",'".cTxt($row["proc"])."','".cTxt($row["especificacion"])."','".cTxt($row["evaluacion"])."','".cTxt($row["metodo"])."',".
				 "'".cTxt($row["tam"])."','".cTxt($row["fre"])."','".cTxt($row["plan"])."','".cTxt($row["id_clase"])."')";
			mysql_query($sql);
			$sql="INSERT INTO pl_operacion_caracteristica (id_planificacion,id_operacion,id_caracteristica) VALUES ".
				 "($this->id_planificacion,".$ido.",".$row["id_caracteristica"].")";
			mysql_query($sql);	
		}
	}
	
	
	function comprobarOperacionGuardada($idp,$ido){
		if($row[0]==0) return false;
		else return true;
	}
	
	function pintarFilaAMFE($idp,$ido,$suelta=false){
		global $app_rutaWEB;
		$sql="SELECT count(*) FROM pl_operaciones WHERE id_operacion=$ido AND id_planificacion=$idp";
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		if($row[0]!=0){
			//************* cargo los datos
			$sql="SELECT id_modo FROM pl_operacion_modo om WHERE id_planificacion=$idp AND id_operacion=$ido";
			//echo $sql."--<br>";
			$res=mysql_query($sql);
			$modos=array();
			while($row=@mysql_fetch_row($res)) $modos[]=$row[0];
			$cuantos=count($modos);
			$a=array();
			if($cuantos>0){	
				$sql="".				
				"SELECT  m.id_modo,e.id_efecto,c.id_causa, ".	
				"m.nombre as modo,if(m.o_valor IS NULL,'0',m.o_valor) as valorOcurrencia, ".
				"e.nombre as efecto,if(e.g_valor IS NULL,'0',e.g_valor) as valorGravedad, ".
				"c.nombre as causa,if(c.d_valor IS NULL,'0',c.d_valor) as valorDetectabilidad, ".	
				"c.d_controles, ".
				"c.accion as accion, ".							
				"om.responsable as responsable, om.plazo as plazo, om.accion_tomada as accionTomada,om.OC,om.GR,om.DE ".	
				"FROM pl_operacion_modo om ".
				"LEFT JOIN pl_modos m ON om.id_modo=m.id_modo AND om.id_planificacion=$idp AND om.id_operacion=$ido ".
				"LEFT JOIN pl_modo_efecto me ON m.id_modo=me.id_modo AND me.id_planificacion=$idp ".
				"LEFT JOIN pl_efectos e ON e.id_efecto=me.id_efecto  AND e.id_planificacion=$idp  ".
				"LEFT JOIN pl_modo_causa mc ON m.id_modo=mc.id_modo  AND mc.id_planificacion=$idp  ".
				"LEFT JOIN pl_causas c ON c.id_causa=mc.id_causa  AND c.id_planificacion=$idp  ".
				"WHERE m.id_modo IN (".implode(",",$modos).") ".
				"AND m.id_planificacion=".$idp." ".
				"ORDER BY m.id_modo asc, m.nombre asc, e.nombre asc, c.nombre asc";
				$res=mysql_query($sql);		
				if($row=mysql_fetch_array($res)){
					$sql="SELECT id_operacion,nombre FROM pl_operaciones WHERE id_planificacion=$idp AND id_operacion=$ido";
					$reee=mysql_query($sql);
					$rooo=mysql_fetch_row($reee);
					$nombreOperacion=$rooo[1];
					$idOperacion=$rooo[0];
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
						$a[$row["id_modo"]]["responsable"]=$row["responsable"];
						$a[$row["id_modo"]]["plazo"]=$row["plazo"];
						$a[$row["id_modo"]]["accionTomada"]=$row["accionTomada"];
						$a[$row["id_modo"]]["despuesOC"]=$row["OC"];
						$a[$row["id_modo"]]["despuesGR"]=$row["GR"];
						$a[$row["id_modo"]]["despuesDE"]=$row["DE"];
						
						if(strpos($listaEfectos,"#".$row["id_efecto"]."#")===false && $row["id_efecto"]!=""){
							$a[$row["id_modo"]]["efectos"][$countEfectos]["nombre"]=$row["efecto"];
							$a[$row["id_modo"]]["efectos"][$countEfectos++]["gravedad"]=$row["valorGravedad"];
							$listaEfectos.="#".$row["id_efecto"]."#";
						}
						if(strpos($listaCausas,"#".$row["id_causa"]."#")===false && $row["id_causa"]!=""){
							$a[$row["id_modo"]]["causas"][$countCausas]["nombre"]=$row["causa"];
							$a[$row["id_modo"]]["causas"][$countCausas]["accion"]=$row["accion"];
							$a[$row["id_modo"]]["causas"][$countCausas]["detectabilidad"]=$row["valorDetectabilidad"];
							$a[$row["id_modo"]]["causas"][$countCausas++]["controles"]=$row["d_controles"];
							$listaCausas.="#".$row["id_causa"]."#";
						}
						$modoAnterior=$row["id_modo"];
					}while($row=mysql_fetch_array($res));
				}			
			}else{
				$sql="SELECT nombre FROM pl_operaciones WHERE id_planificacion=".$idp." AND id_operacion=".$ido;
				$res=mysql_query($sql);
				$row=mysql_fetch_row($res);
				$nombreOperacion=$row[0];
				$idOperacion=$ido;
				$a[0]["nombre"]="(sin modos)";	
				$a[0]["causas"][0]["nombre"]="";
				$a[0]["efectos"][0]["nombre"]="";
				$a[0]["causas"]=array();
				$a[0]["efectos"]=array();
				$a[0]["valorOcurrencia"]="0";
				$a[0]["responsable"]="0";
				$a[0]["plazo"]="";
				$a[0]["accionTomada"]="";
				$a[0]["despuesOC"]="0";
				$a[0]["despuesGR"]="0";
				$a[0]["despuesDE"]="0";
			}		
			
			//************* genero la cadena de texto con todo
			
			$imagen="&nbsp;<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;";
			$txt="";
			if(is_array($a)){
				$clase=$fGris?"FilaGris":"Fila1";
				$ini=true;
				foreach($a as $k=>$m){
					if($ini){
						$txt=$suelta?"<td class=\"$clase\" rowspan='".(count($a))."'>(sin componente)</td>":"";
						$txt.="<td class=\"$clase\" rowspan='".(count($a))."' valign=center onClick=\"editarOperacion('".$idOperacion."')\"";
						$txt.=" onmouseover='filaover(this)' onmouseout='filaout(this)'  style='cursor: pointer'>";
						$txt.="&nbsp;".$nombreOperacion."";
						$txt.="</td>";
						$ini=false;
					}
					$txt.="<td class=\"$clase\" align=\"Left\" valign=\"top\" ";
					if($m["nombre"]!="(sin modos)"){
						$txt.="onClick=\"editarModo('$this->id_planificacion','$idOperacion','".$k."')\" ";
						$txt.="onMouseOver=\"filaover(this)\" onMouseOut=\"filaout(this)\"  style='cursor: pointer' ";
					}
					
					$txt.=">";
					$txt.="<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">";
					$txt.="&nbsp;".$m["nombre"]."</td>";
					$txt.="<td class=\"$clase\" align=\"Left\" valign=\"top\">";
					$gravedades=Array();
					foreach($m["efectos"] as $e){
						$txt.="<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;".txtParaInput($e["nombre"])."<br>";
						$gravedades[]=txtParaInput($e["gravedad"]);
					}
					$txt.="&nbsp;</td>";
					$txt.="<td class=\"$clase\" align=\"Left\" valign=\"top\">";
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
					$txt.="<td class=\"$clase\" align=\"left\" valign=\"top\">".(count($controles)>0?$imagen.implode("<br>".$imagen,$controles):"&nbsp;")."</td>";
					$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".($m["valorOcurrencia"]==""?"&nbsp;":$m["valorOcurrencia"])."</td>";
					$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".implode("<br>",$gravedades)."</td>";
					$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".implode("<br>",$detectabilidades)."</td>";
					
					$npr=array();
					$gr=max($gravedades);
					foreach($detectabilidades as $de) $npr[]=$gr*$de*$m["valorOcurrencia"];
					
					$txt.="<td class=\"$clase\" align=\"center\" valign=\"top\">".implode("<br>",$npr)."</td>";
					$txt.="<td class=\"$clase\" align=\"left\" valign=\"top\">";
					if(count($acciones)>0){
						$txt.=$imagen;
						$txt.=implode("<br>".$imagen,$acciones);
					}else $txt.="&nbsp;";
					$txt.="</td>";
					
					if($m["nombre"]=="(sin modos)"){
						$txt.="<td class=\"Fila1\" colspan=7 valign=top align=left>&nbsp;";
						$txt.="<img src=\"".$app_rutaWEB."/html/img/FlechaDe.gif\">&nbsp;No hay modos</td>";
					}else{
						$txt.="<td class=\"Fila1\" valign=top align=left>&nbsp;".Responsable::getNombre($m["responsable"])." ".muestraFecha($m["plazo"])."</td>";
						$txt.="<td class=\"Fila1\" valign=top align=left>&nbsp;".txtParaInput($m["accionTomada"])."</td>";
						$txt.="<td class=\"Fila1\" valign=top align=center>&nbsp;".$m["despuesOC"]."</td>";
						$txt.="<td class=\"Fila1\" valign=top align=center>&nbsp;".$m["despuesGR"]."</td>";
						$txt.="<td class=\"Fila1\" valign=top align=center>&nbsp;".$m["despuesDE"]."</td>";
						$txt.="<td class=\"Fila1\" valign=top align=center>&nbsp;".($m["despuesOC"]*$m["despuesGR"]*$m["despuesDE"])."</td>";
					}
					$txt.="</tr>";
				}
			}
		}else{
			$txt.="<td colspan=17 class=\"Fila1\" align=left>".
			 		"<b><br>&nbsp;&nbsp;Guarde la planificación para mostrar correctamente el AMFE.<br>&nbsp;</b> ".
			 		"</td></tr>";						
		}
		return $txt;
		
		
	}
	function pintarFilaExportacionAMFE($idp,$ido,$suelta=false){
		global $app_rutaWEB;
		global $app_inicioWEB;
		$inicioRuta=str_replace($app_inicioWEB,"",$app_rutaWEB);
		$sql="SELECT count(*) FROM pl_operaciones WHERE id_operacion=$ido AND id_planificacion=$idp";
		$res=mysql_query($sql);
		$row=mysql_fetch_row($res);
		if($row[0]!=0){
			//************* cargo los datos
			$sql="SELECT id_modo FROM pl_operacion_modo om WHERE id_planificacion=$idp AND id_operacion=$ido";
			//echo $sql."--<br>";
			$res=mysql_query($sql);
			$modos=array();
			while($row=@mysql_fetch_row($res)) $modos[]=$row[0];
			$cuantos=count($modos);
			$a=array();
			if($cuantos>0){	
				$sql="".				
				"SELECT  m.id_modo,e.id_efecto,c.id_causa, ".	
				"m.nombre as modo,if(m.o_valor IS NULL,'0',m.o_valor) as valorOcurrencia, ".
				"e.nombre as efecto,if(e.g_valor IS NULL,'0',e.g_valor) as valorGravedad, ".
				"c.nombre as causa,if(c.d_valor IS NULL,'0',c.d_valor) as valorDetectabilidad, ".	
				"c.d_controles, ".
				"c.accion as accion, ".							
				"om.responsable as responsable, om.plazo as plazo, om.accion_tomada as accionTomada,om.OC,om.GR,om.DE ".	
				"FROM pl_operacion_modo om ".
				"LEFT JOIN pl_modos m ON om.id_modo=m.id_modo AND om.id_planificacion=$idp AND om.id_operacion=$ido ".
				"LEFT JOIN pl_modo_efecto me ON m.id_modo=me.id_modo AND me.id_planificacion=$idp ".
				"LEFT JOIN pl_efectos e ON e.id_efecto=me.id_efecto  AND e.id_planificacion=$idp  ".
				"LEFT JOIN pl_modo_causa mc ON m.id_modo=mc.id_modo  AND mc.id_planificacion=$idp  ".
				"LEFT JOIN pl_causas c ON c.id_causa=mc.id_causa  AND c.id_planificacion=$idp  ".
				"WHERE m.id_modo IN (".implode(",",$modos).") ".
				"AND m.id_planificacion=".$idp." ".
				"ORDER BY m.id_modo asc, m.nombre asc, e.nombre asc, c.nombre asc";
				$res=mysql_query($sql);		
				if($row=mysql_fetch_array($res)){
					$sql="SELECT id_operacion,nombre FROM pl_operaciones WHERE id_planificacion=$idp AND id_operacion=$ido";
					$reee=mysql_query($sql);
					$rooo=mysql_fetch_row($reee);
					$nombreOperacion=$rooo[1];
					$idOperacion=$rooo[0];
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
						$a[$row["id_modo"]]["responsable"]=$row["responsable"];
						$a[$row["id_modo"]]["plazo"]=$row["plazo"];
						$a[$row["id_modo"]]["accionTomada"]=$row["accionTomada"];
						$a[$row["id_modo"]]["despuesOC"]=$row["OC"];
						$a[$row["id_modo"]]["despuesGR"]=$row["GR"];
						$a[$row["id_modo"]]["despuesDE"]=$row["DE"];
						
						if(strpos($listaEfectos,"#".$row["id_efecto"]."#")===false && $row["id_efecto"]!=""){
							$a[$row["id_modo"]]["efectos"][$countEfectos]["nombre"]=$row["efecto"];
							$a[$row["id_modo"]]["efectos"][$countEfectos++]["gravedad"]=$row["valorGravedad"];
							$listaEfectos.="#".$row["id_efecto"]."#";
						}
						if(strpos($listaCausas,"#".$row["id_causa"]."#")===false && $row["id_causa"]!=""){
							$a[$row["id_modo"]]["causas"][$countCausas]["nombre"]=$row["causa"];
							$a[$row["id_modo"]]["causas"][$countCausas]["accion"]=$row["accion"];
							$a[$row["id_modo"]]["causas"][$countCausas]["detectabilidad"]=$row["valorDetectabilidad"];
							$a[$row["id_modo"]]["causas"][$countCausas++]["controles"]=$row["d_controles"];
							$listaCausas.="#".$row["id_causa"]."#";
						}
						$modoAnterior=$row["id_modo"];
					}while($row=mysql_fetch_array($res));
				}			
			}else{
				$sql="SELECT nombre FROM pl_operaciones WHERE id_planificacion=".$idp." AND id_operacion=".$ido;
				$res=mysql_query($sql);
				$row=mysql_fetch_row($res);
				$nombreOperacion=$row[0];
				$idOperacion=$ido;
				$a[0]["nombre"]="(sin modos)";	
				$a[0]["causas"][0]["nombre"]="";
				$a[0]["efectos"][0]["nombre"]="";
				$a[0]["causas"]=array();
				$a[0]["efectos"]=array();
				$a[0]["valorOcurrencia"]="0";
				$a[0]["responsable"]="";
				$a[0]["plazo"]="";
				$a[0]["accionTomada"]="";
				$a[0]["despuesOC"]="0";
				$a[0]["despuesGR"]="0";
				$a[0]["despuesDE"]="0";
			}		
			
			//************* genero la cadena de texto con todo
			
			$imagen="";
			$txt="";
			$st=" STYLE=\"font-size:7.0pt\" ";
			if(is_array($a)){
				$ini=true;
				foreach($a as $m){
					$txt.="<TR>\n";
					if($ini){
						$txt.="	<TD ROWSPAN='".(count($a))."' VALIGN=TOP COLSPAN=9 $st>&nbsp;".$nombreOperacion."</TD>\n";
						$ini=false;
					}
					$txt.="	<TD ALIGN=LEFT VALIGN=TOP COLSPAN=8 $st>&nbsp;".$m["nombre"]."</TD>\n";
					$txt.="	<TD ALIGN=LEFT VALIGN=TOP COLSPAN=9 $st>";
					$gravedades=Array();
					foreach($m["efectos"] as $e){
						$txt.="&nbsp;· ".txtParaInput($e["nombre"])."<BR>";
						$gravedades[]=txtParaInput($e["gravedad"]);
					}
					$txt.="&nbsp;</TD>\n";
					$txt.="	<TD ALIGN=LEFT VALIGN=TOP COLSPAN=9 $st>";
					$detectabilidades=Array();
					$acciones=Array();
					$controles=Array();
					foreach($m["causas"] as $c){
						$txt.=$c["nombre"]!=""?"&nbsp;· ".txtParaInput($c["nombre"])."<br>":"&nbsp;";
						$detectabilidades[]=$c["detectabilidad"];
						$acciones[]="· ".($c["accion"]==""?"(sin acción)":$c["accion"]);
						$controles[]="· ".($c["controles"]==""?"(sin control)":$c["controles"]);
					}
					if(count($gravedades)==0) $gravedades=Array("0");
					if(count($detectabilidades)==0) $detectabilidades=Array("0");
					$txt.="&nbsp;</TD>\n";
					$txt.="	<TD ALIGN=LEFT   VALIGN=TOP COLSPAN=10  $st>".(count($controles)>0?$imagen.implode("<br>".$imagen,$controles):"&nbsp;")."</TD>\n";
					$txt.="	<TD ALIGN=CENTER VALIGN=TOP COLSPAN=3 $st>".($m["valorOcurrencia"]==""?"&nbsp;":$m["valorOcurrencia"])."</TD>\n";
					$txt.="	<TD ALIGN=CENTER VALIGN=TOP COLSPAN=3 $st>".implode("<br>",$gravedades)."</TD>\n";
					$txt.="	<TD ALIGN=CENTER VALIGN=TOP COLSPAN=3 $st>".implode("<br>",$detectabilidades)."</TD>\n";
					
					$npr=array();
					$gr=max($gravedades);
					foreach($detectabilidades as $de) $npr[]=$gr*$de*$m["valorOcurrencia"];
					
					$txt.="	<TD ALIGN=CENTER VALIGN=TOP COLSPAN=5 $st>".implode("<br>",$npr)."</TD>\n";
					$txt.="	<TD ALIGN=LEFT   VALIGN=TOP  COLSPAN=11 $st>";
					if(count($acciones)>0){
						$txt.=$imagen;
						$txt.=implode("<br>".$imagen,$acciones);
					}else $txt.="&nbsp;";
					$txt.="</TD>\n";
					
					if($m["nombre"]=="(sin modos)"){
						$txt.="<TD COLSPAN=30 VALIGN=TOP ALIGN=LEFT $st>&nbsp;";
						$txt.="&nbsp;No hay modos</TD>\n";
					}else{
						$txt.="	<TD VALIGN=TOP ALIGN=LEFT  COLSPAN=6 $st>&nbsp;".txtParaInput($m["responsable"]." ".muestraFecha($m["plazo"]))."</TD>\n";
						$txt.="	<TD VALIGN=TOP ALIGN=LEFT  COLSPAN=10 $st>&nbsp;".txtParaInput($m["accionTomada"])."</td>";
						$txt.="	<TD VALIGN=TOP ALIGN=CENTER COLSPAN=3 $st>&nbsp;".$m["despuesOC"]."</TD>\n";
						$txt.="	<TD VALIGN=TOP ALIGN=CENTER COLSPAN=3 $st>&nbsp;".$m["despuesGR"]."</TD>\n";
						$txt.="	<TD VALIGN=TOP ALIGN=CENTER COLSPAN=3 $st>&nbsp;".$m["despuesDE"]."</TD>\n";
						$txt.="	<TD VALIGN=TOP ALIGN=CENTER COLSPAN=5 $st>&nbsp;".($m["despuesOC"]*$m["despuesGR"]*$m["despuesDE"])."</TD>\n";
					}
					$txt.="</TR>\n";
				}
			}
		}
		return $txt;
	}
}
?>