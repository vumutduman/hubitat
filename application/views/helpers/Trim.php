<?php
class Zend_View_Helper_Trim extends Zend_View_Helper_Abstract {
	function trim($str, $end=100, $start=0){
		$str = strip_tags(str_replace("\'", "&#8217;", str_replace("\"", "&quot;", trim($str))));		
		preg_match_all("/./u", $str, $ar);
		if(strlen($str) > $end)
			return join("",array_slice($ar[0],$start,$end))."...";
		else
			return join("",array_slice($ar[0],$start,$end));
	}
}
?>