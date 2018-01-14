<?php
class ModelUtils extends Zend_Db_Table_Abstract{
	public function getRequiredFields(){
		if(isset($this->_requiredFields)){
			return $this->_requiredFields;
		}
		
		return false;
	}
	
	public function validateInputs($params){
		$fields = $this->getRequiredFields();
		if($fields){
			foreach($params as $k => $v){
				if(in_array($k, $fields)){
					$val = trim($v);
					if(strlen($val) == 0){
						return false;
					}
				}
			}
		}
		
		return true;
	}
	
}