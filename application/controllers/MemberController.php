<?php
/**
 * artuc Content Management System v3
 * @author artuc <mustafa@artuc.net>
 * @package artinCMS
 */
require_once('application/artuc/eMailer.php');
require_once('application/artuc/ImageResize.php');
class IndexController extends Zend_Controller_Action{
	public $oDB;
	public $oConfig;
	public $siteLayout;
	public $oUser;

	public function init(){
		$this->oConfig = Zend_Registry::get('oConfig');
		$this->siteLayout = Zend_Layout::getMvcInstance();
		$this->siteLayout->setLayout('layout');
		$this->view->setEscape('stripslashes');

		$connection = new MongoClient();
		$this->oDB = $connection->hubitat;

	}
	
	public function indexAction(){
		$this->view->headTitle('Member Profile');
	}


	public function addToLog($data){
		$data['on_date'] = new MongoDate();
		$data['by'] = 'artuc';
		$this->oDB->userLogs->insert($data);
	}

	public function postDispatch(){
		$this->view->headTitle(' | Hubitat');
	}
}
