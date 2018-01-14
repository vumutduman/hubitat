<?php
/**
 * artinCMS Console Operations Controller
 * @author artuc <mustafa@artuc.net>
 * @package artinCMS
 */
require_once('application/artuc/ImageResize.php');
class ConsoleController extends Zend_Controller_Action{
	public $oDB;
	public $oConfig;
	public $siteLayout;
	
	public function init(){
		$this->oConfig = Zend_Registry::get('oConfig');

		$this->siteLayout = Zend_Layout::getMvcInstance();
		$this->siteLayout->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();
		$this->view->setEscape('stripslashes');

		$connection = new MongoClient();
		$this->oDB = $connection->hubitat;
	}

	public function fileDateAction(){
		$files = $this->oDB->files;
		$oFiles = $files->find(array())->sort(array('on_date'=>1));
		$records = iterator_to_array($oFiles);
		if(count($records) > 0){
			echo "<pre>";
			foreach($records as $file){
				$ed = date_parse($file['EndDate']);
				
				$endDate = DateTime::createFromFormat('d-M-Y', $ed['day'].'-'.$ed['month'].'-'.$ed['year']);
				$today = new DateTime();
				$to_list = array();
				$file_list = array();

				if($today > $endDate){
					echo $file['Name'];
					if(array_key_exists('crew', $file)){
						$crew = $this->view->Content()->getCrewByParams(array('_id'=>$file['crew']));
						$to_list[] = $crew;
						$file_list[] = $file;
					}
				}

				if(count($to_list) > 0){
					$this->view->file_list = $file_list;
					$mailingContent = $this->view->render('resource/mail/file-reminder.phtml');
					$mailer = new eMailer();
					$deliver = array();
					foreach($to_list as $crew){
						$deliver[] = array("toName"=>$crew['Name'], "toEmail"=>$crew['Email']);
					}
					$mailer->setParams($deliver,
						$mailingContent,
						$this->view->translate('Hubitat File Reminder')
					);
				}
				echo "<hr />";
			}
		}
	}

}














































