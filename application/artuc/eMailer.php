<?php
/**
 * Uses phpmailer mail functions and Zend MVC views for mail formatting.
 * Check layouts/mailer/ folder for formatted mails.
 * @author artuc
 * @created 13.05.2013 - 01:31
 */
require_once('application/artuc/PHPMailerAutoload.php');
class eMailer{
	/**
	 * 
	 * Sends mail over smtp auth
	 * @param array(toEmail, toName) $to 
	 * @param html string (rendered with the zend_view) $content
	 * @param string (translated with zend_translate) $subject
	 */
	public $mailer;
	function setParams($to, $content, $subject){
		$oAppConfig = Zend_Registry::get('oConfig');
		
		$this->mailer = new PHPMailer();
		$this->mailer->IsSMTP();
		$this->mailer->IsHTML(true);
		$this->mailer->CharSet="UTF-8";
		$this->mailer->SMTPDebug = 0;
		$this->mailer->SMTPAuth = true;
		$this->mailer->SMTPSecure = 'ssl';
		
		$this->mailer->Host = $oAppConfig->resources->mailSettings->mailHost;
		$this->mailer->Port = $oAppConfig->resources->mailSettings->mailPort;
		$this->mailer->Username = $oAppConfig->resources->mailSettings->fromEmail;
		$this->mailer->Password = $oAppConfig->resources->mailSettings->fromPassword;
		
		$this->mailer->FromName = $oAppConfig->resources->mailSettings->fromName;
		$this->mailer->From = $oAppConfig->resources->mailSettings->fromEmail;
		$this->mailer->Subject = $subject;
		
		foreach($to as $t){
			$this->mailer->AddAddress($t['toEmail'], $t['toName']);
		}
		
		$this->mailer->MsgHTML($content);
		return $this->mailer->Send();
	}
}
?>