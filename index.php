<?php
	set_include_path(get_include_path().PATH_SEPARATOR.'application/models/'.PATH_SEPARATOR.'application/artuc/');
	ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);
	date_default_timezone_set('Europe/Istanbul');
	
	require_once "Zend/Loader/Autoloader.php";
	$oAutoloader = Zend_Loader_Autoloader::getInstance();
	$oAutoloader->setFallbackAutoloader(true);
	
	Zend_Registry::set('oConfig', $oConfig = new Zend_Config_Xml('application/config.xml', 'production', array('skipExtends' => true, 'allowModifications' => false)));

	$siteLayout = Zend_Layout::startMvc($oConfig->resources->template->toArray());
	
	Zend_Registry::set('Zend_Locale', new Zend_Locale('tr'));
	Zend_Loader::loadClass('Zend_Translate');
	$oFrontController = Zend_Controller_Front::getInstance();
	$oFrontController->setControllerDirectory('application/controllers');
	$oFrontController->throwExceptions(true);
	$oRouter = $oFrontController->getRouter();
	$oConfig = new Zend_Config_Xml('application/routes.xml');
	$oRouter->addConfig($oConfig, 'routes');
	
	$oFrontController->dispatch();