<?php
/**
 * @author artuc
 * @version 1.0
 *  
 * Members Helper
 * @author artuc <mustafa@artuc.net>
 * @package BacakVar
 * 
 */
require_once 'application/artuc/ImageResize.php';
class Zend_View_Helper_Members extends Zend_View_Helper_Abstract{
	public $oConfig;
	public $oDB;
	public $oUser;
	
	public function Members(){
		
	}
	
	public function requireLogin(){
		if(isset($this->oUser)){
			return true;
		}
		
		return false;
	}
	
	public function createUsername($email){
		$getFirst = explode('@', $email);
		$clearStr = ImageResize::renametotr($getFirst[0]);
		
		$username = $clearStr;
		$mMembers = new SiteMembers();
		$oMember = $mMembers->fetchRow("username='".$clearStr[0]."'");
		if($oMember != null && $oMember->count() > 0){
			$username = $username.'_'.substr(time(), 0,7);
		}
		
		return $username;
	}
	
	public function shortName($memberName){
		$expMemberName = explode(' ', $memberName);
		return ucfirst($expMemberName[0]);
	}
	
	public function getToUserID($currentUserID, $fromUserID, $toUserID){
		if($currentUserID == $fromUserID){
			return $toUserID;
		}
		
		return $fromUserID;
	}
	
	public function getMemberById($memberID, $field=false){
		$mMembers = new SiteMembers();
		$oMember = $mMembers->fetchRow("ID='".$memberID."'");
		if($oMember != null){
			if($field !== false){
				return $oMember->$field;
			}
			
			return $oMember;
		}
		
		return false;
	}
}