<?
class Pest {
	var $pestActual;
	var $tam="100%";	//tamaño de la tabla entera
	var $alt="500px"; //altura de la tabla
	var $num=0;				//numero de pestañas
	var	$aEtiq=Array();
	var	$aParam=Array();
	var	$aFuncts=Array();
	
	function pest ($pe=0) {
		$this->baseURL=$dir;
		$this->pestActual=$pe;
	}
	
	function add ( $etiqueta , $url , $funcionJS="" ) {
		if($etiqueta!="" && $url!=""){
			$this->aEtiq[]=$etiqueta;
			$this->aParam[]=$url;
			$this->aFuncts[]=" ".$funcionJS;		
			$this->num++;
		}
	}
		
	function setTam ( $valor ) {
		$this->tam=$valor;		
	}
	
	function setAlt ( $valor ) {
		$this->alt=$valor;		
	}
	
	function pintar ( ) {
		
		$txt.= "<table width=".$this->tam." border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>\n";
		
		for($i=0;$i<$this->num;$i++) {
			
			$act="";
			if($this->pestActual==$i) $act="Activa";
			
			$txt.="<td valign=\"bottom\" width=100px>";
      $txt.="  	<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" ALIGN=LEFT>";
	    $txt.="      <tr>";
	    $txt.="        <td class=\"Pest".$act."1\">&nbsp;</td>";
	    $txt.="        <td class=\"Pest".$act."2\" nowrap>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	    
	    $txt.=  $act==""  ?  "<a href=\"".$this->aParam[$i]."\" ".$this->aFuncts[$i].">".$this->aEtiq[$i]."</a>"  :  $this->aEtiq[$i]  ;	    
	    
	    $txt.="				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
	    $txt.="        <td class=\"Pest".$act."3\">&nbsp;</td>";
	    $txt.="      </tr>";
      $txt.="  	</table>";
      $txt.="  </td>";
		}
		$txt.="<td valign=\"bottom\" width=100%>&nbsp;</td>";
		$txt.="</tr></table>\n";
		
		echo $txt;
	}

}
	


?>