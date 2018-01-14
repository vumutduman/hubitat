<?php
/**
 * @author artuc
 * @version 3
 *  
 * Content Helper
 * @uses viewHelper Zend_View_Helper
 * @author artuc <mustafa@artuc.net>
 * @package artinCMS
 * 
 */
class Zend_View_Helper_Content extends Zend_View_Helper_Abstract{
	public $oConfig;
	public $oDB;
	
	public function Content(){
		$client = $connection = new MongoClient();
		$this->oDB = $connection->hubitat;

		return $this;
	}

	public function getRemainingDays($startDate, $endDate){
		$s_date = new DateTime();
		$e_date = new DateTime($endDate);

		$diff = $e_date->diff($s_date)->format("%a");
		return $diff;
	}

	public function getProjectNo(){
		$workorder_count = $this->oDB->workOrders->find();
		if($workorder_count){
			$arr_workorder = iterator_to_array($workorder_count);
			$workorder_count = count($arr_workorder);

			if($workorder_count > 0){
				return date('Ym').'-'.sprintf("%'.04d\n", $workorder_count);
			}
		}

		return date('Ym').'-'.sprintf("%'.04d\n", 1);
	}

	public function getVessel($params, $field=false){
		$vessel = $this->oDB->vessels->findOne($params);
		if($vessel){
			if($field){
				return $vessel[$field];
			}

			return $vessel;
		}

		return false;
	}

	public function getCrewByParams($params){
		$crew = $this->oDB->crews->findOne($params);
		if($crew){
			return $crew;
		}
		
		return false;
	}


	public function updateCrewByParams($params, $data){
		$crew = $this->oDB->crews->findOne($params);
		if($crew){
			$this->oDB->crews->update($params, array('$set'=>$data));
			return true;
		}else{
			exit('No Crew Found');
		}

		return false;
	}

	public function getFileType($type){
		$fileType = ['type'=> $type];
		switch($type){
			case 'class':
				$fileType['title'] = 'Class';
			break;
			case 'manufacturer':
				$fileType['title'] = 'Manufacturer';
			break;
			case 'port-authority':
				$fileType['title'] = 'Port Authority';
			break;
			case 'check-list':
				$fileType['title'] = 'Check List';
			break;
			case 'iso':
				$fileType['title'] = 'ISO';
			break;
			case 'other':
				$fileType['title'] = 'Other';
			break;
		}

		return $fileType;
	}
}