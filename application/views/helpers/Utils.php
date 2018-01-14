<?php
/**
 * @author artuc
 * @version 1.0
 *  
 * Content Helper
 * @author artuc <mustafa@artuc.net>
 * @package BacakVar
 * 
 */
class Zend_View_Helper_Utils extends Zend_View_Helper_Abstract{
	public $oConfig;
	public $oDB;
	
	public function Utils(){
		$this->oConfig = Zend_Registry::get('oConfig');
		$this->oDB = Zend_Db_Table_Abstract::getDefaultAdapter();
		$this->language = new Zend_Session_Namespace('language');
		
		return $this;
	}
	
	public function htmlFilter($msgDetails){
		$msgDetails = htmlspecialchars($msgDetails);
		$msgDetails = strip_tags($msgDetails);
		$msgDetails = nl2br($msgDetails, true);
		$msgDetails = str_replace(array("select","update","delete","insert","alter"), array("*","*","*","*","*"), strtolower($msgDetails));
		
		return $msgDetails;
	}
	
	public function ago($date, $granularity=2){
		$difference = time() - $date;
		$periods = array('decade' => 315360000,
				'yıl' => 31536000,
				'ay' => 2628000,
				'hafta' => 604800,
				'gün' => 86400,
				'saat' => 3600,
				'dakika' => 60,
				'saniye' => 1
		);
	
		$retval = '';
	
		foreach($periods as $key => $value) {
			if($difference >= $value){
				$time = floor($difference/$value);
				$difference %= $value;
				$retval .= ($retval ? ' ' : '').$time.' ';
				$retval .= (($time > 1) ? $key : $key);
				$granularity--;
			}
			if($granularity == '0'){
				break;
			}
		}
		if($retval){
			return $retval;
		}else{
			return 'az';
		}
	}
	
	public function getCurrency($price, $currency){
		return $price.' TL';
		//return $price.' '.$currency;
	}
	
	public function createRandom($length){
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVQXYZ';
		$text = substr(str_shuffle($chars), 0, $length);
		
		return $text;
	}
}