<?php
class ErrorController extends Zend_Controller_Action{
	private $oConfig;
	public $siteLayout;
	
	public function init(){
		$this->siteLayout = Zend_Layout::getMvcInstance();
		$this->oConfig = Zend_Registry::get('oConfig');
	}
	
    public function errorAction(){
    	$this->siteLayout = Zend_Layout::getMvcInstance();
		$this->view->message = 'Sistemde bakım çalışması yapılmaktadır. Lütfen yeniden deneyin.';        
    }
    
    public function postDispatch(){
    	$this->view->headTitle(' HATA | Hubitat');
    }
}