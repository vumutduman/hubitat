<?php
class Zend_View_Helper_TR extends Zend_View_Helper_Abstract{
	function TR(){
		$this->oConfig = Zend_Registry::get('oConfig');
		return $this;
	}
	
	function upper($str){
	    return mb_convert_case(str_replace('i','İ', $str), MB_CASE_UPPER, "UTF-8"); 
	    //return strtoupper(str_replace('i','İ', $str));	    
	 }
	
	function lower($str){
	    return strtolower(str_replace('i','İ', $str));
	 }
	
	function firstLetter($str){
		$str = mb_convert_case(str_replace(array("i","I"),array("İ","ı"), $str), MB_CASE_TITLE, "UTF-8");
		return $str;
	}

	function returnDate(){
		$date = date('d.m.Y', mktime(0, 0, 0, date('m'), date('d')+3, date('Y')));
		
		return $date;
	}
	
	function date($date, $format){
		$trChar = array(
			'Sunday'=> 'Pazar', 
			'Monday'=> 'Pazartesi', 
			'Tuesday'=> 'Salı', 
			'Wednesday'=> 'Çarşamba', 
			'Thursday' => 'Perşembe', 
			'Friday'=>'Cuma', 
			'Saturday' => 'Cumartesi',
			'January' => 'Ocak', 
			'February' => 'Şubat', 
			'March' => 'Mart', 
			'April' => 'Nisan', 
			'May' => 'Mayıs', 
			'June' => 'Haziran', 
			'July'=>' Temmuz', 
			'August' => 'Ağustos', 
			'September'=>' Eylül', 
			'October'=> 'Ekim', 
			'November'=> 'Kasım', 
			'December' => 'Aralık'
		);
		
		return strtr(date($format, strtotime($date)), $trChar);
	}
}
?>