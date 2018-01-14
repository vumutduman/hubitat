<?php
class Zend_View_Helper_Assets extends Zend_View_Helper_Abstract{
	public $oConfig;
	public function Assets(){
		$this->oConfig = Zend_Registry::get('oConfig');
		return $this;
	}
	
	public function baseUrl(){
		return $this->oConfig->webhost;
	}

	public function member($avatar){
		return 'content/images/member/'.$avatar;
	}
}