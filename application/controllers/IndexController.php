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
	public $formData;

	public function init(){
		$this->oConfig = Zend_Registry::get('oConfig');
		$this->siteLayout = Zend_Layout::getMvcInstance();
		$this->siteLayout->setLayout('layout');
		$this->view->setEscape('stripslashes');

		$connection = new MongoClient();
		$this->oDB = $connection->hubitat;

		$this->formData = new Zend_Session_Namespace('formData');
		$this->oUser = new Zend_Session_Namespace('panelUser');
		if(isset($this->oUser->hubiUser)){
			$this->view->oUser = $this->oUser->hubiUser;
		}
	}
	
	public function indexAction(){
		$this->requireLogin();
		$this->view->headTitle('Home');
		$this->view->isMainpage = true;
	}

	//Overview
	public function overviewAction(){
		$this->requireLogin();
		$this->view->headTitle('Overview');
		$this->view->isMainpage = true;
	}

	//Workorders
	public function workOrdersAction(){
		$this->requireLogin();
		$this->view->headTitle('Work Orders');
		$this->view->moduleTitle = 'Work Orders';
		$workorders = $this->oDB->workorders;
		if($this->oUser->hubiUser['Role'] == 'Admin'){
			$workOrderFilter = array();
		}else{
			$workOrderFilter = array("Crew"=>$this->oUser->hubiUser['_id']);
		}

		$records = iterator_to_array($workorders->find($workOrderFilter)->sort(array('Meta.updated'=>1)));
		if(count($records) > 0){
			$this->view->workOrders = $records;
		}
	}

	public function workorderAddAction(){
		$this->requireLogin();
		$this->view->headTitle('Work Orders ');
		$this->view->moduleTitle = 'Work Orders';

		$workorders = $this->oDB->workorders;

		if($this->oUser->hubiUser['Role'] == 'Admin'){
			$workOrderFilter = array();
		}else{
			$workOrderFilter = array("Crew"=>$this->oUser->hubiUser['_id']);
		}

		$records = iterator_to_array($workorders->find($workOrderFilter)->sort(array('Meta.updated'=>1)));
		if(count($records) > 0){
			$this->view->workOrders = $records;
		}

		$this->view->headTitle("Create Workorder");

		//Fetch crews
		$crews = $this->oDB->crews->find(array())->sort(array('Name'=>1));
		if($crews){
			$crewList = iterator_to_array($crews);
			if(count($crewList) > 0){
				$this->view->oCrews = $crewList;
			}
		}

		//Fetch offices
		$offices = $this->oDB->offices->find(array());
		if($offices){
			$officeList = iterator_to_array($offices);
			if(count($officeList) > 0){
				$this->view->oOffices = $officeList;
			}
		}

		//Fetch vessels
		$vessels = $this->oDB->vessels->find(array());
		if($vessels){
			$vesselList = iterator_to_array($vessels);
			if(count($vesselList) > 0){
				$this->view->vesselList = $vesselList;
			}
		}

		//Fetch Agencies
		$agencies = $this->oDB->agencies->find(array());
		if($agencies){
			$agencyList = iterator_to_array($agencies);
			if(count($agencyList) > 0){
				$this->view->agencyList = $agencyList;
			}
		}

		//Fetch Certificates
		$certificates = $this->oDB->certificates->find(array());
		if($certificates){
			$certificatesList = iterator_to_array($certificates);
			if(count($certificatesList) > 0){
				$this->view->CertificateList = $certificatesList;
			}
		}

		if($this->getRequest()->isPost()){
			print_r($this->_getAllParams());
			exit();
		}
	}

	public function workorderDetailAction(){
		$this->requireLogin();
		$this->view->headTitle('Work Orders ');
		$this->view->moduleTitle = 'Work Orders';

		$workorders = $this->oDB->workorders;

		if($this->oUser->hubiUser['Role'] == 'Admin'){
			$workOrderFilter = array("Office"=>$this->oUser->hubiUser['Office']);
		}else{
			$workOrderFilter = array("Crew"=>$this->oUser->hubiUser['_id']);
		}

		$records = iterator_to_array($workorders->find($workOrderFilter)->sort(array('Meta.updated'=>1)));
		if(count($records) > 0){
			$this->view->workOrders = $records;
		}

		if($this->oUser->hubiUser['Role'] == 'Admin'){
			$workOrderFilter = array("_id"=>new MongoId($this->_getParam('workorderId')));
		}else{
			$workOrderFilter = array("_id"=>new MongoId($this->_getParam('workorderId')), "Crew"=>array($this->oUser->hubiUser['_id']));
		}

		$workOrder = $workorders->findOne($workOrderFilter);
		if($workOrder){
			$this->view->headTitle($workOrder['ProjectNo']);
			$this->view->workOrder = $workOrder;

			//Fetch crews
			$crews = $this->oDB->crews->find(array())->sort(array('Name'=>1));
			if($crews){
				$crewList = iterator_to_array($crews);
				if(count($crewList) > 0){
					$this->view->oCrews = $crewList;
				}
			}

			//Fetch offices
			$offices = $this->oDB->offices->find(array());
			if($offices){
				$officeList = iterator_to_array($offices);
				if(count($officeList) > 0){
					$this->view->oOffices = $officeList;
				}
			}

			//Fetch vessels
			$vessels = $this->oDB->vessels->find(array());
			if($vessels){
				$vesselList = iterator_to_array($vessels);
				if(count($vesselList) > 0){
					$this->view->vesselList = $vesselList;
				}

				//Vessel detail
				$vesselDetail = $this->oDB->vessels->findOne(array('_id'=> new MongoId($workOrder['Vessel']['_id'])));
				if($vesselDetail){
					$this->view->vessel = $vesselDetail;
				}
			}

			//Fetch Agencies
			$agencies = $this->oDB->agencies->find(array());
			if($agencies){
				$agencyList = iterator_to_array($agencies);
				if(count($agencyList) > 0){
					$this->view->agencyList = $agencyList;
				}

				//Agency detail
				$agencyDetail = $this->oDB->agencies->findOne(array('_id'=> new MongoId($workOrder['Agency'])));
				if($agencyDetail){
					$this->view->agency = $agencyDetail;
				}
			}

			//Fetch Certificates
			$certificates = $this->oDB->certificates->find(array());
			if($certificates){
				$certificatesList = iterator_to_array($certificates);
				if(count($certificatesList) > 0){
					$this->view->certificatesList = $certificatesList;
				}
			}

			//Fetch Certificates
			$certificates = $this->oDB->certificates->find(array());
			if($certificates){
				$certificatesList = iterator_to_array($certificates);
				if(count($certificatesList) > 0){
					$this->view->CertificateList = $certificatesList;
				}
			}
			
			if($this->getRequest()->isPost()){
				echo '<pre>';
				print_r($this->_getAllParams());

				print_r($workOrder);
				exit();
			}
		}
	}

	//Partial update WorkOrder
	public function workorderUpdateAction(){
		$this->requireLogin();
		$this->siteLayout->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();

		if($this->getRequest()->isPost()){
			$workorders = $this->oDB->workorders;

			if($this->oUser->hubiUser['Role'] == 'Admin'){
				$workOrderFilter = array("Office"=>$this->oUser->hubiUser['Office']);
			}else{
				$workOrderFilter = array("Crew"=>$this->oUser->hubiUser['_id']);
			}

			if($this->oUser->hubiUser['Role'] == 'Admin'){
				$workOrderFilter = array("_id"=>new MongoId($this->_getParam('workorderId')));
			}else{
				$workOrderFilter = array("_id"=>new MongoId($this->_getParam('workorderId')), "Crew"=>array($this->oUser->hubiUser['_id']));
			}
			$workOrder = $workorders->findOne($workOrderFilter);
			if($workOrder){
				$log = array("date"=>new MongoDate(), "events"=>[]);
				if($this->_getParam('Crew', false)){
					$workOrder['Crew'] = $this->_getParam('Crew');
					$log["events"][] = array("Event"=> "Updated Workorder: Assigned to New Crew:" . $this->_getParam('Crew'));
				}

				if($this->_getParam('Office', false)){
					$workOrder['Office'] = $this->_getParam('Office');
					$log["events"][] = array("Event"=> "Updated Workorder: Assigned to New Office:" . $this->_getParam('Office'));
				}

				if($this->_getParam('Agency', false)){
					$workOrder['Agenct'] = $this->_getParam('Agency');
					$log["events"][] = array("Event"=> "Updated Workorder: Changed Agency to:" . $this->_getParam('Agency'));
				}
				
				$workOrder['Logs'][] = $log;
				unset($workOrder['_id']);
				$workorders->update(array('_id'=> new MongoId($this->_getParam('workorderId'))), array('$set'=> $workOrder));
				
				$this->_helper->json(array("Status"=>true));
			}else{
				$this->_helper->json(array("Status"=>false));
			}
		}else{
			$this->_helper->json(array("Status"=>false));
		}
	}

	//Crews
	public function crewAction(){
		$this->requireLogin();
		$this->view->headTitle('Crews');
		$this->view->moduleTitle = 'Crews';
		$crews = $this->oDB->crews;
		$records = iterator_to_array($crews->find(array())->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oUserList = $records;
		}

		$offices = $this->oDB->offices->find(array());
		if($offices){
			$officeList = iterator_to_array($offices);
			if(count($officeList) > 0){
				$this->view->offices = $officeList;
			}
		}
	}

	public function crewDetailAction(){
		$this->requireLogin();
		$this->view->headTitle('Crew Details');
		$this->view->moduleTitle = 'Crews';
		$crews = $this->oDB->crews;
		//Fetch all
		$records = iterator_to_array($crews->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oUserList = $records;
			$crew = $crews->findOne(array('_id'=>new MongoId($this->_getParam('crewId'))));
			if($crew){
				$det = $this->view->Content()->getCrewByParams(array('Email'=>$crew['Email']));
				$this->view->oCrew = $crew;
				$offices = $this->oDB->offices->find(array());
				if($offices){
					$officeList = iterator_to_array($offices);
					if(count($officeList) > 0){
						$this->view->offices = $officeList;
					}
				}

				if($this->getRequest()->isPost()){
					$imageName = $crew['Avatar'];
					if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
			        	$path = 'content/images/profile/';
			            $imageResizer = new ImageResize();
			            $imageName = $imageResizer->rename($path.'md/', time());
			            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
			            $imageResizer->resize(128,128);
			            $imageResizer->save($path.'md/'.$imageName);
			            $imageResizer->resize(64,64);
			            $imageResizer->save($path.'sm/'.$imageName);
			        }

			        //Update profile
			        $oCrew = array(
						'Username'=>$this->_getParam('Username'),
						'Name'=>$this->_getParam('Name'),
						'Surname'=>$this->_getParam('Surname'),
						'Office'=>$this->_getParam('Office'),
						'Phone'=>$this->_getParam('Phone'),
						'GSM'=>$this->_getParam('GSM'),
						'Email'=>$this->_getParam('Email'),
						'Avatar' => $imageName,
						'Meta' => array(
							'updated' => new MongoDate()
						)
					);
					if($this->oUser->hubiUser['Role'] == 'Admin'){
						$oCrew['Role'] = $this->_getParam('Role');
						if($this->_getParam('Password', 0)){
							$oCrew['Password'] = SHA1($this->_getParam('Password'));
						}
					}

			        $crewId = $this->_getParam('crewId');
			        if($this->oUser->hubiUser['_id']->__toString() == $crewId){
						if($this->_getParam('Password', 0)){
							$oCrew['Password'] = SHA1($this->_getParam('Password'));
						}
					}

					$this->oDB->crews->update(array('Username' => $this->_getParam('Username')), array('$set'=> $oCrew));
					$this->view->isUpdated = true;
					$oCrew['_id'] = $crewId;
					
					$this->view->oCrew = $oCrew;
					$this->addToLog(array('Action'=>'Updated','Type'=>'Crew','params'=>$this->_getParam('Username')));
				}
				
				$this->view->headTitle(" | ".$crew['Name']." ".$crew['Surname']);
			}
		}
		
	}

	public function crewAddAction(){
		$this->requireLogin();
		if($this->getRequest()->isPost()){
			$checkEmail = $this->view->Content()->getCrewByParams(array('Email'=>$this->_getParam('Email')));
			$checkUsername = $this->view->Content()->getCrewByParams(array('Username'=>$this->_getParam('Username')));
			if($checkEmail === false && $checkUsername === false){
				$imageName = 'empty.png';
				
				if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
		        	$path = 'content/images/profile/';
		            $imageResizer = new ImageResize();
		            $imageName = $imageResizer->rename($path.'md/', time());
		            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
		            $imageResizer->resize(128,128);
		            $imageResizer->save($path.'md/'.$imageName);

		            $imageResizer->resize(64,64);
		            $imageResizer->save($path.'sm/'.$imageName);
		        }			
				$oCrew = array(
					'Username'=>$this->_getParam('Username'),
					'Name'=>$this->_getParam('Name'),
					'Surname'=>$this->_getParam('Surname'),
					'Office'=> ($this->_getParam('Office') == 0 ? false : $this->_getParam('Office')),
					'Phone'=>$this->_getParam('Phone'),
					'GSM'=>$this->_getParam('GSM'),
					'Email'=>$this->_getParam('Email'),
					'Password'=>$this->_getParam('Password'),
					'Avatar' => $imageName,
					'Meta' => array(
						'created' => new MongoDate(),
						'updated' => new MongoDate(),
						'status' => 1
					)
				);

				if($this->_getParam('Password', 0)){
					$oCrew['Password'] = SHA1($this->_getParam('Password'));
				}

				if($this->_getParam('Username', 0)){
					$oCrew['Username'] = $this->_getParam('Username');
				}
				
				if($this->oUser->hubiUser['Role'] == 'Admin'){
					$oCrew['Role'] = $this->_getParam('Role');
				}else{
					$oCrew['Role'] = 'User';
				}

				$this->oDB->crews->insert($oCrew);
				$this->addToLog(array('Action'=>'Created','Type'=>'Crew','params'=>$this->_getParam('Username')));
				$this->_redirect('/crews');
			}else{
				$this->formData->crew = $this->_getAllParams();
				$response = array(
					'status'=>false, 
					'error'=>'Bu Kullanıcı adı ya da E-mail adresi kullanılmaktadır.'
				);
				$this->addToLog(array('Action'=>'Fail','Type'=>'Crew','response'=>$response, 'params'=>$this->_getParam('Username')));
				print_r($response);
				exit();
			}
		}else{
			$this->_redirect('/error');
		}
	}

	public function crewDeleteAction(){
		$this->requireLogin();
		$office = $this->oDB->crews->findOne(array('_id'=> new MongoId($this->_getParam('crewId'))));
		if($office){
			$this->oDB->crews->remove(array('_id'=> new MongoId($this->_getParam('crewId'))), array("justOne" =>true));

			/** TODO remove related content from collections **/
			$this->addToLog(array('Action'=>'Deleted','Type'=>'Crew','params'=>$this->_getParam('crewId')));
			$this->_redirect('/crews');
		}
	}

	//Offices
	public function officesAction(){
		$this->requireLogin();

		$this->view->headTitle('Offices');
		$this->view->moduleTitle = 'Offices';
		$offices = $this->oDB->offices;
		$records = iterator_to_array($offices->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oOfficeList = $records;
		}

		$crews = $this->oDB->crews;
		$crewCursor = iterator_to_array($crews->find(array('$or'=>array(array('Office'=>null))))->sort(array('Name'=>1)));
		if(count($crewCursor) > 0){
			$this->view->oCrewList= $crewCursor;
		}
	}

	public function officeDetailAction(){
		$this->requireLogin();
		$this->view->headTitle('Office Details');
		$this->view->moduleTitle = 'Offices';
		$offices = $this->oDB->offices;

		//Fetch all
		$records = iterator_to_array($offices->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oOfficeList = $records;

			$oOffice = $offices->findOne(array('_id'=>new MongoId($this->_getParam('officeId'))));

			if($oOffice){
				//Fetch all
				$crews = $this->oDB->crews;
				$filter = array('$or'=>array(array("Office"=>null), array("Office"=>array("_Id"=> $this->_getParam('officeId')))));
				$crewIterator = $crews->find($filter)->sort(array("Name"=> 1));
				$crewCursor = iterator_to_array($crewIterator);

				if(count($crewCursor) > 0){
					$this->view->oCrewList= $crewCursor;
				}

				if($this->getRequest()->isPost()){
					$imageName = $oOffice['Avatar'];
					if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
			        	$path = 'content/images/profile/';
			            $imageResizer = new ImageResize();
			            $imageName = $imageResizer->rename($path.'md/', time());
			            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
			            $imageResizer->resize(128,128);
			            $imageResizer->save($path.'md/'.$imageName);
			            $imageResizer->resize(64,64);
			            $imageResizer->save($path.'sm/'.$imageName);
			        }

			        //Update office
			        $oOffice = array(
						'Name'=>$this->_getParam('Name'),
						'ShortName'=>$this->_getParam('ShortName'),
						'Phone'=>$this->_getParam('Phone'),
						'Email'=>$this->_getParam('Email'),
						'Address'=>$this->_getParam('Address'),
						'Avatar' => $imageName,
						'Meta' => array(
							'updated' => new MongoDate()
						)
					);
			        
			        $crewId = $this->_getParam('crewId', false);
			        if($crewId){
				        $crew = $this->view->Content()->getCrewByParams(array('_id'=>new MongoId($crewId)));
				        $person = array('Name'=>$crew['Name'], 'Surname'=> $crew['Surname'], '_Id'=>New MongoId($crewId));
				        $oOfice['Person'] = $person;
			        }else{
			        	$oOfice['Person'] = null;
			        }

					$this->oDB->offices->update(array('_id'=>new MongoId($this->_getParam('officeId'))), array('$set'=> $oOffice));

					//Update crew with  office
					if($crewId){
						$office = array('Name'=> $oOfice['Name'], '_Id'=> $oOffice['_id']);
						$this->view->Content()->updateCrewByParams(array('_id'=>new MongoId($crewId)), array('Office'=>$office));
					}

					$oOffice['_id'] = $this->_getParam('officeId');
					$this->view->isUpdated = true;
					$this->addToLog(array('Action'=>'Updated','Type'=>'Office','params'=>$this->_getParam('officeId')));
				}

				$this->view->oOffice = $oOffice;
			}else{
				exit('No Office found with given ID.');
			}
		}
	}

	public function officeAddAction(){
		$this->requireLogin();
		if($this->getRequest()->isPost()){
			$imageName = 'empty.png';
			
			if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
	        	$path = 'content/images/profile/';
	            $imageResizer = new ImageResize();
	            $imageName = $imageResizer->rename($path.'md/', time());
	            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
	            $imageResizer->resize(128,128);
	            $imageResizer->save($path.'md/'.$imageName);
	            $imageResizer->resize(64,64);
	            $imageResizer->save($path.'sm/'.$imageName);
	        }			
			
	        //Update office
	        $oOffice = array(
				'Name'=>$this->_getParam('Name'),
				'ShortName'=>$this->_getParam('ShortName'),
				'Phone'=>$this->_getParam('Phone'),
				'Email'=>$this->_getParam('Email'),
				'Address'=>$this->_getParam('Address'),
				'Avatar' => $imageName,
				'Person' => null,
				'Meta' => array(
					'updated' => new MongoDate()
				)
			);
	        
	        $crewId = $this->_getParam('crewId', false);
			if($crewId){
				$crew = $this->view->Content()->getCrewByParams(array('_id'=>new MongoId($crewId)));
		        $person = array('Name'=>$crew['Name'], 'Surname'=> $crew['Surname'], '_Id'=>New MongoId($crewId));
		        $oOffice['Person'] = $person;
		    }

			$this->oDB->offices->insert($oOffice);

			//Update crew with new inserted office
			if($crewId){
				$office = array('Name'=>$oOffice['Name'], '_Id'=>$oOffice['_id']);
				//$this->view->Content()->updateCrewByParams(array('_id'=>new MongoId($crewId)), array('Office'=>$office));
			}

			$this->view->isUpdated = true;
			$this->view->oOffice = $oOffice;
			$this->addToLog(array('Action'=>'Created','Type'=>'Office','params'=>$this->_getParam('Name')));
			$this->_redirect('/offices');
		}
	}

	public function officeDeleteAction(){
		$this->requireLogin();
		$office = $this->oDB->offices->findOne(array('_id'=> new MongoId($this->_getParam('officeId'))));
		if($office){
			$this->oDB->offices->remove(array('_id'=> new MongoId($this->_getParam('officeId'))), array("justOne" =>true));

			/** TODO remove related content from collections **/
			$this->addToLog(array('Action'=>'Deleted','Type'=>'Office','params'=>$this->_getParam('officeId')));				
			$this->_redirect('/offices');
		}
	}


	//Vessels
	public function vesselsAction(){
		$this->requireLogin();
		$this->view->headTitle('Vessels');
		$this->view->moduleTitle = 'Vessels';
		$vessels = $this->oDB->vessels;
		$records = iterator_to_array($vessels->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oVesselList = $records;
		}
	}

	public function vesselDetailAction(){
		$this->requireLogin();
		$this->view->headTitle('Vessel Details');
		$this->view->moduleTitle = 'Vessels';
		$vessels = $this->oDB->vessels;

		//Fetch all
		$records = iterator_to_array($vessels->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oVesselList = $records;
			$vessel = $vessels->findOne(array('_id'=>new MongoId($this->_getParam('vesselId'))));
			if($vessel){
				$this->view->oVessel = $vessel;

				if($this->getRequest()->isPost()){
					$imageName = $vessel['Avatar'];
					if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
			        	$path = 'content/images/profile/';
			            $imageResizer = new ImageResize();
			            $imageName = $imageResizer->rename($path.'md/', time());
			            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
			            $imageResizer->resize(128,128);
			            $imageResizer->save($path.'md/'.$imageName);
			            $imageResizer->resize(64,64);
			            $imageResizer->save($path.'sm/'.$imageName);
			        }

			        //Update vessel details
			        $oVessel = array(
						'Name'=>$this->_getParam('Name'),
						'Phone'=>$this->_getParam('Phone'),
						'Owner'=>$this->_getParam('Owner'),
						'Flag'=>$this->_getParam('Flag'),
						'IMO'=>$this->_getParam('IMO'),
						'Class'=>$this->_getParam('Class'),
						'Avatar' => $imageName,
						'Meta' => array(
							'updated' => new MongoDate()
						)
					);
			        
			        $vesselId = $this->_getParam('vesselId');
					$this->oDB->vessels->update(array('_id'=>new MongoId($this->_getParam('vesselId'))), array('$set'=> $oVessel));
					$this->view->isUpdated = true;
					$oVessel['_id'] = $this->_getParam('vesselId');
					$this->view->oVessel = $oVessel;
					$this->addToLog(array('Action'=>'Updated','Type'=>'Vessel','params'=>$this->_getParam('vesselId')));
				}
				
			}
		}		
	}

	public function vesselAddAction(){
		$this->requireLogin();
		if($this->getRequest()->isPost()){
			$imageName = 'empty.png';
			
			if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
	        	$path = 'content/images/profile/';
	            $imageResizer = new ImageResize();
	            $imageName = $imageResizer->rename($path.'md/', time());
	            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
	            $imageResizer->resize(128,128);
	            $imageResizer->save($path.'md/'.$imageName);

	            $imageResizer->resize(64,64);
	            $imageResizer->save($path.'sm/'.$imageName);
	        }			

	        //Update vessel details
	        $oVessel = array(
				'Name'=>$this->_getParam('Name'),
				'Phone'=>$this->_getParam('Phone'),
				'Owner'=>$this->_getParam('Owner'),
				'Flag'=>$this->_getParam('Flag'),
				'IMO'=>$this->_getParam('IMO'),
				'Class'=>$this->_getParam('Class'),
				'Avatar' => $imageName,
				'Meta' => array(
					'updated' => new MongoDate()
				)
			);
	        
			$this->oDB->vessels->insert($oVessel);
			$this->view->isUpdated = true;
			$this->view->oVessel = $oVessel;
			$this->addToLog(array('Action'=>'Created','Type'=>'Vessel','params'=>$this->_getParam('vesselId')));
			$this->_redirect('/vessels');
		}
	}

	public function vesselDeleteAction(){
		$this->requireLogin();
		$vessel = $this->oDB->vessels->findOne(array('_id'=> new MongoId($this->_getParam('vesselId'))));
		if($vessel){
			$this->oDB->vessels->remove(array('_id'=> new MongoId($this->_getParam('vesselId'))), array("justOne" =>true));

			/** TODO remove related content from collections **/
			$this->addToLog(array('Action'=>'Deleted','Type'=>'Vessel','params'=>$this->_getParam('vesselId')));
			$this->_redirect('/vessels');
		}
	}


	//Agencies
	public function agenciesAction(){
		$this->requireLogin();
		$this->view->headTitle('Agencies');
		$this->view->moduleTitle = 'Agencies';
		$agencies = $this->oDB->agencies;
		$records = iterator_to_array($agencies->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oAgencyList = $records;
		}
	}

	public function agencyDetailAction(){
		$this->requireLogin();
		$this->view->headTitle('Agency Details');
		$this->view->moduleTitle = 'Agencies';
		$agencies = $this->oDB->agencies;

		//Fetch all
		$records = iterator_to_array($agencies->find()->sort(array('Name'=>1)));
		if(count($records) > 0){
			$this->view->oAgencyList = $records;
			$agency = $agencies->findOne(array('_id'=>new MongoId($this->_getParam('agencyId'))));
			if($agency){
				$this->view->oAgency = $agency;

				if($this->getRequest()->isPost()){
					$imageName = $agency['Avatar'];
					if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
			        	$path = 'content/images/profile/';
			            $imageResizer = new ImageResize();
			            $imageName = $imageResizer->rename($path.'md/', time());
			            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
			            $imageResizer->resize(128,128);
			            $imageResizer->save($path.'md/'.$imageName);
			            $imageResizer->resize(64,64);
			            $imageResizer->save($path.'sm/'.$imageName);
			        }

			        //Update agency details
			        $oAgency = array(
						'Name'=>$this->_getParam('Name'),
						'Phone'=>$this->_getParam('Phone'),
						'Email'=>$this->_getParam('Email'),
						'Address'=>$this->_getParam('Address'),
						'Person'=>$this->_getParam('Person'),
						'Avatar'=>$imageName,
						'Meta' => array(
							'updated' => new MongoDate()
						)
					);
			        
			        $agencyId = $this->_getParam('agencyId');
					$this->oDB->agencies->update(array('_id'=>new MongoId($this->_getParam('agencyId'))), array('$set'=> $oAgency));
					$this->view->isUpdated = true;
					$oAgency['_id'] = $this->_getParam('agencyId');
					$this->view->oAgency = $oAgency;
					$this->addToLog(array('Action'=>'Updated','Type'=>'Agency','params'=>$this->_getParam('agencyId')));
				}
			}
		}	
	}

	public function agencyAddAction(){
		$this->requireLogin();
		if($this->getRequest()->isPost()){
			$imageName = 'empty.png';
			
			if(isset($_FILES['ProfileImage']) && strlen($_FILES['ProfileImage']['tmp_name']) > 0){
	        	$path = 'content/images/profile/';
	            $imageResizer = new ImageResize();
	            $imageName = $imageResizer->rename($path.'md/', time());
	            $imageResizer->load($_FILES['ProfileImage']['tmp_name']);
	            $imageResizer->resize(128,128);
	            $imageResizer->save($path.'md/'.$imageName);

	            $imageResizer->resize(64,64);
	            $imageResizer->save($path.'sm/'.$imageName);
	        }

	        //Update agency details
	        $oAgency = array(
				'Name'=>$this->_getParam('Name'),
				'Phone'=>$this->_getParam('Phone'),
				'Email'=>$this->_getParam('Email'),
				'Address'=>$this->_getParam('Address'),
				'Person'=>$this->_getParam('Person'),
				'Avatar' => $imageName,
				'Meta' => array(
					'updated' => new MongoDate()
				)
			);
	        
			$this->oDB->agencies->insert($oAgency);
			$this->view->isUpdated = true;
			$this->view->oAgency = $oAgency;
			$this->addToLog(array('Action'=>'Created','Type'=>'Agency','params'=>$this->_getParam('Name')));
			$this->_redirect('/agencies');
		}
	}

	public function agencyDeleteAction(){
		$this->requireLogin();
		$agency = $this->oDB->agencies->findOne(array('_id'=> new MongoId($this->_getParam('agencyId'))));
		if($agency){
			$this->oDB->agencies->remove(array('_id'=> new MongoId($this->_getParam('agencyId'))), array("justOne" =>true));

			/** TODO remove related content from collections **/
			$this->addToLog(array('Action'=>'Deleted','Type'=>'Agency','params'=>$this->_getParam('agencyId')));
			$this->_redirect('/agencies');
		}
	}

	//Files
	public function filesAction(){
		$this->requireLogin();
		$this->view->headTitle('Files');
		$files = $this->oDB->files;
		$fileType = $this->view->Content()->getFileType($this->_getParam('type'));
		$oFiles = $files->find(array('Type'=>$fileType['type']))->sort(array('on_date'=>-1));
		$records = iterator_to_array($oFiles);
		if(count($records) > 0){
			$this->view->oFilesList = $records;
		}

		$this->view->fileType = $fileType;
		$this->view->moduleTitle = $fileType['title'];
	}

	public function fileDetailsAction(){
		$this->requireLogin();
		$this->siteLayout->disableLayout();
		$fileType = $this->view->Content()->getFileType($this->_getParam('type'));
		$this->view->fileType = $fileType;
		if($this->getRequest()->isPost()){
			$this->getHelper('viewRenderer')->setNoRender();

			$oFile = array(
				'Name' => $this->_getParam('Name'),
				'StartDate' => $this->_getParam('StartDate'),
				'EndDate' => $this->_getParam('EndDate'),
				'Type' => $fileType['type'],
				'crew' => $this->oUser->hubiUser['_id'],
				'updated_on'=> new MongoDate()
			);

			if(isset($_FILES['oFile']) && strlen($_FILES['oFile']['tmp_name']) > 0){
				$path = 'content/files/';
				$imageService = new ImageResize();
				$fileName = $imageService->renametotr($_FILES['oFile']['name']);
				copy($_FILES['oFile']['tmp_name'], $path.$fileName);
				$oFile['File'] = $fileName;
			}

			if($this->_getParam('fileId', false)){
				$mFile = $this->oDB->files->findOne(array('_id'=>new MongoId($this->_getParam('fileId'))));
				if($mFile){
					$this->oDB->files->update(array('_id'=>new MongoId($this->_getParam('fileId'))), array('$set'=> $oFile));
					$this->addToLog(array('Action'=>'Updated','Type'=>'File', 'params'=>$fileName));

					$this->_redirect('/files/'.$fileType['type']);
				}
			}else{
				$oFile['on_date'] = new MongoDate();
				$this->oDB->files->insert($oFile);
				$this->_redirect('/files/'.$fileType['type']);
			}
		}else{
			$oFile = $this->oDB->files->findOne(array(
				'_id'=>new MongoId($this->_getParam('fileId')))
			);
			if($oFile){
				$this->view->oFile = $oFile;
			}else{
				$this->_redirect('/files/'.$fileType['type']);		
			}
		}
	}

	//Reset Password
	public function resetPasswordAction(){
		$this->siteLayout->disableLayout();	
		if($this->getRequest()->isPost()){
			if(!$this->_getParam('code', false)){
				$user = $this->oDB->crews->findOne(array('Email'=>$this->_getParam('email')));
				if($user){
					$resetCode = $this->view->Utils()->createRandom(16);
					$data = array(
						'on_date' => new MongoDate(),
						'code' => $resetCode
					);
					$this->oDB->crews->update(array('_id'=>$user['_id']), array('$set'=> $data));
					$this->view->resetUser = $user;
					$this->view->resetCode = $resetCode;

					$mailingContent = $this->view->render('resource/mail/reset-password-mail.phtml');
					$mailer = new eMailer();
					$mailer->setParams(array(array("toName"=>$user['Name'].' '.$user['Surname'], "toEmail"=>$user['Email'])),
						$mailingContent,
						$this->view->translate('Hubitat Password Reset')
					);
					$this->_redirect('/login/1');
				}else{
					$this->_redirect('/login/2');
				}
			}else{
				$user = $this->oDB->crews->findOne(array('code'=>$this->_getParam('code')));
				if($user){
					if($this->_getParam('new_password') == $this->_getParam('new_password_again')){
						$data = array('on_date'=>new MongoDate(), 'code'=>null, 'Password'=>SHA1($this->_getParam('new_password')));
						$this->oDB->crews->update(array('_id'=>$user['_id']), array('$set'=> $data));
						$this->oUser->hubiUser = $user;
						$this->_redirect('/crews/'.$user['_id']);
					}else{
						$this->view->password_error = true;
					}
				}
			}
		}else{
			if($this->_getParam('code', 0)){
				$user = $this->oDB->crews->findOne(array('code'=>$this->_getParam('code')));
				if($user){
					$this->view->user = $user;
				}
				$this->view->error = true;
			}else{
				$this->view->error = true;
			}
		}
	}

	//Login
	public function loginAction(){
		$this->siteLayout->disableLayout();
		if($this->_getParam('reset', 0)){
			$this->view->password_reminder = $this->_getParam('reset');
		}

		if($this->getRequest()->isPost()){
			$user = $this->oDB->crews->findOne(array('Email'=>$this->_getParam('emailAddr'), 'Password'=>SHA1($this->_getParam('Password'))));
			if($user){
				$this->oUser->hubiUser = $user;
				$this->_redirect('/crews/'.$user['_id']);
			}else{
				$this->view->isError = true;
			}
		}
	}

	//Logout
	public function logoutAction(){
		$this->siteLayout->disableLayout();
		$this->oUser->hubiUser = false;
		$this->_redirect('/login');
	}

	//UserLog Service
	public function addToLog($data){
		$data['on_date'] = new MongoDate();
		$data['by'] = 'artuc';
		$this->oDB->userLogs->insert($data);
	}

	//Authentication Control
	public function requireLogin(){
		if(isset($this->oUser->hubiUser) && is_array($this->oUser->hubiUser)){
			return true;
		}else{
			$this->_redirect('/login');
		}
	}

	public function postDispatch(){
		$this->view->headTitle(' | Hubitat');
	}
}
